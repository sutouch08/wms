<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive_po extends PS_Controller
{
  public $menu_code = 'ICPURC';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RECEIVE';
	public $title = 'รับสินค้าจากการซื้อ';
  public $filter;
  public $error;
	public $isAPI;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/receive_po';
    $this->load->model('inventory/receive_po_model');
    $this->load->model('stock/stock_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/products_model');

		$this->isAPI = is_true(getConfig('WMS_API'));
  }


  public function index()
  {
    $this->load->helper('channels');
    $filter = array(
      'code'    => get_filter('code', 'receive_code', ''),
      'invoice' => get_filter('invoice', 'receive_invoice', ''),
      'po'      => get_filter('po', 'receive_po', ''),
      'vendor'  => get_filter('vendor', 'receive_vendor', ''),
      'from_date' => get_filter('from_date', 'receive_from_date', ''),
      'to_date' => get_filter('to_date', 'receive_to_date', ''),
      'status' => get_filter('status', 'receive_status', 'all'),
			'is_wms' => get_filter('is_wms', 'receive_is_wms', 'all'),
      'sap' => get_filter('sap', 'receive_sap', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->receive_po_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$document = $this->receive_po_model->get_data($filter, $perpage, $this->uri->segment($segment));

    if(!empty($document))
    {
      foreach($document as $rs)
      {
        $rs->qty = $this->receive_po_model->get_sum_qty($rs->code);
      }
    }

    $filter['document'] = $document;

		$this->pagination->initialize($init);
    $this->load->view('inventory/receive_po/receive_po_list', $filter);
  }



  public function view_detail($code)
  {
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');
    $this->load->model('approve_logs_model');

    $doc = $this->receive_po_model->get($code);
    if(!empty($doc))
    {
      $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    }

    $details = $this->receive_po_model->get_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }


    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'approve_logs' => $this->approve_logs_model->get($doc->request_code)
    );

    $this->load->view('inventory/receive_po/receive_po_detail', $ds);
  }



  public function print_detail($code)
  {
    $this->load->library('printer');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');

    $doc = $this->receive_po_model->get($code);
    if(!empty($doc))
    {
      $zone = $this->zone_model->get($doc->zone_code);
      $doc->zone_name = $zone->name;
      $doc->warehouse_name = $zone->warehouse_name;
    }

    $details = $this->receive_po_model->get_details($code);

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

    $this->load->view('print/print_received', $ds);
  }



	public function save()
  {
    $sc = TRUE;

    if($this->input->post('receive_code'))
    {
      $this->load->model('masters/products_model');
      $this->load->model('masters/zone_model');
      $this->load->model('inventory/movement_model');
      $this->load->model('inventory/receive_po_request_model');

      $code = $this->input->post('receive_code');

			$doc = $this->receive_po_model->get($code);
			$date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : now();

			$header = json_decode($this->input->post('header'));

			if(!empty($header))
			{
				$items = json_decode($this->input->post('items'));

				if(!empty($items))
				{

					$vendor_code = $header->vendor_code;
		      $vendor_name = $header->vendorName;
		      $po_code = $header->poCode;
		      $invoice = $header->invoice;
		      $zone_code = ($this->isAPI && $doc->is_wms == 1) ? getConfig('WMS_ZONE') : $header->zone_code;
		      $warehouse_code = ($this->isAPI && $doc->is_wms == 1) ? getConfig('WMS_WAREHOUSE') : $this->zone_model->get_warehouse_code($zone_code);
		      $approver = get_null($header->approver);
		      $request_code = get_null($header->requestCode);
					$DocCur = $header->DocCur;
					$DocRate = $header->DocRate;

					$arr = array(
		        'vendor_code' => $vendor_code,
		        'vendor_name' => $vendor_name,
		        'po_code' => $po_code,
		        'invoice_code' => $invoice,
		        'zone_code' => $zone_code,
		        'warehouse_code' => $warehouse_code,
		        'update_user' => get_cookie('uname'),
		        'approver' => $approver,
		        'request_code' => $request_code,
						'currency' => empty($DocCur) ? "THB" : $DocCur,
						'rate' => empty($DocRate) ? 1 : $DocRate
		      );

					$this->db->trans_begin();

		      if($this->receive_po_model->update($code, $arr) === FALSE)
		      {
		        $sc = FALSE;
		        $this->error = 'Update Document Fail';
		      }
		      else
		      {
		        if(!empty($items))
		        {
		          //--- ลบรายการเก่าก่อนเพิ่มรายการใหม่
		          $this->receive_po_model->drop_details($code);

							$details = array();

		          foreach($items as $rs)
		          {
		            if($rs->qty != 0)
		            {
		              $pd = $this->products_model->get($rs->product_code);

		              if(!empty($pd))
		              {
		                $bf = $rs->backlogs; ///--- ยอดค้ารับ ก่อนรับ
		                $af = ($bf - $rs->qty) > 0 ? ($bf - $rs->qty) : 0;  //--- ยอดค้างรับหลังรับแล้ว

		                $ds = array(
		                  'receive_code' => $code,
		                  'style_code' => $pd->style_code,
		                  'product_code' => $pd->code,
		                  'product_name' => $pd->name,
		                  'price' => $rs->price,
		                  'qty' => $rs->qty,
		                  'amount' => $rs->qty * $rs->price,
		                  'before_backlogs' => $bf,
		                  'after_backlogs' => $af,
											'currency' => empty($DocCur) ? "THB" : $DocCur,
											'rate' => empty($DocRate) ? 1 : $DocRate,
											'vatGroup' => $rs->vatGroup,
											'vatRate' => $rs->vatRate
		                );

										if($this->isAPI && $doc->is_wms)
										{
											$de = new stdClass;
											$de->receive_code = $code;
											$de->style_code = $pd->style_code;
											$de->product_code = $pd->code;
											$de->product_name = $pd->name;
											$de->unit_code = $pd->unit_code;
											$de->price = $rs->price;
											$de->qty = $rs->qty;
											$de->amount = $rs->qty * $rs->price;
											$de->before_backlogs = $bf;
											$de->after_backlogs = $af;

											$details[] = $de;
										}

		                if($this->receive_po_model->add_detail($ds) === FALSE)
		                {
		                  $sc = FALSE;
		                  $this->error = 'Add Receive Row Fail';
		                  break;
		                }
		                else
		                {
											if($this->isAPI === FALSE OR $doc->is_wms == 0)
											{
												//--- insert Movement in
			                  $arr = array(
			                    'reference' => $code,
			                    'warehouse_code' => $warehouse_code,
			                    'zone_code' => $zone_code,
			                    'product_code' => $rs->product_code,
			                    'move_in' => $rs->qty,
			                    'move_out' => 0,
			                    'date_add' => $date_add
			                  );

			                  $this->movement_model->add($arr);
											}
		                }
		              }
		              else
		              {
		                $sc = FALSE;
		                $this->error = 'ไม่พบรหัสสินค้า : '.$item.' ในระบบ';
		              }
		            }
		          }

							if($this->isAPI && $doc->is_wms)
							{
								$this->receive_po_model->set_status($code, 3);
							}
							else
							{
								$arr = array(
									'shipped_date' => now(),
									'status' => 1
								);

								$this->receive_po_model->update($code, $arr);
							}

		        }

		        if($sc === TRUE)
		        {
		          $this->receive_po_request_model->update_receive_code($request_code, $code);
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

					if($this->isAPI === TRUE && $doc->is_wms == 1 && $sc === TRUE)
					{
						$this->wms = $this->load->database('wms', TRUE);
						$this->load->library('wms_receive_api');
						$doc->vendor_code = $vendor_code;
						$doc->vendor_name = $vendor_name;

						$rs = $this->wms_receive_api->export_receive_po($doc, $po_code, $invoice, $details);

						if(!$rs)
						{
							$sc = FALSE;
							$this->error = "บันทึกเอกสารสำเร็จ แต่ส่งข้อมูลไป WMS ไม่สำเร็จ <br/> ".$this->wms_receive_api->error;
						}
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Items rows not found!";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Header data not found!";
			}
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบข้อมูล';
    }

    if($sc === TRUE && ($this->isAPI === FALSE OR $doc->is_wms == 0))
    {
      $this->export_receive($code);
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



	public function send_to_wms($code)
	{
		$sc = TRUE;
		$doc = $this->receive_po_model->get($code);

		if(!empty($doc))
		{
			if($doc->status == 3)
			{
				$details = $this->receive_po_model->get_details($code);

				if(!empty($details))
				{
					$this->wms = $this->load->database('wms', TRUE);
					$this->load->library('wms_receive_api');

					$ex = $this->wms_receive_api->export_receive_po($doc, $doc->po_code, $doc->invoice_code, $details);

					if(!$ex)
					{
						$sc = FALSE;
						$thiis->error = "ส่งข้อมูลไป WMS ไม่สำเร็จ <br/>{$this->wms_receive_api->error}";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "No items in document";
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
			$this->error = "Invalid document code";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function do_export($code)
  {
    $rs = $this->export_receive($code);

    echo $rs === TRUE ? 'success' : $this->error;
  }


  private function export_receive($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_receive($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }


  public function cancle_received()
  {
    $sc = TRUE;
    if($this->input->post('receive_code'))
    {
      $this->load->model('inventory/movement_model');
      $code = $this->input->post('receive_code');
			$reason = $this->input->post('reason');

      //---- check doc status is open or close
      //---- if closed user cannot cancle document
      $status = $this->receive_po_model->get_doc_status($code);
      if($status === 'O')
      {
        $this->db->trans_start();
        $this->receive_po_model->cancle_details($code);
        $this->receive_po_model->set_status($code, 2); //--- 0 = ยังไม่บันทึก 1 = บันทึกแล้ว 2 = ยกเลิก
				$this->receive_po_model->set_cancle_reason($code, $reason);
        $this->movement_model->drop_movement($code);
        $this->db->trans_complete();

        if($this->db->trans_status() === FALSE)
        {
          $sc = FALSE;
          $this->error = 'ยกเลิกรายการไม่สำเร็จ';
        }
        else
        {
          $this->cancle_sap_doc($code);
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = 'เอกสารถูกปิดไปแล้วไม่สามารถดำเนินการใดๆได้';
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบเลขทีเอกสาร';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function cancle_sap_doc($code)
  {
    $sc = TRUE;

    $middle = $this->receive_po_model->get_middle_receive_po($code);
    if(!empty($middle))
    {
      foreach($middle as $rs)
      {
        $this->receive_po_model->drop_sap_received($rs->DocEntry);
      }
    }

    return $sc;
  }



  public function get_po_detail()
  {
    $sc = '';
    $this->load->model('masters/products_model');
    $po_code = $this->input->get('po_code');
    $details = $this->receive_po_model->get_po_details($po_code);
    $ro = getConfig('RECEIVE_OVER_PO');
    $rate = ($ro * 0.01);
    $ds = array();
    if(!empty($details))
    {
      $no = 1;
      $totalQty = 0;
      $totalBacklog = 0;

      foreach($details as $rs)
      {
				if($rs->OpenQty > 0)
				{
					$dif = $rs->Quantity - $rs->OpenQty;
					$barcode = $this->products_model->get_barcode($rs->ItemCode);
	        $arr = array(
	          'no' => $no,
						'uid' => $rs->DocEntry.$rs->LineNum,
	          'barcode' => empty($barcode) ? $rs->ItemCode : $barcode,
	          'pdCode' => $rs->ItemCode,
	          'pdName' => $rs->Dscription,
	          'price' => $rs->price,
						'currency' => $rs->Currency,
						'Rate' => $rs->Rate,
						'vatGroup' => $rs->VatGroup,
						'vatRate' => $rs->VatPrcnt,
	          'qty' => number($rs->Quantity),
	          'limit' => ($rs->Quantity + ($rs->Quantity * $rate)) - $dif,
	          'backlog' => number($rs->OpenQty),
	          'isOpen' => $rs->LineStatus === 'O' ? TRUE : FALSE
	        );
	        array_push($ds, $arr);
	        $no++;
	        $totalQty += $rs->Quantity;
	        $totalBacklog += $rs->OpenQty;
				}
      }

      $arr = array(
        'qty' => number($totalQty),
        'backlog' => number($totalBacklog)
      );
      array_push($ds, $arr);

      $sc = json_encode($ds);
    }
    else
    {
      $sc = 'ใบสั่งซื้อไม่ถูกต้อง หรือ ใบสั่งซื้อถูกปิดไปแล้ว';
    }

    echo $sc;
  }



  public function get_receive_request_po_detail()
  {
    $this->load->model('inventory/receive_po_request_model');
    $this->load->model('masters/products_model');

    $sc = '';
    $code = $this->input->get('request_code');
    $doc  = $this->receive_po_request_model->get($code);
    if(!empty($doc))
    {
      $details = $this->receive_po_request_model->get_details($code);

      $data = array(
        'code' => $doc->code,
        'vendor_code' => $doc->vendor_code,
        'vendor_name' => $doc->vendor_name,
        'invoice_code' => $doc->invoice_code,
        'po_code' => $doc->po_code
      );

      $ds = array();
      if(!empty($details))
      {
        $no = 1;
        $totalQty = 0;
        $totalBacklog = 0;

        foreach($details as $rs)
        {
          $backlogs = $this->receive_po_request_model->get_backlogs($doc->po_code, $rs->product_code);
          $arr = array(
            'no' => $no,
            'barcode' => $this->products_model->get_barcode($rs->product_code),
            'pdCode' => $rs->product_code,
            'pdName' => $rs->product_name,
            'price' => $rs->price,
            'qty' => number($rs->qty),
            'limit' => $rs->qty,
            'backlog' => number($backlogs),
            'isOpen' => TRUE
          );
          array_push($ds, $arr);
          $no++;
          $totalQty += $rs->qty;
          $totalBacklog += $backlogs;
        }

        $arr = array(
          'qty' => number($totalQty),
          'backlog' => number($totalBacklog)
        );
        array_push($ds, $arr);

        $data['data'] = $ds;

        $sc = json_encode($data);
      }
      else
      {
        $sc = 'ใบสั่งซื้อไม่ถูกต้อง หรือ ใบสั่งซื้อถูกปิดไปแล้ว';
      }
    }
    else
    {
      $sc = "ใบขออนุมัติไม่ถูกต้อง";
    }


    echo $sc;
  }



  public function edit($code)
  {
		$this->load->helper('currency');
    $document = $this->receive_po_model->get($code);
    $ds['document'] = $document;
    $ds['is_strict'] = getConfig('STRICT_RECEIVE_PO');
    $ds['allow_over_po'] = getConfig('ALLOW_RECEIVE_OVER_PO');
    $this->load->view('inventory/receive_po/receive_po_edit', $ds);
  }



	public function get_po_currency()
	{
		$po_code = $this->input->get('po_code');

		$rs = $this->ms->select('DocCur, DocRate')->where('DocNum', $po_code)->get('OPOR');

		if($rs->num_rows() === 1)
		{
			echo json_encode($rs->row());
		}
		else
		{
			echo "not found";
		}
	}



  public function add_new()
  {
    $this->load->view('inventory/receive_po/receive_po_add');
  }



  //--- check exists document code
  public function is_exists($code)
  {
    $ext = $this->receive_po_model->is_exists($code);
    if($ext)
    {
      echo 'เลขที่เอกสารซ้ำ';
    }
    else
    {
      echo 'not_exists';
    }
  }




  public function add()
  {
    $sc = array();

    if($this->input->post('date_add'))
    {
      $date_add = db_date($this->input->post('date_add'), TRUE);

      if($this->input->post('code'))
      {
        $code = $this->input->post('code');
      }
      else
      {
        $code = $this->get_new_code($date_add);
      }

      $arr = array(
        'code' => $code,
        'bookcode' => getConfig('BOOK_CODE_RECEIVE_PO'),
        'vendor_code' => NULL,
        'vendor_name' => NULL,
        'po_code' => NULL,
        'invoice_code' => NULL,
        'remark' => get_null(trim($this->input->post('remark'))),
        'date_add' => $date_add,
        'user' => get_cookie('uname'),
				'is_wms' => $this->input->post('is_wms')
      );

      $rs = $this->receive_po_model->add($arr);

      if($rs)
      {
        redirect($this->home.'/edit/'.$code);
      }
      else
      {
        set_error('เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
        redirect($this->home.'/add_new');
      }
    }
  }



  public function update_header()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $date = db_date($this->input->post('date_add'), TRUE);
    $remark = get_null(trim($this->input->post('remark')));
		$is_wms = $this->input->post('is_wms');

    if(!empty($code))
    {
      $doc = $this->receive_po_model->get($code);

      if(!empty($doc))
      {
        if($doc->status == 0)
        {
          $arr = array(
            'date_add' => $date,
            'remark' => $remark,
						'is_wms' => $is_wms
          );

          if(! $this->receive_po_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสารถูกบันทึกแล้วไม่สามารถแก้ไขได้";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบข้อมูล";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขทีเอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }





  public function get_sell_stock($item_code, $warehouse = NULL, $zone = NULL)
  {
    $sell_stock = $this->stock_model->get_sell_stock($item_code, $warehouse, $zone);
    $reserv_stock = $this->orders_model->get_reserv_stock($item_code, $warehouse, $zone);
    $availableStock = $sell_stock - $reserv_stock;
    return $availableStock < 0 ? 0 : $availableStock;
  }




  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RECEIVE_PO');
    $run_digit = getConfig('RUN_DIGIT_RECEIVE_PO');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->receive_po_model->get_max_code($pre);
    if(!empty($code))
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
      'receive_code',
      'receive_invoice',
      'receive_po',
      'receive_vendor',
      'receive_from_date',
      'receive_to_date',
      'receive_status',
      'receive_sap',
			'receive_is_wms'
    );

    clear_filter($filter);
    echo "done";
  }


  public function get_vender_by_po($po_code)
  {
    $rs = $this->receive_po_model->get_vender_by_po($po_code);
    if(!empty($rs))
    {
      $arr = array(
        'code' => $rs->CardCode,
        'name' => $rs->CardName
      );

      echo json_encode($arr);
    }
    else
    {
      echo 'Not found';
    }
  }

} //--- end class
