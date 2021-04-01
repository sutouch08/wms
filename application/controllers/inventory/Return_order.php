<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Return_order extends PS_Controller
{
  public $menu_code = 'ICRTOR';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RETURN';
	public $title = 'คืนสินค้า(ลดหนี้ขาย)';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/return_order';
    $this->load->model('inventory/return_order_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/customers_model');
    $this->load->model('masters/products_model');
  }


  public function index()
  {
    $filter = array(
      'code'    => get_filter('code', 'sm_code', ''),
      'invoice' => get_filter('invoice', 'sm_invoice', ''),
      'customer_code' => get_filter('customer_code', 'sm_customer_code', ''),
      'from_date' => get_filter('from_date', 'sm_from_date', ''),
      'to_date' => get_filter('to_date', 'sm_to_date', ''),
      'status' => get_filter('status', 'sm_status', 'all'),
      'approve' => get_filter('approve', 'sm_approve', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->return_order_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$document = $this->return_order_model->get_data($filter, $perpage, $this->uri->segment($segment));

    if(!empty($document))
    {
      foreach($document as $rs)
      {
        $rs->qty = $this->return_order_model->get_sum_qty($rs->code);
        $rs->amount = $this->return_order_model->get_sum_amount($rs->code);
        $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
      }
    }

    $filter['docs'] = $document;
		$this->pagination->initialize($init);
    $this->load->view('inventory/return_order/return_order_list', $filter);
  }




  public function add_details($code)
  {
    $sc = TRUE;
    // print_r($this->input->post());
    // exit();
    if($this->input->post())
    {
      $this->load->model('inventory/movement_model');
      //--- start transection
      $this->db->trans_begin();

      $doc = $this->return_order_model->get($code);
      if(!empty($doc))
      {
        $qtys = $this->input->post('qty');
        $item = $this->input->post('item');
        $sold_qtys = $this->input->post('sold_qty');
        $prices = $this->input->post('price');
        $discounts = $this->input->post('discount');

        $vat = getConfig('SALE_VAT_RATE'); //--- 0.07
        //--- drop old detail
        $this->return_order_model->drop_details($code);

        if(count($qtys) > 0)
        {
          foreach($qtys as $row => $qty)
          {
            if($qty > 0)
            {
              $price = round($prices[$row], 2);
              $discount = $discounts[$row];
              $disc_amount = $discount == 0 ? 0 : $qty * ($price * ($discount * 0.01));
              $amount = ($qty * $price) - $disc_amount;
              $arr = array(
                'return_code' => $code,
                'invoice_code' => $doc->invoice,
                'product_code' => $item[$row],
                'product_name' => $this->products_model->get_name($item[$row]),
                'sold_qty' => $sold_qtys[$row],
                'qty' => $qty,
                'price' => $price,
                'discount_percent' => $discount,
                'amount' => $amount,
                'vat_amount' => get_vat_amount($amount)
              );

              if($this->return_order_model->add_detail($arr) === FALSE)
              {
                $sc = FALSE;
                $this->error = 'บันทึกรายการไม่สำเร็จ';
                break;
              }
              else
              {
                $ds = array(
                  'reference' => $code,
                  'warehouse_code' => $doc->warehouse_code,
                  'zone_code' => $doc->zone_code,
                  'product_code' => $item[$row],
                  'move_in' => $qty,
                  'date_add' => $doc->date_add
                );

                if($this->movement_model->add($ds) === FALSE)
                {
                  $sc = FALSE;
                  $message = 'บันทึก movement ไม่สำเร็จ';
                }
              }
            } //--- end if qty > 0
          } //--- end foreach
        }//-- end if count($qtys)

        $this->return_order_model->set_status($code, 1);

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
        //--- empty document
        $sc = FALSE;
        set_error('ไม่พบเลขที่เอกสาร');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('ไม่พบจำนวนในการรับคืน');
    }

    if($sc === TRUE)
    {
      set_message('Success');
      redirect($this->home.'/view_detail/'.$code);
    }
    else
    {
      redirect($this->home.'/edit/'.$code);
    }

  }

  public function delete_detail($id)
  {
    $rs = $this->return_order_model->delete_detail($id);
    echo $rs === TRUE ? 'success' : 'ลบรายการไม่สำเร็จ';
  }


  public function unsave($code)
  {
    $sc = TRUE;
    $this->load->model('inventory/movement_model');
    if($this->pm->can_edit)
    {
      if($this->return_order_model->set_status($code, 0) === FALSE)
      {
        $sc = FALSE;
        $message = 'ยกเลิกการบันทึกไม่สำเร็จ';
      }
      else
      {
        if($this->movement_model->drop_movement($code) === FALSE)
        {
          $sc = FALSE;
          $message = 'ลบ movement ไม่สำเร็จ';
        }
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'คุณไม่มีสิทธิ์ในการยกเลิกการบันทึก';
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function approve($code)
  {
    if($this->pm->can_approve)
    {
      $this->load->model('approve_logs_model');
      if($this->return_order_model->approve($code))
      {
        $this->approve_logs_model->add($code, 1, get_cookie('uname'));
        $export = $this->do_export($code);
        echo $export === TRUE ? 'success' : $this->error;
      }
      else
      {
        echo 'อนุมัติเอกสารไม่สำเร็จ';
      }
    }
    else
    {
      echo 'คุณไม่มีสิทธิ์อนุมัติ';
    }
  }



  public function unapprove($code)
  {
    if($this->pm->can_approve)
    {
      //--- check document in SAP
      $sap = $this->return_order_model->get_sap_return_order($code);
      if(!empty($sap))
      {
        $this->load->model('approve_logs_model');

        if($this->return_order_model->unapprove($code))
        {
          $this->approve_logs_model->add($code, 0, get_cookie('uname'));
          echo 'success';
        }
        else
        {
          echo 'ยกเลิกอนุมัติเอกสารไม่สำเร็จ';
        }
      }
    }
    else
    {
      echo 'คุณไม่มีสิทธิ์อนุมัติ';
    }
  }


  public function add_new()
  {
    $this->load->view('inventory/return_order/return_order_add');
  }


  public function add()
  {
    if($this->input->post('date_add'))
    {
      $date_add = db_date($this->input->post('date_add'), TRUE);
      $invoice = trim($this->input->post('invoice'));
      $customer_code = trim($this->input->post('customer_code'));
      $zone = $this->zone_model->get($this->input->post('zone_code'));
      $remark = trim($this->input->post('remark'));

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
        'bookcode' => getConfig('BOOK_CODE_RETURN_ORDER'),
        'invoice' => $invoice,
        'customer_code' => $customer_code,
        'warehouse_code' => $zone->warehouse_code,
        'zone_code' => $zone->code,
        'user' => get_cookie('uname'),
        'date_add' => $date_add,
        'remark' => $remark
      );

      $rs = $this->return_order_model->add($arr);
      if($rs === TRUE)
      {
        redirect($this->home.'/edit/'.$code);
      }
      else
      {
        set_error("เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง");
        redirect($this->home.'/add_new');
      }
    }
    else
    {
      set_error("ไม่พบข้อมูลเอกสารหรือฟอร์มว่างเปล่า กรุณาตรวจสอบ");
      redirect($this->home.'/add_new');
    }
  }


  public function edit($code)
  {
    $doc = $this->return_order_model->get($code);
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    $details = $this->return_order_model->get_details($code);

    $detail = array();
      //--- ถ้าไม่มีรายละเอียดให้ไปดึงจากใบกำกับมา
    if(empty($details))
    {
      $details = $this->return_order_model->get_invoice_details($doc->invoice);
      if(!empty($details))
      {
        //--- ถ้าได้รายการ ให้ทำการเปลี่ยนรหัสลูกค้าให้ตรงกับเอกสาร
        $cust = $this->return_order_model->get_customer_invoice($doc->invoice);
        if(!empty($cust))
        {
          $this->return_order_model->update($doc->code, array('customer_code' => $cust->customer_code));
        }
        //--- เปลี่ยนข้อมูลที่จะแสดงให้ตรงกันด้วย
        $doc->customer_code = $cust->customer_code;
        $doc->customer_name = $cust->customer_name;

        foreach($details as $rs)
        {
          if($rs->qty > 0)
          {
            $dt = new stdClass();
            $dt->id = 0;
            $dt->invoice_code = $doc->invoice;
            $dt->barcode = $this->products_model->get_barcode($rs->product_code);
            $dt->product_code = $rs->product_code;
            $dt->product_name = $rs->product_name;
            $dt->sold_qty = round($rs->qty, 2);
            $dt->discount_percent = round($rs->discount, 2);
            $dt->qty = round($rs->qty, 2);
            $dt->price = round(add_vat($rs->price), 2);
            $dt->amount = round((get_price_after_discount($dt->price, $dt->discount_percent) * $rs->qty), 2);

            $detail[] = $dt;
          }
        }
      }
    }
    else
    {
      foreach($details as $rs)
      {
        $returned_qty = $this->return_order_model->get_returned_qty($doc->invoice, $rs->product_code);
        $qty = $rs->sold_qty - ($returned_qty - $rs->qty);
        if($qty > 0)
        {
          $dt = new stdClass();
          $dt->id = $rs->id;
          $dt->invoice_code = $doc->invoice;
          $dt->barcode = $this->products_model->get_barcode($rs->product_code);
          $dt->product_code = $rs->product_code;
          $dt->product_name = $rs->product_name;
          $dt->sold_qty = $qty;
          $dt->discount_percent = $rs->discount_percent;
          $dt->qty = $rs->qty;
          $dt->price = round($rs->price,2);
          $dt->amount = round($rs->amount,2);

          $detail[] = $dt;
        }
      }
    }


    $ds = array(
      'doc' => $doc,
      'details' => $detail
    );

    if($doc->status == 0)
    {
      $this->load->view('inventory/return_order/return_order_edit', $ds);
    }
    else
    {
      $this->load->view('inventory/return_order/return_order_view_detail', $ds);
    }

  }



  public function update()
  {
    $sc = TRUE;
    if($this->input->post('return_code'))
    {
      $code = $this->input->post('return_code');
      $date_add = db_date($this->input->post('date_add'), TRUE);
      $invoice = trim($this->input->post('invoice'));
      $customer_code = $this->input->post('customer_code');
      $zone = $this->zone_model->get($this->input->post('zone_code'));
      $remark = $this->input->post('remark');

      $arr = array(
        'date_add' => $date_add,
        'invoice' => $invoice,
        'customer_code' => $customer_code,
        'warehouse_code' => $zone->warehouse_code,
        'zone_code' => $zone->code,
        'remark' => $remark,
        'update_user' => get_cookie('uname')
      );

      if($this->return_order_model->update($code, $arr) === FALSE)
      {
        $sc = FALSE;
        $message = 'ปรับปรุงข้อมูลไม่สำเร็จ';
      }

    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function view_detail($code)
  {
    $this->load->model('approve_logs_model');
    $doc = $this->return_order_model->get($code);
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);

    $return_details = $this->return_order_model->get_details($code);
    $details = array();

    if(!empty($return_details))
    {
      foreach($return_details as $rs)
      {
        $dt = new stdClass();
        $dt->id = $rs->id;
        $dt->invoice_code = $rs->invoice_code;
        $dt->barcode = $this->products_model->get_barcode($rs->product_code);
        $dt->product_code = $rs->product_code;
        $dt->product_name = $rs->product_name;
        $dt->price = $rs->price;
        $dt->discount_percent = $rs->discount_percent;
        $dt->sold_qty = $rs->sold_qty;
        $dt->qty = $rs->qty;
        $dt->amount = $rs->amount;
        $details[] = $dt;
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'approve_list' => $this->approve_logs_model->get($code)
    );

    $this->load->view('inventory/return_order/return_order_view_detail', $ds);
  }


  public function get_invoice($invoice)
  {
    $sc = TRUE;
    $details = $this->return_order_model->get_invoice_details($invoice);
    $ds = array();
    if(empty($details))
    {
      $sc = FALSE;
      $message = 'ไม่พบข้อมูล';
    }

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $returned_qty = $this->return_order_model->get_returned_qty($invoice, $rs->product_code);
        $qty = $rs->qty - $returned_qty;
        $row = new stdClass();
        if($qty > 0)
        {
          $row->barcode = $this->products_model->get_barcode($rs->product_code);
          $row->invoice = $invoice;
          $row->code = $rs->product_code;
          $row->name = $rs->product_name;
          $row->price = round($rs->price, 2);
          $row->discount = round($rs->discount, 2);
          $row->qty = round($qty, 2);
          $row->amount = 0;
          $ds[] = $row;
        }
      }
    }

    echo $sc === TRUE ? json_encode($ds) : $message;
  }




  public function print_detail($code)
  {
    $this->load->library('printer');
    $doc = $this->return_order_model->get($code);
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    $details = $this->return_order_model->get_details($code);

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }
    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_return', $ds);
  }



  public function cancle_return($code)
  {
    $sc = TRUE;
    if($this->pm->can_delete)
    {
      $this->db->trans_start();
      $this->return_order_model->set_status($code, 2);
      $this->return_order_model->cancle_details($code);
      $this->db->trans_complete();

      if($this->db->trans_status() === FALSE)
      {
        $sc = FALSE;
        $message = $this->db->error();
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'คุณไม่มีสิทธิ์ในการยกเลิกเอกสาร';
    }

    echo $sc === TRUE ? 'success' : $message;
  }




  public function get_item()
  {
    if($this->input->post('barcode'))
    {
      $barcode = trim($this->input->post('barcode'));
      $item = $this->products_model->get_product_by_barcode($barcode);
      if(!empty($item))
      {
        echo json_encode($item);
      }
      else
      {
        echo 'not-found';
      }
    }
  }





  public function do_export($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_return($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }




  //---- เรียกใช้จากภายนอก
  public function export_return($code)
  {
    if($this->do_export($code))
    {
      echo 'success';
    }
    else
    {
      echo $this->error;
    }
  }



  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RETURN_ORDER');
    $run_digit = getConfig('RUN_DIGIT_RETURN_ORDER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->return_order_model->get_max_code($pre);
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


  public function clear_filter()
  {
    $filter = array(
      'sm_code',
      'sm_invoice',
      'sm_customer_code',
      'sm_from_date',
      'sm_to_date',
      'sm_status',
      'sm_approve'
    );
    clear_filter($filter);
  }


} //--- end class
?>
