<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive_material extends PS_Controller
{
  public $menu_code = 'ICPURM';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RECEIVE';
	public $title = 'รับวัตถุดิบจากการซื้อ';
  public $filter;
  public $error;
  public $is_mobile = FALSE;
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/receive_material';
    $this->load->model('inventory/receive_material_model');
    $this->load->model('stock/stock_model');
    $this->load->model('masters/products_model');
    $this->load->helper('currency');
    $this->load->helper('warehouse');
    $this->load->helper('zone');
    $this->load->helper('receive_material');
    $this->load->library('user_agent');
    $this->is_mobile = $this->agent->is_mobile();
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'receive_code', ''),
      'invoice' => get_filter('invoice', 'receive_invoice', ''),
      'po_code' => get_filter('po_code', 'receive_po', ''),
      'vendor' => get_filter('vendor', 'receive_vendor', ''),
      'user' => get_filter('user', 'receive_user', 'all'),
      'from_date' => get_filter('from_date', 'receive_from_date', ''),
      'to_date' => get_filter('to_date', 'receive_to_date', ''),
      'warehouse' => get_filter('warehouse', 'receive_warehouse', 'all'),
      'status' => get_filter('status', 'receive_status', 'all'),
      'is_export' => get_filter('is_export', 'receive_export', 'all')
    );


    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->receive_material_model->count_rows($filter);
      $filter['data'] = $this->receive_material_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);

      $this->load->view('inventory/receive_material/receive_material_list', $filter);
    }
  }


  public function view_sticker()
  {
    $ds = [];
    $count = 0;
    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
    $path = $this->config->item('upload_path').'grpo-sticker/';
    $file	= 'uploadFile';
    $config = array(   // initial config for upload class
      "allowed_types" => "xlsx",
      "upload_path" => $path,
      "file_name"	=> "import-file-".date('YmdHis'),
      "max_size" => 5120,
      "overwrite" => TRUE
    );

    $this->load->library("upload", $config);
    $this->load->library('excel');

    if( ! $this->upload->do_upload($file))
    {
      echo $this->upload->display_errors();
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
        $i = 1;

        foreach($collection as $rs)
        {
          if($i == 1)
          {
            $i++;
          }
          else
          {
            $ds[] = (object)[
              'sku' => trim($rs['A']),
              'barcode' => trim($rs['B']),
              'po' => trim($rs['C']),
              'lot' => trim($rs['D']),
              'pcsNo' => trim($rs['E']),
              'grade' => trim($rs['F']),
              'qty' => trim($rs['G']),
              'unit' => 'kgs.'
            ];

            $count++;
          }
        }
      }

      $arr = array(
        'ds' => $ds,
        'total' => $count
      );

      $this->load->helper('print');
      $this->load->view('inventory/receive_material/sticker', $arr);
    }
  }


  public function add_new()
  {
    $ds = ['code' => $this->get_new_code(date('Y-m-d'))];
    $this->load->view('inventory/receive_material/receive_material_add', $ds);
  }


  public function add()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));
    $code = NULL;

    if($this->pm->can_add)
    {
      if( ! empty($ds))
      {
        $date_add = db_date($ds->date_add, TRUE);
        $posting_date = empty($ds->posting_date) ? NULL : db_date($ds->posting_date, TRUE);
        $code = $this->get_new_code($date_add);

        $arr = array(
          'code' => $code,
          'bookcode' => getConfig('BOOK_CODE_RECEIVE_MATERIAL'),
          'vendor_code' => $ds->vendor_code,
          'vendor_name' => $ds->vendor_name,
          'user' => $this->_user->uname,
          'date_add' => $date_add,
          'shipped_date' => $posting_date,
          'po_code' => get_null($ds->po_code),
          'invoice_code' => get_null($ds->invoice_code),
          'Currency' => $ds->currency,
          'Rate' => $ds->rate,
          'warehouse_code' => $ds->warehouse_code,
          'zone_code' => get_null($ds->zone_code),
          'remark' => get_null($ds->remark)
        );

        if( ! $this->receive_material_model->add($arr))
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
      'code' => $code
    );

    echo json_encode($arr);
  }


  public function edit($code)
  {
    if($this->pm->can_add OR $this->pm->can_edit)
    {
      $doc = $this->receive_material_model->get($code);

      if( ! empty($doc))
      {
        $details = $this->receive_material_model->get_details($code);

        if( ! empty($details))
        {
          $ro = getConfig('RECEIVE_OVER_PO');
    	    $rate = ($ro * 0.01);

          foreach($details as $rs)
          {
            //-- get Quantity, Openqty
            $row = $this->receive_material_model->get_po_row($rs->baseEntry, $rs->baseLine);

            if( ! empty($row))
            {
              $diff = $row->Quantity - $row->OpenQty;
              $rs->backlogs = $row->OpenQty;
              $rs->limit = ($row->Quantity + ($row->Quantity * $rate)) - $diff;
              $rs->line_status = $row->LineStatus;
              $rs->batchRows = $this->receive_material_model->get_batch_item_by_id($rs->id);
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
          'warehouse_code' => empty($doc->warehouse_code) ? getConfig('DEFAULT_WAREHOUSE') : $doc->warehouse_code
        );

        $this->load->view('inventory/receive_material/receive_material_edit', $ds);
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


  public function save()
  {
    $sc = TRUE;
    $ex = 0;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code))
    {
      $doc = $this->receive_material_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 'P')
        {
          if($ds->save_type == 'C')
          {
            if(empty($ds->warehouse_code))
            {
              $sc = FALSE;
              $this->error = "Warehouse is required";
            }

            if($sc === TRUE && empty($ds->zone_code))
            {
              $sc = FALSE;
              $this->error = "Zone is required";
            }

            if($sc === TRUE)
            {
              $this->load->model('masters/zone_model');

              if( ! $this->zone_model->is_exists_zone_warehouse($ds->zone_code, $ds->warehouse_code))
              {
                $sc = FALSE;
                $this->error = "Zone and warehouse missmatch";
              }
            }
          }

          if($sc === TRUE)
          {
            $totalQty = 0;
            $docTotal = 0;
            $vatSum = 0;

            $date_add = db_date($ds->date_add, TRUE);
            $posting_date = empty($ds->posting_date) ? NULL : db_date($ds->posting_date, TRUE);

            $arr = array(
              'vendor_code' => $ds->vendor_code,
              'vendor_name' => $ds->vendor_name,
              'po_code' => $ds->po_code,
              'Currency' => $ds->currency,
              'Rate' => $ds->rate,
              'invoice_code' => get_null($ds->invoice_code),
              'warehouse_code' => get_null($ds->warehouse_code),
              'zone_code' => get_null($ds->zone_code),
              'update_user' => $this->_user->uname,
              'date_upd' => now(),
              'date_add' => $date_add,
              'shipped_date' => $ds->save_type == 'C' ? (empty($posting_date) ? now() : $posting_date) : $posting_date,
              'remark' => get_null($ds->remark),
              'status' => $ds->save_type
            );


            $this->db->trans_begin();

            if( ! $this->receive_material_model->update($ds->code, $arr))
            {
              $sc = FALSE;
              set_error('update');
            }

            if($sc === TRUE)
            {
              //--- drop receive rows
              if( ! $this->receive_material_model->delete_batch_details($ds->code))
              {
                $sc = FALSE;
                $this->error = "Failed to delete prevoius batch rows";
              }

              if($sc === TRUE)
              {
                if( ! $this->receive_material_model->delete_details($ds->code))
                {
                  $sc = FALSE;
                  $this->error = "Failed to delete prevoius item rows";
                }
              }
            }
          }

          if($sc === TRUE)
          {
            if( ! empty($ds->items))
            {
              $lineNum = 1;

              foreach($ds->items as $rs)
              {
                if($sc === FALSE) { break; }

                $lineTotal = $rs->qty * $rs->Price;
                $vatAmount = get_vat_amount($lineTotal, $rs->vatRate);

                $arr = array(
                  'receive_code' => $doc->code,
                  'lineNum' => $lineNum,
                  'baseCode' => $rs->baseCode,
                  'baseEntry' => $rs->baseEntry,
                  'baseLine' => $rs->baseLine,
                  'ItemCode' => $rs->product_code,
                  'ItemName' => $rs->product_name,
                  'Qty' => $rs->qty,
                  'PriceBefDi' => $rs->PriceBefDi,
                  'PriceAfVAT' => $rs->PriceAfVAT,
                  'Price' => $rs->Price,
                  'LineTotal' => $lineTotal,
                  'BinCode' => get_null($ds->zone_code),
                  'WhsCode' => get_null($ds->warehouse_code),
                  'UomCode' => $rs->UomCode,
                  'UomCode2' => $rs->UomCode2,
                  'UomEntry' => $rs->UomEntry,
                  'UomEntry2' => $rs->UomEntry2,
                  'unitMsr' => $rs->unitMsr,
                  'unitMsr2' => $rs->unitMsr2,
                  'NumPerMsr' => $rs->NumPerMsr,
                  'NumPerMsr2' => $rs->NumPerMsr2,
                  'VatGroup' => $rs->vatGroup,
                  'VatRate' => $rs->vatRate,
                  'VatAmount' => $vatAmount,
                  'Currency' => $rs->currency,
                  'Rate' => $rs->rate,
                  'LineStatus' => $ds->save_type == 'C' ? 'C' : 'O',
                  'valid' => $ds->save_type == 'C' ? 1 : 0,
                  'hasBatch' => $rs->hasBatch == 'Y' ? 1 : 0
                );

                $id = $this->receive_material_model->add_detail($arr);
                $lineNum++;

                $totalQty += $rs->qty;
                $docTotal += $lineTotal;
                $vatSum += $vatAmount;

                if($id)
                {
                  if($ds->save_type == 'C')
                  {
                    $this->load->model('inventory/movement_model');

                    $movement = array(
                      'reference' => $ds->code,
                      'warehouse_code' => $ds->warehouse_code,
                      'zone_code' => $ds->zone_code,
                      'product_code' => $rs->product_code,
                      'move_in' => $rs->qty,
                      'move_out' => 0,
                      'date_add' => empty($posting_date) ? now() : $posting_date
                    );

                    if( ! $this->movement_model->add($movement))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to insert stock movement";
                    }
                  }

                  if($sc === TRUE)
                  {
                    if( ! empty($rs->batchRows))
                    {
                      foreach($rs->batchRows as $ro)
                      {
                        if($sc === FALSE) { break; }

                        $br = array(
                          'receive_code' => $doc->code,
                          'receive_detail_id' => $id,
                          'baseEntry' => $rs->baseEntry,
                          'baseLine' => $rs->baseLine,
                          'ItemCode' => $rs->product_code,
                          'ItemName' => $rs->product_name,
                          'BatchNum' => $ro->batchNo,
                          'BatchAttr1' => get_null($ro->batchAttr1),
                          'BatchAttr2' => get_null($ro->batchAttr2),
                          'Qty' => $ro->batchQty,
                          'WhsCode' => get_null($ds->warehouse_code)
                        );

                        if( ! $this->receive_material_model->add_batch_row($br))
                        {
                          $sc = FALSE;
                          $this->error = "Failed to insert batch row";
                        }
                      }
                    }
                  }
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "Failed to insert item row";
                }
              }
            }
          }

          if($sc === TRUE)
          {
            $arr = array(
              'DocTotal' => $docTotal,
              'VatSum' => $vatSum,
              'TotalQty' => $totalQty
            );

            $this->receive_material_model->update($doc->code, $arr);
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }

          if($sc === TRUE && $ds->save_type == 'C')
          {
            //---- send to sab via sap api
            if(is_true(getConfig('SAP_API')))
            {
              $this->load->library('sap_api');

              if( ! $this->sap_api->exportGRPO($doc->code))
              {
                $ex = 1;
                $this->error = $this->sap_api->error;
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
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'ex' => $ex
    );

    echo json_encode($arr);
  }


  public function rollback()
  {
    $sc = TRUE;
    $this->load->model('inventory/movement_model');
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->receive_material_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'O' OR ($doc->status == 'C' && $this->_SuperAdmin))
        {
          if($this->receive_material_model->is_exists_in_sap($code))
          {
            $sc = FALSE;
            $this->error = "เอกสารนี้เข้า SAP แล้ว หากต้องการแก้ไข กรุณายกเลิกเอกสารบน SAP ก่อน";
          }

          if($sc === TRUE)
          {
            $this->db->trans_begin();

            //--- drop movement
            if( ! $this->movement_model->drop_movement($doc->code))
            {
              $sc = FALSE;
              $this->error = "Failed to delete stock movement";
            }

            //---- rollback line status
            if($sc === TRUE)
            {
              if( ! $this->receive_material_model->update_details($doc->code, ['LineStatus' => 'O']))
              {
                $sc = FALSE;
                $this->error = "Failed to update items line status";
              }
            }

            //--- rellback document status
            if($sc === TRUE)
            {
              $arr = array(
                'status' => 'P',
                'is_export' => 'N',
                'inv_code' => NULL,
                'date_upd' => now(),
                'update_user' => $this->_user->uname
              );

              if( ! $this->receive_material_model->update($doc->code, $arr))
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


  public function view_detail($code)
  {
    $doc = $this->receive_material_model->get($code);

    if( ! empty($doc))
    {
      $details = $this->receive_material_model->get_details($code);

      if( ! empty($details))
      {
        foreach($details as $rs)
        {
          $rs->batchRows = $this->receive_material_model->get_batch_item_by_id($rs->id);
        }
      }

      $ds = array(
        'doc' => $doc,
        'details' => $details
      );

      $this->load->view('inventory/receive_material/receive_material_view_detail', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function cancel()
  {
    $sc = TRUE;
    $reason = $this->input->post('reason');
    $code = $this->input->post('code');

    if($this->pm->can_delete)
    {
      if( ! empty($code))
      {
        $doc = $this->receive_material_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status != 'D')
          {
            if($doc->status == 'C')
            {
              //-- check sap docment
              if($this->receive_material_model->is_exists_in_sap($code))
              {
                $sc = FALSE;
                $this->error = "เอกสารเข้า SAP แล้ว หากต้องการยกเลิก กรุณายกเลิกเอกสารบน SAP ก่อน";
              }
            }

            if($sc === TRUE)
            {
              $this->load->model('inventory/movement_model');

              $this->db->trans_begin();

              // Remove movement
              if( ! $this->movement_model->drop_movement($code))
              {
                $sc = FALSE;
                $this->error = "Failed to remove stock movement";
              }

              // Set Line STatus
              if($sc === TRUE)
              {
                $arr = array(
                  'LineStatus' => 'D'
                );

                if( ! $this->receive_material_model->update_details($code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update line status";
                }
              }

              // Set document status
              if($sc === TRUE)
              {
                $arr = array(
                  'status' => 'D',
                  'inv_code' => NULL,
                  'cancle_reason' => $reason,
                  'cancle_user' => $this->_user->uname,
                  'date_upd' => now()
                );

                if( ! $this->receive_material_model->update($code, $arr))
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


  public function export()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->receive_material_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status === 'C')
        {
          $this->load->library('sap_api');

          if( ! $this->sap_api->exportGRPO($code))
          {
            $sc = FALSE;
            $this->error = "Export Error: {$this->sap_api->error}";
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


  public function print($code)
  {
    $this->load->library('printer');

    $doc = $this->receive_material_model->get($code);

    if( ! empty($doc))
    {
      $details = $this->receive_material_model->get_details($code);

      $ds = array(
        'doc' => $doc,
        'details' => $details
      );

      $this->load->view('print/print_material_received', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function get_vendor()
  {
    $ds = [];
    $txt = trim($_REQUEST['term']);

    $qr  = "SELECT CardCode, CardName FROM OCRD WHERE CardType = 'S' ";

    if($txt != '*')
    {
      $qr .= "AND (CardCode LIKE N'%{$txt}%' OR CardName LIKE N'%{$txt}%') ";
    }

    $qr .= "ORDER BY 1 OFFSET 0 ROWS FETCH NEXT 50 ROWS ONLY";

    $vendor = $this->ms->query($qr);

    if($vendor->num_rows() > 0)
    {
      foreach($vendor->result() as $rs)
      {
        $ds[] = $rs->CardCode.' | '.$rs->CardName;
      }
    }

    echo json_encode($ds);
  }


  public function get_po_code($vendor = NULL)
  {
    $ds = [];
    $txt = trim($_REQUEST['term']);

    //---- receive product if over due date or not
    $receive_due = getConfig('RECEIVE_OVER_DUE'); //--- 1 = receive , 0 = not receive

    $this->ms->select('DocNum, CardCode, CardName, DocCur, DocRate')->where('DocStatus', 'O');

    if( ! empty($vendor))
    {
      $this->ms->where('CardCode', $vendor);
    }

    if($txt != '*')
    {
      $this->ms->group_start();
      $this->ms->like('DocNum', $txt);
      $this->ms->or_like('NumAtCard', $txt);
      $this->ms->group_end();
    }

    if($receive_due == 0)
    {
      //--- not receive
      $days = getConfig('PO_VALID_DAYS');
      $date = date('Y-m-d',strtotime("-{$days} day")); //--- ย้อนไป $days วัน
      $this->ms->where('DocDueDate >=', sap_date($date));
    }

    $po = $this->ms->order_by('DocDate', 'DESC')->limit(100)->get('OPOR');

    if($po->num_rows() > 0)
    {
      foreach($po->result() as $rs)
      {
        $ds[] = array(
          'label' => $rs->DocNum.' | '.$rs->CardCode.' | '.$rs->CardName,
          'currency' => $rs->DocCur,
          'rate' => $rs->DocRate
        );
      }
    }
		else
		{
			$ds[] = "not found";
		}

    echo json_encode($ds);
  }


  public function get_po_details()
  {
    $sc = TRUE;
    $ds = [];
    $po_code = $this->input->post('po_code');

    if( ! empty($po_code))
    {
      $po = $this->receive_material_model->get_po($po_code);

      if( ! empty($po))
      {
        $ro = round(floatval(getConfig('RECEIVE_OVER_PO')), 2);
        $rate = $ro * 0.01;

        $details = $this->receive_material_model->get_po_details($po_code);

        if( ! empty($details))
        {
          $no = 1;
          foreach($details as $rs)
          {
            if($rs->OpenQty > 0)
    				{
              $dif = $rs->Quantity - $rs->OpenQty;
              $onOrder = $this->receive_material_model->get_on_order_qty($rs->ItemCode, $po_code, $rs->DocEntry, $rs->LineNum);

              $qty = $rs->OpenQty - $onOrder;

              $ds[] = array(
                'no' => $no,
                'uid' => $rs->DocEntry.'-'.$rs->LineNum,
                'baseCode' => $po_code,
                'baseEntry' => $rs->DocEntry,
                'baseLine' => $rs->LineNum,
                'pdCode' => $rs->ItemCode,
                'pdName' => $rs->Dscription,
                'price' => round($rs->Price, 4),
                'price_label' => number($rs->Price, 2),
                'PriceBefDi' => $rs->PriceBefDi,
                'PriceAfVAT' => $rs->PriceAfVAT,
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
                'isOpen' => $rs->LineStatus === 'O' ? TRUE : FALSE,
                'has_batch' => $rs->ManBtchNum,
                'uomCode' => $rs->UomCode,
                'uomCode2' => $rs->UomCode2,
                'unitMsr' => $rs->unitMsr,
                'unitMsr2' => $rs->unitMsr2,
                'uomEntry' => $rs->UomEntry,
                'uomEntry2' => $rs->UomEntry2,
                'numPerMsr' => $rs->NumPerMsr,
                'numPerMsr2' => $rs->NumPerMsr2
              );

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
        $this->error = "ไม่พบใบสั่งซื้อ {$po_code}";
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


  public function get_zone($warehouse_code = NULL)
  {
    $ds = [];
    $txt = trim($_REQUEST['term']);
    $this->db->select('code, name');

    if( ! empty($warehouse_code))
    {
      $this->db->where('warehouse_code', $warehouse_code);
    }

    if( $txt != '*')
    {
      $this->db
      ->group_start()
      ->like('code', $txt)
      ->or_like('name', $txt)
      ->group_end();
    }

    $rs = $this->db->order_by('code', 'ASC')->limit(50)->get('zone');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $ds[] = $rd->code.' | '.$rd->name;
      }
    }
    else
    {
      $ds[] = "not found";
    }

    echo json_encode($ds);
  }


  public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RECEIVE_METERIAL');
    $run_digit = getConfig('RUN_DIGIT_RECEIVE_METERIAL');

    $prefix = empty($prefix) ? 'RM' : $prefix;
    $run_digit = empty($run_digit) ? 5 : $run_digit;

    $pre = $prefix .'-'.$Y.$M;

    $code = $this->receive_material_model->get_max_code($pre);

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


  public function get_template_file()
  {
    $path = $this->config->item('upload_path').'grpo-sticker/';
    $file_name = $path."grpo-sticker-template.xlsx";

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


  public function gen_new_code()
  {
    $date_add = db_date($this->input->post('date_add'), TRUE);
    $code = $this->get_new_code($date_add);

    $arr = array(
      'status' => 'success',
      'code' => $code
    );

    echo json_encode($arr);
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
      'receive_export',
      'receive_user'
    );

    return clear_filter($filter);
  }

} //--- end class
