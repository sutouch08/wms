<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Production_receipt extends PS_Controller
{
  public $menu_code = 'PDGRPD';
	public $menu_group_code = 'PD';
  public $menu_sub_group_code = '';
	public $title = 'Receipt from Production';
  public $segment = 4;
  public $error;


  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'productions/production_receipt';
    $this->load->model('productions/production_receipt_model');
    $this->load->model('masters/products_model');
    $this->load->model('stock/stock_model');
    $this->load->model('inventory/movement_model');
    $this->load->helper('warehouse');
    $this->load->helper('production_receipt');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'receipt_code', ''),
      'reference' => get_filter('reference', 'receipt_reference', ''),
      'order_ref' => get_filter('order_ref', 'receipt_order_ref', ''),
      'item_code' => get_filter('item_code', 'receipt_item_code', ''),
      'from_date' => get_filter('from_date', 'receipt_from_date', ''),
      'to_date' => get_filter('to_date', 'receipt_to_date', ''),
      'user' => get_filter('user', 'receipt_user', 'all'),
      'status' => get_filter('status', 'receipt_status', 'all'),
      'is_exported' => get_filter('is_exported', 'receipt_is_exported', 'all')
    );


    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->production_receipt_model->count_rows($filter);
      $filter['data'] = $this->production_receipt_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

      $init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('productions/production_receipt/production_receipt_list', $filter);
    }
  }


  public function add_new($code = NULL)
  {
    $ds = array(
      'code' => NULL,
      'orderRef' => NULL,
      'ItemCode' => NULL,
      'details' => NULL
    );

    if( ! empty($code))
    {
      $docNum = $this->production_receipt_model->get_production_order_doc_num($code);

      if( ! empty($docNum))
      {
        $pdo = $this->production_receipt_model->get_production_order($docNum);

        if( ! empty($pdo))
        {
          $ds = array(
            'code' => $pdo->DocNum,
            'orderRef' => $code,
            'ItemCode' => $pdo->ItemCode,
            'details' => []
          );

          $details = $this->production_receipt_model->get_production_order_details($pdo->DocEntry);

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              $balance = $rs->PlannedQty - $rs->IssuedQty;
              $instock = $this->stock_model->get_item_stock($rs->ItemCode,  $rs->wareHouse);

              $rs->uid = genUid();
              $rs->fromWhsCode = $rs->wareHouse;
              $rs->PlannedQty = number($rs->PlannedQty, 2);
              $rs->IssuedQty = number($rs->IssuedQty, 2);
              $rs->BalanceQty = $balance > 0 ? number($balance, 2) : 0.00;
              $rs->InStock = number($instock, 2);
            }

            $ds['details'] = $details;
          }
        }
      }
    }

    $this->load->view('productions/production_receipt/production_receipt_add', $ds);
  }


  public function add()
  {
    $sc = TRUE;
    $code = NULL;
    $ex = 0;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->date_add) && ! empty($ds->baseRef) && ! empty($ds->rows))
    {
      $date_add = db_date($ds->date_add);
      $shipped_date = db_date($ds->shipped_date);

      $code = $this->get_new_code($date_add);

      $arr = array(
        'code' => $code,
        'reference' => $ds->baseRef,
        'orderRef' => get_null($ds->orderRef),
        'date_add' => $date_add,
        'shipped_date' => $shipped_date,
        'user' => $this->_user->uname,
        'remark' => get_null($ds->remark),
        'Status' => 'C'
      );

      $this->db->trans_begin();

      if( ! $this->production_receipt_model->add($arr))
      {
        $sc = FALSE;
        set_error('insert');
      }

      if($sc === TRUE)
      {
        foreach($ds->rows as $rs)
        {
          if($sc === FALSE) { break;}

          if(empty($rs->BinCode) OR empty($rs->WhsCode))
          {
            $sc = FALSE;
            $this->error = "Missing Warehouse or Bin Location for line item {$rs->ItemCode}";
          }

          if($sc === TRUE)
          {
            $arr = array(
              'receipt_code' => $code,
              'LineNum' => $rs->LineNum,
              'BaseType' => $rs->BaseType,
              'BaseRef' => $rs->BaseRef,
              'BaseEntry' => $rs->BaseEntry,
              'ItemCode' => $rs->ItemCode,
              'ItemName' => $rs->ItemName,
              'WhsCode' => $rs->WhsCode,
              'BinCode' => $rs->BinCode,
              'Qty' => $rs->Qty,
              'TranType' => $rs->TranType,
              'UomEntry' => $rs->UomEntry,
              'UomCode' => $rs->UomCode,
              'unitMsr' => $rs->Uom,
              'LineStatus' => 'C',
              'hasBatch' => $rs->hasBatch,
              'uid' => $rs->uid
            );

            $id = $this->production_receipt_model->add_detail($arr);

            if($id)
            {
              if( ! empty($rs->batchRows))
              {
                foreach($rs->batchRows as $ro)
                {
                  if($sc === FALSE) { break;}

                  if(empty($ro->BatchNum))
                  {
                    $sc = FALSE;
                    $this->error = "Batch Number is required for item {$rs->ItemCode}";
                  }

                  if($sc === TRUE)
                  {
                    $br = array(
                      'receipt_code' => $code,
                      'receipt_detail_id' => $id,
                      'ItemCode' => $ro->ItemCode,
                      'ItemName' => $ro->ItemName,
                      'BatchNum' => $ro->BatchNum,
                      'BatchAttr1' => get_null($ro->BatchAttr1),
                      'BatchAttr2' => get_null($ro->BatchAttr2),
                      'Qty' => $ro->Qty,
                      'WhsCode' => $rs->WhsCode,
                      'BinCode' => $rs->BinCode,
                      'uid' => $ro->uid
                    );

                    if( ! $this->production_receipt_model->add_batch_rows($br))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to add batch row for line item {$rs->ItemCode}";
                    }

                    if($sc === TRUE)
                    {
                      $move_in = array(
                        'reference' => $code,
                        'warehouse_code' => $rs->WhsCode,
                        'zone_code' => $rs->BinCode,
                        'product_code' => $ro->ItemCode,
                        'batchNum' => $ro->BatchNum,
                        'move_in' => $ro->Qty,
                        'move_out' => 0
                      );

                      if( ! $this->movement_model->add($move_in))
                      {
                        $sc = FALSE;
                        $this->error = "Failed to insert stock movement out for {$ro->ItemCode} : {$ro->BatchNum}";
                      }
                    }
                  }
                } // end foreach
              }
              else
              {
                $move_in = array(
                  'reference' => $code,
                  'warehouse_code' => $rs->WhsCode,
                  'zone_code' => $rs->BinCode,
                  'product_code' => $rs->ItemCode,
                  'move_in' => $rs->Qty,
                  'move_out' => 0
                );

                if( ! $this->movement_model->add($move_in))
                {
                  $sc = FALSE;
                  $this->error = "Failed to insert stock movement out for {$rs->ItemCode}";
                }
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Failed to add item row for line item {$rs->ItemCode}";
            }
          } //--- $sc
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
        if(is_true(getConfig('SAP_API')))
        {
          $this->load->library('sap_api');

          if( ! $this->sap_api->exportProductionReceipt($code))
          {
            $sc = FALSE;
            $ex = 1;
            $this->error = "Create Document success but send data to SAP failed";
          }
        }
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
      'ex' => $ex,
      'code' => $code
    );

    echo json_encode($arr);
  }


  public function view_detail($code)
  {
    $this->load->helper('zone');

    $doc = $this->production_receipt_model->get($code);

    if( ! empty($doc))
    {
      $details = $this->production_receipt_model->get_details($code);

      if( ! empty($details))
      {
        $no = 1;

        foreach($details as $rs)
        {
          $rs->batchRows = $this->production_receipt_model->get_batch_rows($rs->id);
        }
      }


      $ds = array(
        'doc' => $doc,
        'details' => $details
      );

      $this->load->view('productions/production_receipt/production_receipt_view', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function cancel()
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $code = $this->input->post('code');
      $reason = $this->input->post('reason');
      $force = $this->input->post('force_cancel') == 1 ? TRUE : FALSE;

      if( ! empty($code) && ! empty($reason))
      {
        $doc = $this->production_receipt_model->get($code);

        if( ! empty($doc))
        {
          if($doc->Status != 'D')
          {
            if($this->production_receipt_model->is_exists_in_sap($code))
            {
              $sc = FALSE;
              $this->error = "เอกสารนี้เข้าระบบ SAP แล้ว หากต้องการแก้ไข กรุณายกเลิกเอกสารบน SAP ก่อน";
            }

            if($sc === TRUE)
            {
              $this->db->trans_begin();

              //--- set cancel batch
              if( ! $this->production_receipt_model->update_batches($code, ['Status' => 'D']))
              {
                $sc = FALSE;
                $this->error = "Failed to update batch rows status";
              }

              if($sc === TRUE)
              {
                if( ! $this->production_receipt_model->update_details($code, ['LineStatus' => 'D']))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update item rows status";
                }
              }

              if($sc === TRUE)
              {
                $arr = array(
                  'Status' => 'D',
                  'update_user' => $this->_user->uname,
                  'cancel_user' => $this->_user->uname,
                  'cancel_reason' => get_null($reason),
                  'cancel_date' => now(),
                  'date_upd' => now()
                );

                if( ! $this->production_receipt_model->update($code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update document status";
                }
              }

              if($sc === TRUE)
              {
                if( ! $this->movement_model->drop_movement($code))
                {
                  $sc = FALSE;
                  $this->error = "Failed to delete stock_movement";
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


  public function do_export()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->production_receipt_model->get($code);

      if( ! empty($doc))
      {
        if($doc->Status == 'C')
        {
          if(is_true(getConfig('SAP_API')))
          {
            $this->load->library('sap_api');

            if( ! $this->sap_api->exportProductionReceipt($code))
            {
              $sc = FALSE;
              $this->error = "Send to SAP failed : {$this->sap_api->error}";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "SAP API not available";
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


  public function get_production_order_code()
  {
    $ds = [];

    $txt = trim($_REQUEST['term']);

    $qr = "SELECT TOP(100) DocNum, ItemCode FROM OWOR WHERE Status = 'R' ";

    if($txt != '*')
    {
      $qr .= "AND (DocNum LIKE '%{$txt}%' OR ItemCode LIKE '%{$txt}%') ";
    }

    $qr .= "ORDER BY DocEntry ASC";

    $qs = $this->ms->query($qr);

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $ds[] = $rs->DocNum.' | '.$rs->ItemCode;
      }
    }
    else
    {
      $ds[] = 'Not found';
    }

    echo json_encode($ds);
  }


  public function get_production_order_data()
  {
    $sc = TRUE;
    $ds = [];
    $code = trim($this->input->post('baseCode'));

    if( ! empty($code))
    {
      $pd = $this->production_receipt_model->get_production_order_data($code);

      if( ! empty($pd))
      {
        $pd->uid = genUid();
        $pd->Planned = number($pd->PlannedQty, 2);
        $pd->Completed = number($pd->CompleteQty, 2);
        $pd->Rejected = number($pd->RejectQty, 2);
        $ds['data'] = $pd;
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid document number";
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );

    echo json_encode($arr);
  }


  public function sap_production_order($docNum)
  {
    if( ! empty($docNum))
    {
      $doc = $this->production_receipt_model->get_production_order_data($docNum);

      if( ! empty($doc))
      {
        $this->load->helper('production_order');
        $doc->WhsName = warehouse_name($doc->WhsCode);
        $details = $this->production_receipt_model->get_production_order_details($doc->DocEntry);

        if( ! empty($details))
        {
          $this->load->model('productions/production_order_model');

          foreach($details as $rs)
          {
            $rs->issued = $this->production_order_model->get_issue_qty_by_item($rs->ItemCode, $doc->DocEntry, $rs->LineNum);
          }
        }

        $ds = array(
          'doc' => $doc,
          'details' => $details
        );

        $this->load->view('productions/sap_production_order_detail', $ds);
      }
      else
      {
        $this->page_error();
      }
    }
    else
    {
      $this->page_error();
    }
  }


  public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_PRODUCTION_RECEIPT');
    $run_digit = getConfig('RUN_DIGIT_PRODUCTION_RECEIPT');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->production_receipt_model->get_max_code($pre);

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
      'receipt_code',
      'receipt_order_ref',
      'receipt_reference',
      'receipt_from_date',
      'receipt_to_date',
      'receipt_user',
      'receipt_status',
      'receipt_is_exported'
    );

    return clear_filter($filter);
  }

} // end class
?>
