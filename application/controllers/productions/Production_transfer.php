<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Production_transfer extends PS_Controller
{
  public $menu_code = 'PDTRAN';
	public $menu_group_code = 'PD';
  public $menu_sub_group_code = '';
	public $title = 'Transfer For Production';
  public $segment = 4;
  public $error;


  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'productions/production_transfer';
    $this->load->model('productions/production_transfer_model');
    $this->load->model('masters/products_model');
    $this->load->model('stock/stock_model');
    $this->load->model('inventory/movement_model');
    $this->load->helper('warehouse');
    $this->load->helper('production_transfer');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'tr_code', ''),
      'reference' => get_filter('reference', 'tr_reference', ''),
      'orderRef' => get_filter('orderRef', 'tr_orderRef', ''),
      'from_date' => get_filter('from_date', 'tr_from_date', ''),
      'to_date' => get_filter('to_date', 'tr_to_date', ''),
      'fromWhsCode' => get_filter('fromWhsCode', 'tr_fromWhsCode', 'all'),
      'toWhsCode' => get_filter('toWhsCode', 'tr_toWhsCode', 'all'),
      'user' => get_filter('user', 'tr_user', 'all'),
      'status' => get_filter('status', 'tr_status', 'all'),
      'is_exported' => get_filter('is_exported', 'tr_is_exported', 'all')
    );


    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->production_transfer_model->count_rows($filter);
      $filter['data'] = $this->production_transfer_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

      $init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('productions/production_transfer/production_transfer_list', $filter);
    }
  }


  public function add_new($code = NULL)
  {
    $ds = array(
      'code' => NULL,
      'orderRef' => NULL,
      'ItemCode' => NULL,
      'details' => NULL,
      'remark' => NULL
    );

    if( ! empty($code))
    {
      $docNum = $this->production_transfer_model->get_production_order_doc_num($code);

      if( ! empty($docNum))
      {
        $pdo = $this->production_transfer_model->get_production_order($docNum);

        if( ! empty($pdo))
        {
          $ds = array(
            'code' => $pdo->DocNum,
            'orderRef' => $code,
            'ItemCode' => $pdo->ItemCode,
            'details' => []
          );

          $details = $this->production_transfer_model->get_production_order_details($pdo->DocEntry);

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
              $rs->Qty = $balance > 0 ? number($balance, 2) : 0.00;
              $rs->InStock = number($instock, 2);
              $rs->hasBatch = $rs->ManBtchNum;
            }

            $ds['details'] = $details;
          }
        }
      }
    }

    $this->load->view('productions/production_transfer/production_transfer_add', $ds);
  }


  public function add()
  {
    $sc = TRUE;
    $code = NULL;
    $ex = 0;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->date_add) && ! empty($ds->fromWhsCode) && ! empty($ds->toWhsCode))
    {
      if($ds->type != 'P' && empty($ds->rows))
      {
        $sc = FALSE;
        $this->error = "ไม่พบรายการโอนย้าย";
      }

      if($sc === TRUE && ! empty($ds->rows))
      {
        foreach($ds->rows as $rs)
        {
          if($sc === FALSE) { break; }

          if($sc === TRUE && empty($rs->fromWhsCode))
          {
            $sc = FALSE;
            $this->error = "Missing From Warehouse for line item {$rs->ItemCode}";
          }

          if($sc === TRUE && empty($rs->batchRows) && empty($rs->fromBinCode))
          {
            $sc = FALSE;
            $this->error = "Missing From Bin for line item {$rs->ItemCode}";
          }

          if($sc === TRUE && empty($rs->toWhsCode))
          {
            $sc = FALSE;
            $this->error = "Missing To Warehouse for line item {$rs->ItemCode}";
          }

          if($sc === TRUE && empty($rs->batchRows) && empty($rs->toBinCode))
          {
            $sc = FALSE;
            $this->error = "Missing To Bin for line item {$rs->ItemCode}";
          }

          if($sc === TRUE && ! empty($rs->batchRows))
          {
            foreach($rs->batchRows as $ro)
            {
              if($sc === FALSE) { break; }

              if($sc === TRUE && empty($ro->BatchNum))
              {
                $sc = FALSE;
                $this->error = "Batch Number is required for item {$rs->ItemCode}";
              }

              if($sc === TRUE && empty($ro->fromWhsCode))
              {
                $sc = FALSE;
                $this->error = "Missing from warehouse for batch row {$rs->ItemCode} - {$ro->BatchNum}";
              }

              if($sc === TRUE && empty($ro->fromBinCode))
              {
                $sc = FALSE;
                $this->error = "Missing from Bin for batch row {$rs->ItemCode} - {$ro->BatchNum}";
              }

              if($sc === TRUE && empty($ro->toWhsCode))
              {
                $sc = FALSE;
                $this->error = "Missing to warehouse for batch row {$rs->ItemCode} - {$ro->BatchNum}";
              }

              if($sc === TRUE && empty($ro->toBinCode))
              {
                $sc = FALSE;
                $this->error = "Missing to Bin for batch row {$rs->ItemCode} - {$ro->BatchNum}";
              }

              if($sc === TRUE)
              {
                $instock = $this->stock_model->get_item_batch_qty($ro->ItemCode, $ro->BatchNum, $ro->fromWhsCode, $ro->fromBinCode);

                if($instock < $ro->Qty)
                {
                  $sc = FALSE;
                  $this->error = "สต็อกคงเหลือไม่เพียงพอ Item: {$ro->ItemCode}, Batch No: {$ro->BatchNum}, Zone: {$ro->fromBinCode}";
                }
              }
            }
          }

          if($sc === TRUE && empty($rs->batchRows))
          {
            $instock = $this->stock_model->get_item_stock($rs->ItemCode, $rs->fromWhsCode, $rs->fromBinCode);

            if($instock < $rs->Qty)
            {
              $sc = FALSE;
              $this->error = "สต็อกคงเหลือไม่เพียงพอ Item: {$ro->ItemCode}, Zone: {$ro->fromBinCode}";
            }
          }
        }
      }

      if($sc === TRUE)
      {
        $date_add = db_date($ds->date_add);
        $shipped_date = db_date($ds->shipped_date);

        $code = $this->get_new_code($date_add);

        $arr = array(
          'code' => $code,
          'reference' => $ds->baseRef,
          'orderRef' => get_null($ds->orderRef),
          'ItemCode' => get_null($ds->itemCode),
          'CardCode' => get_null($ds->cardCode),
          'CardName' => get_null($ds->cardName),
          'fromWhsCode' => $ds->fromWhsCode,
          'toWhsCode' => $ds->toWhsCode,
          'toBinCode' => get_null($ds->toBinCode),
          'date_add' => $date_add,
          'shipped_date' => $shipped_date,
          'user' => $this->_user->uname,
          'remark' => get_null($ds->remark),
          'Status' => $ds->type //-- P = Draft, R = Released, C = Closed
        );

        $this->db->trans_begin();

        if( ! $this->production_transfer_model->add($arr))
        {
          $sc = FALSE;
          set_error('insert');
        }

        if($sc === TRUE)
        {
          foreach($ds->rows as $rs)
          {
            if($sc === FALSE) { break;}

            if($sc === TRUE)
            {
              $arr = array(
                'transfer_code' => $code,
                'LineNum' => $rs->LineNum,
                'ItemCode' => $rs->ItemCode,
                'ItemName' => $rs->ItemName,
                'fromWhsCode' => $rs->fromWhsCode,
                'fromBinCode' => get_null($rs->fromBinCode),
                'toWhsCode' => $rs->toWhsCode,
                'toBinCode' => get_null($rs->toBinCode),
                'Qty' => $rs->Qty,
                'UomEntry' => $rs->UomEntry,
                'UomCode' => $rs->UomCode,
                'unitMsr' => $rs->Uom,
                'LineStatus' => $ds->type == 'C' ? 'C' : 'O',
                'hasBatch' => $rs->hasBatch,
                'uid' => $rs->uid
              );

              $id = $this->production_transfer_model->add_detail($arr);

              if($id)
              {
                if( ! empty($rs->batchRows))
                {
                  foreach($rs->batchRows as $ro)
                  {
                    if($sc === FALSE) { break;}

                    if($sc === TRUE)
                    {
                      $br = array(
                        'transfer_code' => $code,
                        'transfer_detail_id' => $id,
                        'ItemCode' => $ro->ItemCode,
                        'ItemName' => $ro->ItemName,
                        'BatchNum' => $ro->BatchNum,
                        'BatchAttr1' => get_null($ro->BatchAttr1),
                        'BatchAttr2' => get_null($ro->BatchAttr2),
                        'Qty' => $ro->Qty,
                        'fromWhsCode' => $ro->fromWhsCode,
                        'fromBinCode' => $ro->fromBinCode,
                        'toWhsCode' => $ro->toWhsCode,
                        'toBinCode' => $ro->toBinCode,
                        'uid' => $ro->uid
                      );

                      if( ! $this->production_transfer_model->add_batch_rows($br))
                      {
                        $sc = FALSE;
                        $this->error = "Failed to add batch row for line item {$rs->ItemCode}";
                      }

                      if($sc === TRUE)
                      {
                        if($ds->type == 'C')
                        {
                          $move_out = array(
                            'reference' => $code,
                            'warehouse_code' => $ro->fromWhsCode,
                            'zone_code' => $ro->fromBinCode,
                            'product_code' => $ro->ItemCode,
                            'batchNum' => $ro->BatchNum,
                            'move_in' => 0,
                            'move_out' => $ro->Qty
                          );

                          if( ! $this->movement_model->add($move_out))
                          {
                            $sc = FALSE;
                            $this->error = "Failed to insert stock movement out for {$ro->ItemCode} : {$ro->BatchNum}";
                          }

                          if($sc === TRUE)
                          {
                            $move_in = array(
                              'reference' => $code,
                              'warehouse_code' => $ro->toWhsCode,
                              'zone_code' => $ro->toBinCode,
                              'product_code' => $ro->ItemCode,
                              'batchNum' => $ro->BatchNum,
                              'move_in' => $ro->Qty,
                              'move_out' => 0
                            );

                            if( ! $this->movement_model->add($move_in))
                            {
                              $sc = FALSE;
                              $this->error = "Failed to insert stock movement in for {$ro->ItemCode} : {$ro->BatchNum}";
                            }
                          }
                        }
                      }
                    }
                  } // end foreach
                }
                else
                {
                  if($ds->type == 'C')
                  {
                    $move_out = array(
                      'reference' => $code,
                      'warehouse_code' => $rs->fromWhsCode,
                      'zone_code' => $rs->fromBinCode,
                      'product_code' => $rs->ItemCode,
                      'move_in' => 0,
                      'move_out' => $rs->Qty
                    );

                    if( ! $this->movement_model->add($move_out))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to insert stock movement out for {$rs->ItemCode}";
                    }

                    if($sc === TRUE)
                    {
                      $move_in = array(
                        'reference' => $code,
                        'warehouse_code' => $rs->toWhsCode,
                        'zone_code' => $rs->toBinCode,
                        'product_code' => $rs->ItemCode,
                        'move_in' => $rs->Qty,
                        'move_out' => 0
                      );

                      if( ! $this->movement_model->add($move_in))
                      {
                        $sc = FALSE;
                        $this->error = "Failed to insert stock movement in for {$rs->ItemCode}";
                      }
                    }
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

        if($sc === TRUE && $ds->type == 'C')
        {
          if(is_true(getConfig('SAP_API')))
          {
            $this->load->library('sap_api');

            if( ! $this->sap_api->exportProductionTransfer($code))
            {
              $sc = FALSE;
              $ex = 1;
              $this->error = "Create Document success but send data to SAP failed";
            }
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


  public function edit($code)
  {
    $this->load->helper('zone');

    $doc = $this->production_transfer_model->get($code);

    if( ! empty($doc))
    {
      $doc->toBinName = empty($doc->toBinCode) ? NULL : zone_name($doc->toBinCode);

      $details = $this->production_transfer_model->get_details($code);

      if( ! empty($details))
      {
        $no = 1;

        foreach($details as $rs)
        {
          $rs->InStock = $this->stock_model->get_item_stock($rs->ItemCode, $rs->fromWhsCode, $rs->fromBinCode);
          $rs->batchRows = $this->production_transfer_model->get_batch_rows($rs->id);
        }
      }


      $ds = array(
        'doc' => $doc,
        'details' => $details
      );

      $this->load->view('productions/production_transfer/production_transfer_edit', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function save()
  {
    $sc = TRUE;
    $code = NULL;
    $doc = NULL;
    $ex = 0;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->date_add) && ! empty($ds->fromWhsCode) && ! empty($ds->toWhsCode))
    {
      if($ds->type != 'P' && empty($ds->rows))
      {
        $sc = FALSE;
        $this->error = "ไม่พบรายการโอนย้าย";
      }

      if($sc === TRUE)
      {
        $doc = $this->production_transfer_model->get($ds->code);

        if(empty($doc))
        {
          $sc = FALSE;
          set_error('notfound');
        }
      }

      if($sc === TRUE)
      {
        if($doc->Status == 'C' OR $doc->Status == 'D')
        {
          $sc = FALSE;
          $this->error = $doc->Status == 'D' ? 'Document already canceled cannot be change' : 'Document already Closed cannot be change';
        }
      }

      //--- validate data and parse to rows before write to
      if($sc === TRUE && ! empty($ds->rows))
      {
        foreach($ds->rows as $rs)
        {
          if($sc === FALSE) { break;}

          if($sc === TRUE && empty($rs->fromWhsCode))
          {
            $sc = FALSE;
            $this->error = "Missing From Warehouse for line item {$rs->ItemCode}";
          }

          if($sc === TRUE && empty($rs->batchRows) && empty($rs->fromBinCode))
          {
            $sc = FALSE;
            $this->error = "Missing From Bin for line item {$rs->ItemCode}";
          }

          if($sc === TRUE && empty($rs->toWhsCode))
          {
            $sc = FALSE;
            $this->error = "Missing To Warehouse line item {$rs->ItemCode}";
          }

          if($sc === TRUE && empty($rs->batchRows) && empty($rs->toBinCode))
          {
            $sc = FALSE;
            $this->error = "Missing To Bin for line item {$rs->ItemCode}";
          }

          if( ! empty($rs->batchRows))
          {
            foreach($rs->batchRows as $ro)
            {
              if($sc === FALSE) { break;}

              if($sc === TRUE && empty($ro->BatchNum))
              {
                $sc = FALSE;
                $this->error = "Batch Number is required for item {$rs->ItemCode}";
              }

              if($sc === TRUE && empty($ro->fromWhsCode))
              {
                $sc = FALSE;
                $this->error = "Missing from warehouse for batch row {$rs->ItemCode} - {$ro->BatchNum}";
              }

              if($sc === TRUE && empty($ro->fromBinCode))
              {
                $sc = FALSE;
                $this->error = "Missing from Bin for batch row {$rs->ItemCode} - {$ro->BatchNum}";
              }

              if($sc === TRUE && empty($ro->toWhsCode))
              {
                $sc = FALSE;
                $this->error = "Missing to warehouse for batch row {$rs->ItemCode} - {$ro->BatchNum}";
              }

              if($sc === TRUE && empty($ro->toBinCode))
              {
                $sc = FALSE;
                $this->error = "Missing to Bin for batch row {$rs->ItemCode} - {$ro->BatchNum}";
              }

              if($sc === TRUE)
              {
                $instock = $this->stock_model->get_item_batch_qty($ro->ItemCode, $ro->BatchNum, $ro->fromWhsCode, $ro->fromBinCode);

                if($instock < $ro->Qty)
                {
                  $sc = FALSE;
                  $this->error = "สต็อกคงเหลือไม่เพียงพอ Item: {$ro->ItemCode}, Batch No: {$ro->BatchNum}, Zone: {$ro->fromBinCode}";
                }
              }
            } // foreach
          }

          if($sc === TRUE && empty($rs->batchRows))
          {
            $instock = $this->stock_model->get_item_stock($rs->ItemCode, $rs->fromWhsCode, $rs->fromBinCode);

            if($instock < $rs->Qty)
            {
              $sc = FALSE;
              $this->error = "สต็อกคงเหลือไม่เพียงพอ Item: {$rs->ItemCode}, Zone: {$rs->fromBinCode}";
            }
          }
        } //-- foreach
      }

      if($sc === TRUE)
      {
        $date_add = db_date($ds->date_add);
        $shipped_date = db_date($ds->shipped_date);

        $code = $ds->code;

        $arr = array(
          'reference' => $ds->baseRef,
          'orderRef' => get_null($ds->orderRef),
          'ItemCode' => get_null($ds->itemCode),
          'CardCode' => get_null($ds->cardCode),
          'CardName' => get_null($ds->cardName),
          'fromWhsCode' => $ds->fromWhsCode,
          'toWhsCode' => $ds->toWhsCode,
          'toBinCode' => get_null($ds->toBinCode),
          'date_add' => $date_add,
          'shipped_date' => $shipped_date,
          'update_user' => $this->_user->uname,
          'date_upd' => now(),
          'remark' => get_null($ds->remark),
          'Status' => $ds->type //-- P = Draft, R = Released, C = Closed
        );

        $this->db->trans_begin();

        if( ! $this->production_transfer_model->update($code, $arr))
        {
          $sc = FALSE;
          set_error('update');
        }

        if($sc === TRUE)
        {
          if( ! $this->production_transfer_model->drop_all_batch($code))
          {
            $sc = FALSE;
            $this->error = "Failed to remove prev batch data";
          }
        }

        if($sc === TRUE)
        {
          if( ! $this->production_transfer_model->drop_all_details($code))
          {
            $sc = FALSE;
            $this->error = "Failed to remove prev item rows";
          }
        }

        if($sc === TRUE)
        {
          foreach($ds->rows as $rs)
          {
            if($sc === FALSE) { break;}

            $arr = array(
              'transfer_code' => $code,
              'LineNum' => $rs->LineNum,
              'ItemCode' => $rs->ItemCode,
              'ItemName' => $rs->ItemName,
              'fromWhsCode' => $rs->fromWhsCode,
              'toWhsCode' => $rs->toWhsCode,
              'fromBinCode' => get_null($rs->fromBinCode),
              'toBinCode' => get_null($rs->toBinCode),
              'Qty' => $rs->Qty,
              'UomEntry' => $rs->UomEntry,
              'UomCode' => $rs->UomCode,
              'unitMsr' => $rs->Uom,
              'LineStatus' => $ds->type == 'C' ? 'C' : 'O',
              'hasBatch' => $rs->hasBatch,
              'uid' => $rs->uid
            );

            $id = $this->production_transfer_model->add_detail($arr);

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
                      'transfer_code' => $code,
                      'transfer_detail_id' => $id,
                      'ItemCode' => $ro->ItemCode,
                      'ItemName' => $ro->ItemName,
                      'BatchNum' => $ro->BatchNum,
                      'BatchAttr1' => get_null($ro->BatchAttr1),
                      'BatchAttr2' => get_null($ro->BatchAttr2),
                      'Qty' => $ro->Qty,
                      'fromWhsCode' => $ro->fromWhsCode,
                      'fromBinCode' => $ro->fromBinCode,
                      'toWhsCode' => $ro->toWhsCode,
                      'toBinCode' => $ro->toBinCode,
                      'uid' => $ro->uid
                    );

                    if( ! $this->production_transfer_model->add_batch_rows($br))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to add batch row for line item {$rs->ItemCode}";
                    }

                    if($sc === TRUE)
                    {
                      if($ds->type == 'C')
                      {
                        $move_out = array(
                          'reference' => $code,
                          'warehouse_code' => $ro->fromWhsCode,
                          'zone_code' => $ro->fromBinCode,
                          'product_code' => $ro->ItemCode,
                          'batchNum' => $ro->BatchNum,
                          'move_in' => 0,
                          'move_out' => $ro->Qty
                        );

                        if( ! $this->movement_model->add($move_out))
                        {
                          $sc = FALSE;
                          $this->error = "Failed to insert stock movement out for {$ro->ItemCode} : {$ro->BatchNum}";
                        }

                        if($sc === TRUE)
                        {
                          $move_in = array(
                            'reference' => $code,
                            'warehouse_code' => $ro->toWhsCode,
                            'zone_code' => $ro->toBinCode,
                            'product_code' => $ro->ItemCode,
                            'batchNum' => $ro->BatchNum,
                            'move_in' => $ro->Qty,
                            'move_out' => 0
                          );

                          if( ! $this->movement_model->add($move_in))
                          {
                            $sc = FALSE;
                            $this->error = "Failed to insert stock movement in for {$ro->ItemCode} : {$ro->BatchNum}";
                          }
                        }
                      }
                    }
                  }
                } // end foreach
              }
              else
              {
                if($ds->type == 'C')
                {
                  $move_out = array(
                    'reference' => $code,
                    'warehouse_code' => $rs->fromWhsCode,
                    'zone_code' => $rs->fromBinCode,
                    'product_code' => $rs->ItemCode,
                    'move_in' => 0,
                    'move_out' => $rs->Qty
                  );

                  if( ! $this->movement_model->add($move_out))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert stock movement out for {$rs->ItemCode}";
                  }

                  if($sc === TRUE)
                  {
                    $move_in = array(
                      'reference' => $code,
                      'warehouse_code' => $rs->toWhsCode,
                      'zone_code' => $rs->toBinCode,
                      'product_code' => $rs->ItemCode,
                      'move_in' => $rs->Qty,
                      'move_out' => 0
                    );

                    if( ! $this->movement_model->add($move_in))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to insert stock movement in for {$rs->ItemCode}";
                    }
                  }
                }
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Failed to add item row for line item {$rs->ItemCode}";
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

        if($sc === TRUE && $ds->type == 'C')
        {
          if(is_true(getConfig('SAP_API')))
          {
            $this->load->library('sap_api');

            if( ! $this->sap_api->exportProductionTransfer($code))
            {
              $sc = FALSE;
              $ex = 1;
              $this->error = "Create Document success but send data to SAP failed";
            }
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

    $doc = $this->production_transfer_model->get($code);

    if( ! empty($doc))
    {
      $details = $this->production_transfer_model->get_details($code);

      if( ! empty($details))
      {
        $no = 1;

        foreach($details as $rs)
        {
          $rs->batchRows = $this->production_transfer_model->get_batch_rows($rs->id);
        }
      }


      $ds = array(
        'doc' => $doc,
        'details' => $details
      );

      $this->load->view('productions/production_transfer/production_transfer_view', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function close()
  {
    $sc = TRUE;
    $ex = 0;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->production_transfer_model->get($code);

      if( ! empty($doc))
      {
        if($doc->Status == 'R')
        {
          $details = $this->production_transfer_model->get_details($code);

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              if($sc === FALSE) { break; }

              $batchRows = $this->production_transfer_model->get_batch_rows($rs->id);

              if(empty($batchRows))
              {
                $instock = $this->stock_model->get_item_stock($rs->ItemCode, $rs->fromWhsCode, $rs->fromBinCode);

                if($instock < $rs->Qty)
                {
                  $sc = FALSE;
                  $this->error = "สต็อกคงเหลือไม่เพียงพอ Item: {$ItemCode}  Zone: {$rs->fromBinCode}";
                }
              }

              if( ! empty($batchRows))
              {
                $rs->batchRows = $batchRows;

                foreach($batchRows as $ro)
                {
                  $instock = $this->stock_model->get_item_batch_qty($ro->ItemCode, $ro->BatchNum, $ro->fromWhsCode, $ro->fromBinCode);

                  if($instock < $ro->Qty)
                  {
                    $sc = FALSE;
                    $this->error = "สต็อกคงเหลือไม่เพียงพอ สำหร้บ {$ro->ItemCode} Batch No: {$ro->BatchNum} Zone: {$ro->fromBinCode}";
                  }
                }
              }
            }

            if($sc === TRUE)
            {
              $this->db->trans_begin();

              $arr = array(
                'Status' => 'C',
                'update_user' => $this->_user->uname,
                'date_upd' => now()
              );

              if( ! $this->production_transfer_model->update($code, $arr))
              {
                $sc = FALSE;
                set_error('update');
              }

              if($sc === TRUE)
              {
                foreach($details as $rs)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  if( ! empty($rs->batchRows))
                  {
                    foreach($rs->batchRows as $ro)
                    {
                      if($sc === FALSE) { break; }

                      if($sc === TRUE)
                      {
                        $move_out = array(
                          'reference' => $code,
                          'warehouse_code' => $ro->fromWhsCode,
                          'zone_code' => $ro->fromBinCode,
                          'product_code' => $ro->ItemCode,
                          'batchNum' => $ro->BatchNum,
                          'move_in' => 0,
                          'move_out' => $ro->Qty
                        );

                        if( ! $this->movement_model->add($move_out))
                        {
                          $sc = FALSE;
                          $this->error = "Failed to insert stock movement out for {$ro->ItemCode} : {$ro->BatchNum}";
                        }

                        if($sc === TRUE)
                        {
                          $move_in = array(
                            'reference' => $code,
                            'warehouse_code' => $ro->toWhsCode,
                            'zone_code' => $ro->toBinCode,
                            'product_code' => $ro->ItemCode,
                            'batchNum' => $ro->BatchNum,
                            'move_in' => $ro->Qty,
                            'move_out' => 0
                          );

                          if( ! $this->movement_model->add($move_in))
                          {
                            $sc = FALSE;
                            $this->error = "Failed to insert stock movement in for {$ro->ItemCode} : {$ro->BatchNum}";
                          }
                        }
                      }
                    }
                  }

                  if(empty($rs->batchRows))
                  {
                    $move_out = array(
                      'reference' => $code,
                      'warehouse_code' => $rs->fromWhsCode,
                      'zone_code' => $rs->fromBinCode,
                      'product_code' => $rs->ItemCode,
                      'move_in' => 0,
                      'move_out' => $rs->Qty
                    );

                    if( ! $this->movement_model->add($move_out))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to insert stock movement out for {$rs->ItemCode}";
                    }

                    if($sc === TRUE)
                    {
                      $move_in = array(
                        'reference' => $code,
                        'warehouse_code' => $rs->toWhsCode,
                        'zone_code' => $rs->toBinCode,
                        'product_code' => $rs->ItemCode,
                        'move_in' => $rs->Qty,
                        'move_out' => 0
                      );

                      if( ! $this->movement_model->add($move_in))
                      {
                        $sc = FALSE;
                        $this->error = "Failed to insert stock movement in for {$rs->ItemCode}";
                      }
                    }
                  }

                } //-- foreach details

                if($sc === TRUE)
                {
                  $arr = array(
                    'Status' => 'C'
                  );

                  if( ! $this->production_transfer_model->update_batches($code, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to update batch rows status";
                  }
                }

                if($sc === TRUE)
                {
                  $arr = array(
                    'LineStatus' => 'C'
                  );

                  if( ! $this->production_transfer_model->update_details($code, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to update item rows status";
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
                if(is_true(getConfig('SAP_API')))
                {
                  $this->load->library('sap_api');

                  if( ! $this->sap_api->exportProductionTransfer($code))
                  {
                    $sc = FALSE;
                    $ex = 1;
                    $this->error = "Create Document success but send data to SAP failed";
                  }
                }
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบรายการโอนย้าย";
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
        $doc = $this->production_transfer_model->get($code);

        if( ! empty($doc))
        {
          if($doc->Status != 'D')
          {
            if($this->production_transfer_model->is_exists_in_sap($code))
            {
              $sc = FALSE;
              $this->error = "เอกสารนี้เข้าระบบ SAP แล้ว หากต้องการแก้ไข กรุณายกเลิกเอกสารบน SAP ก่อน";
            }

            if($sc === TRUE)
            {
              $this->db->trans_begin();

              //--- set cancel batch
              if( ! $this->production_transfer_model->update_batches($code, ['Status' => 'D']))
              {
                $sc = FALSE;
                $this->error = "Failed to update batch rows status";
              }

              if($sc === TRUE)
              {
                if( ! $this->production_transfer_model->update_details($code, ['LineStatus' => 'D']))
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

                if( ! $this->production_transfer_model->update($code, $arr))
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


  public function rollback()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->production_transfer_model->get($code);

      if( ! empty($doc))
      {
        if($doc->Status == 'D')
        {
          $sc = FALSE;
          $this->error = "Document already Canceled cannot be rollback";
        }

        if($sc === TRUE)
        {
          if($doc->Status == 'R' OR $doc->Status == 'C')
          {
            if($doc->Status == 'C')
            {
              if($this->production_transfer_model->is_exists_in_sap($code))
              {
                $sc = FALSE;
                $this->error = "เอกสารเข้า SAP แล้ว หากต้องการย้อนสถานะ กรุณายกเลิกเอกสารบน SAP ก่อน";
              }
            }

            $this->db->trans_begin();

            if($sc === TRUE && $doc->Status == 'C')
            {
              if( ! $this->movement_model->drop_movement($code))
              {
                $sc = FALSE;
                $this->error = "Failed to remove movement";
              }
            }

            if($sc === TRUE)
            {
              $arr = array('LineStatus' => 'O');

              if( ! $this->production_transfer_model->update_details($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update item rows status";
              }
            }

            if($sc === TRUE)
            {
              $arr = array(
                'Status' => 'P',
                'is_exported' => 'N',
                'inv_code' => NULL,
                'update_user' => $this->_user->uname,
                'date_upd' => now()
              );

              if( ! $this->production_transfer_model->update($code, $arr))
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

    $this->_response($sc);
  }


  public function print_pick_list($code)
  {
    $doc = $this->production_transfer_model->get($code);

    if( ! empty($doc))
    {
      $this->load->library('printer');

      $details = $this->production_transfer_model->get_details($code);

      if( ! empty($details))
      {
        $no = 1;

        foreach($details as $rs)
        {
          $rs->batchRows = $this->production_transfer_model->get_batch_rows($rs->id);
        }
      }

      $ds = array(
        'doc' => $doc,
        'details' => $details
      );

      $this->load->view('print/print_transfer_pick_list', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function do_export()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->production_transfer_model->get($code);

      if( ! empty($doc))
      {
        if($doc->Status == 'C')
        {
          if(is_true(getConfig('SAP_API')))
          {
            $this->load->library('sap_api');

            if( ! $this->sap_api->exportProductionTransfer($code))
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


  public function get_vender_code_and_name()
  {
    $ds = [];

    $txt = trim($_REQUEST['term']);

    $qr = "SELECT CardCode, CardName FROM OCRD WHERE CardCode LIKE N'%{$txt}%' OR CardName LIKE N'%{$txt}%' ORDER BY CardCode ASC OFFSET 0 ROWS FETCH NEXT 50 ROWS ONLY";

    $rs = $this->ms->query($qr);

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $ro)
      {
        $ds[] = $ro->CardCode.' | '.$ro->CardName;
      }
    }
    else
    {
      $ds[] = "not found";
    }

    echo json_encode($ds);
  }


  public function get_production_order_code($status = 'R')
  {
    $ds = [];

    $txt = trim($_REQUEST['term']);

    $qr  = "SELECT TOP(100) DocNum, ItemCode, Status FROM OWOR WHERE Status != 'C' ";

    if($status == 'R' OR $status == 'L')
    {
      $qr .= "AND Status = '{$status}' ";
    }
    else
    {
      $qr .= "AND Status IN('R', 'L') ";
    }

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
        $ds[] = $rs->DocNum.' | '.$rs->ItemCode.' | '.($rs->Status == 'R' ? 'Released' : 'Closed');
      }
    }
    else
    {
      $ds[] = 'Not found';
    }

    echo json_encode($ds);
  }


  public function get_production_order_details()
  {
    $sc = TRUE;
    $code = $this->input->post('baseCode');
    $toWhs = $this->input->post('toWhsCode');
    $binCode = $this->input->post('toBinCode');

    if( ! empty($code))
    {
      $pdo = $this->production_transfer_model->get_production_order($code);

      if( ! empty($pdo))
      {
        $details = $this->production_transfer_model->get_production_order_details($pdo->DocEntry);

        if(! empty($details))
        {
          foreach($details as $rs)
          {
            $balance = $rs->PlannedQty - $rs->IssuedQty;
            $instock = $this->stock_model->get_item_stock($rs->ItemCode,  $rs->wareHouse);

            $rs->uid = genUid();
            $rs->fromWhsCode = $rs->wareHouse;
            $rs->toWhsCode = $toWhs;
            $rs->toBinCode = $binCode;
            $rs->PlannedQty = number($rs->PlannedQty, 2);
            $rs->IssuedQty = number($rs->IssuedQty, 2);
            $rs->BalanceQty = $balance > 0 ? number($balance, 2) : 0.00;
            $rs->InStock = number($instock, 2);
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
      'data' => $sc === TRUE ? $details : NULL
    );

    echo json_encode($arr);
  }


  public function get_item_batch_rows()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('filter'));

    $data = [];

    if( ! empty($ds) && ! empty($ds->ItemCode))
    {
      $filter = array(
        'WhsCode' => $ds->WhsCode,
        'BatchNum' => $ds->BatchNum,
        'BatchAttr1' => $ds->BatchAttr1,
        'BatchAttr2' => $ds->BatchAttr2
      );

      $rows = $this->production_transfer_model->get_item_batch_rows($ds->ItemCode, $filter);

      if( ! empty($rows))
      {
        foreach($rows as $rs)
        {
          $uid = md5($rs->ItemCode.$rs->BatchNum.$rs->BinCode);
          $rs->uid = substr($uid, -13);
          $rs->Qty = number($rs->Qty, 2);
          $data[] = $rs;
        }
      }
      else
      {
        $data[] = ['nodata' => 'nodata'];
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
      'data' => $data
    );

    echo json_encode($arr);
  }


  public function get_item_code_and_nam()
  {
    $ds = [];
    $txt = trim($_REQUEST['term']);

    $qr  = "SELECT i.ItemCode, i.ItemName, i.ManBtchNum, i.InvntryUom AS Uom, i.IUoMEntry AS UomEntry, u.UomCode ";
    $qr .= "FROM OITM AS i LEFT JOIN OUOM AS u ON i.IUoMEntry = u.UomEntry ";
    $qr .= "WHERE 1=1 ";

    if($txt != '*')
    {
      $qr .= "AND (i.ItemCode LIKE '%{$txt}%' OR i.ItemName LIKE N'%{$txt}%') ";
    }

    $qr .= "ORDER BY i.ItemCode ASC OFFSET 0 ROWS FETCH NEXT 100 ROWS ONLY";

    $rs = $this->ms->query($qr);

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $ds[] = array(
          'label' => $rd->ItemCode.' | '.$rd->ItemName,
          'ItemCode' => $rd->ItemCode,
          'ItemName' => $rd->ItemName,
          'hasBatch' => $rd->ManBtchNum,
          'Uom' => $rd->Uom,
          'UomEntry' => $rd->UomEntry,
          'UomCode' => $rd->UomCode
        );
      }
    }
    else
    {
      $ds = ['notfount'];
    }

    echo json_encode($ds);
  }


  public function get_available_stock()
  {
    $ItemCode = $this->input->post('ItemCode');
    $WhsCode = $this->input->post('WhsCode');
    $BinCode = $this->input->post('BinCode');

    $qty = $this->stock_model->get_item_stock($ItemCode, $WhsCode, $BinCode);

    $arr = array(
      'status' => 'success',
      'available' => number($qty, 2)
    );

    echo json_encode($arr);
  }


  public function get_bin_item_stock()
  {
    $sc = TRUE;
    $ds = [];

    $WhsCode = trim($this->input->post('WhsCode'));
    $ItemCode = trim($this->input->post('ItemCode'));

    $stock = $this->stock_model->get_available_stock_in_zone($ItemCode, $WhsCode);

    if( ! empty($stock))
    {
      $no = 1;

      foreach($stock as $rs)
      {
        $ds[] = array(
          'no' => $no,
          'uid' => genUid(),
          'WhsCode' => $rs->WhsCode,
          'BinCode' => $rs->BinCode,
          'Qty' => number($rs->OnHandQty, 2)
        );

        $no++;
      }
    }
    else
    {
      $ds[] = ['nodata' => 'nodata'];
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );

    echo json_encode($arr);
  }


  public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_PRODUCTION_TRANSFER');
    $run_digit = getConfig('RUN_DIGIT_PRODUCTION_TRANSFER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->production_transfer_model->get_max_code($pre);

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
      'tr_code',
      'tr_reference',
      'tr_orderRef',
      'tr_from_date',
      'tr_to_date',
      'tr_fromWhsCode',
      'tr_toWhsCode',
      'tr_user',
      'tr_status',
      'tr_is_exported'
    );

    return clear_filter($filter);
  }

} // end class
?>
