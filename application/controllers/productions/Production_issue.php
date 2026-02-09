<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Production_issue extends PS_Controller
{
  public $menu_code = 'PDGDIS';
	public $menu_group_code = 'PD';
  public $menu_sub_group_code = '';
	public $title = 'Goods Issue For Production';
  public $segment = 4;
  public $error;


  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'productions/production_issue';
    $this->load->model('productions/production_issue_model');
    $this->load->model('masters/products_model');
    $this->load->model('stock/stock_model');
    $this->load->model('inventory/movement_model');
    $this->load->helper('warehouse');
    $this->load->helper('production_issue');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'issue_code', ''),
      'reference' => get_filter('reference', 'issue_reference', ''),
      'order_ref' => get_filter('order_ref', 'issue_order_ref', ''),
      'from_date' => get_filter('from_date', 'issue_from_date', ''),
      'to_date' => get_filter('to_date', 'issue_to_date', ''),
      'user' => get_filter('user', 'issue_user', 'all'),
      'status' => get_filter('status', 'issue_status', 'all'),
      'is_exported' => get_filter('is_exported', 'issue_is_exported', 'all')
    );


    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->production_issue_model->count_rows($filter);
      $filter['data'] = $this->production_issue_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

      $init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('productions/production_issue/production_issue_list', $filter);
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
      $docNum = $this->production_issue_model->get_production_order_doc_num($code);

      if( ! empty($docNum))
      {
        $pdo = $this->production_issue_model->get_production_order($docNum);

        if( ! empty($pdo))
        {
          $ds = array(
            'code' => $pdo->DocNum,
            'orderRef' => $code,
            'ItemCode' => $pdo->ItemCode,
            'details' => []
          );

          // $details = $this->production_issue_model->get_production_order_details($pdo->DocEntry);
          //
          // if( ! empty($details))
          // {
          //   foreach($details as $rs)
          //   {
          //     $balance = $rs->PlannedQty - $rs->IssuedQty;
          //     $instock = $this->stock_model->get_item_stock($rs->ItemCode,  $rs->wareHouse);
          //
          //     $rs->uid = genUid();
          //     $rs->fromWhsCode = $rs->wareHouse;
          //     $rs->PlannedQty = number($rs->PlannedQty, 2);
          //     $rs->IssuedQty = number($rs->IssuedQty, 2);
          //     $rs->BalanceQty = $balance > 0 ? number($balance, 2) : 0.00;
          //     $rs->InStock = number($instock, 2);
          //     $rs->hasBatch = $rs->ManBtchNum == 'Y' ? TRUE : FALSE;
          //   }
          //
          //   $ds['details'] = $details;
          // }
        }
      }
    }

    $this->load->view('productions/production_issue/production_issue_add', $ds);
  }


  public function add()
  {
    $sc = TRUE;
    $code = NULL;
    $ex = 0;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->date_add) && ! empty($ds->baseRef))
    {
      if($ds->type != 'P' && empty($ds->rows))
      {
        $sc = FALSE;
        $this->error = "ไม่พบรายการสินค้า";
      }

      if($sc === TRUE && ! empty($ds->rows))
      {
        foreach($ds->rows as $rs)
        {
          $line = $rs->LineNum + 1;

          if($sc === FALSE) { break; }

          if($sc === TRUE && $rs->Qty <= 0)
          {
            $sc = FALSE;
            $this->error = "Invalid Quantity for line item {$rs->ItemCode}";
          }

          if($sc === TRUE && empty($rs->WhsCode))
          {
            $sc = FALSE;
            $this->error = "Missing Warehouse for item {$rs->ItemCode} at Line {$line}";
          }

          if($sc === TRUE && ! $rs->hasBatch && empty($rs->batchRows) && empty($rs->BinCode))
          {
            $sc = FALSE;
            $this->error = "Missing Bin Location for line item {$rs->ItemCode} at Line {$line}";
          }

          if($sc === TRUE && ! empty($rs->batchRows))
          {
            foreach($rs->batchRows as $ro)
            {
              if($sc === FALSE) { break; }

              if($sc === TRUE && empty($ro->BatchNum))
              {
                $sc = FALSE;
                $this->error = "Batch Number is required for item {$ro->ItemCode}";
              }

              if($sc === TRUE && empty($ro->WhsCode))
              {
                $sc = FALSE;
                $this->error = "Missing Warehouse for batch row {$ro->ItemCode} - {$ro->BatchNum}";
              }

              if($sc === TRUE && empty($ro->BinCode))
              {
                $sc = FALSE;
                $this->error = "Missing Bin Location for batch row {$ro->ItemCode} - {$ro->BatchNum}";
              }

              if($sc === TRUE && $ro->Qty <= 0)
              {
                $sc = FALSE;
                $this->error = "Batch Quantity is invalid {$ro->ItemCode} - {$ro->BatchNum}";
              }

              if($sc === TRUE)
              {
                $instock = $this->stock_model->get_item_batch_qty($ro->ItemCode, $ro->BatchNum, $ro->WhsCode, $ro->BinCode);

                if($instock < $ro->Qty)
                {
                  $sc = FALSE;
                  $this->error = "สต็อกคงเหลือไม่เพียงพอ Item: {$ro->ItemCode}, Batch No: {$ro->BatchNum}, Zone: {$ro->BinCode}";
                }
              }
            }
          }

          if($sc === TRUE && empty($rs->batchRows))
          {
            $instock = $this->stock_model->get_item_stock($rs->ItemCode, $rs->WhsCode, $rs->BinCode);

            if($instock < $rs->Qty)
            {
              $sc = FALSE;
              $this->error = "สต็อกคงเหลือไม่เพียงพอ Item: {$ro->ItemCode}, Zone: {$ro->BinCode}";
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
          'ItemCode' => get_null($ds->ItemCode),
          'orderRef' => get_null($ds->orderRef),
          'externalRef' => get_null($ds->externalRef),
          'date_add' => $date_add,
          'shipped_date' => $shipped_date,
          'user' => $this->_user->uname,
          'remark' => get_null($ds->remark),
          'Status' => $ds->type
        );

        $this->db->trans_begin();

        if( ! $this->production_issue_model->add($arr))
        {
          $sc = FALSE;
          set_error('insert');
        }

        if($sc === TRUE)
        {
          foreach($ds->rows as $rs)
          {
            if($sc === FALSE) { break;}

            $arr = array(
              'issue_code' => $code,
              'LineNum' => $rs->LineNum,
              'BaseType' => $rs->BaseType,
              'BaseRef' => $rs->BaseRef,
              'BaseEntry' => $rs->BaseEntry,
              'BaseLine' => $rs->BaseLine,
              'ItemCode' => $rs->ItemCode,
              'ItemName' => $rs->ItemName,
              'WhsCode' => $rs->WhsCode,
              'BinCode' => get_null($rs->BinCode),
              'Qty' => $rs->Qty,
              'UomEntry' => $rs->UomEntry,
              'UomCode' => $rs->UomCode,
              'unitMsr' => $rs->Uom,
              'LineStatus' => $ds->type,
              'hasBatch' => $rs->hasBatch,
              'uid' => $rs->uid
            );

            $id = $this->production_issue_model->add_detail($arr);

            if($id)
            {
              if( ! empty($rs->batchRows))
              {
                foreach($rs->batchRows as $ro)
                {
                  if($sc === FALSE) { break;}

                  $br = array(
                    'issue_code' => $code,
                    'issue_detail_id' => $id,
                    'ItemCode' => $ro->ItemCode,
                    'ItemName' => $ro->ItemName,
                    'BatchNum' => $ro->BatchNum,
                    'BatchAttr1' => get_null($ro->BatchAttr1),
                    'BatchAttr2' => get_null($ro->BatchAttr2),
                    'Qty' => $ro->Qty,
                    'WhsCode' => $ro->WhsCode,
                    'BinCode' => $ro->BinCode,
                    'uid' => $ro->uid
                  );

                  if( ! $this->production_issue_model->add_batch_rows($br))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to add batch row for line item {$rs->ItemCode}";
                  }

                  if($sc === TRUE && $ds->type == 'C')
                  {
                    $move_out = array(
                      'reference' => $code,
                      'warehouse_code' => $ro->WhsCode,
                      'zone_code' => $ro->BinCode,
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
                  }
                } // end foreach
              }
              else
              {
                if($ds->type == 'C')
                {
                  $move_out = array(
                    'reference' => $code,
                    'warehouse_code' => $rs->WhsCode,
                    'zone_code' => $rs->BinCode,
                    'product_code' => $rs->ItemCode,
                    'move_in' => 0,
                    'move_out' => $rs->Qty
                  );

                  if( ! $this->movement_model->add($move_out))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert stock movement out for {$rs->ItemCode}";
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
      }

      if($sc === TRUE && $ds->type == 'C')
      {
        if(is_true(getConfig('SAP_API')))
        {
          $this->load->library('sap_api');

          if( ! $this->sap_api->exportProductionIssue($code))
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


  public function edit($code)
  {
    $this->load->helper('zone');

    $doc = $this->production_issue_model->get($code);

    if( ! empty($doc))
    {
      if($doc->Status == 'P')
      {
        $doc->toBinName = empty($doc->toBinCode) ? NULL : zone_name($doc->toBinCode);

        $details = $this->production_issue_model->get_details($code);

        if( ! empty($details))
        {
          $no = 1;

          foreach($details as $rs)
          {
            $rs->InStock = $this->stock_model->get_item_stock($rs->ItemCode, $rs->WhsCode, $rs->BinCode);
            $rs->batchRows = $this->production_issue_model->get_batch_rows($rs->id);
          }
        }


        $ds = array(
          'doc' => $doc,
          'details' => $details
        );

        $this->load->view('productions/production_issue/production_issue_edit', $ds);
      }
      else
      {
        redirect($this->home.'/view_detail/'.$code);
      }
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
    $ex = 0;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->date_add) && ! empty($ds->baseRef))
    {
      if($ds->type != 'P' && empty($ds->rows))
      {
        $sc = FALSE;
        $this->error = "ไม่พบรายการสินค้า";
      }

      if($sc === TRUE)
      {
        $doc = $this->production_issue_model->get($ds->code);

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

      if($sc === TRUE && ! empty($ds->rows))
      {
        foreach($ds->rows as $rs)
        {
          $line = $rs->LineNum + 1;

          if($sc === FALSE) { break; }

          if($sc === TRUE && $rs->Qty <= 0)
          {
            $sc = FALSE;
            $this->error = "Invalid Quantity for line item {$rs->ItemCode}";
          }

          if($sc === TRUE && empty($rs->WhsCode))
          {
            $sc = FALSE;
            $this->error = "Missing Warehouse for item {$rs->ItemCode} at Line {$line}";
          }

          if($sc === TRUE && ! $rs->hasBatch && empty($rs->batchRows) && empty($rs->BinCode))
          {
            $sc = FALSE;
            $this->error = "Missing Bin Location for line item {$rs->ItemCode} at Line {$line}";
          }

          if($sc === TRUE && ! empty($rs->batchRows))
          {
            foreach($rs->batchRows as $ro)
            {
              if($sc === FALSE) { break; }

              if($sc === TRUE && empty($ro->BatchNum))
              {
                $sc = FALSE;
                $this->error = "Batch Number is required for item {$ro->ItemCode}";
              }

              if($sc === TRUE && empty($ro->WhsCode))
              {
                $sc = FALSE;
                $this->error = "Missing Warehouse for batch row {$ro->ItemCode} - {$ro->BatchNum}";
              }

              if($sc === TRUE && empty($ro->BinCode))
              {
                $sc = FALSE;
                $this->error = "Missing Bin Location for batch row {$ro->ItemCode} - {$ro->BatchNum}";
              }

              if($sc === TRUE && $ro->Qty <= 0)
              {
                $sc = FALSE;
                $this->error = "Batch Quantity is invalid {$ro->ItemCode} - {$ro->BatchNum}";
              }

              if($sc === TRUE)
              {
                $instock = $this->stock_model->get_item_batch_qty($ro->ItemCode, $ro->BatchNum, $ro->WhsCode, $ro->BinCode);

                if($instock < $ro->Qty)
                {
                  $sc = FALSE;
                  $this->error = "สต็อกคงเหลือไม่เพียงพอ Item: {$ro->ItemCode}, Batch No: {$ro->BatchNum}, Zone: {$ro->BinCode}";
                }
              }
            }
          }

          if($sc === TRUE && empty($rs->batchRows))
          {
            $instock = $this->stock_model->get_item_stock($rs->ItemCode, $rs->WhsCode, $rs->BinCode);

            if($instock < $rs->Qty)
            {
              $sc = FALSE;
              $this->error = "สต็อกคงเหลือไม่เพียงพอ Item: {$rs->ItemCode}, Zone: {$rs->BinCode}";
            }
          }
        }
      }

      if($sc === TRUE)
      {
        $date_add = db_date($ds->date_add);
        $shipped_date = db_date($ds->shipped_date);

        $code = $ds->code;

        $arr = array(
          'code' => $code,
          'reference' => $ds->baseRef,
          'ItemCode' => get_null($ds->ItemCode),
          'orderRef' => get_null($ds->orderRef),
          'externalRef' => get_null($ds->externalRef),
          'date_add' => $date_add,
          'shipped_date' => $shipped_date,
          'update_user' => $this->_user->uname,
          'date_upd' => now(),
          'remark' => get_null($ds->remark),
          'Status' => $ds->type
        );

        $this->db->trans_begin();

        if( ! $this->production_issue_model->update($code, $arr))
        {
          $sc = FALSE;
          set_error('update');
        }

        if($sc === TRUE)
        {
          if( ! $this->production_issue_model->drop_all_batch($code))
          {
            $sc = FALSE;
            $this->error = "Failed to remove prev batch data";
          }
        }

        if($sc === TRUE)
        {
          if( ! $this->production_issue_model->drop_all_details($code))
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
              'issue_code' => $code,
              'LineNum' => $rs->LineNum,
              'BaseType' => $rs->BaseType,
              'BaseRef' => $rs->BaseRef,
              'BaseEntry' => $rs->BaseEntry,
              'BaseLine' => $rs->BaseLine,
              'ItemCode' => $rs->ItemCode,
              'ItemName' => $rs->ItemName,
              'WhsCode' => $rs->WhsCode,
              'BinCode' => get_null($rs->BinCode),
              'Qty' => $rs->Qty,
              'UomEntry' => $rs->UomEntry,
              'UomCode' => $rs->UomCode,
              'unitMsr' => $rs->Uom,
              'LineStatus' => $ds->type,
              'hasBatch' => $rs->hasBatch,
              'uid' => $rs->uid
            );

            $id = $this->production_issue_model->add_detail($arr);

            if($id)
            {
              if( ! empty($rs->batchRows))
              {
                foreach($rs->batchRows as $ro)
                {
                  if($sc === FALSE) { break;}

                  $br = array(
                    'issue_code' => $code,
                    'issue_detail_id' => $id,
                    'ItemCode' => $ro->ItemCode,
                    'ItemName' => $ro->ItemName,
                    'BatchNum' => $ro->BatchNum,
                    'BatchAttr1' => get_null($ro->BatchAttr1),
                    'BatchAttr2' => get_null($ro->BatchAttr2),
                    'Qty' => $ro->Qty,
                    'WhsCode' => $ro->WhsCode,
                    'BinCode' => $ro->BinCode,
                    'uid' => $ro->uid
                  );

                  if( ! $this->production_issue_model->add_batch_rows($br))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to add batch row for line item {$rs->ItemCode}";
                  }

                  if($sc === TRUE && $ds->type == 'C')
                  {
                    $move_out = array(
                      'reference' => $code,
                      'warehouse_code' => $ro->WhsCode,
                      'zone_code' => $ro->BinCode,
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
                  }
                } // end foreach
              }
              else
              {
                if($ds->type == 'C')
                {
                  $move_out = array(
                    'reference' => $code,
                    'warehouse_code' => $rs->WhsCode,
                    'zone_code' => $rs->BinCode,
                    'product_code' => $rs->ItemCode,
                    'move_in' => 0,
                    'move_out' => $rs->Qty
                  );

                  if( ! $this->movement_model->add($move_out))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert stock movement out for {$rs->ItemCode}";
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
      }

      if($sc === TRUE && $ds->type == 'C')
      {
        if(is_true(getConfig('SAP_API')))
        {
          $this->load->library('sap_api');

          if( ! $this->sap_api->exportProductionIssue($code))
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

    $doc = $this->production_issue_model->get($code);

    if( ! empty($doc))
    {
      $details = $this->production_issue_model->get_details($code);

      if( ! empty($details))
      {
        $no = 1;

        foreach($details as $rs)
        {
          $rs->batchRows = $this->production_issue_model->get_batch_rows($rs->id);
        }
      }


      $ds = array(
        'doc' => $doc,
        'details' => $details
      );

      $this->load->view('productions/production_issue/production_issue_view', $ds);
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
      $doc = $this->production_issue_model->get($code);

      if(empty($doc))
      {
        $sc = FALSE;
        set_error('notfound');
      }


      if($sc === TRUE)
      {
        if($doc->Status == 'C' OR $doc->Status == 'D')
        {
          $sc = FALSE;
          $this->error = $doc->Status == 'D' ? 'Document already canceled cannot be change' : 'Document already Closed cannot be change';
        }
      }

      if($sc === TRUE)
      {
        $details = $this->production_issue_model->get_details($code);

        if(empty($details))
        {
          $sc = FALSE;
          $this->error = "ไม่พบรายการสินค้า";
        }

        if($sc === TRUE && ! empty($details))
        {
          foreach($details as $rs)
          {
            if($sc === FALSE) { break; }

            $Qty = 0;


            if($sc === TRUE && $rs->Qty <= 0)
            {
              $sc = FALSE;
              $this->error = "Invalid Item Qty for {$rs->ItemCode}";
            }

            if($rs->hasBatch == 0)
            {
              if($sc === TRUE && (empty($rs->WhsCode) OR empty($rs->BinCode)))
              {
                $sc = FALSE;
                $this->error = "Missing Warehouse or Bin Location for {$rs->ItemCode}";
              }

              if($sc === TRUE)
              {
                $instock = $this->stock_model->get_item_stock($rs->ItemCode, $rs->WhsCode, $rs->BinCode);

                if($rs->Qty < $instock)
                {
                  $sc = FALSE;
                  $this->error = "สต็อกคงเหลือไม่เพียงพอ Item: {$rs->ItemCode}, Zone: {$rs->BinCode}";
                }
              }
            }

            if($rs->hasBatch == 1)
            {
              $batchRows = $this->production_issue_model->get_batch_rows($rs->id);

              if(empty($batchRows))
              {
                $sc = FALSE;
                $this->error = "Batch Number is required for item : {$rs->ItemCode}";
              }

              if($sc === TRUE)
              {
                foreach($batchRows as $ro)
                {
                  if($sc === FALSE) { break; }

                  if($sc === TRUE && empty($ro->BatchNum))
                  {
                    $sc = FALSE;
                    $this->error = "Batch Number is required for item {$ro->ItemCode}";
                  }

                  if($sc === TRUE && empty($ro->WhsCode))
                  {
                    $sc = FALSE;
                    $this->error = "Missing Warehouse for batch row {$ro->ItemCode} - {$ro->BatchNum}";
                  }

                  if($sc === TRUE && empty($ro->BinCode))
                  {
                    $sc = FALSE;
                    $this->error = "Missing Bin Location for batch row {$ro->ItemCode} - {$ro->BatchNum}";
                  }

                  if($sc === TRUE && $ro->Qty <= 0)
                  {
                    $sc = FALSE;
                    $this->error = "Batch Quantity is invalid {$ro->ItemCode} - {$ro->BatchNum}";
                  }

                  if($sc === TRUE)
                  {
                    $instock = $this->stock_model->get_item_batch_qty($ro->ItemCode, $ro->BatchNum, $ro->WhsCode, $ro->BinCode);

                    if($instock < $ro->Qty)
                    {
                      $sc = FALSE;
                      $this->error = "สต็อกคงเหลือไม่เพียงพอ Item: {$ro->ItemCode}, Batch No: {$ro->BatchNum}, Zone: {$ro->BinCode}";
                    }
                  }

                  if($sc === TRUE)
                  {
                    $Qty += $ro->Qty;
                  }
                }

                $rs->batchRows = $batchRows;
              }
            }

            if($sc === TRUE && $rs->hasBatch == 1)
            {
              $this->production_issue_model->update_detail($rs->id, ['Qty' => $Qty]);
            }
          }
        }
      }

      if($sc === TRUE)
      {
        $arr = array(
          'Status' => 'C',
          'update_user' => $this->_user->uname,
          'date_upd' => now()
        );

        $this->db->trans_begin();

        if( ! $this->production_issue_model->update($code, $arr))
        {
          $sc = FALSE;
          set_error('update');
        }

        if($sc === TRUE)
        {
          foreach($details as $rs)
          {
            if($sc === FALSE) { break;}

            if( ! empty($rs->hasBatch) && ! empty($rs->batchRows))
            {
              foreach($rs->batchRows as $ro)
              {
                if($sc === FALSE) { break; }

                $move_out = array(
                  'reference' => $code,
                  'warehouse_code' => $ro->WhsCode,
                  'zone_code' => $ro->BinCode,
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
              } // end foreach
            }

            if(empty($rs->hasBatch) && empty($rs->batchRows))
            {
              $move_out = array(
                'reference' => $code,
                'warehouse_code' => $rs->WhsCode,
                'zone_code' => $rs->BinCode,
                'product_code' => $rs->ItemCode,
                'move_in' => 0,
                'move_out' => $rs->Qty
              );

              if( ! $this->movement_model->add($move_out))
              {
                $sc = FALSE;
                $this->error = "Failed to insert stock movement out for {$rs->ItemCode}";
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

      if($sc === TRUE)
      {
        if(is_true(getConfig('SAP_API')))
        {
          $this->load->library('sap_api');

          if( ! $this->sap_api->exportProductionIssue($code))
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
        $doc = $this->production_issue_model->get($code);

        if( ! empty($doc))
        {
          if($doc->Status != 'D')
          {
            if($this->production_issue_model->is_exists_in_sap($code))
            {
              $sc = FALSE;
              $this->error = "เอกสารนี้เข้าระบบ SAP แล้ว หากต้องการแก้ไข กรุณายกเลิกเอกสารบน SAP ก่อน";
            }

            if($sc === TRUE)
            {
              $this->db->trans_begin();

              //--- set cancel batch
              if( ! $this->production_issue_model->update_batches($code, ['Status' => 'D']))
              {
                $sc = FALSE;
                $this->error = "Failed to update batch rows status";
              }

              if($sc === TRUE)
              {
                if( ! $this->production_issue_model->update_details($code, ['LineStatus' => 'D']))
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

                if( ! $this->production_issue_model->update($code, $arr))
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

    if($this->pm->can_delete)
    {
      $code = $this->input->post('code');

      if( ! empty($code))
      {
        $doc = $this->production_issue_model->get($code);

        if( ! empty($doc))
        {
          if($doc->Status == 'D')
          {
            $sc = FALSE;
            $this->error = "Document already Canceled cannot be rollback";
          }

          if($sc === TRUE && $doc->Status == 'C')
          {
            if($this->production_issue_model->is_exists_in_sap($code))
            {
              $sc = FALSE;
              $this->error = "เอกสารนี้เข้าระบบ SAP แล้ว หากต้องการแก้ไข กรุณายกเลิกเอกสารบน SAP ก่อน";
            }

            if($sc === TRUE)
            {
              $this->db->trans_begin();

              //--- set cancel batch
              if( ! $this->production_issue_model->update_batches($code, ['Status' => 'P']))
              {
                $sc = FALSE;
                $this->error = "Failed to update batch rows status";
              }

              if($sc === TRUE)
              {
                if( ! $this->production_issue_model->update_details($code, ['LineStatus' => 'P']))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update item rows status";
                }
              }

              if($sc === TRUE)
              {
                $arr = array(
                  'Status' => 'P',
                  'update_user' => $this->_user->uname,
                  'is_exported' => 'N',
                  'inv_code' => NULL,
                  'date_upd' => now()
                );

                if( ! $this->production_issue_model->update($code, $arr))
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


  public function print_pick_list($code)
  {
    $doc = $this->production_issue_model->get($code);

    if( ! empty($doc))
    {
      $this->load->library('printer');

      $details = $this->production_issue_model->get_details($code);

      if( ! empty($details))
      {
        $no = 1;

        foreach($details as $rs)
        {
          $rs->batchRows = $this->production_issue_model->get_batch_rows($rs->id);
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
      $doc = $this->production_issue_model->get($code);

      if( ! empty($doc))
      {
        if($doc->Status == 'C')
        {
          if(is_true(getConfig('SAP_API')))
          {
            $this->load->library('sap_api');

            if( ! $this->sap_api->exportProductionIssue($code))
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

    if( ! empty($code))
    {
      $pdo = $this->production_issue_model->get_production_order($code);

      if( ! empty($pdo))
      {
        $details = $this->production_issue_model->get_production_order_details($pdo->DocEntry);

        if(! empty($details))
        {
          foreach($details as $rs)
          {
            $balance = $rs->PlannedQty - $rs->IssuedQty;
            $instock = $this->stock_model->get_item_stock($rs->ItemCode,  $rs->wareHouse);

            $rs->uid = genUid();
            $rs->BaseType = 202;
            $rs->whsCode = $rs->wareHouse;
            $rs->PlannedQty = number($rs->PlannedQty, 2);
            $rs->IssuedQty = number($rs->IssuedQty, 2);
            $rs->OpenQty = $balance > 0 ? number($balance, 2) : 0.00;
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

      $rows = $this->production_issue_model->get_item_batch_rows($ds->ItemCode, $filter);

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

    $this->ms
    ->select('i.ItemCode, i.ItemName, i.ManBtchNum, i.InvntryUom AS Uom, i.IUoMEntry AS UomEntry, u.UomCode')
    ->from('OITM AS i')
    ->join('OUOM AS u', 'i.IUoMEntry = u.UomEntry', 'left');

    if($txt != '*')
    {
      $this->ms->like('i.ItemCode', $txt);
    }

    $rs = $this->ms->order_by('i.ItemCode', 'ASC')->limit(100)->get();

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

    $qty = $this->stock_model->get_item_stock($ItemCode, $WhsCode);

    $arr = array(
      'status' => 'success',
      'available' => number($qty, 2)
    );

    echo json_encode($arr);
  }


  public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_PRODUCTION_ISSUE');
    $run_digit = getConfig('RUN_DIGIT_PRODUCTION_ISSUE');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->production_issue_model->get_max_code($pre);

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
      'issue_code',
      'issue_reference',
      'issue_from_date',
      'issue_to_date',
      'issue_user',
      'issue_status',
      'issue_is_exported'
    );

    return clear_filter($filter);
  }

} // end class
?>
