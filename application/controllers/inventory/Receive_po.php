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
  public $wmsApi = FALSE;
  public $sokoApi = FALSE;
  public $required_remark = 1;
  public $is_mobile = FALSE;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/receive_po';
    $this->load->model('inventory/receive_po_model');
    $this->load->model('stock/stock_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/products_model');
    $this->load->library('user_agent');

		$this->isAPI = is_true(getConfig('WMS_API'));
    $this->wmsApi = is_true(getConfig('WMS_API'));
    $this->sokoApi = is_true(getConfig('SOKOJUNG_API'));
    $this->is_mobile = $this->agent->is_mobile();
  }


  public function index()
  {
    $this->load->helper('channels');
    $this->load->helper('warehouse');

    $filter = array(
      'code' => get_filter('code', 'receive_code', ''),
      'invoice' => get_filter('invoice', 'receive_invoice', ''),
      'po' => get_filter('po', 'receive_po', ''),
      'vendor' => get_filter('vendor', 'receive_vendor', ''),
      'user' => get_filter('user', 'receive_user', ''),
      'from_date' => get_filter('from_date', 'receive_from_date', ''),
      'to_date' => get_filter('to_date', 'receive_to_date', ''),
      'warehouse' => get_filter('warehouse', 'receive_warehouse', 'all'),
      'status' => get_filter('status', 'receive_status', ($this->is_mobile ? '3' : 'all')),
			'is_wms' => get_filter('is_wms', 'receive_is_wms', ($this->is_mobile ? '0' : 'all')),
      'wms_export' => get_filter('wms_export', 'receive_wms_export', 'all'),
      'sap' => get_filter('sap', 'receive_sap', 'all'),
      'must_accept' => get_filter('must_accept', 'receive_must_accept', 'all')
    );


    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_rows();

      $segment  = 4; //-- url segment
      $rows     = $this->receive_po_model->count_rows($filter);
      //--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
      $init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
      $document = $this->receive_po_model->get_list($filter, $perpage, $this->uri->segment($segment));

      if(!empty($document))
      {
        foreach($document as $rs)
        {
          $rs->qty = $this->receive_po_model->get_sum_qty($rs->code);
        }
      }

      $filter['document'] = $document;

      $this->pagination->initialize($init);

      if($this->is_mobile)
      {
        $this->load->view('inventory/receive_po/mobile/receive_po_list_mobile', $filter);
      }
      else
      {
        $this->load->view('inventory/receive_po/receive_po_list', $filter);
      }
    }
  }


  public function process($code)
  {
    $this->load->model('masters/zone_model');
    $this->load->helper('warehouse');
		$this->load->helper('currency');

    $doc = $this->receive_po_model->get($code);

    if( ! empty($doc))
    {
      if($doc->is_wms == 0 && $doc->status == 3)
      {
        $details = $this->receive_po_model->get_details($code);

        if( ! empty($details))
        {
          foreach($details as $rs)
          {
            $rs->barcode = $this->products_model->get_barcode($rs->product_code);
          }
        }

        $ds = array(
          'doc' => $doc,
          'details' => $details,
          'allow_over_po' => getConfig('ALLOW_RECEIVE_OVER_PO'),
          'zone' => empty($doc->zone_code) ? NULL : $this->zone_model->get($doc->zone_code)
        );

        $this->load->view('inventory/receive_po/receive_po_process', $ds);
      }
      else
      {
        redirect($this->home . '/view_detail/' . $code);
      }
    }
    else
    {
      $this->page_error();
    }
  }


  public function process_mobile($code)
  {
    $this->load->model('masters/zone_model');
    $this->load->helper('warehouse');
		$this->load->helper('currency');

    $doc = $this->receive_po_model->get($code);

    if( ! empty($doc))
    {
      if($doc->is_wms == 0 && $doc->status == 3)
      {
        $totalQty = 0;
        $totalReceive = 0;

        $uncomplete = $this->receive_po_model->get_in_complete_list($code);

        if(!empty($uncomplete))
        {
          foreach($uncomplete as $rs)
          {
            $rs->barcode = $this->products_model->get_barcode($rs->product_code);
            $totalQty += $rs->qty;
            $totalReceive += $rs->receive_qty;
          }
        }

        $complete = $this->receive_po_model->get_complete_list($code);

        if( ! empty($complete))
        {
          foreach($complete as $rs)
          {
            $rs->barcode = $this->products_model->get_barcode($rs->product_code);
            $totalQty += $rs->qty;
            $totalReceive += $rs->receive_qty;
          }
        }

        $ds = array(
          'title' => $doc->code . "  [PO{$doc->po_code}]"  . "<br/>".$doc->vendor_name,
          'doc' => $doc,
          'uncomplete' => $uncomplete,
          'complete' => $complete,
          'allQty' => $totalQty,
          'totalReceive' => $totalReceive,
          'finished' => empty($uncomplete) ? TRUE : FALSE,
          'allow_over_po' => getConfig('ALLOW_RECEIVE_OVER_PO'),
          'zone' => empty($doc->zone_code) ? NULL : $this->zone_model->get($doc->zone_code)
        );

        $this->load->view('inventory/receive_po/mobile/receive_po_process_mobile', $ds);
      }
      else
      {
        redirect($this->home . '/view_detail/' . $code);
      }
    }
    else
    {
      $this->page_error();
    }
  }


  public function save_receive_rows()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->rows))
    {
      $doc = $this->receive_po_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 3)
        {
          $this->db->trans_begin();

          foreach($ds->rows as $rs)
          {
            if($sc === FALSE)
            {
              break;
            }

            $detail = $this->receive_po_model->get_detail($rs->id);

            if( ! empty($detail))
            {
              $newQty = $rs->qty + $detail->receive_qty;

              if($detail->qty < $newQty)
              {
                $sc = FALSE;
                $this->error = "{$detail->product_code} : จำนวนที่รับ เกินจำนวนที่ส่ง กรุณาตรวจสอบ <br/> จำนวนส่ง : {$detail->qty}<br/>รับแล้ว : {$detail->receive_qty}<br/>บันทึกเพิ่ม : {$rs->qty}";
              }

              if($sc === TRUE)
              {
                $af = $detail->before_backlogs - $newQty;  //--- ยอดค้างรับหลังรับแล้ว
                $amount = round($newQty * $detail->price, 6);

                $arr = array(
                  'receive_qty' => $newQty,
                  'amount' => $amount,
                  'after_backlogs' => $af,
                  'valid' => $detail->qty <= $newQty ? 1 : 0
                );

                if( ! $this->receive_po_model->update_detail($rs->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update receive qty at {$detail->product_code}";
                }
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "No item row id {$rs->id} for {$rs->product_code}";
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


  //---- to finish receive in mobile mode
  public function save_and_close()
  {
    $sc = TRUE;
    $ex = 1;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code))
    {
      $doc = $this->receive_po_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 3)
        {
          if( ! empty($ds->rows))
          {
            $this->db->trans_begin();

            foreach($ds->rows as $rs)
            {
              if($sc === FALSE)
              {
                break;
              }

              $detail = $this->receive_po_model->get_detail($rs->id);

              if( ! empty($detail))
              {
                $newQty = $rs->qty + $detail->receive_qty;

                if($detail->qty < $newQty)
                {
                  $sc = FALSE;
                  $this->error = "{$detail->product_code} : จำนวนที่รับ เกินจำนวนที่ส่ง กรุณาตรวจสอบ <br/> จำนวนส่ง : {$detail->qty}<br/>รับแล้ว : {$detail->receive_qty}<br/>บันทึกเพิ่ม : {$rs->qty}";
                }

                if($sc === TRUE)
                {
                  $af = $detail->before_backlogs - $newQty;  //--- ยอดค้างรับหลังรับแล้ว
                  $amount = round($newQty * $detail->price, 6);

                  $arr = array(
                    'receive_qty' => $newQty,
                    'amount' => $amount,
                    'after_backlogs' => $af,
                    'valid' => $detail->qty <= $newQty ? 1 : 0
                  );

                  if( ! $this->receive_po_model->update_detail($rs->id, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to update receive qty at {$detail->product_code}";
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "No item row id {$rs->id} for {$rs->product_code}";
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

          if($sc === TRUE)
          {
            $this->db->trans_begin();

            $details = $this->receive_po_model->get_details($ds->code);

            if( ! empty($details))
            {
              $this->load->model('inventory/movement_model');

              $movement_date = getConfig('ORDER_SOLD_DATE') == 'D' ? db_date($ds->doc_date, TRUE) : now();

              foreach($details as $rs)
              {
                if($sc === FALSE)
                {
                  break;
                }
                //--- insert Movement in
                $arr = array(
                  'reference' => $ds->code,
                  'warehouse_code' => $doc->warehouse_code,
                  'zone_code' => $doc->zone_code,
                  'product_code' => $rs->product_code,
                  'move_in' => $rs->receive_qty,
                  'move_out' => 0,
                  'date_add' => $movement_date
                );

                if( ! $this->movement_model->add($arr))
                {
                  $sc = FALSE;
                  $this->error = "Insert Movement Failed";
                }
              }
            }

            if($sc === TRUE)
            {
              $posting_date = empty($ds->posting_date) ? NULL : db_date($ds->posting_date, TRUE);

              $arr = array(
                'status' => 1,
                'shipped_date' => empty($doc->shipped_date) ? now() : $doc->shipped_date,
                'update_user' => $this->_user->uname
              );

              if( ! $this->receive_po_model->update($doc->code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to close document";
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
              $this->load->library('export');

              if(! $this->export->export_receive($doc->code))
              {
                $sc = FALSE;
                $ex = 0;
                $this->error = "บันทึกสำเร็จ แต่ส่งข้อมูลเข้า SAP ไม่สำเร็จ <br/> ".trim($this->export->error);
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
      'status' => $sc === TRUE ? 'success' : ($ex == 0 ? 'warning' : 'failed'),
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }

  //---- to finish receive in desktop mode
  public function finish_receive()
  {
    $this->load->model('inventory/movement_model');
    $sc = TRUE;
    $ex = 1;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $doc = $this->receive_po_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 3)
        {
          $shipped_date = getConfig('ORDER_SOLD_DATE') == 'D' ? db_date($doc->date_add, TRUE) : (empty($doc->shipped_date) ? $doc->shipped_date : now());

          if($doc->is_wms == 0)
          {
            $this->db->trans_begin();

            if( ! empty($ds->rows))
            {
              foreach($ds->rows as $rs)
              {
                if($sc === FALSE)
                {
                  break;
                }

                $row = $this->receive_po_model->get_detail($rs->id);

                if( ! empty($row))
                {
                  $amount = $row->price * $rs->receive_qty;
                  $after_backlogs = $row->before_backlogs - $rs->receive_qty;
                  $valid = $row->qty <= $rs->receive_qty ? 1 : 0;

                  $arr = array(
                    'receive_qty' => $rs->receive_qty,
                    'amount' => $amount,
                    'valid' => $valid
                  );

                  if( ! $this->receive_po_model->update_detail($rs->id, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to update item row";
                  }

                  if($sc === TRUE)
                  {
                    //--- insert Movement in
                    $arr = array(
                      'reference' => $doc->code,
                      'warehouse_code' => $doc->warehouse_code,
                      'zone_code' => $doc->zone_code,
                      'product_code' => $row->product_code,
                      'move_in' => $rs->receive_qty,
                      'move_out' => 0,
                      'date_add' => $shipped_date
                    );

                    if( ! $this->movement_model->add($arr))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to create movemnt";;
                    }
                  }
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "Invalid row item or row item has been deleted";
                }
              }
            }

            if($sc === TRUE)
            {
              $arr = array(
                'status' => 1,
                'shipped_date' => $shipped_date,
                'update_user' => $this->_user->uname
              );

              if( ! $this->receive_po_model->update($doc->code, $arr))
              {
                $sc = FALSE;
                $this->error = "Faiiled to update document status";
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
              $this->load->library('export');
              if(! $this->export->export_receive($doc->code))
              {
                $sc = FALSE;
                $ex = 0;
                $this->error = "บันทึกสำเร็จ แต่ส่งข้อมูลเข้า SAP ไม่สำเร็จ <br/> ".trim($this->export->error);
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "เอกสารนี้ต้องรับเข้าที่ SOKO";
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
        $this->error = "Invalid document number";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : ($ex == 0 ? 'warning' : 'failed'),
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


	public function import_data()
	{
		$this->load->library('excel');
		ini_set('max_execution_time', 1200);

    $sc = TRUE;
    $import = 0;
    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
  	$path = $this->config->item('upload_path').'receive_po/';
    $file	= 'uploadFile';
		$config = array(   // initial config for upload class
			"allowed_types" => "xlsx",
			"upload_path" => $path,
			"file_name"	=> "import_receive",
			"max_size" => 5120,
			"overwrite" => TRUE
		);

		$this->load->library("upload", $config);

		if(! $this->upload->do_upload($file))
    {
			echo $this->upload->display_errors();
		}
    else
    {
      $info = $this->upload->data();
      /// read file
			$excel = PHPExcel_IOFactory::load($info['full_path']);
			//get only the Cell Collection
      $cs	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

      $i = 1;
      $count = count($cs);
      $limit = intval(getConfig('IMPORT_ROWS_LIMIT')) + 1;
      $allow = is_true(getConfig('ALLOW_RECEIVE_OVER_PO'));
			$ro = getConfig('RECEIVE_OVER_PO');
	    $rate = ($ro * 0.01);

      if( $count <= $limit )
      {
				$po_code = $cs[1]['C'];

				if(! empty($po_code))
				{
					$vendor = $this->receive_po_model->get_vender_by_po($po_code);
					$cur = $this->receive_po_model->get_po_currency($po_code);

					if(! empty($vendor))
					{
						$ds = array(
							"po_code" => $po_code,
							"invoice_code" => $cs[2]['C'],
							"vendor_code" => $vendor->CardCode,
							"vendor_name" => $vendor->CardName,
							"DocCur" => empty($cur) ? "THB" : $cur->DocCur,
							"DocRate" => empty($cur) ? 1.00 : $cur->DocRate
						);

						$line = array();
						$no = 1;
						$totalBacklog = 0;
						$totalQty = 0;
						$totalReceive = 0;

						foreach($cs as $rs)
						{
							if($i > 7 && !empty($rs['C']))
							{
								$detail = $this->receive_po_model->get_po_detail($po_code, $rs['C']);

								if(!empty($detail))
								{
									$dif = $detail->Quantity - $detail->OpenQty;

                  $arr = array(
                    'no' => $no,
                    'uid' => $detail->DocEntry.$detail->LineNum,
                    'baseCode' => $po_code,
                    'baseEntry' => $detail->DocEntry,
                    'baseLine' => $detail->LineNum,
                    'pdCode' => $detail->ItemCode,
                    'pdName' => $detail->Dscription,
                    'price' => round($detail->price, 4),
                    'priceLabel' => number($detail->price, 4),
                    'currency' => $detail->Currency,
                    'Rate' => empty($detail->Rate) ? 1 : round($detail->Rate, 2),
                    'vatGroup' => $detail->VatGroup,
                    'vatRate' => $detail->VatPrcnt,
                    'qty' => floatval($rs['E']),
                    'limit' => ($detail->Quantity + ($detail->Quantity * $rate)) - $dif,
                    'backLogsLabel' => number($detail->OpenQty),
                    'backlogs' => round($detail->OpenQty, 2),
                    'isOpen' => $detail->LineStatus === 'O' ? TRUE : FALSE
                  );

									array_push($line, $arr);
									$no++;
								}
							}

							$i++;
						} //--- endforeach

						$ds['details'] = $line;
					}
					else
					{
						$sc = FALSE;
						$this->error = "Invalid PO No";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid PO No.";
				}
			}
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}


  public function get_sample_file()
  {
    $path = $this->config->item('upload_path').'receive_po/';
    $file_name = $path."import_receive_template.xlsx";

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
      $doc->zone_name = empty($zone) ? "" : $zone->name;
      $doc->warehouse_name = empty($zone) ? "" : $zone->warehouse_name;
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
    $ex = 1;
    $isSoko = FALSE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $this->load->model('masters/products_model');
      $this->load->model('masters/zone_model');
      $this->load->model('masters/warehouse_model');
      $this->load->model('inventory/movement_model');

      $doc = $this->receive_po_model->get($ds->code);

      $movement_date = getConfig('ORDER_SOLD_DATE') == 'D' ? db_date($ds->doc_date, TRUE) : now();

      if( ! empty($ds->rows))
      {
        $isSoko = ($ds->is_wms == 2 && $this->sokoApi) ? TRUE : FALSE;
        $sokoZone = getConfig('SOKOJUNG_ZONE');

        $zone = $this->zone_model->get($ds->zone_code);

        if(empty($zone))
        {
          $sc = FALSE;
          $this->error = "รหัสโซนไม่ถูกต้อง";
        }

        if($sc === TRUE && ($ds->is_wms == 2 && $zone->code != $sokoZone))
        {
          $sc = FALSE;
          $this->error = "เอกสารต้องรับเข้าที่โซน {$sokoZone}";
        }

        if($sc === TRUE && ($ds->is_wms == 0 && $zone->code == $sokoZone))
        {
          $sc = FALSE;
          $this->error = "เอกสารต้องรับเข้าที่โซนของ WARRIX";
        }

        $date_add = db_date($ds->doc_date, TRUE);
        $due_date = empty($ds->due_date) ? $date_add : db_date($ds->due_date, FALSE);
        $posting_date = empty($ds->posting_date) ? NULL : db_date($ds->posting_date, TRUE);
        $remark = get_null(trim($ds->remark));
    		$is_wms = $ds->is_wms;

        if($sc === TRUE)
        {
          $approver = get_null($ds->approver);
          $must_accept = empty($zone->user_id) ? 0 : 1;

          $arr = array(
            'date_add' => $date_add,
            'due_date' => $due_date,
            'shipped_date' => $posting_date,
            'vendor_code' => $ds->vendor_code,
            'vendor_name' => $ds->vendor_name,
            'po_code' => $ds->po_code,
            'invoice_code' => $ds->invoice,
            'zone_code' => $zone->code,
            'warehouse_code' => $zone->warehouse_code,
            'update_user' => $this->_user->uname,
            'approver' => $ds->approver,
            'currency' => empty($ds->DocCur) ? "THB" : $ds->DocCur,
            'rate' => empty($ds->DocRate) ? 1 : $ds->DocRate,
            'is_wms' => $is_wms,
            'must_accept' => $must_accept
          );

          $this->db->trans_begin();

          if( ! $this->receive_po_model->update($doc->code, $arr))
          {
            $sc = FALSE;
            $this->error = 'Update Document Fail';
          }

          if($sc === TRUE)
          {
            //--- ลบรายการเก่าก่อนเพิ่มรายการใหม่
            if( ! $this->receive_po_model->drop_details($doc->code))
            {
              $sc = FALSE;
              $this->error = "Failed to delete prevoius item rows";
            }

            if($sc === TRUE)
            {
              $details = [];

              foreach($ds->rows as $rs)
              {
                if($sc === FALSE) { break; }

                if($rs->qty != 0)
                {
                  $pd = $this->products_model->get($rs->product_code);

                  if( ! empty($pd))
                  {
                    $bf = $rs->backlogs; ///--- ยอดค้ารับ ก่อนรับ
                    $af = ($bf - $rs->qty) > 0 ? ($bf - $rs->qty) : 0;  //--- ยอดค้างรับหลังรับแล้ว
                    $amount = round($rs->qty * $rs->price, 6);

                    $de = array(
                      'receive_code' => $ds->code,
                      'baseEntry' => $rs->baseEntry,
                      'baseLine' => $rs->baseLine,
                      'style_code' => $pd->style_code,
                      'product_code' => $pd->code,
                      'product_name' => $pd->name,
                      'price' => $rs->price,
                      'qty' => $rs->qty,
                      'receive_qty' => (! $isSoko && $ds->save_type == 1 ) ? $rs->qty : 0,
                      'amount' => $amount,
                      'before_backlogs' => $bf,
                      'after_backlogs' => $af,
                      'currency' => empty($ds->DocCur) ? "THB" : $ds->DocCur,
                      'rate' => empty($ds->DocRate) ? 1 : $ds->DocRate,
                      'vatGroup' => $rs->vatGroup,
                      'vatRate' => $rs->vatRate
                    );

                    if($must_accept == 0 && $isSoko && $ds->save_type != 0)
                    {
                      $details[] = (object) array(
                        'receive_code' => $ds->code,
                        'style_code' => $pd->style_code,
                        'product_code' => $pd->code,
                        'product_name' => $pd->name,
                        'unit_code' => $pd->unit_code,
                        'price' => $rs->price,
                        'qty' => $rs->qty,
                        'amount' => $amount,
                        'before_backlogs' => $bf,
                        'after_backlogs' => $af
                      );
                    }

                    if( ! $this->receive_po_model->add_detail($de))
                    {
                      $sc = FALSE;
                      $this->error = 'Add Receive Row Fail';
                      break;
                    }

                    if($sc === TRUE)
                    {
                      if($must_accept == 0 && ! $isSoko && $ds->save_type == 1)
                      {
                        //--- insert Movement in
                        $arr = array(
                          'reference' => $ds->code,
                          'warehouse_code' => $zone->warehouse_code,
                          'zone_code' => $zone->code,
                          'product_code' => $rs->product_code,
                          'move_in' => $rs->qty,
                          'move_out' => 0,
                          'date_add' => $movement_date
                        );

                        if( ! $this->movement_model->add($arr))
                        {
                          $sc = FALSE;
                          $this->error = "Insert Movement Failed";
                        }
                      }
                    }
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = 'ไม่พบรหัสสินค้า : '.$item.' ในระบบ';
                  }
                } //--- end if qty != 0
              } //-- end foreach

              if($sc === TRUE)
              {
                $arr = array(
                  'status' => $ds->save_type == 0 ? 0 : ($must_accept == 1 ? 4 : (($isSoko OR $ds->save_type == 3) ? 3 : 1))
                );

                if( ! $this->receive_po_model->update($ds->code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Update Document Status Failed";
                }
              }
            }//--- end if $sc === TRUE
          } //--- $sc == TRUE

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }
        } //-- $sc == TRUE

        if($sc === TRUE && $must_accept == 0 && $ds->save_type != 0)
        {
          if($isSoko)
          {
            $this->wms = $this->load->database('wms', TRUE);
            $this->load->library('soko_receive_api');
            $doc->vendor_code = $ds->vendor_code;
            $doc->vendor_name = $ds->vendor_name;
            $doc->is_wms = $ds->is_wms;

            if( ! $this->soko_receive_api->create_receive_po($doc, $ds->po_code, $ds->invoice, $details))
            {
              $sc = FALSE;
              $ex = 0;
              $this->error = "บันทึกเอกสารสำเร็จ แต่ส่งข้อมูลไป Soko jung ไม่สำเร็จ <br/> ".$this->soko_receive_api->error;
            }
          }
          else
          {
            if($ds->save_type == 1)
            {
              $this->load->library('export');
              if(! $this->export->export_receive($doc->code))
              {
                $sc = FALSE;
                $ex = 0;
                $this->error = "บันทึกสำเร็จ แต่ส่งข้อมูลเข้า SAP ไม่สำเร็จ <br/> ".trim($this->export->error);
              }
            }
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
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : ($ex == 0 ? 'warning' : 'failed'),
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function accept_confirm()
  {
    $sc = TRUE;
    $ex = 1;

    $this->load->model('inventory/movement_model');

    $code = $this->input->post('code');
    $save_type = $this->input->post('save_type') == 3 ? 3 : 1;
    $remark = $this->input->post('accept_remark');

    $doc = $this->receive_po_model->get($code);

    $movement_date = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : now();

    $isSoko = ($doc->is_wms == 2 && $this->sokoApi) ? TRUE : FALSE;

    if( ! empty($doc))
    {
      if($doc->status == 4)
      {
        $details = $this->receive_po_model->get_details($code);

        $this->db->trans_begin();

        if( ! empty($details))
        {
          if($save_type == 1 && ! $isSoko)
          {
            //--- update movement
            foreach($details as $rs)
            {
              if($sc === FALSE) { break; }

              if($rs->qty > 0)
              {
                $af = $rs->before_backlogs - $rs->qty;
                $amount = $rs->qty * $rs->price;

                $arr = array(
                  'receive_qty' => $rs->qty,
                  'after_backlogs' => $af,
                  'amount' => round($amount, 4),
                  'valid' => 1
                );

                if( ! $this->receive_po_model->update_detail($rs->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update receive qty";
                }

                if($sc === TRUE)
                {
                  //--- insert Movement in
                  $arr = array(
                    'reference' => $code,
                    'warehouse_code' => $doc->warehouse_code,
                    'zone_code' => $doc->zone_code,
                    'product_code' => $rs->product_code,
                    'move_in' => $rs->qty,
                    'move_out' => 0,
                    'date_add' => $movement_date
                  );

                  if( ! $this->movement_model->add($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Insert Movement Failed";
                  }
                }
              } //-- receive_qty > 0
            } //--- foreach
          }

          if($save_type == 3)
          {
            foreach($details as $rs)
            {
              if($sc === FALSE) { break; }

              if($rs->receive_qty > 0)
              {
                $arr = array(
                  'receive_qty' => 0,
                  'valid' => 0
                );

                if( ! $this->receive_po_model->update_detail($rs->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to reset receive qty";
                }
              }
            }
          }
        } //-- details

        if($sc === TRUE)
        {
          $status = $isSoko ? 3 : ($save_type == 3 ? 3 : 1);

          $arr = array(
            'status' => $status,
            'is_accept' => 1,
            'accept_by' => $this->_user->uname,
            'accept_on' => now(),
            'accept_remark' => $remark
          );

          if($status == 1)
          {
            $arr['shipped_date'] = empty($doc->shipped_date) ? now() : $doc->shipped_date;
          }

          if( ! $this->receive_po_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "Update Document Status Failed";
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
        $this->error = "Invalid Document Status";
      }

      if($sc === TRUE)
      {
        if($isSoko)
        {
          $this->wms = $this->load->database('wms', TRUE);
          $this->load->library('soko_receive_api');
          $doc->vendor_code = $vendor_code;
          $doc->vendor_name = $vendor_name;

          if( ! $this->soko_receive_api->create_receive_po($doc, $po_code, $invoice, $details))
          {
            $sc = FALSE;
            $ex = 0;
            $this->error = "บันทึกเอกสารสำเร็จ แต่ส่งข้อมูลไป Soko jung ไม่สำเร็จ <br/> ".$this->soko_receive_api->error;
          }
        }
        else
        {
          if($save_type == 1)
          {
            $this->load->library('export');

            if(! $this->export->export_receive($code))
            {
              $sc = FALSE;
              $ex = 0;
              $this->error = "บันทึกสำเร็จ แต่ส่งข้อมูลเข้า SAP ไม่สำเร็จ <br/> ".trim($this->export->error);
            }
          }
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid Document Number";
    }

    $arr = array(
    'status' => $sc === TRUE ? 'success' : ($ex == 0 ? 'warning' : 'failed'),
    'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function pull_back()
	{
		$sc = TRUE;
		$code = trim($this->input->post('code'));

		if($this->_SuperAdmin)
		{
			$doc = $this->receive_po_model->get($code);

			if( ! empty($doc))
			{
        if($doc->status == 1)
        {
          $sap = $this->receive_po_model->get_sap_receive_doc($code);

          if( ! empty($sap))
          {
            $sc = FALSE;
            $this->error = "เอกสารถูกนำเข้า SAP แล้ว หากต้องการเปลี่ยนแปลงกรุณายกเลิกเอกสารใน SAP ก่อน";
          }
          else
          {
            $middle = $this->receive_po_model->get_middle_receive_po($code);

            if(!empty($middle))
            {
              foreach($middle as $rows)
              {
                if( ! $this->receive_po_model->drop_sap_received($rows->DocEntry))
                {
                  $sc = FALSE;
                  $this->error = "ลบรายการที่ค้างใน temp ไม่สำเร็จ";
                }
              }
            }
          }
        }

        if($sc === TRUE)
        {
          $this->db->trans_begin();

          if($doc->status == 2)
          {
            $arr = array(
              'is_cancle' => 0
            );

            if( ! $this->receive_po_model->update_details($code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to roll back transections";
            }
          }

          if($sc === TRUE)
          {
            $this->load->model('inventory/movement_model');

            if( ! $this->movement_model->drop_movement($code))
            {
              $sc = FALSE;
              $this->error = "Failed to remove movement";
            }
          }


          if($sc === TRUE)
          {
            $arr = array(
              'status' => 0,
              'inv_code' => NULL
            );

            if( ! $this->receive_po_model->update($code, $arr))
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
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Document number";
			}
		}
		else
		{
			$sc = FALSE;
			set_error('permission');
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


	public function send_to_wms($code)
	{
		$sc = TRUE;

    if($this->wmsApi)
    {
      $doc = $this->receive_po_model->get($code);

  		if(!empty($doc))
  		{
  			if($doc->status == 3)
  			{
          if($doc->is_wms == 1)
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
    						$thiis->error = "ส่งข้อมูลไป Pioneer ไม่สำเร็จ <br/>{$this->wms_receive_api->error}";
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
            $this->error = "Invalid Fulfillment API";
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
    }
    else
    {
      $sc = FALSE;
      $this->error = "API not enabled";
    }

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function send_to_soko($code)
	{
		$sc = TRUE;

    if($this->sokoApi)
    {
      $doc = $this->receive_po_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 3)
        {
          if($doc->is_wms == 2)
          {
            $details = $this->receive_po_model->get_details($code);

            if( ! empty($details))
            {
              $this->wms = $this->load->database('wms', TRUE);
              $this->load->library('soko_receive_api');

              $ex = $this->soko_receive_api->create_receive_po($doc, $doc->po_code, $doc->invoice_code, $details);

              if( ! $ex)
              {
                $sc = FALSE;
                $this->error = "ส่งข้อมูลไป soko chan ไม่สำเร็จ <br/>{$this->soko_receive_api->error}";
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
            $this->error = "Invalid Fulfillment API";
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
    }
    else
    {
      $sc = FALSE;
      $this->error = "Api is not enabled";
    }

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function do_export($code)
  {
    $sc = TRUE;

    if( ! $this->export_receive($code))
    {
      $sc = FALSE;
    }
    else
    {
      $arr = array(
        'inv_code' => NULL
      );

      $this->receive_po_model->update($code, $arr);
    }

    echo $sc === TRUE ? 'success' : $this->error;
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
      $force_cancel = $this->input->post('force_cancel') == 1 ? TRUE : FALSE;

      $doc = $this->receive_po_model->get($code);

      if( ! empty($doc))
      {
        //---- check doc status is open or close
        //---- if closed user cannot cancle document
        $sap = $this->receive_po_model->get_sap_receive_doc($code);

        if(empty($sap))
        {
          $middle = $this->receive_po_model->get_middle_receive_po($code);

          if(! empty($middle))
          {
            foreach($middle as $rs)
            {
              $this->receive_po_model->drop_sap_received($rs->DocEntry);
            }
          }

          if($sc === TRUE && $doc->status == 3 && ! $force_cancel)
          {
            if($doc->is_wms == 2 && ! empty($doc->soko_code) && $this->sokoApi)
            {
              $this->wms = $this->load->database('wms', TRUE);
              $this->load->library('soko_receive_api');

              $ex = $this->soko_receive_api->cancel_receive_po($doc);

              if( ! $ex)
              {
                $sc = FALSE;
                $this->error = "ยกเลิกเอกสารที่ Soko jung ไม่สำเร็จ กรุณาติดต่อเจ้าหน้าที่ <br/>{$this->soko_receive_api->error}";
              }
            }
          }


          if($sc === TRUE)
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
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = 'กรุณายกเลิกใบรับสินค้าบน SAP ก่อนทำการยกเลิก';
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
    $sc = TRUE;
    $ds = array();

    $po_code = $this->input->get('po_code');

    $po = $this->receive_po_model->get_po($po_code);

    if( ! empty($po))
    {
      $ro = getConfig('RECEIVE_OVER_PO');

      $rate = ($ro * 0.01);

      $details = $this->receive_po_model->get_po_details($po_code);

      if( ! empty($details))
      {
        $no = 1;

        foreach($details as $rs)
        {
  				if($rs->OpenQty > 0)
  				{
            $dif = $rs->Quantity - $rs->OpenQty;
            $onOrder = $this->receive_po_model->get_on_order_qty($rs->ItemCode, $po_code, $rs->DocEntry, $rs->LineNum);

            $qty = $rs->OpenQty - $onOrder;

            $arr = array(
              'no' => $no,
              'uid' => $rs->DocEntry.$rs->LineNum,
              'baseCode' => $po_code,
              'baseEntry' => $rs->DocEntry,
              'baseLine' => $rs->LineNum,
              'pdCode' => $rs->ItemCode,
              'pdName' => $rs->Dscription,
              'price' => round($rs->price, 4),
              'price_label' => number($rs->price, 4),
              'currency' => $rs->Currency,
              'Rate' => empty($rs->Rate) ? 1 : $rs->Rate,
              'vatGroup' => $rs->VatGroup,
              'vatRate' => $rs->VatPrcnt,
              'qty_label' => number($qty),
              'qty' => $qty,
              'onOrder' => $onOrder,
              'limit' => ($rs->Quantity + ($rs->Quantity * $rate)) - $dif,
              'backlog_label' => number($rs->OpenQty),
              'backlog' => round($rs->OpenQty, 2),
              'isOpen' => $rs->LineStatus === 'O' ? TRUE : FALSE
            );

            array_push($ds, $arr);
            $no++;
  				}
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ใบสั่งซื้อไม่ถูกต้อง หรือ ใบสั่งซื้อถูกปิดไปแล้ว";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบใบสั่งซื้อ";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'DocNum' => $sc === TRUE ? $po->DocNum : NULL,
      'DocCur' => $sc === TRUE ? $po->DocCur : NULL,
      'DocRate' => $sc === TRUE ? $po->DocRate : NULL,
      'CardCode' => $sc === TRUE ? $po->CardCode : NULL,
      'CardName' => $sc === TRUE ? $po->CardName : NULL,
      'DiscPrcnt' => $sc === TRUE ? $po->DiscPrcnt : NULL,
      'details' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function edit($code)
  {
    $this->load->model('masters/zone_model');
    $this->load->helper('warehouse');
		$this->load->helper('currency');

    $doc = $this->receive_po_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 0 OR ($doc->is_wms == 0 && $doc->status == 3))
      {
        $details = $this->receive_po_model->get_details($code);

        if( ! empty($details))
        {
          $ro = getConfig('RECEIVE_OVER_PO');
    	    $rate = ($ro * 0.01);

          foreach($details as $rs)
          {
            //-- get Quantity, Openqty
            $row = $this->receive_po_model->get_po_row($rs->baseEntry, $rs->baseLine);

            if( ! empty($row))
            {
              $diff = $row->Quantity - $row->OpenQty;
              $rs->backlogs = $row->OpenQty;
              $rs->limit = ($row->Quantity + ($row->Quantity * $rate)) - $diff;
              $rs->line_status = $row->LineStatus;
            }
            else
            {
              $rs->backlogs = 0;
              $rs->limit = 0;
              $rs->line_status = 'D';
            }
          }
        }


        $ds = array(
          'doc' => $doc,
          'details' => $details,
          'allow_over_po' => getConfig('ALLOW_RECEIVE_OVER_PO'),
          'zone' => empty($doc->zone_code) ? NULL : $this->zone_model->get($doc->zone_code)
        );

        $this->load->view('inventory/receive_po/receive_po_edit', $ds);
      }
      else
      {
        redirect($this->home . '/view_detail/' . $code);
      }
    }
    else
    {
      $this->page_error();
    }
  }


	public function get_po_currency()
	{
		$po_code = $this->input->get('po_code');

		$rs = $this->receive_po_model->get_po_currency($po_code);

		if(!empty($rs))
		{
			echo json_encode($rs);
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
    $sc = TRUE;
    $date_add = db_date($this->input->post('date_add'), TRUE);
    $due_date = empty($this->input->post('due_date')) ? $date_add : db_date($this->input->post('due_date'), FALSE);
    $posting_date = empty($this->input->post('posting_date')) ? $due_date : db_date($this->input->post('posting_date', TRUE));
    $is_wms = $this->input->post('is_wms');
    $remark = trim($this->input->post('remark'));

    $code = $this->get_new_code($date_add);

    if( ! empty($code))
    {
      $arr = array(
        'code' => $code,
        'bookcode' => getConfig('BOOK_CODE_RECEIVE_PO'),
        'vendor_code' => NULL,
        'vendor_name' => NULL,
        'po_code' => NULL,
        'invoice_code' => NULL,
        'remark' => get_null($remark),
        'date_add' => $date_add,
        'due_date' => $due_date,
        'shipped_date' => $posting_date,
        'user' => $this->_user->uname,
				'is_wms' => $is_wms
      );

      if( ! $this->receive_po_model->add($arr))
      {
        $sc = FALSE;
        $this->error = "Create Document Failed";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Cannot generate document number at this time";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  public function update_header()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $date_add = db_date($this->input->post('date_add'), TRUE);
    $due_date = empty($this->input->post('due_date')) ? $date_add : db_date($this->input->post('due_date'), FALSE);
    $posting_date = empty($this->input->post('posting_date')) ? $due_date : db_date($this->input->post('posting_date', TRUE));
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
            'date_add' => $date_add,
            'due_date' => $due_date,
            'shipped_date' => $posting_date,
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


  public function get_wms_zone($is_wms, $zone_code)
  {
    if($is_wms == 1 && $this->wmsApi)
    {
      return getConfig('WMS_ZONE');
    }

    if($is_wms == 2 && $this->sokoApi)
    {
      return getConfig('SOKOJUNG_ZONE');
    }

    return $zone_code;
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
      'receive_warehouse',
      'receive_sap',
			'receive_is_wms',
      'receive_wms_export',
      'receive_user',
      'receive_must_accept'
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
