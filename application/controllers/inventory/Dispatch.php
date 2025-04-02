<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dispatch extends PS_Controller
{
  public $menu_code = 'ICODDP';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'PICKPACK';
	public $title = 'Dispatch';
  public $filter;
  public $error;
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/dispatch';
    $this->load->model('inventory/dispatch_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/channels_model');

    $this->load->helper('dispatch');
    $this->load->helper('channels');
    $this->load->helper('sender');
    $this->load->helper('order');
    $this->load->helper('warehouse');

    $this->load->library('user_agent');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'dp_code', ''),
      'plate_no' => get_filter('plate_no', 'dp_plate_no',''),
      'sender' => get_filter('sender', 'dp_sender', 'all'),
      'channels' => get_filter('channels', 'dp_channels', 'all'),
      'from_date' => get_filter('from_date', 'dp_from_date', ''),
      'to_date' => get_filter('to_date', 'dp_to_date', ''),
      'status' => get_filter('status', 'dp_status', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_rows();
      $rows = $this->dispatch_model->count_rows($filter);
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $filter['data'] = $this->dispatch_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $this->load->view('inventory/dispatch/dispatch_list', $filter);
    }
  }


  public function add_new()
  {
    $this->load->view('inventory/dispatch/dispatch_add');
  }


  public function add()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if($this->pm->can_add)
    {
      if( ! empty($ds))
      {
        $date_add = empty($ds->date_add) ?  date('Y-m-d H:i:s') : db_date($ds->date_add);
        $code = $this->get_new_code($date_add);

        $arr = array(
          'code' => $code,
          'channels_code' => get_null($ds->channels_code),
          'channels_name' => get_null($ds->channels_name),
          'sender_code' => $ds->sender_code,
          'sender_name' => $ds->sender_name,
          'plate_no' => $ds->plate_no,
          'plate_province' => $ds->province,
          'driver_name' => $ds->driver_name,
          'date_add' => $date_add,
          'remark' => get_null(trim($ds->remark)),
          'user' => $this->_user->uname
        );

        if( ! $this->dispatch_model->add($arr))
        {
          $sc = FALSE;
          set_error('insert');
        }
        else
        {
          $arr = array(
            'plate_no' => $ds->plate_no,
            'province' => $ds->province
          );

          if( ! $this->dispatch_model->is_exists_carplate($ds->plate_no, $ds->province))
          {
            $this->dispatch_model->add_carplate($arr);
          }

          if( ! $this->dispatch_model->is_exists_driver($ds->driver_name))
          {
            $this->dispatch_model->add_driver($ds->driver_name);
          }
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  public function update()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if($this->pm->can_add)
    {
      if( ! empty($ds))
      {
        $code = $ds->code;

        $arr = array(
          'channels_code' => get_null($ds->channels_code),
          'channels_name' => get_null($ds->channels_name),
          'sender_code' => $ds->sender_code,
          'sender_name' => $ds->sender_name,
          'plate_no' => $ds->plate_no,
          'plate_province' => $ds->province,
          'driver_name' => $ds->driver_name,
          'remark' => get_null(trim($ds->remark)),
          'date_upd' => now(),
          'update_user' => $this->_user->uname
        );

        if( ! $this->dispatch_model->update($code, $arr))
        {
          $sc = FALSE;
          set_error('insert');
        }
        else
        {
          $arr = array(
            'plate_no' => $ds->plate_no,
            'province' => $ds->province
          );

          if( ! $this->dispatch_model->is_exists_carplate($ds->plate_no, $ds->province))
          {
            $this->dispatch_model->add_carplate($arr);
          }

          if( ! $this->dispatch_model->is_exists_driver($ds->driver_name))
          {
            $this->dispatch_model->add_driver($ds->driver_name);
          }
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  public function save()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->dispatch_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'P' OR $doc->status == 'S')
        {
          $this->db->trans_begin();

          $arr = array(
            'status' => 'S',
            'shipped_date' => now(),
            'date_upd' => now(),
            'update_user' => $this->_user->uname
          );

          if( ! $this->dispatch_model->update($doc->code, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to change document status";
          }

          if($sc === TRUE)
          {
            $arr = array(
              'line_status' => 'S',
              'date_upd' => now(),
              'update_user' => $this->_user->uname
            );

            if( ! $this->dispatch_model->update_details($doc->code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update row items";
            }
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }
        }
        else
        {
          $sc = FALSE;
          set_error('status');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function close_dispatch()
  {
    $sc = TRUE;

    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->dispatch_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'S')
        {
          $this->db->trans_begin();

          $arr = array(
            'status' => 'C',
            'shipped_date' => now(),
            'date_upd' => now(),
            'update_user' => $this->_user->uname
          );

          if( ! $this->dispatch_model->update($doc->code, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to change document status";
          }

          if($sc === TRUE)
          {
            $arr = array(
              'line_status' => 'C',
              'date_upd' => now(),
              'update_user' => $this->_user->uname
            );

            if( ! $this->dispatch_model->update_details($doc->code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update row items";
            }
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }
        }
        else
        {
          $sc = FALSE;
          set_error('status');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function add_to_dispatch()
  {
    $sc = TRUE;
    $id = $this->input->post('id');
    $code = $this->input->post('code');
    $channels_code = $this->input->post('channels');
    $channels_name = $this->input->post('channels_name');
    $order_code = $this->input->post('order_code');
    $row = [];

    if( ! empty($order_code) && ! empty($code))
    {
      $order = $this->orders_model->get_order_by_tracking($order_code);

      if(empty($order))
      {
        $order = $this->orders_model->get_order_by_reference($order_code);
      }

      if(empty($order))
      {
        $order = $this->orders_model->get($order_code);
      }

      if( ! empty($order))
      {
        if($order->state == 8 OR ($order->channels_code == 'SHOPEE' && $order->state == 7))
        {

          if( ! empty($channels_code))
          {
            if( ! empty($order->channels_code) && $order->channels_code != $channels_code)
            {
              $sc = FALSE;
              $this->error = "ออเดอร์ไม่ตรงช่องทางขาย";
            }
          }

          if($this->orders_model->is_cancel_request($order->code))
          {
            $sc = FALSE;
            $this->error = "{$order_code} : ออเดอร์นี้ถูกยกเลิกจาก platform";
          }

          $customer = $order->customer_code ." : ".(empty($order->customer_ref) ? $order->customer_name : $order->customer_ref);

          if($sc === TRUE)
          {
            $row = array(
              'dispatch_id' => $id,
              'dispatch_code' => $code,
              'order_code' => $order->code,
              'reference' => get_null($order->reference),
              'channels_code' => get_null($channels_code),
              'channels_name' => get_null($channels_name),
              'customer_code' => get_null($order->customer_code),
              'customer_name' => empty($order->customer_ref) ? get_null($order->customer_name) : $order->customer_ref,
              'user' => $this->_user->uname
            );

            $detail = $this->dispatch_model->get_detail_by_order($code, $order->code);

            if(empty($detail))
            {
              $cartons = $this->dispatch_model->count_order_box($order->code);
              $row['carton_qty'] = $cartons;
              $row['carton_shipped'] = 1;

              $dispatch_detail_id = $this->dispatch_model->add_detail($row);

              if($dispatch_detail_id)
              {
                $this->dispatch_model->update_order($order->code, $id);
                $row['id'] = $dispatch_detail_id;
                $row['channels'] = $channels_name;
                $row['customer'] = $customer;
              }
            }
            else
            {
              $carton_shipped = $detail->carton_shipped + 1;

              if($detail->carton_qty >= $carton_shipped)
              {
                $arr = array(
                  'carton_shipped' => $carton_shipped
                );

                if( ! $this->dispatch_model->update_detail($detail->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update carton shipped";
                }

                if($sc === TRUE)
                {
                  $row['id'] = $detail->id;
                  $row['carton_shipped'] = $carton_shipped;
                  $row['channels'] = $channels_name;
                  $row['customer'] = $customer;
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ออเดอร์ซ้ำ : {$order_code}";
              }
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid order status";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid order number";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $row : NULL
    );

    echo json_encode($arr);
  }


  public function edit($code)
  {
    $this->load->library('user_agent');

    if($this->pm->can_add OR $this->pm->can_edit)
    {
      $doc = $this->dispatch_model->get($code);

      if( ! empty($doc))
      {
        $details = $this->dispatch_model->get_details($doc->code);

        //---- order state = 8 and dispatch id IS NULL
        $totalOrder = $this->dispatch_model->count_orders_by_channels($doc->channels_code);

        $ds = array(
          'doc' => $doc,
          'details' => $details,
          'total_orders' => $totalOrder,
          'total_qty' => empty($details) ? 0 : count($details)
        );

        if($this->agent->is_mobile())
        {
          $ds['title'] = "Dispatch<br/>".$doc->code." | ".$this->channels_model->get_name($doc->channels_code);
          $this->load->view('inventory/dispatch/dispatch_edit_mobile', $ds);
        }
        else
        {
          $this->load->view('inventory/dispatch/dispatch_edit', $ds);
        }
      }
      else
      {
        $this->error_page();
      }
    }
    else
    {
      $this->deny_page();
    }
  }


  public function view_pending_order($code)
  {
    $doc = $this->dispatch_model->get($code);

    if( ! empty($doc))
    {
      $this->title = "Pending Orders";

      $ds = array(
        'doc' => $doc,
        'orders' => $this->dispatch_model->get_peding_order_by_channels($doc->channels_code)
      );

      if($this->agent->is_mobile())
      {
        $ds['title'] = "Pending Orders<br/>{$doc->code} | {$this->channels_model->get_name($doc->channels_code)}";
        $this->load->view('inventory/dispatch/mobile/view_pending_mobile', $ds);
      }
      else
      {
        $this->load->view('inventory/dispatch/dispatch_view_pending', $ds);
      }
    }
    else
    {
      $this->error_page();
    }
  }


  public function view_detail($code)
  {
    $doc = $this->dispatch_model->get($code);

    if( ! empty($doc))
    {
      $ds = array(
        'doc' => $doc,
        'details' => $this->dispatch_model->get_details($doc->code)
      );

      $this->load->view('inventory/dispatch/dispatch_view_detail', $ds);
    }
    else
    {
      $this->error_page();
    }
  }


  public function delete_dispatch_details()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $doc = $this->dispatch_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 'P' OR $doc->status == 'S')
        {
          if( ! empty($ds->rows))
          {
            $this->db->trans_begin();

            foreach($ds->rows as $rs)
            {
              if($sc === FALSE) { break; }

              $detail = $this->dispatch_model->get_detail($rs->id);

              if( ! empty($detail))
              {
                if($detail->line_status == 'P' OR $detail->line_status == 'S')
                {
                  if( ! $this->dispatch_model->delete_detail($detail->id))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to delete item row";
                  }

                  if($sc === TRUE)
                  {
                    if( ! $this->dispatch_model->update_order($rs->order_code, NULL))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update order dispatch id";
                    }
                  }
                }
              }
            } //-- end foreach

            if($sc === TRUE)
            {
              $this->db->trans_commit();
            }
            else
            {
              $this->db->trans_rollback();
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Missing Order Number";
          }
        }
        else
        {
          $sc = FALSE;
          set_error('status');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function remove_detail()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $order_code = $this->input->post('order_code');
    $action = "delete";

    if( ! empty($code) && ! empty($order_code))
    {
      $doc = $this->dispatch_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'P' OR $doc->status == 'S')
        {
          $detail = $this->dispatch_model->get_detail_by_order($doc->code, $order_code);

          if( ! empty($detail))
          {
            if($detail->carton_shipped > 1)
            {
              $carton_shipped = $detail->carton_shipped - 1;

              if( ! $this->dispatch_model->update_detail($detail->id, ['carton_shipped' => $carton_shipped]))
              {
                $sc = FALSE;
                $this->error = "Failed to update carton shipped";
              }
              else
              {
                $action = "update";
              }
            }
            else
            {
              $this->db->trans_begin();

              if( ! $this->dispatch_model->delete_detail($detail->id))
              {
                $sc = FALSE;
                $this->error = "Failed to delete {$order_code}";
              }

              if($sc === TRUE)
              {
                $arr = array(
                  'dispatch_id' => NULL
                );

                if( ! $this->orders_model->update($detail->order_code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update order status";
                }
              }

              if($sc === TRUE)
              {
                $this->db->trans_commit();
              }
              else
              {
                $this->db->trans_rollback();
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "{$order_code} not found";
          }
        }
        else
        {
          $sc = FALSE;
          set_error('status');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'id' => $sc === TRUE ? $detail->id : NULL,
      'action' => $action
    );

    echo json_encode($arr);
  }


  public function cancel_dispatch()
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $code = $this->input->post('code');
      $reason = trim($this->input->post('reason'));

      if( ! empty($code))
      {
        $doc = $this->dispatch_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status != 'D')
          {
            if($doc->status != 'C')
            {
              $this->db->trans_begin();

              $details = $this->dispatch_model->get_details($code);

              if( ! empty($details))
              {

                foreach($details as $rs)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  if( ! $this->orders_model->update($rs->order_code, ['dispatch_id' => NULL]))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to roll back order dispatch status";
                  }
                }

                if($sc === TRUE)
                {
                  if( ! $this->dispatch_model->delete_details($code))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to delete dispatch details";
                  }
                }
              }

              if($sc === TRUE)
              {
                $arr = array(
                  'status' => 'D',
                  'cancel_date' => now(),
                  'cancel_user' => $this->_user->uname,
                  'cancel_reason' => $reason
                );

                if( ! $this->dispatch_model->update($code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update document status";
                }
              }

              if($sc === TRUE)
              {
                $this->db->trans_commit();
              }
              else
              {
                $this->db->trans_rollback();
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Document already closed cannot be cancel";
            }
          }
        }
        else
        {
          $sc = FALSE;
          set_error('notfound');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }


  public function get_dispatch_table()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    $details = $this->dispatch_model->get_details($code);

    if( ! empty($details))
    {
      $channels = get_channels_array();
      $no = 1;
      foreach($details as $rs)
      {
        $rs->no = $no;
        $rs->customer = $rs->customer_code.':'.$rs->customer_name;
        $rs->channels = empty($channels[$rs->channels_code]) ? NULL : $channels[$rs->channels_code];
        $no++;
      }
    }
    else
    {
      $details[] =  (object)['nodata' => 'nodata'];
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $details : NULL
    );

    echo json_encode($arr);
  }


  public function print_dispatch($code)
  {
    $this->load->model('masters/sender_model');
    $doc = $this->dispatch_model->get($code);

    if( ! empty($doc))
    {
      $this->load->library('xprinter');

      $ds = array(
        'doc' => $doc,
        'details' => $this->dispatch_model->get_details($doc->code)
      );

      $this->load->view('print/print_dispatch', $ds);
    }
    else
    {
      $this->page_error();
    }
  }



  private function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_DISPATCH');
    $run_digit = getConfig('RUN_DIGIT_DISPATCH');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->dispatch_model->get_max_code($pre);

    if( ! empty($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }


  public function clear_filter(){
    $filter = array(
      'dp_code',
      'dp_channels',
      'dp_plate_no',
      'dp_sender',
      'dp_from_date',
      'dp_to_date',
      'dp_status'
    );

    return clear_filter($filter);
  }

} //--- end class
?>
