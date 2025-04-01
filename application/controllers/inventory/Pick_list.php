<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pick_list extends PS_Controller
{
  public $menu_code = 'ICODPL';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'PICKPACK';
	public $title = 'Pick List';
  public $segment = 4;
  public $is_mobile = FALSE;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/pick_list';
    $this->load->model('inventory/pick_list_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/channels_model');
    $this->load->model('stock/stock_model');
    $this->load->model('inventory/prepare_model');

    $this->load->helper('channels');
    $this->load->helper('warehouse');
    $this->load->helper('zone');
    $this->load->library('user_agent');

    $this->is_mobile = $this->agent->is_mobile();
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'pl_code', ''),
      'warehouse' => get_filter('warehouse', 'pl_warehouse', 'all'),
      'zone' => get_filter('zone', 'pl_zone', 'all'),
      'channels' => get_filter('channels', 'pl_channels', 'all'),
      'user' => get_filter('user', 'pl_user', 'all'),
      'status' => get_filter('status', 'pl_status', 'all'),
      'is_exported' => get_filter('is_exported', 'pl_is_exported', 'all'),
      'from_date' => get_filter('from_date', 'pl_from_date', ''),
      'to_date' => get_filter('to_date', 'pl_to_date', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
  		$perpage = get_rows();
  		$rows = $this->pick_list_model->count_rows($filter, $this->is_mobile);
      $filter['list'] = $this->pick_list_model->get_list($filter, $perpage, $this->uri->segment($this->segment), $this->is_mobile);
  		$init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);

      if($this->is_mobile)
      {
        $this->title = "Pick List - รอจัด";
        $this->load->view('inventory/pick_list/mobile/pick_list_mobile', $filter);
      }
      else
      {
        $this->load->view('inventory/pick_list/pick_list', $filter);
      }
    }
  }


  public function process($code)
  {
    if($this->pm->can_add OR $this->pm->can_edit)
    {
      $doc = $this->pick_list_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'R')
        {
          $arr = array(
            'status' => 'Y',
            'update_user' => $this->_user->uname
          );

          $this->pick_list_model->update($doc->code, $arr);
        }

        $incomplete = $this->pick_list_model->get_incomplete_rows($doc->code);
        $complete = $this->pick_list_model->get_complete_rows($doc->code); //--- reload later
        $totalProcess = $this->pick_list_model->get_total_process_qty($doc->code);
        $totalReleaseQty = empty($totalProcess) ? 0 : $totalProcess->release_qty; //--- sum of release qty
        $totalPickQty = empty($totalProcess) ? 0 : $totalProcess->pick_qty; //-- sum of picked qty

        if( ! empty($incomplete))
        {
          foreach($incomplete as $rs)
          {
            $rs->barcode = $this->products_model->get_barcode($rs->product_code);
            $rs->stock_in_zone = $this->get_stock_in_zone($rs->product_code, $doc->warehouse_code);
            $rs->balance = $rs->release_qty - $rs->pick_qty;
          }
        }

        $ds = array(
          'doc' => $doc,
          'incomplete' => $incomplete,
          'complete' => $complete,
          'totalReleaseQty' => $totalReleaseQty,
          'totalPickQty' => $totalPickQty,
          'transection' => $this->pick_list_model->get_pick_transections($doc->code),
          'finished' => empty($incomplete) ? TRUE : FALSE
        );

        if($this->is_mobile)
        {
          $ds['title'] = $doc->code.'<br/>ต้นทาง '.$doc->warehouse_code.'  ปลายทาง '.$doc->zone_code;
          $this->load->view('inventory/pick_list/mobile/pick_process_mobile', $ds);
        }
        else
        {
          $this->load->view('inventory/pick_list/pick_process', $ds);
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


  public function do_picking()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $status = $this->pick_list_model->get_status_by_id($ds->id);

      if($status === 'Y')
      {
        $row = $this->pick_list_model->get_row($ds->row_id);

        if( ! empty($row))
        {
          if($row->line_status == 'O')
          {
            $sum_qty = $row->pick_qty + $ds->qty;

            if($sum_qty > $row->release_qty)
            {
              $sc = FALSE;
              $this->error = "จำนวนเกินที่กำหนด";
            }

            if($sc === TRUE)
            {
              $stock = $this->stock_model->get_stock_zone($ds->zone_code, $row->product_code);

              if($ds->qty > $stock)
              {
                $sc = FALSE;
                $this->error = "สต็อกในโซนไม่เพียงพอ";
              }
            }

            if($sc === TRUE)
            {
              $trans = $this->pick_list_model->get_exists_transection($row->pick_id, $row->product_code, $ds->zone_code);

              $this->db->trans_begin();

              if( ! empty($trans))
              {
                $qty = $trans->qty + $ds->qty;

                $arr = array(
                  'qty' => $qty,
                  'user' => $this->_user->uname
                );

                if( ! $this->pick_list_model->update_transection($trans->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update pick transection";
                }
              }
              else
              {
                $arr = array(
                  'pick_id' => $row->pick_id,
                  'pick_code' => $row->pick_code,
                  'product_code' => $row->product_code,
                  'product_name' => $row->product_name,
                  'zone_code' => $ds->zone_code,
                  'qty' => $ds->qty,
                  'user' => $this->_user->uname
                );

                if( ! $this->pick_list_model->add_transection($arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to insert pick transection";
                }
              }

              if($sc === TRUE)
              {
                $arr = array(
                  'pick_qty' => $sum_qty,
                  'valid' => $row->release_qty == $sum_qty ? 1 : 0
                );

                if( ! $this->pick_list_model->update_row($ds->row_id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update pick row";
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
            $this->error = "Invalid row status";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่พบรายการ";
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
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'pick_qty' => $sc === TRUE ? $sum_qty : NULL
    );

    echo json_encode($arr);
  }


  public function finish_pick()
  {
    $sc = TRUE;

    $ex = 0;

    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->pick_list_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'Y')
        {
          $this->load->model('inventory/movement_model');

          $to_warehouse_code = $this->zone_model->get_warehouse_code($doc->zone_code);
          $from_warehouse_code = $doc->warehouse_code;

          if( ! empty($to_warehouse_code) && ! empty($from_warehouse_code))
          {
            $this->db->trans_begin();

            //---- close picklist
            $arr = array(
              'status' => 'C',
              'shipped_date' => now(),
              'update_user' => $this->_user->uname
            );

            if( ! $this->pick_list_model->update($code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update document status";
            }

            //--- close pick rows
            if($sc === TRUE)
            {
              if( ! $this->pick_list_model->update_rows($code, ['line_status' => 'C']))
              {
                $sc = FALSE;
                $this->error = "Failed to update pick rows status";
              }
            }

            //--- close pick details
            if($sc === TRUE)
            {
              if( ! $this->pick_list_model->update_details($code, ['line_status' => 'C']))
              {
                $sc = FALSE;
                $this->error = "Failed to update pick details status";
              }
            }

            //---- insert movement
            if($sc === TRUE)
            {
              $trans = $this->pick_list_model->get_pick_transections($code);

              if( ! empty($trans))
              {
                foreach($trans as $rs)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  //--- move out
                  $arr = array(
                    'reference' => $code,
                    'warehouse_code' => $from_warehouse_code,
                    'zone_code' => $rs->zone_code,
                    'product_code' => $rs->product_code,
                    'move_in' => 0,
                    'move_out' => $rs->qty,
                    'date_add' => $doc->date_add
                  );

                  if( ! $this->movement_model->add($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to create movement";
                  }

                  if($sc === TRUE)
                  {
                    //--- move in
                    $arr = array(
                      'reference' => $code,
                      'warehouse_code' => $to_warehouse_code,
                      'zone_code' => $doc->zone_code,
                      'product_code' => $rs->product_code,
                      'move_in' => $rs->qty,
                      'move_out' => 0,
                      'date_add' => $doc->date_add
                    );

                    if( ! $this->movement_model->add($arr))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to create movement";
                    }
                  }
                } //--- end foreach
              } //-- ! empty($trans)
            } //--- $sc = TRUE

            if($sc === TRUE)
            {
              $this->db->trans_commit();
            }
            else
            {
              $this->db->trans_rollback();
            }

            if($sc === TRUE)
            {
              $this->load->library('export');

              if( ! $this->export->export_pick_list($code))
              {
                $ex = 1;
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Invalid warehouse code";
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
      'ex' => $ex
    );

    echo json_encode($arr);
  }


  public function get_stock_in_zone($item_code, $warehouse = NULL)
  {
    $sc = "ไม่มีสินค้า";

    $stock = $this->stock_model->get_stock_in_zone($item_code, $warehouse);

    if( ! empty($stock))
    {
      $sc = "";

      foreach($stock as $rs)
      {
        $prepared = $this->prepare_model->get_buffer_zone($item_code, $rs->code);
        $picked = $this->pick_list_model->get_picked_zone($rs->code, $item_code);
        $qty = $rs->qty - $prepared - $picked;

        if($qty > 0)
        {
          $sc .= $rs->name.' : '.($qty).'<br/>';
        }
      }
    }

    return empty($sc) ? 'ไม่พบสินค้า' : $sc;
  }


  public function get_stock_zone($zone_code, $item_code)
  {
    $stock = $this->stock_model->get_stock_zone($zone_code, $item_code); //-- stock in zone
    $prepared = $this->prepare_model->get_buffer_zone($item_code, $zone_code); //--- buffer
    $picked = $this->pick_list_model->get_picked_zone($zone_code, $item_code); //-- pick list transection

    return $stock - $prepared - $picked;
  }


  public function get_zone_code()
  {
    $ds = array();
    $sc = TRUE;
    $zone_code = trim($this->input->get('zone_code'));
    $warehouse_code = trim($this->input->get('warehouse_code'));

    if($zone_code)
    {
      $zone = $this->zone_model->get_zone($zone_code, $warehouse_code);

      if( ! empty($zone))
      {
        $ds = array(
          'code' => $zone->code,
          'name' => $zone->name
        );
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบโซน";
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
      'zone' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function get_pick_row()
  {
    $sc = TRUE;
    $id = $this->input->post('row_id');
    $warehouse_code = $this->input->post('warehouse_code');

    $ds = [];
    $stock_in_zone = "";
    $row = $this->pick_list_model->get_row($id);

    if( ! empty($row))
    {
      $ds = array(
        'pick_qty' => $row->pick_qty,
        'balance_qty' => $row->release_qty - $row->pick_qty,
        'stock_in_zone' => $this->get_stock_in_zone($row->product_code, $warehouse_code)
      );
    }
    else
    {
      $sc = FALSE;
      $this->error = "Pick row not found !";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function get_incomplete_table($code)
  {
    $sc = TRUE;
    $ds = [];

    $doc = $this->pick_list_model->get($code);

    if( ! empty($doc))
    {
      $incomplete = $this->pick_list_model->get_incomplete_rows($code);

      if( ! empty($incomplete))
      {
        $no = 1;

        foreach($incomplete as $rs)
        {
          $ds[] = (object) array(
            'no' => $no,
            'id' => $rs->id,
            'barcode' => $this->products_model->get_barcode($rs->product_code),
            'product_code' => $rs->product_code,
            'product_name' => $rs->product_name,
            'release_qty' => $rs->release_qty,
            'pick_qty' => $rs->pick_qty,
            'balance' => $rs->release_qty - $rs->pick_qty,
            'stock_in_zone' => $this->get_stock_in_zone($rs->product_code, $doc->warehouse_code)
          );

          $no++;
        }
      }
      else
      {
        $ds[] = array('nodata' => 'nodata');
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid document number";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );

    echo json_encode($arr);
  }


  public function get_complete_table($code)
  {
    $sc = TRUE;
    $ds = [];
    $complete = $this->pick_list_model->get_complete_rows($code);

    if( ! empty($complete))
    {
      $no = 1;

      foreach($complete as $rs)
      {
        $ds[] = (object) array(
          'no' => $no,
          'id' => $rs->id,
          'product_code' => $rs->product_code,
          'product_name' => $rs->product_name,
          'releaseQty' => $rs->release_qty,
          'pickQtty' => $rs->pick_qty
        );

        $no++;
      }
    }
    else
    {
      $ds[] = array('nodata' => 'nodata');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );

    echo json_encode($arr);
  }


  public function get_transection_table($code)
  {
    $sc = TRUE;
    $ds = [];
    $trans = $this->pick_list_model->get_pick_transections($code);

    if( ! empty($trans))
    {
      $no = 1;

      foreach($trans as $rs)
      {
        $ds[] = (object) array(
          'no' => $no,
          'id' => $rs->id,
          'product_code' => $rs->product_code,
          'product_name' => $rs->product_name,
          'qty' => $rs->qty,
          'zone_code' => $rs->zone_code,
          'user' => $rs->user,
          'date_upd' => thai_date($rs->date_upd, TRUE),
          'valid' => $rs->is_complete
        );

        $no++;
      }
    }
    else
    {
      $ds[] = array('nodata' => 'nodata');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );

    echo json_encode($arr);
  }


  public function delete_transection()
  {
    $sc = TRUE;
    $id = $this->input->get('id');

    $trans = $this->pick_list_model->get_transection($id);

    if( ! empty($trans))
    {
      $doc = $this->pick_list_model->get_by_id($trans->pick_id);

      if( ! empty($doc))
      {
        if($doc->status == 'Y' OR $doc->status == 'R')
        {
          $row = $this->pick_list_model->get_pick_row($trans->pick_code, $trans->product_code);

          if( ! empty($row))
          {
            if($row->pick_qty < $trans->qty OR $row->release_qty < $trans->qty)
            {
              $sc = FALSE;
              $this->error = "Invalid qty :release qty = {$row->release_qty} , pick qty = {$row->pick_qty}, transection qty = {$trans->qty}";
            }

            if($sc === TRUE)
            {
              $this->db->trans_begin();

              //-- delete transection
              if( ! $this->pick_list_model->delete_transection($trans->id))
              {
                $sc = FALSE;
                $this->error = "Failed to delete transection";
              }

              //--- update pick_row
              if($sc === TRUE)
              {
                $arr = array(
                  'pick_qty' => $row->pick_qty - $trans->qty,
                  'valid' => 0
                );

                if( ! $this->pick_list_model->update_row($row->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update pick qty";
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
            $this->error = "Pick row not found";
          }

        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid document status";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Document not found!";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Transection not found !";
    }

    $this->_response($sc);
  }


  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('inventory/pick_list/pick_list_add');
    }
    else
    {
      $this->deny_page();
    }
  }


  public function add()
  {
    $sc = TRUE;

    if($this->pm->can_add)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds))
      {
        $date_add = db_date($ds->date_add, TRUE);
        $code = $this->get_new_code($date_add);

        $arr = array(
          'code' => $code,
          'date_add' => $date_add,
          'bookcode' => 'MV',
          'channels_code' => get_null($ds->channels_code),
          'warehouse_code' => $ds->warehouse_code,
          'zone_code' => $ds->zone_code,
          'user' => $this->_user->uname,
          'remark' => get_null($ds->remark)
        );

        if( ! $this->pick_list_model->add($arr))
        {
          $sc = FALSE;
          set_error('insert');
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


  public function edit($code)
  {
    $doc = $this->pick_list_model->get($code);

    if( ! empty($doc))
    {
      $ds = array(
        'doc' => $doc,
        'details' => $this->pick_list_model->get_details($code),
        'orders' => $this->pick_list_model->get_pick_orders($code),
        'rows' => $this->pick_list_model->get_pick_rows($code),
        'trans' => $this->pick_list_model->get_pick_transections($code)
      );

      $this->load->view('inventory/pick_list/pick_list_edit', $ds);
    }
    else
    {
      $this-page_error();
    }
  }


  public function update()
  {
    $sc = TRUE;

    if($this->pm->can_add OR $this->pm->can_edit)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds))
      {
        $date_add = db_date($ds->date_add, TRUE);

        $arr = array(
          'date_add' => $date_add,
          'channels_code' => get_null($ds->channels_code),
          'warehouse_code' => $ds->warehouse_code,
          'zone_code' => $ds->zone_code,
          'user' => $this->_user->uname,
          'remark' => get_null($ds->remark)
        );

        if( ! $this->pick_list_model->update($ds->code, $arr))
        {
          $sc = FALSE;
          set_error('insert');
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
      'code' => $sc === TRUE ? $ds->code : NULL
    );

    echo json_encode($arr);
  }


  public function cancel()
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $code = $this->input->post('code');

      if( ! empty($code))
      {
        $doc = $this->pick_list_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status === 'P')
          {
            $this->db->trans_begin();

            if( ! $this->pick_list_model->update_details($code, ['line_status' => 'D']))
            {
              $sc = FALSE;
              $this->error = "Failed to update details status";
            }

            if($sc === TRUE)
            {
              if( ! $this->pick_list_model->update($code, ['status' => 'D', 'update_user' => $this->_user->uname]))
              {
                $sc = FALSE;
                $this->error = "Failed to update document status";
              }
            }

            if($sc === TRUE)
            {
              if( ! $this->pick_list_model->remove_order_pick_list_id($doc->id))
              {
                $sc = FALSE;
                $this->error = "Failed to remove order pick list id";
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
            if($doc->status != 'D')
            {
              $sc = FALSE;
              set_error('status');
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


  public function view_detail($code)
  {
    $doc = $this->pick_list_model->get($code);

    if( ! empty($doc))
    {
      $ds = array(
        'doc' => $doc,
        'details' => $this->pick_list_model->get_details($code),
        'orders' => $this->pick_list_model->get_pick_orders($code),
        'rows' => $this->pick_list_model->get_pick_rows($code),
        'trans' => $this->pick_list_model->get_pick_transections($code)
      );

      $this->load->view('inventory/pick_list/pick_list_view_details', $ds);
    }
    else
    {
      $this-page_error();
    }
  }


  public function add_to_pick_list()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $code = $ds->code;

      $doc = $this->pick_list_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'P')
        {
          if( ! empty($ds->orders))
          {
            $res = [];

            foreach($ds->orders as $order_code)
            {
              if($this->pick_list_model->is_order_in_correct_state($order_code))
              {
                $details = $this->pick_list_model->get_order_details($order_code);

                if( ! empty($details))
                {
                  foreach($details as $rs)
                  {
                    if( ! $this->pick_list_model->is_exists_order_detail($doc->id, $order_code, $rs->product_code))
                    {
                      $res[$order_code][] = array(
                        'pick_id' => $doc->id,
                        'pick_code' => $doc->code,
                        'order_code' => $order_code,
                        'product_code' => $rs->product_code,
                        'product_name' => $rs->product_name,
                        'qty' => $rs->qty,
                        'user' => $this->_user->uname
                      );
                    }
                  }
                }
              }
            }

            if( ! empty($res))
            {
              $this->db->trans_begin();

              foreach($res as $key => $val)
              {
                if($sc === FALSE)
                {
                  break;
                }

                foreach($val as $row)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  if( ! $this->pick_list_model->add_detail($row))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to add pick list detail";
                  }
                }

                if($sc === TRUE)
                {
                  $po = array(
                    'pick_id' => $doc->id,
                    'pick_code' => $doc->code,
                    'order_code' => $key
                  );

                  if( ! $this->pick_list_model->add_pick_order($po))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to add pick list order";
                  }
                }

                if($sc === TRUE)
                {
                  $this->orders_model->update($key, ['pick_list_id' => $doc->id]);
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
      'status' =>  $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function release_pick_list()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    $doc = $this->pick_list_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'P')
      {
        $details = $this->pick_list_model->get_details($code);

        if( ! empty($details))
        {
          $rows = [];

          foreach($details as $rs)
          {
            $key = $rs->product_code;

            if(isset($rows[$key]))
            {
              $rows[$key]->qty += $rs->qty;
            }
            else
            {
              $rows[$key] = (object) array(
                'product_code' => $rs->product_code,
                'product_name' => $rs->product_name,
                'qty' => $rs->qty
              );
            }
          }
        }

        if( ! empty($rows))
        {
          $this->db->trans_begin();

          $arr = ['status' => 'R', 'update_user' => $this->_user->uname];

          if( ! $this->pick_list_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update document status";
          }

          if($sc === TRUE)
          {
            foreach($rows as $row)
            {
              if($sc === FALSE) { break; }

              $arr = array(
                'pick_id' => $doc->id,
                'pick_code' => $doc->code,
                'product_code' => $row->product_code,
                'product_name' => $row->product_name,
                'release_qty' => $row->qty
              );

              if( ! $this->pick_list_model->add_row($arr))
              {
                $sc = FALSE;
                $this->error = "Failed to insert pick rows";
              }
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
          $this->error = "Cannot calculate summary pick items";
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

    $this->_response($sc);
  }


  public function unrelease_pick_list($code)
  {
    $sc = TRUE;

    $doc = $this->pick_list_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'R' OR $doc->status == 'Y')
      {
        if($this->pick_list_model->is_exists_transectons($code))
        {
          $sc = FALSE;
          $this->error = "Cannot rollback status : transections exists";
        }

        if($sc === TRUE)
        {
          if( ! $this->pick_list_model->delete_rows($code))
          {
            $sc = FALSE;
            $this->error = "Cannot rollback status : Failed to delete summary rows";
          }
        }

        if($sc === TRUE)
        {
          $arr = ['status' => 'P', 'update_user' => $this->_user->uname];

          if( ! $this->pick_list_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update document status";
          }
        }
      }
      else
      {
        if($doc->status != 'P')
        {
          $sc = FALSE;
          set_error('status');
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('notfound');
    }

    $this->_response($sc);
  }


  public function get_order_list()
  {
    $sc = TRUE;
    $filter = json_decode($this->input->post('filter'));
    $res = [];

    if( ! empty($filter))
    {
      $ds = array(
        'code' => $filter->order_code,
        'channels' => $filter->channels,
        'customer' => $filter->customer,
        'warehouse_code' => $filter->warehouse_code,
        'from_date' => $filter->from_date,
        'to_date' => $filter->to_date,
        'is_pick_list' => $filter->is_pick_list
      );

      $orders = $this->pick_list_model->get_order_list($ds);

      if( ! empty($orders))
      {
        $no = 1;

        foreach($orders as $rs)
        {
          $res[] = (object) array(
            'no' => $no,
            'id' => $rs->id,
            'code' => $rs->code,
            'channels' => $rs->channels_name,
            'customer' => $rs->customer_name,
            'date_add' => thai_date($rs->date_add, FALSE),
            'pick_list_id' => $rs->pick_list_id
          );

          $no++;
        }
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $res : NULL
    );

    echo json_encode($arr);
  }


  public function delete_orders()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $code = $ds->code;

      $doc = $this->pick_list_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'P')
        {
          if( ! empty($ds->orders))
          {
            foreach($ds->orders as $order_code)
            {
              $so = TRUE;

              $this->db->trans_begin();
              //--- delete details
              if(! $this->pick_list_model->delete_order($code, $order_code))
              {
                $so = FALSE;

              }

              if($so === TRUE)
              {
                if( ! $this->pick_list_model->delete_detail_by_order($code, $order_code))
                {
                  $so = FALSE;
                }
              }

              if($so === TRUE)
              {
                if( ! $this->orders_model->update($order_code, ['pick_list_id' => NULL]))
                {
                  $so = FALSE;
                }
              }

              if($so === TRUE)
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
            set_error('required');
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


  public function print_order_list($code)
  {
    $orders = [];
    $doc = $this->pick_list_model->get($code);
    $ds = $this->pick_list_model->get_pick_orders($code);

    if( ! empty($ds))
    {
      $this->load->library('ixqrcode');
      $this->load->model('masters/products_model');
      $this->load->model('masters/channels_model');

      $channels_name = $this->channels_model->get_name($doc->channels_code);

      foreach($ds as $rs)
      {
        $qr = array(
          'data' => $rs->order_code,
          'size' => 8,
          'level' => 'H',
          'savename' => NULL
        );

        ob_start();
        $this->ixqrcode->generate($qr);
        $qr = base64_encode(ob_get_contents());
        ob_end_clean();

        $orders[] = (object)['file' => $qr, 'code' => $rs->order_code, 'channels' => $channels_name];
      }
    }

    $this->load->library('printer');

    $pl = array(
      'orders' => $orders,
    );

    $this->load->view('print/print_pick_order_list', $pl);
  }


  public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_PICK_LIST');
    $run_digit = getConfig('RUN_DIGIT_PICK_LIST');
    $prefix = empty($prefix) ? 'PL' : $prefix;
    $run_digit = empty($run_digit) ? 5 : $run_digit;
    $pre = $prefix.'-'.$Y.$M;
    $code = $this->pick_list_model->get_max_code($pre);

    if( ! empty($code))
    {
      $run_no = mb_substr($code, ($run_digit * -1), NULL, 'UTF-8') + 1;
      $new_code = $pre . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $pre . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }


  public function clear_filter()
  {
    $filter = array(
      'pl_code',
      'pl_warehouse',
      'pl_zone',
      'pl_channels',
      'pl_user',
      'pl_status',
      'pl_is_exported',
      'pl_from_date',
      'pl_to_date',
    );

    return clear_filter($filter);
  }


} //--- end class
?>
