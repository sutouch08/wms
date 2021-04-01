<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Return_lend extends PS_Controller
{
  public $menu_code = 'ICRTLD';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RETURN';
	public $title = 'คืนสินค้าจากการยืม';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/return_lend';
    $this->load->model('inventory/return_lend_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/employee_model');
    $this->load->model('masters/products_model');

    $this->load->helper('employee');
  }


  public function index()
  {
    $filter = array(
      'code'    => get_filter('code', 'rl_code', ''),
      'lend_code' => get_filter('lend_code', 'lend_code', ''),
      'empName' => get_filter('empName', 'empName', ''),
      'from_date' => get_filter('from_date', 'rl_from_date', ''),
      'to_date' => get_filter('to_date', 'rl_to_date', ''),
      'status' => get_filter('status', 'rl_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->return_lend_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$document = $this->return_lend_model->get_list($filter, $perpage, $this->uri->segment($segment));

    if(!empty($document))
    {
      foreach($document as $rs)
      {
        $rs->qty = $this->return_lend_model->get_sum_qty($rs->code);
        $rs->amount = $this->return_lend_model->get_sum_amount($rs->code);
        $rs->empName = $this->employee_model->get_name($rs->empID);
      }
    }

    $filter['docs'] = $document;
		$this->pagination->initialize($init);
    $this->load->view('inventory/return_lend/return_lend_list', $filter);
  }



  public function add_new()
  {
    $ds['new_code'] = $this->get_new_code();
    $this->load->view('inventory/return_lend/return_lend_add', $ds);
  }


  public function add()
  {
    $sc = TRUE;

    if($this->input->post('date_add'))
    {
      $this->load->model('inventory/lend_model');
      $this->load->model('inventory/movement_model');
      //--- retrive data form
      $date_add = db_date($this->input->post('date_add'), TRUE);
      $empID = $this->input->post('empID');
      $zone_code = $this->input->post('zone_code');
      $lend_code = $this->input->post('lendCode');
      $remark = $this->input->post('remark');
      $qtys = $this->input->post('qty');
      //--- end data form

      $lend = $this->lend_model->get($lend_code);
      $zone = $this->zone_model->get($zone_code);
      $from_warehouse = $this->zone_model->get_warehouse_code($lend->zone_code);
      $isManual = getConfig('MANUAL_DOC_CODE');

      if($this->input->post('code'))
      {
        $code = trim($this->input->post('code'));
      }
      else
      {
        $code = $this->get_new_code($date_add);
      }



      $arr = array(
        'code' => $code,
        'bookcode' => getConfig('BOOK_CODE_RETURN_LEND'),
        'lend_code' => $lend_code,
        'empID' => $empID,
        'from_warehouse' => $from_warehouse, //--- warehouse ต้นทาง ดึงจากเอกสารยืม
        'from_zone' => $lend->zone_code, //--- zone ต้นทาง ดึงจากเอกสารยืม
        'to_warehouse' => $zone->warehouse_code,
        'to_zone' => $zone->code,
        'date_add' => $date_add,
        'user' => get_cookie('uname'),
        'remark' => $remark,
        'status' => 1
      );

      //--- start transection;
      $this->db->trans_begin();

      //--- add new lend return
      $rs = $this->return_lend_model->add($arr);

      if($rs)
      {
        foreach($qtys as $pdCode => $qty)
        {
          if($qty > 0)
          {
            $item = $this->products_model->get($pdCode);
            $amount = $qty * $item->price;
            $ds = array(
              'return_code' => $code,
              'lend_code' => $lend_code,
              'product_code' => $item->code,
              'product_name' => $item->name,
              'qty' => $qty,
              'price' => $item->price,
              'amount' => $amount,
              'vat_amount' => get_vat_amount($amount)
            );

            if(! $this->return_lend_model->add_detail($ds))
            {
              $sc = FALSE;
              $this->error = "เพิ่มรายการไม่สำเร็จ : {$item->code}";
            }
            else
            {
              //--- insert Movement out
              $arr = array(
                'reference' => $code,
                'warehouse_code' => $lend->warehouse_code,
                'zone_code' => $lend->zone_code,
                'product_code' => $item->code,
                'move_in' => 0,
                'move_out' => $qty,
                'date_add' => db_date($this->input->post('date_add'), TRUE)
              );
              $this->movement_model->add($arr);

              //--- insert Movement in
              $arr = array(
                'reference' => $code,
                'warehouse_code' => $zone->warehouse_code,
                'zone_code' => $zone->code,
                'product_code' => $item->code,
                'move_in' => $qty,
                'move_out' => 0,
                'date_add' => db_date($this->input->post('date_add'), TRUE)
              );
              $this->movement_model->add($arr);

              if( ! $this->return_lend_model->update_receive($lend_code, $item->code, $qty))
              {
                $sc = FALSE;
                $this->error = "Update ยอดรับไม่สำเร็จ {$item->code}";
              }
            }
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เพิ่มเอกสารไม่สำเร็จ";
      }

      if($sc === FALSE)
      {
        $this->db->trans_rollback();
      }
      else
      {
        $this->db->trans_commit();
      }

      if($sc === FALSE)
      {
        set_error($this->error);
        redirect($this->home.'/add_new');
      }
      else
      {
        $this->export_return_lend($code);

        redirect($this->home.'/view_detail/'.$code);
      }
    }
    else
    {
      set_error("วันที่ไม่ถูกต้อง");
      redirect($this->home.'/add_new');
    }
  }




  public function unsave($code)
  {
    $sc = TRUE;
    $doc = $this->return_lend_model->get($code);
    if(!empty($doc))
    {
      $this->load->model('inventory/movement_model');
      $this->load->model('inventory/lend_model');

      //--- start transection
      $this->db->trans_begin();

      //--- 1 remove movement
      if( ! $this->movement_model->drop_movement($code) )
      {
        $sc = FALSE;
        $this->error = "ลบ movement ไม่สำเร็จ";
      }

      //--- 2 update order_lend_detail
      if($sc === TRUE)
      {
        $details = $this->return_lend_model->get_lend_details($code);
        if(!empty($details))
        {
          foreach($details as $rs)
          {
            //--- exit loop if any error
            if($sc === FALSE)
            {
              break;
            }

            $qty = $rs->qty * -1;  //--- convert to negative for add in function
            if( ! $this->return_lend_model->update_receive($rs->lend_code, $rs->product_code, $qty))
            {
              $sc = FALSE;
              $this->error = "ปรับปรุง ยอดรับ {$rs->product_code} ไม่สำเร็จ";
            }
          } //-- end foreach
        } //--- end if !empty $details
      } //--- end if $sc

      //--- 3. change lend_details status to 0 (not save)
      if($sc === TRUE)
      {
        if( ! $this->return_lend_model->change_details_status($code, 0))
        {
          $sc = FALSE;
          $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
        }
      }

      //--- 4. change return_lend document to 0 (not save)
      if($sc === TRUE)
      {
        if( ! $this->return_lend_model->change_status($code, 0))
        {
          $sc = FALSE;
          $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
        }
      }

      //--- commit or rollback transection
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
      $this->error = "เลขที่เอกสารไม่ถูกต้อง : {$code}";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }





  public function edit($code)
  {
    $doc = $this->return_lend_model->get($code);
    if(!empty($doc))
    {
      $doc->zone_name = $this->zone_model->get_name($doc->to_zone);
      $doc->empName = $this->employee_model->get_name($doc->empID);
    }

    $details = $this->return_lend_model->get_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
        $rs->backlogs = $rs->lend_qty - $rs->receive;
      }
    }

    $ds['doc'] = $doc;
    $ds['details'] = $details;

    $this->load->view('inventory/return_lend/return_lend_edit', $ds);
  }





  public function update()
  {
    $sc = TRUE;
    $code = $this->input->post('return_code');
    if($code)
    {
      $this->load->model('inventory/lend_model');
      $this->load->model('inventory/movement_model');

      //--- retrive data form
      $date_add = db_date($this->input->post('date_add', TRUE));
      $empID = $this->input->post('empID');
      $zone_code = $this->input->post('zone_code');
      $lend_code = $this->input->post('lendCode');
      $remark = $this->input->post('remark');
      $qtys = $this->input->post('qty');
      //--- end data form

      $lend = $this->lend_model->get($lend_code);
      $zone = $this->zone_model->get($zone_code);

      $arr = array(
        'lend_code' => $lend_code,
        'empID' => $empID,
        'from_warehouse' => $lend->warehouse_code, //--- warehouse ต้นทาง ดึงจากเอกสารยืม
        'from_zone' => $lend->zone_code, //--- zone ต้นทาง ดึงจากเอกสารยืม
        'to_warehouse' => $zone->warehouse_code,
        'to_zone' => $zone->code,
        'date_add' => $date_add,
        'update_user' => get_cookie('uname'),
        'remark' => $remark,
        'status' => 1
      );

      //--- start transection;
      $this->db->trans_begin();

      //--- update lend return
      $update = $this->return_lend_model->update($code, $arr);

      if($update)
      {
        //--- drop all details before add new details
        if(! $this->return_lend_model->drop_details($code))
        {
          $sc = FALSE;
          $thsi->error = "ลบรายการเก่าไม่สำเร็จ";
        }

        if($sc === TRUE)
        {
          foreach($qtys as $pdCode => $qty)
          {
            if($qty > 0)
            {
              $item = $this->products_model->get($pdCode);
              $amount = $qty * $item->price;
              $ds = array(
                'return_code' => $code,
                'lend_code' => $lend_code,
                'product_code' => $item->code,
                'product_name' => $item->name,
                'qty' => $qty,
                'price' => $item->price,
                'amount' => $amount,
                'vat_amount' => get_vat_amount($amount)
              );

              if(! $this->return_lend_model->add_detail($ds))
              {
                $sc = FALSE;
                $this->error = "เพิ่มรายการไม่สำเร็จ : {$item->code}";
              }
              else
              {
                //--- insert Movement out
                $arr = array(
                  'reference' => $code,
                  'warehouse_code' => $lend->warehouse_code,
                  'zone_code' => $lend->zone_code,
                  'product_code' => $item->code,
                  'move_in' => 0,
                  'move_out' => $qty,
                  'date_add' => db_date($this->input->post('date_add'), TRUE)
                );

                $this->movement_model->add($arr);

                //--- insert Movement in
                $arr = array(
                  'reference' => $code,
                  'warehouse_code' => $zone->warehouse_code,
                  'zone_code' => $zone->code,
                  'product_code' => $item->code,
                  'move_in' => $qty,
                  'move_out' => 0,
                  'date_add' => db_date($this->input->post('date_add'), TRUE)
                );

                $this->movement_model->add($arr);

                //--- update backlogs
                if( ! $this->return_lend_model->update_receive($lend_code, $item->code, $qty))
                {
                  $sc = FALSE;
                  $this->error = "Update ยอดรับไม่สำเร็จ {$item->code}";
                }
              } //--- end add detail
            } //-- end if qty
          } //--- end foreach;
        } //--- end if $sc
      }
      else //-- if $rs
      {
        $sc = FALSE;
        $this->error = "เพิ่มเอกสารไม่สำเร็จ";
      }

      if($sc === FALSE)
      {
        $this->db->trans_rollback();
      }
      else
      {
        $this->db->trans_commit();
      }

      if($sc === FALSE)
      {
        set_error($this->error);
        redirect($this->home.'/add_new');
      }
      else
      {
        $this->export_return_lend($code);

        redirect($this->home.'/view_detail/'.$code);
      }
    }
    else
    {
      set_error("ไม่พบเอกสาร {$code}");
      redirect($this->home.'/edit/'.$code);
    }
  }



  public function cancle_return($code)
  {
    $sc = TRUE;

    $doc = $this->return_lend_model->get($code);
    if(!empty($doc))
    {
      //--- if document saved
      if($doc->status == 1)
      {
        $this->load->model('inventory/movement_model');
        $this->load->model('inventory/lend_model');

        //--- start transection
        $this->db->trans_begin();

        //--- 1 remove movement
        if( ! $this->movement_model->drop_movement($code) )
        {
          $sc = FALSE;
          $this->error = "ลบ movement ไม่สำเร็จ";
        }

        //--- 2 update order_lend_detail
        if($sc === TRUE)
        {
          $details = $this->return_lend_model->get_lend_details($code);
          if(!empty($details))
          {
            foreach($details as $rs)
            {
              //--- exit loop if any error
              if($sc === FALSE)
              {
                break;
              }

              $qty = $rs->qty * -1;  //--- convert to negative for add in function
              if( ! $this->return_lend_model->update_receive($rs->lend_code, $rs->product_code, $qty))
              {
                $sc = FALSE;
                $this->error = "ปรับปรุง ยอดรับ {$rs->product_code} ไม่สำเร็จ";
              }
            } //-- end foreach
          } //--- end if !empty $details
        } //--- end if $sc

        //--- 3. change lend_details status to 2 (cancle)
        if($sc === TRUE)
        {
          if( ! $this->return_lend_model->change_details_status($code, 2))
          {
            $sc = FALSE;
            $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
          }
        }

        //--- 4. change return_lend document to 0 (not save)
        if($sc === TRUE)
        {
          if( ! $this->return_lend_model->change_status($code, 2))
          {
            $sc = FALSE;
            $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
          }
        }

        //--- commit or rollback transection
        if($sc === TRUE)
        {
          $this->db->trans_commit();
        }
        else
        {
          $this->db->trans_rollback();
        }
      }
      else if($doc->status == 0)  //--- if not save
      {
        //--- just change status
        $this->db->trans_begin();

        if($sc === TRUE)
        {
          //--- change lend_details status to 2 (cancle)
          if( ! $this->return_lend_model->change_details_status($code, 2))
          {
            $sc = FALSE;
            $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
          }
        }

        //--- change return_lend document to 0 (not save)
        if($sc === TRUE)
        {
          if( ! $this->return_lend_model->change_status($code, 2))
          {
            $sc = FALSE;
            $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
          }
        }

        //--- commit or rollback transection
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
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }




  public function view_detail($code)
  {
    $this->load->model('inventory/lend_model');
    $doc = $this->return_lend_model->get($code);
    if(!empty($doc))
    {
      $doc->empName = $this->employee_model->get_name($doc->empID);
      $doc->from_zone_name = $this->zone_model->get_name($doc->from_zone);
      $doc->to_zone_name = $this->zone_model->get_name($doc->to_zone);
      $doc->user_name = $this->user_model->get_name($doc->user);
    }

    $details = $this->lend_model->get_backlogs_list($doc->lend_code);

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->return_qty = $this->return_lend_model->get_return_qty($doc->code, $rs->product_code);
      }
    }

    $data['doc'] = $doc;
    $data['details'] = $details;
    $this->load->view('inventory/return_lend/return_lend_view_detail', $data);
  }




  public function get_lend_details($code)
  {
    $sc = TRUE;
    $this->load->model('inventory/lend_model');
    $doc = $this->lend_model->get($code);

    if(!empty($doc))
    {
      $ds = array(
        'empID' => $doc->empID,
        'empName' => $doc->empName
      );

      $details = $this->return_lend_model->get_backlogs($code);

      $rows = array();
      if(!empty($details))
      {
        $no = 1;
        $totalLend = 0;
        $totalReceived = 0;
        $totalBacklogs = 0;

        foreach($details as $rs)
        {
          $barcode = $this->products_model->get_barcode($rs->product_code);
          $backlogs = $rs->qty - $rs->receive;
          if($backlogs > 0)
          {
            $arr = array(
              'no' => $no,
              'itemCode' => $rs->product_code,
              'barcode' => (!empty($barcode) ? $barcode : $rs->product_code), //--- หากไม่มีบาร์โค้ดให้ใช้รหัสสินค้าแทน
              'lendQty' => $rs->qty,
              'received' => $rs->receive,
              'backlogs' => $backlogs
            );

            array_push($rows, $arr);
            $no++;
            $totalLend += $rs->qty;
            $totalReceived += $rs->receive;
            $totalBacklogs += $backlogs;
          }
        }

        $arr = array(
          'totalLend' => $totalLend,
          'totalReceived' => $totalReceived,
          'totalBacklogs' => $totalBacklogs
        );

        array_push($rows, $arr);
      }
      else
      {
        array_push($rows, array('nodata' => 'nodata'));
      }

      $ds['details'] = $rows;
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่ใบยืมสินค้า";
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }



  private function export_return_lend($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_return_lend($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }
//--- end export transfer


 public function do_export($code)
 {
   $rs = $this->export_return_lend($code);
   echo $rs === TRUE ? 'success' : $this->error;
 }



  public function print_return($code)
  {
    $this->load->model('inventory/lend_model');
    $this->load->library('printer');
    $doc = $this->return_lend_model->get($code);
    $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
    $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    $doc->from_zone_name = $this->zone_model->get_name($doc->from_zone);
    $doc->to_zone_name = $this->zone_model->get_name($doc->to_zone);
    $doc->empName = $this->employee_model->get_name($doc->empID);

    $details = $this->lend_model->get_backlogs_list($doc->lend_code);

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->return_qty = $this->return_lend_model->get_return_qty($doc->code, $rs->product_code);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_return_lend', $ds);
  }



  public function get_new_code($date = '')
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RETURN_LEND');
    $run_digit = getConfig('RUN_DIGIT_RETURN_LEND');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->return_lend_model->get_max_code($pre);
    if(! is_null($code))
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



  public function is_exists($code, $old_code = NULL)
  {
    $exists = $this->return_lend_model->is_exists($code, $old_code);
    if($exists)
    {
      echo 'เลขที่เอกสารซ้ำ';
    }
    else
    {
      echo 'not_exists';
    }
  }



  public function clear_filter()
  {
    $filter = array('rl_code','lend_code','empName','rl_from_date','rl_to_date','rl_status');
    clear_filter($filter);
  }


} //--- end class
?>
