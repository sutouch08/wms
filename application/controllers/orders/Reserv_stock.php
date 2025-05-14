<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reserv_stock extends PS_Controller
{
  public $menu_code = 'SORSST';
	public $menu_group_code = 'SO';
	public $title = 'จองสต็อกสินค้า';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/reserv_stock';
    $this->load->model('orders/reserv_stock_model');
    $this->load->model('masters/products_model');
    $this->load->helper('reserv_stock');
    $this->load->helper('warehouse');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'rs_code', ''),
      'name' => get_filter('name', 'rs_name', ''),
      'from_date' => get_filter('from_date','rs_from_date', ''),
      'to_date' => get_filter('to_date', 'rs_to_date', ''),
      'start_date' => get_filter('start_date', 'rs_start_date', ''),
      'end_date' => get_filter('end_date', 'rs_end_date', ''),
      'status' => get_filter('status', 'rs_status', 'all'),
      'active' => get_filter('active', 'rs_active', 'all')
    );


    if( $this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_rows();

      $segment = 4; //-- url segment

      $rows = $this->reserv_stock_model->count_rows($filter);

      //--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
      $init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $filter['data'] = $this->reserv_stock_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $this->load->view('reserv_stock/reserv_stock_list', $filter);
    }
  }


  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('reserv_stock/reserv_stock_add');
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

      if( ! empty($ds) && ! empty($ds->name) && ! empty($ds->start_date) && ! empty($ds->end_date))
      {
        $arr = array(
          'code' => $this->get_new_code(),
          'name' => $ds->name,
          'warehouse_code' => $ds->warehouse_code,
          'status' => 'D',
          'active' => $ds->active == 1 ? 1 : 0,
          'start_date' => db_date($ds->start_date),
          'end_date' => db_date($ds->end_date),
          'user' => $this->_user->uname
        );

        $id = $this->reserv_stock_model->add($arr);

        if(empty($id))
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
      'id' => $sc === TRUE ? $id : NULL
    );

    echo json_encode($arr);
  }


  public function edit($id)
  {
    if($this->pm->can_add OR $this->pm->can_eidt)
    {
      $doc = $this->reserv_stock_model->get($id);

      if( ! empty($doc))
      {
        $ds = array(
          'doc' => $doc,
          'details' => $this->reserv_stock_model->get_details($id)
        );

        $this->load->view('reserv_stock/reserv_stock_edit', $ds);
      }
      else
      {
        $this->page_error();
      }
    }
    else
    {
      $this->deny_page();
    }
  }


  public function update()
  {
    $sc = TRUE;

    if($this->pm->can_add OR $this->pm->can_edit)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds) && ! empty($ds->name) && ! empty($ds->start_date) && ! empty($ds->end_date))
      {
        $arr = array(
          'name' => $ds->name,
          'warehouse_code' => $ds->warehouse_code,
          'active' => $ds->active == 1 ? 1 : 0,
          'status' => 'D',
          'start_date' => db_date($ds->start_date),
          'end_date' => db_date($ds->end_date),
          'date_upd' => now(),
          'update_user' => $this->_user->uname
        );

        if( ! $this->reserv_stock_model->update($ds->id, $arr))
        {
          $sc = FALSE;
          set_error('update');
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


  public function save()
  {
    $sc = TRUE;
    $id = $this->input->post('id');

    $doc = $this->reserv_stock_model->get($id);

    if( ! empty($doc))
    {
      if($doc->status == 'D')
      {
        $arr = array(
          'status' => 'P',
          'date_upd' => now(),
          'update_user' => $this->_user->uname
        );

        if( ! $this->reserv_stock_model->update($id, $arr))
        {
          $sc = FALSE;
          set_error('update');
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


  public function approve()
  {
    $sc = TRUE;
    $id = $this->input->post('id');

    $doc = $this->reserv_stock_model->get($id);

    if( ! empty($doc))
    {
      if($doc->status == 'P')
      {
        $arr = array(
          'status' => 'A',
          'date_upd' => now(),
          'update_user' => $this->_user->uname
        );

        if( ! $this->reserv_stock_model->update($id, $arr))
        {
          $sc = FALSE;
          set_error('update');
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


  public function rejected()
  {
    $sc = TRUE;
    $id = $this->input->post('id');

    $doc = $this->reserv_stock_model->get($id);

    if( ! empty($doc))
    {
      if($doc->status == 'P')
      {
        $arr = array(
          'status' => 'R',
          'date_upd' => now(),
          'update_user' => $this->_user->uname
        );

        if( ! $this->reserv_stock_model->update($id, $arr))
        {
          $sc = FALSE;
          set_error('update');
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


  public function view_detail($id)
  {
    $doc = $this->reserv_stock_model->get($id);

    if( ! empty($doc))
    {
      $ds = array(
        'doc' => $doc,
        'details' => $this->reserv_stock_model->get_details($id)
      );

      $this->load->view('reserv_stock/reserv_stock_view_detail', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function delete_reserv_stock()
  {
    $sc = TRUE;
    $id = $this->input->post('id');

    if($this->pm->can_delete)
    {
      $this->db->trans_begin();

      if( ! $this->reserv_stock_model->delete_items($id))
      {
        $sc = FALSE;
        $this->error = "Failed to delete items";
      }

      if($sc === TRUE)
      {
        if( ! $this->reserv_stock_model->delete($id))
        {
          $sc = FALSE;
          $this->error = "Failed to delete reserv_stock";
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
      set_error('permission');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function add_item()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->id) && ! empty($ds->code) && ! empty($ds->product_code) && ! empty($ds->qty))
    {
      $id = NULL;

      $da = array(
        'reserv_id' => $ds->id,
        'reserv_code' => $ds->code,
        'product_code' => $ds->product_code,
        'product_name' => $ds->product_name,
        'qty' => $ds->qty
      );

      $row = $this->reserv_stock_model->get_detail_by_product($ds->id, $ds->product_code);

      if(empty($row))
      {
        $da['user'] = $this->_user->uname;

        $id = $this->reserv_stock_model->add_detail($da);

        if( ! $id)
        {
          $sc = FALSE;
          set_error('insert');
        }
        else
        {
          $da['id'] = $id;
          $da['qty'] = number($ds->qty, 2);
        }
      }
      else
      {
        $id = $row->id;

        $da['date_upd'] = now();
        $da['update_user'] = $this->_user->uname;

        if( ! $this->reserv_stock_model->update_detail($id, $da))
        {
          $sc = FALSE;
          set_error('update');
        }
        else
        {
          $da['id'] = $id;
          $da['qty'] = number($ds->qty, 2);
        }
      }

      if($sc === TRUE)
      {
        $arr = array(
          'status' => 'D',
          'date_upd' => now(),
          'update_user' => $this->_user->uname
        );

        $this->reserv_stock_model->update($ds->id, $arr);
      }

      $this->update_summary($ds->id);
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'id' => $sc === TRUE ? $id : NULL,
      'data' => $sc === TRUE ? $da : NULL
    );

    echo json_encode($arr);
  }


  public function get_product_grid($style_code)
  {
    $sc = TRUE;
    $this->load->library('purchase_grid');

    $grid = $this->purchase_grid->getProductGrid($style_code);

    if( ! empty($grid->data))
    {
      $tbs = '<table class="table table-bordered border-1" style="min-width:'.$grid->width.'px;">';
      $tbe = '</table>';
      $grid->data = $tbs.$grid->data.$tbe;
    }

    echo json_encode($grid);
  }


  public function add_items()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->id) && ! empty($ds->code) && ! empty($ds->items))
    {
      if( ! empty($ds->items))
      {
        foreach($ds->items as $rs)
        {
          $row = $this->reserv_stock_model->get_detail_by_product($ds->id, $rs->product_code);

          if(empty($row))
          {
            $arr = array(
              'reserv_id' => $ds->id,
              'reserv_code' => $ds->code,
              'product_code' => $rs->product_code,
              'product_name' => $rs->product_name,
              'qty' => $rs->qty,
              'user' => $this->_user->uname
            );

            $this->reserv_stock_model->add_detail($arr);
          }
          else
          {
            $arr = array(
              'qty' => $rs->qty,
              'date_upd' => now(),
              'update_user' => $this->_user->uname
            );

            $this->reserv_stock_model->update_detail($row->id, $arr);
          }
        }

        if($sc === TRUE)
        {
          $arr = array(
            'status' => 'D',
            'date_upd' => now(),
            'update_user' => $this->_user->uname
          );

          $this->reserv_stock_model->update($ds->id, $arr);
        }

        $this->update_summary($ds->id);
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function cancel()
  {
    $sc = TRUE;
    $id = $this->input->post('id');

    $doc = $this->reserv_stock_model->get($id);

    if( ! empty($doc))
    {
      if($doc->status != 'C')
      {
        $arr = array(
          'status' => 'C',
          'date_upd' => now(),
          'update_user' => $this->_user->uname,
          'cancel_date' => now(),
          'cancel_user' => $this->_user->uname
        );

        if( ! $this->reserv_stock_model->update($id, $arr))
        {
          $sc = FALSE;
          set_error('update');
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


  public function update_summary($id)
  {
    $arr = array(
      'totalSKU' => $this->reserv_stock_model->count_sku($id),
      'totalQty' => $this->reserv_stock_model->get_sum_qty($id)
    );

    return $this->reserv_stock_model->update($id, $arr);
  }


  public function remove_items()
  {
    $sc = TRUE;

    if($this->pm->can_edit)
    {
      $id = $this->input->post('id');
      $ids = $this->input->post('ids');

      if( ! empty($ids))
      {
        if( ! $this->reserv_stock_model->remove_items($ids))
        {
          $sc = FALSE;
          $this->error = "Failed to delete items";
        }
        else
        {
          $this->update_summary($id);
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Missing required parameter";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }


  public function get_active_items()
  {
    $items = $this->reserv_stock_model->get_active_items();

    if( ! empty($items))
    {
      echo json_encode($items);
    }
    else
    {
      echo "Not found";
    }
  }


  public function import_items()
  {
    $sc = TRUE;

    ini_set('max_execution_time', 1200);
    ini_set('memory_limit','1000M');
    $this->load->library('excel');

    $id = $this->input->post('id');
    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
    $path = $this->config->item('upload_path').'reserv_stock/';
    $file	= 'uploadFile';
    $config = array(   // initial config for upload class
      "allowed_types" => "xlsx",
      "upload_path" => $path,
      "file_name"	=> "reserv_stock-".date('YmdHis'),
      "max_size" => 5120,
      "overwrite" => TRUE
    );

    $this->load->library("upload", $config);

    if(! $this->upload->do_upload($file))
    {
      $sc = FALSE;
      $this->error = $this->upload->display_errors();
    }
    else
    {
      $info = $this->upload->data();
      /// read file
      $excel = PHPExcel_IOFactory::load($info['full_path']);
      //get only the Cell Collection
      $collection	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

      if( ! empty($collection))
      {
        $count = count($collection);
        $limit = intval(getConfig('IMPORT_ROWS_LIMIT')) + 1;

        if($count > $limit)
        {
          $sc = FALSE;
          $this->error = "ไฟล์มีจำนวนรายการเกิน {$limit} บรรทัด";
        }

        if($sc === TRUE)
        {
          $doc = $this->reserv_stock_model->get($id);

          $i = 1;

          foreach($collection as $rs)
          {
            if($sc === FALSE) { break; }

            if($i == 1)
            {
              if($rs['A'] != 'SKU')
              {
                $sc = FALSE;
                $this->error = "Column A Should be 'SKU'";
              }

              if($rs['B'] != 'Qty')
              {
                $sc = FALSE;
                $this->error = "Column B should be 'Qty'";
              }
            }
            else
            {
              if( ! empty($rs['A']) && ! empty($rs['B']))
              {
                $pd = $this->products_model->get(trim($rs['A']));

                if( ! empty($pd))
                {
                  $row = $this->reserv_stock_model->get_detail_by_product($doc->id, $pd->code);

                  if(empty($row))
                  {
                    $arr = array(
                      'reserv_id' => $doc->id,
                      'reserv_code' => $doc->code,
                      'product_code' => $pd->code,
                      'product_name' => $pd->name,
                      'qty' => intval($rs['B']),
                      'user' => $this->_user->uname
                    );

                    $this->reserv_stock_model->add_detail($arr);
                  }
                  else
                  {
                    $arr = array(
                      'qty' => intval($rs['B']),
                      'date_upd' => now(),
                      'update_user' => $this->_user->uname
                    );

                    $this->reserv_stock_model->update_detail($row->id, $arr);
                  }
                }
              }
            }

            $i++;
          }

          if($sc === TRUE)
          {
            $this->update_summary($id);
          }
        }
      }
    }

    $this->_response($sc);
  }


  public function get_new_code()
  {
    $date = date('Y-m-d');
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = "RS";
    $run_digit = 3;
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->reserv_stock_model->get_max_code($pre);

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


  public function clear_filter()
  {
    $filter = array(
      'rs_code',
      'rs_name',
      'rs_from_date',
      'rs_to_date',
      'rs_start_date',
      'rs_end_date',
      'rs_status',
      'rs_active'
    );

    return clear_filter($filter);
  }

}//-- end class
?>
