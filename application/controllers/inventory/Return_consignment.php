<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Return_consignment extends PS_Controller
{
  public $menu_code = 'ICRTSM';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RETURN';
	public $title = 'ลดหนี้ฝากขายเทียม';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/return_consignment';
    $this->load->model('inventory/return_consignment_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/customers_model');
    $this->load->model('masters/products_model');
  }


  public function index()
  {
    $filter = array(
      'code'    => get_filter('code', 'cn_code', ''),
      'invoice' => get_filter('invoice', 'cn_invoice', ''),
      'customer_code' => get_filter('customer_code', 'cn_customer_code', ''),
      'from_date' => get_filter('from_date', 'cn_from_date', ''),
      'to_date' => get_filter('to_date', 'cn_to_date', ''),
      'status' => get_filter('status', 'cn_status', 'all'),
      'approve' => get_filter('approve', 'cn_approve', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->return_consignment_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$document = $this->return_consignment_model->get_data($filter, $perpage, $this->uri->segment($segment));

    if(!empty($document))
    {
      foreach($document as $rs)
      {
        $rs->qty = $this->return_consignment_model->get_sum_qty($rs->code);
        $rs->amount = $this->return_consignment_model->get_sum_amount($rs->code);
        $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
      }
    }

    $filter['docs'] = $document;
		$this->pagination->initialize($init);
    $this->load->view('inventory/return_consignment/return_consignment_list', $filter);
  }




  public function add_details($code)
  {
    $sc = TRUE;

    if($this->input->post('qty'))
    {
      $this->load->model('inventory/movement_model');
      //--- start transection
      $this->db->trans_begin();

      $doc = $this->return_consignment_model->get($code);
      if(!empty($doc))
      {
        $items = $this->input->post('item');
        $qtys = $this->input->post('qty');
        $prices = $this->input->post('price');
        $sold = $this->input->post('sold_qty');
        $discount = $this->input->post('discount');
        $vat = getConfig('SALE_VAT_RATE'); //--- 0.07
				$date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : (empty($doc->received_date) ? now() : $doc->received_date);

        //--- drop old detail
        $this->return_consignment_model->drop_details($code);

        if(!empty($qtys))
        {
          foreach($qtys as $no => $qty)
          {
            if($qty > 0)
            {
              $disc_amount = $qty * ($prices[$no] * ($discount[$no] * 0.01));
              $amount = ($qty * $prices[$no]) - $disc_amount;
              $arr = array(
                'return_code' => $code,
                'invoice_code' => $doc->invoice,
                'product_code' => $items[$no],
                'product_name' => $this->products_model->get_name($items[$no]),
                'sold_qty' => $sold[$no],
                'qty' => $qty,
                'price' => $prices[$no],
                'discount_percent' => $discount[$no],
                'amount' => $amount,
                'vat_amount' => get_vat_amount($amount)
              );

              if($this->return_consignment_model->add_detail($arr) === FALSE)
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
                  'product_code' => $items[$no],
                  'move_in' => $qty,
                  'date_add' => $doc->date_add
                );

                if($this->movement_model->add($ds) === FALSE)
                {
                  $sc = FALSE;
                  $message = 'บันทึก movement ไม่สำเร็จ';
                }
              }
            }

          } //--- endforeach

          $this->return_consignment_model->set_status($code, 1);

        }
        else
        {
          $sc = FALSE;
          set_error('ไม่พบจำนวนในการรับคืน');
        } //--- end if empty qty


        if($this->db->trans_status() === FALSE)
        {
          $sc = FALSE;
          set_error($this->db->error());
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
        //--- empty document
        $sc = FALSE;
        set_error('ไม่พบเลขที่เอกสาร');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('ไม่พบข้อมูลในฟอร์ม');
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




  public function delete_detail($id = NULL)
  {
    if($id === NULL)
    {
      echo 'success';
    }
    else
    {
      $rs = $this->return_consignment_model->delete_detail($id);
      echo $rs === TRUE ? 'success' : 'ลบรายการไม่สำเร็จ';
    }

  }


  public function unsave($code)
  {
    $sc = TRUE;
    $this->load->model('inventory/movement_model');
    if($this->pm->can_edit)
    {
      if($this->return_consignment_model->set_status($code, 0) === FALSE)
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
      $rs = $this->return_consignment_model->approve($code);
      if($rs === TRUE)
      {
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


  public function add_new()
  {
    $this->load->view('inventory/return_consignment/return_consignment_add');
  }


  public function add()
  {
    $sc = TRUE;

    if($this->input->post('date_add'))
    {
      $date_add = db_date($this->input->post('date_add'), TRUE);
      $invoice = trim($this->input->post('invoice'));
      $customer_code = trim($this->input->post('customer_code'));
      $from_zone = $this->zone_model->get($this->input->post('from_zone'));
      $zone = $this->zone_model->get($this->input->post('zone_code'));
      $remark = trim($this->input->post('remark'));
      $gp = empty($this->input->post('gp')) ? 0 : $this->input->post('gp');

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
        'bookcode' => getConfig('BOOK_CODE_RETURN_CONSIGNMENT'),
        'invoice' => $invoice,
        'customer_code' => $customer_code,
        'from_warehouse_code' => $from_zone->warehouse_code,
        'from_zone_code' => $from_zone->code,
        'warehouse_code' => $zone->warehouse_code,
        'zone_code' => $zone->code,
        'gp' => $gp,
        'user' => get_cookie('uname'),
        'date_add' => $date_add,
        'remark' => $remark
      );

      if(! $this->return_consignment_model->add($arr))
      {
        $sc = FALSE;
        $this->error = "เพิ่มเอกสารไม่สำเร็จ";
      }
      else
      {
        if(!empty($invoice))
        {
          $inv_amount = $this->return_consignment_model->get_sap_invoice_amount($invoice);
          if(!empty($inv_amount))
          {
            $inv_arr = array(
              'return_code' => $code,
              'invoice_code' => $invoice,
              'invoice_amount' => $inv_amount
            );

            $this->return_consignment_model->add_invoice($inv_arr);
          }

        }

      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบข้อมูลเอกสารหรือฟอร์มว่างเปล่า กรุณาตรวจสอบ";
    }

    if($sc === TRUE)
    {
      $ds = array(
        'status' => 'success',
        'code' => $code
      );
    }
    else
    {
      $ds = array(
        'status' => 'error',
        'message' => $this->error
      );
    }

    echo json_encode($ds);

  }


  public function edit($code)
  {
    $this->load->helper('return_consignment');
    $doc = $this->return_consignment_model->get($code);
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    $doc->from_zone_name = $this->zone_model->get_name($doc->from_zone_code);

    $invoice_list = $this->return_consignment_model->get_all_invoice($code);
    $doc->invoice_amount = round($this->return_consignment_model->get_sum_invoice_amount($code), 2);
    $doc->invoice_list = getInvoiceList($code, $invoice_list, $doc->status);

    $details = $this->return_consignment_model->get_details($code);
    $no = 0;
    $detail = array();
      //--- ถ้าไม่มีรายละเอียดให้ไปดึงจากใบกำกับมา
    if(empty($details))
    {
      $details = NULL;
    }
    else
    {
      foreach($details as $rs)
      {
        $returned_qty = $this->return_consignment_model->get_returned_qty($doc->invoice, $rs->product_code);
        $qty = $rs->sold_qty - ($returned_qty - $rs->qty);
        if($qty > 0)
        {
          $no++;
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
          $dt->no = $no;

          $detail[] = $dt;
        }
      }
    }


    $ds = array(
      'doc' => $doc,
      'details' => $detail,
      'no' => $no
    );

    if($doc->status == 0)
    {
      $this->load->view('inventory/return_consignment/return_consignment_edit', $ds);
    }
    else
    {
      $this->load->view('inventory/return_consignment/return_consignment_view_detail', $ds);
    }

  }


  public function get_invoice_list($code)
  {
    $arr = array(
      'invoice_list' => '',
      'amount' => 0
    );

    $invoice = $this->return_consignment_model->get_all_invoice($code);
    if(!empty($invoice))
    {
      $list = "";
      $amount = 0;
      $i = 1;
      foreach($invoice as $rs)
      {
        $list .= $i === 1 ? $rs->invoice_code : ", {$rs->invoice_code}";
        $amount += $rs->invoice_amount;
        $i++;
      }

      $arr['invoice_list'] = $list;
      $arr['amount'] = $amount;
    }

    return $arr;
  }



  public function get_active_check_list($zone_code)
  {
    $ds = array();
    $this->load->model('inventory/consign_check_model');
    $list = $this->consign_check_model->get_active_check_list($zone_code); //--- saved and not valid

    if(!empty($list))
    {
      foreach($list as $rs)
      {
        $arr = array(
          'code' => $rs->code,
          'date_add' => thai_date($rs->date_add)
        );

        array_push($ds, $arr);
      }
    }
    else
    {
      array_push($ds, array('nodata' => 'nodata'));
    }

    echo json_encode($ds);
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
      $from_zone = $this->zone_model->get($this->input->post('from_zone_code'));
      $remark = $this->input->post('remark');
      $gp = empty($this->input->post('gp')) ? 0 : $this->input->post('gp');

      $arr = array(
        'date_add' => $date_add,
        'invoice' => $invoice,
        'customer_code' => $customer_code,
        'from_warehouse_code' => $from_zone->warehouse_code,
        'from_zone_code' => $from_zone->code,
        'warehouse_code' => $zone->warehouse_code,
        'zone_code' => $zone->code,
        'gp' => $gp,
        'remark' => $remark,
        'update_user' => get_cookie('uname')
      );

      if($this->return_consignment_model->update($code, $arr) === FALSE)
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
    $doc = $this->return_consignment_model->get($code);
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);

    $return_details = $this->return_consignment_model->get_details($code);
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
      'details' => $details
    );

    $this->load->view('inventory/return_consignment/return_consignment_view_detail', $ds);
  }



  function load_check_list($code)
  {
    $sc = TRUE;
    if($this->input->post('check_code'))
    {
      $this->load->model('inventory/consign_check_model');
      $doc = $this->return_consignment_model->get($code);
      $check_code = $this->input->post('check_code');
      $input_type = 2; //---- load diff
      $details = $this->consign_check_model->get_returned_details($check_code);
      if(!empty($details))
      {
        $this->db->trans_begin();

        //---- update return code in consign_check mark as loaded
        if(! $this->return_consignment_model->update_ref_code($code, $check_code))
        {
          $sc = FALSE;
          $this->error = "Update ref_code failed";
        }
        else
        {
          $ds = array();
          foreach($details as $rs)
          {
            $item = $this->products_model->get($rs->product_code);
            $discLabel = $this->consignment_order_model->get_item_gp($item->code, $doc->zone_code);
            $disc = parse_discount_text($discLabel, $item->price);
            $discount = $disc['discount_amount'];
            $amount = ($item->price - $discount) * $rs->diff;
            $detail = $this->consignment_order_model->get_exists_detail($code, $item->code, $item->price, $discLabel, $input_type);
            if($sc == FALSE)
            {
              break;
            }

            if(!empty($item))
            {
              $rs->barcode = $item->barcode;
              $rs->code = $item->code;
              $rs->name = $item->name;
              $rs->price = round($item->price, 2);
              $rs->discount = $doc->gp.' %';

            }
            else
            {
              $sc = FALSE;
              $this->error = "รหัสสินค้าไม่ถูกต้อง : {$rs->product_code}";
            }

            $rs->barcode = $item->barcode;
            $row->barcode = $this->products_model->get_barcode($rs->product_code);
            $row->invoice = $invoice;
            $row->code = $rs->product_code;
            $row->name = $rs->product_name;
            $row->price = round($rs->price, 2);
            $row->discount = round($rs->discount, 2);
            $row->qty = round($qty, 2);
            $row->amount = 0;
            $ds[] = $row;
            if(empty($detail))
            {
              //--- add new row
              $arr = array(
                'consign_code' => $code,
                'style_code' => $item->style_code,
                'product_code' => $item->code,
                'product_name' => $item->name,
                'cost' => $item->cost,
                'price' => $item->price,
                'qty' => $rs->diff,
                'discount' => $discLabel,
                'discount_amount' => $discount * $rs->diff,
                'amount' => $amount,
                'ref_code' => $check_code,
                'input_type' => $input_type
              );

              $this->consignment_order_model->add_detail($arr);
            }
            else
            {

              //-- update new rows
              //--- ถ้าจำนวนที่ยังไม่บันทึก รวมกับจำนวนใหม่ไม่เกินยอดในโซน หรือ คลังสามารถติดลบได้
              $new_qty = $rs->diff + $detail->qty;
              //--- add new row
              $arr = array(
                'qty' => $new_qty,
                'discount_amount' => $discount * $new_qty,
                'amount' => ($item->price - $discount) * $new_qty
              );

              $this->consignment_order_model->update_detail($detail->id, $arr);
            }
          }
        }


      }

      $this->consign_check_model->update_return_code($check_code, $code, 1);

      $this->db->trans_complete();

      if($this->db->trans_status() === FALSE)
      {
        $this->error = "เพิ่มรายการไม่สำเร็จ";
        $sc = FALSE;
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสารกระทบยอด";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function add_invoice()
  {
    $sc = TRUE;
    $this->load->helper('return_consignment');
    $invoice = $this->input->post('invoice');
    $customer_code = $this->input->post('customer_code');
    $code = $this->input->post('return_code');
    $doc = $this->return_consignment_model->get($code);

    if(!empty($doc))
    {
      //--- check invoice with customer
      $amount = $this->return_consignment_model->get_sap_invoice_amount($invoice, $customer_code);
      if(!empty($amount))
      {
        //--- check invoice in table
        $isExists = $this->return_consignment_model->is_exists_invoice($invoice, $code);
        if($isExists === FALSE)
        {
          //-- เตรียมข้อมูลเพิ่มเข้าตาราง
          $arr = array(
            'return_code' => $code,
            'invoice_code' => $invoice,
            'invoice_amount' => $amount
          );

          if($this->return_consignment_model->add_invoice($arr))
          {
            $invoice_list = $this->return_consignment_model->get_all_invoice($code);
            $amount = $this->return_consignment_model->get_sum_invoice_amount($code);

            $ds = array(
              'invoice' => getInvoiceList($code,$invoice_list, $doc->status),
              'amount' => $amount
            );

          }
          else
          {
            $sc = FALSE;
            $this->error = "เพิ่มบิลไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เลขที่บิลซ้ำ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่บิลไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "เลขที่เอกสารไม่ถูกต้อง";
    }

    echo $sc === FALSE ? $this->error : json_encode($ds);
  }




  function remove_invoice()
  {
    $this->load->helper('return_consignment');
    $sc = TRUE;
    $code = $this->input->get('return_code');
    $invoice_code = $this->input->get('invoice_code');
    $doc = $this->return_consignment_model->get($code);

    if(!empty($doc))
    {
      if(!empty($invoice_code))
      {
        if($this->return_consignment_model->delete_invoice($code, $invoice_code))
        {
          $amount = $this->return_consignment_model->get_sum_invoice_amount($code);
          $invoice_list = $this->return_consignment_model->get_all_invoice($code);

          $arr = array(
            'invoice' => getInvoiceList($code, $invoice_list, $doc->status),
            'amount' => $amount
          );
        }
        else
        {
          $sc = FALSE;
          $this->error = "ลบ Invoice ไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเลขที่ invoice";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }


    echo $sc === TRUE ? json_encode($arr) : $this->error;
  }



  public function get_invoice()
  {
    $sc = TRUE;
    $invoice = $this->input->get('invoice');
    $customer_code = $this->input->get('customer_code');
    $no = empty($this->input->get('no')) ? 0 : $this->input->get('no');

    $details = $this->return_consignment_model->get_invoice_details($invoice, $customer_code);
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
        $returned_qty = $this->return_consignment_model->get_returned_qty($invoice, $rs->product_code);
        $qty = $rs->qty - $returned_qty;
        $row = new stdClass();
        if($qty > 0)
        {
          $no++;
          $row->barcode = $this->products_model->get_barcode($rs->product_code);
          $row->invoice = $invoice;
          $row->code = $rs->product_code;
          $row->name = $rs->product_name;
          $row->price = round(add_vat($rs->price, $rs->vat_rate), 2);
          $row->discount = round($rs->discount, 2);
          $row->qty = round($qty, 2);
          $row->amount = 0;
          $row->no = $no;
          $ds[] = $row;

        }
      }
    }

    $data = array(
      'top' => $no,
      'data' => $ds
    );

    echo $sc === TRUE ? json_encode($data) : $message;
  }




  public function search_invoice_code($customer_code = NULL)
  {
    $sc = array();

    if(!empty($customer_code))
    {
      $txt = $_REQUEST['term'];
      $result = $this->return_consignment_model->search_invoice_code($customer_code, $txt);
      if(!empty($result))
      {
        foreach($result as $rs)
        {
          $sc[] = $rs->DocNum.' | '.number($rs->DocTotal, 2);
        }
      }
      else
      {
        $sc[] = 'not found';
      }
    }
    else
    {
      $sc[] = 'กรุณาระบุลูกค้า';
    }

    echo json_encode($sc);
  }




  public function print_detail($code)
  {
    $this->load->library('printer');
    $this->load->helper('return_consignment');
    $doc = $this->return_consignment_model->get($code);
    $doc->invoice_text = getAllInvoiceText($this->return_consignment_model->get_all_invoice($code));
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    $details = $this->return_consignment_model->get_details($code);

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

    $this->load->view('print/print_return_consignment', $ds);
  }



  public function cancle_return($code)
  {
    $sc = TRUE;
    if($this->pm->can_delete)
    {
      $this->db->trans_start();
      $this->return_consignment_model->set_status($code, 2);
      $this->return_consignment_model->cancle_details($code);
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
    if(! $this->export->export_return_consignment($code))
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
    $prefix = getConfig('PREFIX_RETURN_CONSIGNMENT');
    $run_digit = getConfig('RUN_DIGIT_RETURN_CONSIGNMENT');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->return_consignment_model->get_max_code($pre);
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
      'cn_code',
      'cn_invoice',
      'cn_customer_code',
      'cn_from_date',
      'cn_to_date',
      'cn_status',
      'cn_approve'
    );

    clear_filter($filter);
  }


} //--- end class
?>
