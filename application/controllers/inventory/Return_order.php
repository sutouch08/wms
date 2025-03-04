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
	public $wms;
	public $isAPI;
  public $segment = 4;
  public $required_remark = 1;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/return_order';
    $this->load->model('inventory/return_order_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/customers_model');
    $this->load->model('masters/products_model');

		$this->isAPI = is_true(getConfig('WMS_API'));
    $this->wmsApi = is_true(getConfig('WMS_API'));
    $this->sokoApi = is_true(getConfig('SOKOJUNG_API'));
  }


  public function index()
  {
		$this->load->helper('warehouse');
    $this->load->helper('print');

    $filter = array(
      'code'    => get_filter('code', 'sm_code', ''),
      'invoice' => get_filter('invoice', 'sm_invoice', ''),
      'customer_code' => get_filter('customer_code', 'sm_customer_code', ''),
      'from_date' => get_filter('from_date', 'sm_from_date', ''),
      'to_date' => get_filter('to_date', 'sm_to_date', ''),
      'status' => get_filter('status', 'sm_status', 'all'),
      'approve' => get_filter('approve', 'sm_approve', 'all'),
			'zone' => get_filter('zone', 'sm_zone', ''),
			'api' => get_filter('api', 'sm_api', 'all'),
      'wms_export' => get_filter('wms_export', 'sm_wms_export', 'all'),
      'is_pos_api' => get_filter('is_pos_api', 'sm_pos_api', 'all'),
      'must_accept' => get_filter('must_accept', 'sm_must_accept', 'all'),
      'sap' => get_filter('sap', 'sm_sap', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$rows = $this->return_order_model->count_rows($filter);
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
		$document = $this->return_order_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

    if(!empty($document))
    {
      foreach($document as $rs)
      {
        $rs->qty = $this->return_order_model->get_sum_qty($rs->code);
        $rs->amount = $this->return_order_model->get_sum_amount($rs->code);
      }
    }

    $filter['docs'] = $document;
    $filter['allow_import_return'] = is_true(getConfig('ALLOW_IMPORT_RETURN'));
		$this->pagination->initialize($init);
    $this->load->view('inventory/return_order/return_order_list', $filter);
  }


  public function import_excel_file()
	{
    ini_set('max_execution_time', 1200);
    ini_set('memory_limit','1000M');

    $this->load->library('excel');

    $sc = TRUE;
    $import = 0;
    $uid = genUid();
    $Ymd = date('Ymd');
    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
  	$path = $this->config->item('upload_path').'return/';
    $file	= 'uploadFile';
    $config = array(
      "allowed_types" => "xlsx",
      "upload_path" => $path,
      "file_name"	=> "SM-import-{$Ymd}-{$uid}",
      "max_size" => 5120,
      "overwrite" => TRUE
    );

    $this->load->library("upload", $config);

    if(! $this->upload->do_upload($file))
    {
      $sc = FALSE;
      $this->error = $this->upload->display_errors();
    }

    if($sc === TRUE)
    {
      //---- checking data
      $info = $this->upload->data();
      /// read file
      $excel = PHPExcel_IOFactory::load($info['full_path']);
      //get only the Cell Collection
      $collection	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

      $i = 1;
      $count = count($collection);
      $limit = intval(getConfig('IMPORT_ROWS_LIMIT')) + 1;

      if($count <= $limit)
      {
        $ds = array();
        $zn = array(); //-- ไว้เก็บโซน object
        $bookcode = getConfig('BOOK_CODE_RETURN_ORDER');

        /*
        Loop เพื่อ จัดข้อมูลในรูปแบบเอกสารมี order เป็น key หลัก ในมิติที่ 1 ไว้ใช้สร้างเอกสาร
        รายการสินค้า จะถูกเพิ่มเข้าใน invoice เป็น array มิติที่ 2
        $ds[order_code] = array(
          [0] => line data object,
          [1] => line data object
        );
        */

        foreach($collection as $cs)
        {
          if($sc === FALSE)
          {
            break;
          }

          if($i === 1)
          {
            $i++;

            $headCol = array(
              'A' => 'Date',
              'B' => 'Order Code',
              'C' => 'Warehouse Code',
              'D' => 'Zone Code',
              'E' => 'Item Code',
              'F' => 'Return Qty',
              'G' => 'Interface',
              'H' => 'WMS',
              'I' => 'Remark'
            );

            foreach($headCol as $col => $field)
            {
              if($cs[$col] !== $field)
              {
                $sc = FALSE;
                $this->error = 'Column '.$col.' Should be '.$field;
                break;
              }
            }
          }
          else
          {
            if( ! empty($cs['A']))
            {
              if(empty(trim($cs['B'])))
              {
                $sc = FALSE;
                $this->error = "Missing Order Code at Line{$i}";
              }

              if(empty(trim($cs['C'])))
              {
                $sc = FALSE;
                $this->error = "Missing Warhouse Code at Line{$i}";
              }

              if(empty(trim($cs['D'])))
              {
                $sc = FALSE;
                $this->error = "Missing Zone Code at Line{$i}";
              }

              if(empty(trim($cs['E'])))
              {
                $sc = FALSE;
                $this->error = "Missing Item Code at Line{$i}";
              }

              if(empty(trim($cs['F'])) OR intval($cs['F']) <= 0)
              {
                $sc = FALSE;
                $this->error = "Invalid Reqturn Qty at Line{$i}";
              }

              if($sc === TRUE)
              {
                $date = db_date(trim($cs['A']));
                $order_code = trim($cs['B']); //--- order code use to be 1st dimention array
                $zone_code = trim($cs['D']);
                $item_code = trim($cs['E']);
                $return_qty = intval(trim($cs['F']));
                $api = trim($cs['G']);
                $is_wms = trim($cs['H']) == 2 ? 2 : 0;
                $remark = empty($cs['I']) ? NULL : get_null(trim($cs['I']));


                //--- ถ้ายังไม่มี order_code ให้สร้างใหม่
                if( ! isset($ds[$order_code]))
                {
                  //--- check date format only check not convert
                  if( ! is_valid_date($date))
                  {
                    $sc = FALSE;
                    $this->error = "Invalid Date format at Line{$i}";
                  }

                  //--- check warehouse and zone
                  if( empty($zn[$zone_code]))
                  {
                    $zone = $this->zone_model->get($zone_code);

                    if( ! empty($zone))
                    {
                      $zn[$zone_code] = $zone;
                    }
                    else
                    {
                      $sc = FALSE;
                      $this->error = "Invalid Zone Code at Line{$i}";
                    }
                  }
                  else
                  {
                    $zone = $zn[$zone_code];
                  }


                  //---- ไว้สร้างเอกสารใหม่
                  if($sc == TRUE)
                  {
                    $invoice = $this->return_order_model->get_invoice_detail_by_order_item($order_code, $item_code);

                    if(empty($invoice))
                    {
                      $sc = FALSE;
                      $this->error = "Invoice not exists for {$order_code} : {$item_code} at Line {$i}";
                    }

                    if($sc === TRUE)
                    {
                      if($invoice->qty < $return_qty)
                      {
                        $sc = FALSE;
                        $this->error = "Return quantity ({$return_qty}) exceed invoice quantity ({intval($invoice)}) at Line {$i}";
                      }
                    }

                    if($sc === TRUE)
                    {
                      $invoice->price = round(add_vat($invoice->price), 2);
                      $amount = round((get_price_after_discount($invoice->price, $invoice->discount) * $return_qty), 2);
                      $vat_amount = round(get_vat_amount($amount), 2);

                      $ds[$order_code] = (object) array(
                        'date_add' => $date,
                        'invoice' => $invoice->code,
                        'customer_code' => $invoice->customer_code,
                        'customer_name' => $invoice->customer_name,
                        'order_code' => $order_code,
                        'warehouse_code' => $zone->warehouse_code,
                        'zone_code' => $zone->code,
                        'must_accept' => empty($zone->user_id) ? 0 : 1,
                        'is_wms' => $is_wms,
                        'api' => (empty($api) OR $api == 'N') ? 0 : 1,
                        'remark' => $remark,
                        'details' => array((object)array(
                          'invoice_code' => $invoice->code,
                          'order_code' => $order_code,
                          'product_code' => $invoice->product_code,
                          'product_name' => $invoice->product_name,
                          'sold_qty' => round($invoice->qty, 2),
                          'return_qty' => $return_qty,
                          'price' => $invoice->price,
                          'discount_percent' => round($invoice->discount, 2),
                          'amount' => $amount,
                          'vat_amount' => $vat_amount
                        ))
                      );
                    }
                  }
                }
                else
                {
                  $invoice = $this->return_order_model->get_invoice_detail_by_order_item($order_code, $item_code);
                  $invoice->price = round(add_vat($invoice->price), 2);
                  $amount = round((get_price_after_discount($invoice->price, $invoice->discount) * $return_qty), 2);
                  $vat_amount = round(get_vat_amount($amount), 2);

                  $ds[$order_code]->details[] = (object)array(
                    'invoice_code' => $invoice->code,
                    'order_code' => $order_code,
                    'product_code' => $invoice->product_code,
                    'product_name' => $invoice->product_name,
                    'sold_qty' => round($invoice->qty, 2),
                    'return_qty' => $return_qty,
                    'price' => $invoice->price,
                    'discount_percent' => round($invoice->discount, 2),
                    'amount' => $amount,
                    'vat_amount' => $vat_amount
                  );
                }
              } //--- endif $sc === TRUE

              $i++;
            }
          }  //--- end if $i === 1
        } //--- foreach collection

        //--- เก็บข้อมูลครบแล้ว
        if($sc === TRUE && ! empty($ds))
        {
          $this->db->trans_begin();

          foreach($ds as $sm)
          {
            if($sc === FALSE)
            {
              break;
            }

            $code = $this->get_new_code($sm->date_add);

            if(empty($code))
            {
              $sc = FALSE;
              $this->error = "Failed to generate document number for {$sm->order_code}";
            }

            if($sc === TRUE)
            {
              $arr = array(
                'code' => $code,
                'bookcode' => $bookcode,
                'invoice' => $sm->invoice,
                'customer_code' => $sm->customer_code,
                'warehouse_code' => $sm->warehouse_code,
                'zone_code' => $sm->zone_code,
                'user' => $this->_user->uname,
                'date_add' => $sm->date_add,
                'remark' => $sm->remark,
        				'is_wms' => $sm->is_wms,
        				'api' => $sm->api,
                'status' => 1,
                'must_accept' => $sm->must_accept,
                'is_import' => 1,
                'import_id' => $uid
              );

              if( ! $this->return_order_model->add($arr))
              {
                $sc = FALSE;
                $this->error = "เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
              }

              if($sc === TRUE)
              {
                if( ! empty($sm->details))
                {
                  foreach($sm->details as $rs)
                  {
                    $arr = array(
                      'return_code' => $code,
                      'invoice_code' => $rs->invoice_code,
                      'order_code' => get_null($rs->order_code),
                      'product_code' => $rs->product_code,
                      'product_name' => $rs->product_name,
                      'sold_qty' => $rs->sold_qty,
                      'qty' => $rs->return_qty,
                      'receive_qty' => $sm->is_wms == 2 ? 0 : $rs->return_qty,
                      'price' => $rs->price,
                      'discount_percent' => $rs->discount_percent,
                      'amount' => $rs->amount,
                      'vat_amount' => $rs->vat_amount
                    );

                    if( ! $this->return_order_model->add_detail($arr))
                    {
                      $sc = FALSE;
                      $this->error = "บันทึกรายการไม่สำเร็จ @ {$rs->product_code} : {$rs->order_code}";
                    }
                  } //-- end foreach
                } // end if
              } // endif
            }
          } //--- end foreach

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }
        } //-- endif
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไฟล์มีรายการเกิน {$limit} บรรทัด";
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
	}


  public function get_template_file()
  {
    $path = $this->config->item('upload_path').'return/';
    $file_name = $path."import_return_template.xlsx";

    if(file_exists($file_name))
    {
      header('Content-Description: File Transfer');
      header('Content-Type:Application/octet-stream');
      header('Cache-Control: no-cache, must-revalidate');
      header('Expires: 0');
      header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
      header('Content-Length: '.filesize($file_name));
      header('Pragma: public');

      flush();
      readfile($file_name);
      die();
    }
    else
    {
      echo "File Not Found";
    }
  }


  public function add_details($code)
  {
    $sc = TRUE;

    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      $doc = $this->return_order_model->get($code);


      if( ! empty($doc))
      {
        if($doc->status == 0)
        {
          $vat = getConfig('SALE_VAT_RATE'); //--- 0.07

          //--- start transection
          $this->db->trans_begin();

          if($this->return_order_model->drop_details($code))
          {
            foreach($data as $rs)
            {
              if($sc === FALSE)
              {
                break;
              }

              if($rs->qty > 0)
              {
                $price = round($rs->price, 2);
                $discount = $rs->discount_percent;
                $disc_amount = $discount == 0 ? 0 : $rs->qty * ($price * ($discount * 0.01));
                $amount = ($rs->qty * $price) - $disc_amount;

                $receive_qty = $doc->is_wms == 0 ? $rs->qty : ($doc->api == 0 ? $rs->qty :($doc->is_wms == 1 && $this->wmsApi ? 0 : ($doc->is_wms == 2 && $this->sokoApi ? 0 : $rs->qty)));

                $arr = array(
                  'return_code' => $code,
                  'invoice_code' => $doc->invoice,
                  'order_code' => get_null($rs->order_code),
                  'product_code' => $rs->product_code,
                  'product_name' => $rs->product_name,
                  'sold_qty' => $rs->sold_qty,
                  'qty' => $rs->qty,
                  'receive_qty' => $receive_qty,
                  'price' => $price,
                  'discount_percent' => $discount,
                  'amount' => $amount,
                  'vat_amount' => get_vat_amount($amount)
                );

                if( ! $this->return_order_model->add_detail($arr))
                {
                  $sc = FALSE;
                  $this->error = "บันทึกรายการไม่สำเร็จ @ {$rs->product_code} : {$rs->order_code}";
                }
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Failed to delete previous details";
          }


          if($sc === TRUE)
          {
            if( ! $this->return_order_model->set_status($code, 1))
            {
              $sc = FALSE;
              $this->error = "Failed to change document status";
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
          $this->error = "สถานะเอกสารไม่ถูกต้อง";
        }
      }
      else
      {
        //--- empty document
        $sc = FALSE;
        $this->error = "ไม่พบเลขที่เอกสาร";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function delete_detail($id)
  {
    $rs = $this->return_order_model->delete_detail($id);
    echo $rs === TRUE ? 'success' : 'ลบรายการไม่สำเร็จ';
  }


  public function unsave($code)
  {
    $sc = TRUE;

    if($this->pm->can_edit)
    {
      $docNum = $this->return_order_model->get_sap_doc_num($code);

      if(empty($docNum))
      {
        $arr = array(
          'status' => 0,
          'is_approve' => 0,
          'approver' => NULL,
          'inv_code' => NULL
        );

        if( ! $this->return_order_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = 'ยกเลิกการบันทึกไม่สำเร็จ';
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "กรุณายกเลิกเอกสาร ลดหนี้เลขที่ {$docNum} ใน SAP ก่อนยกเลิกการบันทึก";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = 'คุณไม่มีสิทธิ์ในการยกเลิกการบันทึก';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function approve($code)
  {
    $this->load->model('inventory/movement_model');

		$sc = TRUE;

    $ex = 0;

    if($this->pm->can_approve)
    {
      $this->load->model('approve_logs_model');

			$doc = $this->return_order_model->get($code);

			if( ! empty($doc))
			{
				if($doc->status == 1 ) //--- status บันทึกแล้วเท่านั้น
				{
          $this->db->trans_begin();

          if( ! $this->return_order_model->approve($code))
          {
            $sc = FALSE;
            $this->error = "Approve Faiiled";
          }

					if($sc === TRUE)
					{
            $this->approve_logs_model->add($code, 1, $this->_user->uname);

            if($doc->must_accept == 1)
            {
              $this->return_order_model->set_status($code, 4);
            }
            else
            {
              $shipped_date = getConfig('ORDER_SOLD_DATE') === 'D' ? $doc->date_add : now();

              if(empty($doc->shipped_date))
              {
                $arr = array('shipped_date' => $shipped_date);

                $this->return_order_model->update($code, $arr);
              }

              $details = $this->return_order_model->get_details($doc->code);

              if($doc->is_wms == 0 OR $doc->api == 0 OR (($doc->is_wms == 1 && ! $this->wmsApi) OR ($doc->is_wms == 2 && ! $this->sokoApi)))
              {
                if( ! empty($details))
                {
                  //---- add movement
                  foreach($details as $rs)
                  {
                    if($sc === FALSE) { break; }

                    $arr = array(
                      'reference' => $doc->code,
                      'warehouse_code' => $doc->warehouse_code,
                      'zone_code' => $doc->zone_code,
                      'product_code' => $rs->product_code,
                      'move_in' => $rs->receive_qty,
                      'date_add' => db_date($doc->date_add, TRUE)
                    );

                    if($this->movement_model->add($arr) === FALSE)
                    {
                      $sc = FALSE;
                      $this->error = 'บันทึก movement ไม่สำเร็จ';
                    }
                  }

                  if($sc === TRUE)
                  {
                    $this->return_order_model->update($code, array('is_complete' => 1));
                  }
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "ไม่พบรายการรับคืน";
                }
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

          if($sc === TRUE)
          {
            if($doc->must_accept == 0)
            {
              if( ! empty($details))
              {
                if($doc->is_wms == 0 OR $doc->api == 0 OR (($doc->is_wms == 1 && ! $this->wmsApi) OR ($doc->is_wms == 2 && ! $this->sokoApi)))
                {
                  if( ! $this->do_export($code))
                  {
                    $ex = 1;
                    $this->error = "อนุมัติสำเร็จ แต่ส่งข้อมูลไป SAP ไม่สำเร็จ กรุณา refresh หน้าจอแล้วกดส่งข้อมูลอีกครั้ง";
                  }
                }

                if($doc->is_wms == 1 && $doc->api == 1 && $this->wmsApi)
                {
                  $this->wms = $this->load->database('wms', TRUE);
                  $this->load->library('wms_receive_api');

                  if($this->wms_receive_api->export_return_order($doc, $details))
                  {
                    $this->return_order_model->set_status($code, 3); //--- on wms process;
                  }
                  else
                  {
                    $ex = 1;
                    $this->error = "อนุมัติสำเร็จ แต่ส่งข้อมูลเข้า Pioneer ไม่สำเร็จ กรุณา refresh หน้าจอแล้วกดส่งข้อมูลอีกครั้ง";
                  }
                }

                if($doc->is_wms == 2 && $doc->api == 1 && $this->sokoApi)
                {
                  $this->wms = $this->load->database('wms', TRUE);
                  $this->load->library('soko_receive_api');

                  if($this->soko_receive_api->create_return_order($doc, $details))
                  {
                    $this->return_order_model->set_status($code, 3); //--- on wms process;
                  }
                  else
                  {
                    $ex = 1;
                    $this->error = "อนุมัติสำเร็จ แต่ส่งข้อมูลเข้า SOKOCHAN ไม่สำเร็จ กรุณา refresh หน้าจอแล้วกดส่งข้อมูลอีกครั้ง";
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการรับคืน";
              }
            }
          }
				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid status";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = 'เลขที่เอกสารไม่ถูกต้อง';
			}
    }
    else
    {
			$sc = FALSE;
			$this->error = 'คุณไม่มีสิทธิ์อนุมัติ';
    }

		$arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? ($ex == 1 ? $this->error : 'success') : $this->error,
      'ex' => $ex
    );

    echo json_encode($arr);
  }


  public function accept_confirm()
  {
    $sc = TRUE;

    $ex = 0;

    $this->load->model('inventory/movement_model');

    $code = $this->input->post('code');

    $remark = trim($this->input->post('accept_remark'));

    $doc = $this->return_order_model->get($code);

    if(!empty($doc))
    {
      $date_add = getConfig('ORDER_SOLD_DATE') === 'D' ? $doc->date_add : now();

      if($doc->status == 4 )
      {
        $status = $doc->is_wms == 0 ? 1 : ($doc->is_wms == 1 && $this->wmsApi ? 3 : ($doc->is_wms == 2 && $this->sokoApi ? 3 : 1));
        $ship_date = $doc->is_wms == 0 ? $date_add : ($doc->is_wms == 1 && $this->wmsApi ? NULL : ($doc->is_wms == 2 && $this->sokoApi ? NULL : $date_add));

        $arr = array(
          "status" => $status,
          "shipped_date" => empty($doc->shipped_date) ? $ship_date : $doc->shipped_date,
          "is_accept" => 1,
          "accept_by" => $this->_user->uname,
          "accept_on" => now(),
          "accept_remark" => $remark
        );

        $this->db->trans_begin();

        if( ! $this->return_order_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = "Update Acception Failed";
        }

        if($sc === TRUE)
        {
          $details = $this->return_order_model->get_details($doc->code);

          if( ! empty($details))
          {
            if($doc->is_wms == 0 OR $doc->api == 0 OR (($doc->is_wms == 1 && ! $this->wmsApi) OR ($doc->is_wms == 2 && ! $this->sokoApi)))
            {
              foreach($details as $rs)
              {
                if($sc === FALSE)
                {
                  break;
                }

                $arr = array(
                  'reference' => $doc->code,
                  'warehouse_code' => $doc->warehouse_code,
                  'zone_code' => $doc->zone_code,
                  'product_code' => $rs->product_code,
                  'move_in' => $rs->receive_qty,
                  'date_add' => db_date($doc->date_add, TRUE)
                );

                if($this->movement_model->add($arr) === FALSE)
                {
                  $sc = FALSE;
                  $this->error = 'บันทึก movement ไม่สำเร็จ';
                }
              }

              if($sc === TRUE)
              {
                $this->return_order_model->update($code, array('is_complete' => 1));
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบรายการรับคืน";
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

        if($sc === TRUE)
        {
          if($doc->is_wms == 0 OR $doc->api == 0 OR (($doc->is_wms == 1 && ! $this->wmsApi) OR ($doc->is_wms == 2 && ! $this->sokoApi)))
          {
            if( ! $this->do_export($code))
            {
              $sc = FALSE;
              $this->error = "อนุมัติสำเร็จ แต่ส่งข้อมูลไป SAP ไม่สำเร็จ กรุณา refresh หน้าจอแล้วกดส่งข้อมูลอีกครั้ง";
            }
          }

          if($doc->is_wms == 1 && $doc->api == 1 && $this->wmsApi)
          {
            $this->wms = $this->load->database('wms', TRUE);
            $this->load->library('wms_receive_api');

            if($this->wms_receive_api->export_return_order($doc, $details))
            {
              $this->return_order_model->set_status($code, 3); //--- on wms process;
            }
            else
            {
              $sc = FALSE;
              $this->error = "ยืนยันสำเร็จ แต่ส่งข้อมูลเข้า Pioneer ไม่สำเร็จ กรุณา refresh หน้าจอแล้วกดส่งข้อมูลอีกครั้ง";
            }
          }

          if($doc->is_wms == 2 && $doc->api == 1 && $this->sokoApi)
          {
            $this->wms = $this->load->database('wms', TRUE);
            $this->load->library('soko_receive_api');

            if($this->soko_receive_api->create_return_order($doc, $details))
            {
              $this->return_order_model->set_status($code, 3); //--- on wms process;
            }
            else
            {
              $sc = FALSE;
              $this->error = "ยืนยันสำเร็จ แต่ส่งข้อมูลเข้า SOKOCHAN ไม่สำเร็จ กรุณา refresh หน้าจอแล้วกดส่งข้อมูลอีกครั้ง";
            }
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid status";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'เลขที่เอกสารไม่ถูกต้อง';
    }


		echo $sc === TRUE ? 'success' : $this->error;
  }


  public function unapprove($code)
  {
		$sc = TRUE;

    if($this->pm->can_approve)
    {
      //--- check document in SAP
      $sap = $this->return_order_model->get_sap_return_order($code);

      if(empty($sap))
      {
        //-- delete temp data
        $temp = $this->return_order_model->get_middle_return_doc($code);

        if( ! empty($temp))
        {
          foreach($temp as $tmp)
          {
            if( ! $this->return_order_model->drop_middle_exits_data($tmp->DocEntry))
            {
              $sc = FALSE;
              $this->error = "Failed to delete SAP Temp";
            }
          }
        }

        if($sc === TRUE)
        {
          $this->load->model('inventory/movement_model');
          $this->load->model('approve_logs_model');

          $arr = array(
            'status' => 1,
            'is_approve' => 0,
            'is_accept' => NULL,
            'accept_on' => NULL,
            'accept_by' => NULL,
            'accept_remark' => NULL
          );

          $this->db->trans_begin();

          if( ! $this->return_order_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update document status";
          }

          if($sc === TRUE)
          {
            $this->approve_logs_model->add($code, 0, $this->_user->uname);

            if( ! $this->movement_model->drop_movement($code))
            {
              $sc = FALSE;
              $this->error = "Failed to delete movement";
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
				$this->error = "เอกสารเข้า SAP แล้ว กรุณายกเลิกเอกสารใน SAP ก่อน";
			}
    }
    else
    {
			$sc = FALSE;
      $this->error = 'คุณไม่มีสิทธิ์อนุมัติ';
    }

		echo $sc === TRUE ? 'success' : $this->error;
  }


  public function add_new()
  {
    $sokoZone = $this->zone_model->get(getConfig('SOKOJUNG_ZONE'));
    $wmsZone = $this->zone_model->get(getConfig('WMS_ZONE'));

    $ds = array(
      'soko_zone_code' => empty($sokoZone) ? NULL : $sokoZone->code,
      'soko_zone_name' => empty($sokoZone) ? NULL : $sokoZone->name,
      'wms_zone_code' => empty($wmsZone) ? NULL : $wmsZone->code,
      'wms_zone_name' => empty($wmsZone) ? NULL : $wmsZone->name
    );

    $this->load->view('inventory/return_order/return_order_add', $ds);
  }


  public function add()
  {
    $sc = TRUE;
    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      $date_add = db_date($data->date_add, TRUE);
      $shipped_date = empty($data->shipped_date) ? NULL : db_date($data->shipped_date, TRUE);
      $wmsZone = getConfig('WMS_ZONE');
      $sokoZone = getConfig('SOKOJUNG_ZONE');
      $is_wms = $data->is_wms;

      if($is_wms == 1 && $this->wmsApi && $data->zone_code != $wmsZone)
      {
        $sc = FALSE;
        $this->error = "เอกสารต้องรับเข้าที่โซน {$wmsZone}";
      }

      if($is_wms == 2 && $this->sokoApi && $data->zone_code != $sokoZone)
      {
        $sc = FALSE;
        $this->error = "เอกสารต้องรับเข้าที่โซน {$sokoZone}";
      }

      if($is_wms == 0 && ($data->zone_code == $wmsZone OR $data->zone_code == $sokoZone))
      {
        $sc = FALSE;
        $this->error = "เอกสารต้องรับเข้าที่โซนของ WARRIX";
      }

			$zone = $this->zone_model->get($data->zone_code);

      if(empty($zone))
      {
        $sc = FALSE;
        $this->error = "รหัสโซนไม่ถูกต้อง";
      }


      if($sc === TRUE)
      {
        $code = $this->get_new_code($date_add);

        $must_accept = empty($zone->user_id) ? 0 : 1;

        $arr = array(
          'code' => $code,
          'bookcode' => getConfig('BOOK_CODE_RETURN_ORDER'),
          'invoice' => $data->invoice,
          'customer_code' => $data->customer_code,
          'warehouse_code' => $zone->warehouse_code,
          'zone_code' => $zone->code,
          'user' => $this->_user->uname,
          'date_add' => $date_add,
          'shipped_date' => $shipped_date,
          'remark' => get_null(trim($data->remark)),
  				'is_wms' => $is_wms,
  				'api' => $data->api,
          'must_accept' => $must_accept
        );

        if( ! $this->return_order_model->add($arr))
        {
          $sc = FALSE;
          $this->error = "เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error("required");
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
    $doc = $this->return_order_model->get($code);
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    $details = $this->return_order_model->get_details($code);

    $wmsZone = $this->zone_model->get(getConfig('WMS_ZONE'));
    $sokoZone = $this->zone_model->get(getConfig('SOKOJUNG_ZONE'));

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
						$dt->order_code = $rs->order_code;
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

				$dt = new stdClass();
				$dt->id = $rs->id;
				$dt->invoice_code = $doc->invoice;
				$dt->order_code = $rs->order_code;
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


    $ds = array(
      'doc' => $doc,
      'details' => $detail,
      'wms_zone_code' => empty($wmsZone) ? NULL : $wmsZone->code,
      'wms_zone_name' => empty($wmsZone) ? NULL : $wmsZone->name,
      'soko_zone_code' => empty($sokoZone) ? NULL : $sokoZone->code,
      'soko_zone_name' => empty($sokoZone) ? NULL : $sokoZone->name
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
    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      $code = $data->code;
      $date_add = db_date($data->date_add, TRUE);
      $shipped_date = empty($data->shipped_date) ? NULL : db_date($data->shipped_date, TRUE);
      $wmsZone = getConfig('WMS_ZONE');
      $sokoZone = getConfig('SOKOJUNG_ZONE');
      $is_wms = $data->is_wms;

      if($is_wms == 1 && $this->wmsApi && $data->zone_code != $wmsZone)
      {
        $sc = FALSE;
        $this->error = "เอกสารต้องรับเข้าที่โซน {$wmsZone}";
      }

      if($is_wms == 2 && $this->sokoApi && $data->zone_code != $sokoZone)
      {
        $sc = FALSE;
        $this->error = "เอกสารต้องรับเข้าที่โซน {$sokoZone}";
      }

      if($is_wms == 0 && ($data->zone_code == $wmsZone OR $data->zone_code == $sokoZone))
      {
        $sc = FALSE;
        $this->error = "เอกสารต้องรับเข้าที่โซนของ WARRIX";
      }

      if($sc === TRUE)
      {
        $zone = $this->zone_model->get($data->zone_code);

        if(empty($zone))
        {
          $sc = FALSE;
          $this->error = "รหัสโซนไม่ถูกต้อง";
        }
      }

      if($sc === TRUE)
      {
        $must_accept = empty($zone->user_id) ? 0 : 1;

        $arr = array(
          'date_add' => $date_add,
          'shipped_date' => $shipped_date,
          'invoice' => $data->invoice,
          'customer_code' => $data->customer_code,
          'warehouse_code' => $zone->warehouse_code,
          'zone_code' => $zone->code,
          'is_wms' => $is_wms,
          'api' => $data->api,
          'remark' => get_null(trim($data->remark)),
          'must_accept' => $must_accept,
          'update_user' => $this->_user->uname
        );

        if( ! $this->return_order_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = 'ปรับปรุงข้อมูลไม่สำเร็จ';
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function update_shipped_date()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $shipped_date = $this->input->post('shipped_date');

    if( ! empty($code) && ! empty($shipped_date))
    {
      $doc = $this->return_order_model->get($code);

      if( ! empty($doc))
      {
        $arr = array(
          'shipped_date' => empty($shipped_date) ? NULL : db_date($shipped_date, TRUE)
        );

        if( ! $this->return_order_model->update($code, $arr))
        {
          $sc = FALSE;
          set_error('update');
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


  public function view_detail($code)
  {
    $this->load->model('approve_logs_model');
    $doc = $this->return_order_model->get($code);
    $details = $this->return_order_model->get_details($code);
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
					$row->order_code = $rs->order_code;
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


  //--- auto complete
  public function get_sap_invoice_code($customer_code = NULL)
  {
    $txt = trim($_REQUEST['term']);
		$sc = array();

		$this->ms
    ->select('DocNum, CardCode, CardName, NumAtCard');

    if( ! empty($customer_code))
    {
      $this->ms
      ->where('CardCode', $customer_code);
    }

    $this->ms->where('CANCELED', 'N');

		if($txt != '*')
		{
			$this->ms->like('DocNum', $txt);
		}

		$this->ms->order_by('DocNum', 'DESC')->limit(50);
		$rs = $this->ms->get('OINV');

		if($rs->num_rows() > 0)
		{
			foreach($rs->result() as $row)
			{
				$sc[] = array(
          'code' => $row->DocNum,
          'customer_code' => $row->CardCode,
          'customer_name' => $row->CardName,
          'label' => $row->DocNum. ' | '.$row->CardCode.' | '.$row->CardName.' | '.$row->NumAtCard
        );
			}
		}
		else
		{
			$sc[] = "not found";
		}

		echo json_encode($sc);
  }


	//--- print received
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


  public function print_wms_return($code)
  {
    $this->load->library('xprinter');
    $doc = $this->return_order_model->get($code);
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    $details = $this->return_order_model->get_count_item_details($code); //--- get only count item

    $ds = array(
      'order' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_wms_return', $ds);
  }


  public function cancle_return($code)
  {
    $sc = TRUE;
    $reason = trim($this->input->post('reason'));
    $force_cancel = $this->input->post('force_cancel') == 1 ? TRUE : FALSE;

    if($this->pm->can_delete)
    {
			$doc = $this->return_order_model->get($code);

			if( ! empty($doc))
			{
				if($doc->status != 2)
				{
          if($doc->status == 3 && $doc->is_wms == 1 && ! $this->_SuperAdmin)
          {
            $sc = FALSE;
            $this->error = "เอกสารอยู่ระหว่างการรับเข้าไม่อนุญาติให้ยกเลิก";
          }

					//--- check sap
					$sap = $this->return_order_model->get_sap_doc_num($code);

					if(empty($sap))
					{
						//--- cancle middle
						if($sc === TRUE)
						{
							if($this->drop_middle_exits_data($code))
							{
								$this->db->trans_begin();

                //--- set details to cancle
                if( ! $this->return_order_model->update_details($code, array('is_cancle' => 1)))
                {
                  $sc = FALSE;
                  $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
                }

                if($sc === TRUE)
                {
                  $arr = array(
                    'inv_code' => NULL,
                    'status' => 2,
                    'cancle_reason' => $reason,
                    'cancle_user' => $this->_user->uname,
                    'cancle_date' => now()
                  );

                  if( ! $this->return_order_model->update($code, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "ยกเลิกเอกสารไม่สำเร็จ";
                  }
                }

                if($sc === TRUE)
                {
                  if($doc->status == 3 && ! $force_cancel)
                  {
                    if($doc->is_wms == 2 && $doc->api == 1 && $this->sokoApi)
                    {
                      $this->wms = $this->load->database('wms', TRUE);
                      $this->load->library('soko_receive_api');

                      if( ! $this->soko_receive_api->cancel_receive_po($doc))
                      {
                        $sc = FALSE;
                        $this->error = "SOKOCHAN Error : ".$this->soko_receive_api->error;
                      }
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
								$this->error = "Cannot Delete Middle Temp data";
							}
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "กรุณายกเลิกเอกสารใน SAP ก่อนดำเนินการ";
					}
				}
				else
				{
					$sc = FALSE;
          $this->error = "เอกสารถูกยกเลิกไปแล้ว";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Document Number";
			}
    }
    else
    {
      $sc = FALSE;
      $this->error = 'คุณไม่มีสิทธิ์ในการยกเลิกเอกสาร';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function roll_back_expired()
  {
    $sc = TRUE;

    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->return_order_model->get($code);

      if( ! empty($doc))
      {
        if($doc->is_expire == 1)
        {
          $arr = array(
            'is_expire' => 0
          );

          if( ! $this->return_order_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "ย้อนสถานะเอกสารไม่สำเร็จ";
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid document number";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


	public function drop_middle_exits_data($code)
  {
    $sc = TRUE;
    $middle = $this->return_order_model->get_middle_return_doc($code);

    if(!empty($middle))
    {
      foreach($middle as $rs)
      {
        if( ! $this->return_order_model->drop_middle_exits_data($rs->DocEntry))
				{
					$sc = FALSE;
				}
      }
    }

    return $sc;
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


	public function send_to_wms()
	{
		$sc = TRUE;

    if($this->wmsApi)
    {
      if($this->input->post('code'))
  		{
  			$code = trim($this->input->post('code'));

  			$doc = $this->return_order_model->get($code);

  			if( ! empty($doc))
  			{
  				if($doc->status != 2 && $doc->status != 0)
  				{
  					$details = $this->return_order_model->get_details($doc->code);

  					if( ! empty($details))
  					{
  						$this->wms = $this->load->database('wms', TRUE);
  						$this->load->library('wms_receive_api');
  						$rs = $this->wms_receive_api->export_return_order($doc, $details);

  						if($rs)
  						{
  							$this->return_order_model->set_status($doc->code, 3);
  						}
  						else
  						{
  							$sc = FALSE;
  							$this->error = $this->wms_receive_api->error;
  						}
  					}
  					else
  					{
  						$sc = FALSE;
  						$this->error = "ไม่พบรายการคืนสินค้า";
  					}
  				}
  				else
  				{
  					$sc = FALSE;
  					$this->error = "สถานะเอกสารไม่ถุกต้อง";
  				}
  			}
  			else
  			{
  				$sc = FALSE;
  				$this->error = "รหัสเอกสารไม่ถูกต้อง";
  			}
  		}
  		else
  		{
  			$sc = FALSE;
  			$this->error = "Missing required parameter: code";
  		}
    }
    else
    {
      $sc = FALSE;
      $this->error = "API is not enabled";
    }



		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function send_to_soko()
	{
		$sc = TRUE;

    if($this->sokoApi)
    {
      if($this->input->post('code'))
      {
        $code = trim($this->input->post('code'));

        $doc = $this->return_order_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status != 2 && $doc->status != 0)
          {
            $details = $this->return_order_model->get_details($doc->code);

            if( ! empty($details))
            {
              $this->wms = $this->load->database('wms', TRUE);
              $this->load->library('soko_receive_api');

              if( $this->soko_receive_api->create_return_order($doc, $details))
              {
                $this->return_order_model->set_status($doc->code, 3);
              }
              else
              {
                $this->error = "ส่งข้อมูลไม่สำเร็จ : {$this->soko_receive_api->error}";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "ไม่พบรายการคืนสินค้า";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "สถานะเอกสารไม่ถุกต้อง";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "รหัสเอกสารไม่ถูกต้อง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Missing required parameter: code";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "API is not enabled";
    }

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function send_to_fulfillment()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->return_order_model->get($code);

      if( ! empty($doc))
      {
        if($doc->api == 1)
        {
          if($doc->status != 2 && $doc->status != 0)
          {
            if($doc->is_wms == 1 && $this->wmsApi)
            {
              $details = $this->return_order_model->get_details($doc->code);

              if( ! empty($details))
              {
                $this->wms = $this->load->database('wms', TRUE);

                $this->load->library('wms_receive_api');

                if( ! $this->wms_receive_api->export_return_order($doc, $details))
                {
                  $sc = FALSE;
                  $this->error = $this->wms_receive_api->error;
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการคืนสินค้า";
              }
            }

            if($doc->is_wms == 2 && $this->sokoApi)
            {
              $details = $this->return_order_model->get_details($doc->code);

              if( ! empty($details))
              {
                $this->wms = $this->load->database('wms', TRUE);
                $this->load->library('soko_receive_api');

                if( ! $this->soko_receive_api->create_return_order($doc, $details))
                {
                  $this->error = "ส่งข้อมูลไม่สำเร็จ : {$this->soko_receive_api->error}";
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการคืนสินค้า";
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "สถานะเอกสารไม่ถุกต้อง";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "This document was set to not interface";
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
      'sm_approve',
			'sm_warehouse',
      'sm_zone',
      'sm_must_accept',
			'sm_api',
      'sm_wms_export',
      'sm_pos_api',
      'sm_sap'
    );
    clear_filter($filter);
  }
} //--- end class
?>
