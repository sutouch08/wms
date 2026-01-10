<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Production_order extends PS_Controller
{
  public $menu_code = 'PDORDS';
	public $menu_group_code = 'PD';
  public $menu_sub_group_code = '';
	public $title = 'Production Order';
  public $segment = 4;
  public $error;


  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'productions/production_order';
    $this->load->model('productions/production_order_model');
    $this->load->model('masters/products_model');
    $this->load->model('stock/stock_model');
    $this->load->helper('warehouse');
    $this->load->helper('production_order');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'pdo_code', ''),
      'inv_code' => get_filter('inv_code', 'pdo_inv_code', ''),
      'product_code' => get_filter('product_code', 'pdo_product_code', ''),
      'from_date' => get_filter('from_date', 'order_from_date', ''),
      'to_date' => get_filter('to_date', 'order_to_date', ''),
			'status' => get_filter('status', 'pdo_status', 'all'),
      'is_exported' => get_filter('is_exported', 'pdo_is_exported', 'all')
    );


    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->production_order_model->count_rows($filter);
      $filter['data'] = $this->production_order_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

      $init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('productions/production_order/production_order_list', $filter);
    }
  }


  public function add_new()
  {
    $this->load->view('productions/production_order/production_order_add');
  }


  public function add()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));
    $code = NULL;

    if( ! empty($ds))
    {
      if( ! empty($ds->ItemCode) && ! empty($ds->PostDate) && ! empty($ds->rows))
      {
        $doc_date = db_date($ds->PostDate);
        $due_date = db_date($ds->DueDate);

        $code = $this->get_new_code($doc_date);

        $arr = array(
          'code' => $code,
          'PostDate' => $doc_date,
          'DueDate' => $due_date,
          'ItemCode' => trim($ds->ItemCode),
          'ProdName' => get_null(trim($ds->ItemName)),
          'Status' => $ds->Status,
          'Type' => $ds->Type,
          'PlannedQty' => floatval($ds->PlannedQty),
          'Uom' => $ds->Uom,
          'Warehouse' => $ds->Warehouse,
          'CardCode' => get_null($ds->CardCode),
          'OriginType' => empty($ds->OriginType) ? 'M' : $ds->OriginType,
          'OriginNum' => get_null($ds->OriginNum),
          'OriginAbs' => get_null($ds->OriginAbs),
          'Comments' => get_null($ds->remark),
          'user' => $this->_user->uname
        );

        $this->db->trans_begin();

        $id = $this->production_order_model->add($arr);

        if( ! empty($id))
        {
          foreach($ds->rows as $row)
          {
            if($sc === FALSE) { break; }

            $arr = array(
              'order_id' => $id,
              'order_code' => $code,
              'LineNum' => $row->LineNum,
              'ItemCode' => $row->ItemCode,
              'ItemName' => $row->ItemName,
              'BaseQty' => floatval($row->BaseQty),
              'PlannedQty' => floatval($row->PlannedQty),
              'IssueType' => $row->IssueType,
              'WhsCode' => $row->WhsCode,
              'Uom' => get_null($row->Uom),
              'UomEntry' => get_null($row->UomEntry),
              'UomCode' => get_null($row->UomCode),
              'ItemType' => $row->ItemType,
              'uid' => $row->uid
            );

            if( ! $this->production_order_model->add_detail($arr))
            {
              $sc = FALSE;
              $this->error = "Failed to create item row @ line {$row->LineNum}";
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Failed to create document";
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
        set_error('required');
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
      'code' => $code
    );

    echo json_encode($arr);
  }


  public function edit($code)
  {
    $doc = $this->production_order_model->get($code);

    if( ! empty($doc))
    {
      if($doc->Status == 'P')
      {
        $ds = array(
          'doc' => $doc,
          'details' => $this->production_order_model->get_details($code)
        );

        $this->load->view('productions/production_order/production_order_edit', $ds);
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


  public function update()
  {
    $sc = TRUE;
    $ex = 0;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->ItemCode) && ! empty($ds->rows))
    {
      $doc = $this->production_order_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->Status == 'P')
        {
          $doc_date = db_date($ds->PostDate);
          $due_date = db_date($ds->DueDate);
          $code = $doc->code;

          $arr = array(
            'PostDate' => $doc_date,
            'DueDate' => $due_date,
            'ItemCode' => trim($ds->ItemCode),
            'ProdName' => get_null(trim($ds->ItemName)),
            'Status' => $ds->Status,
            'Type' => $ds->Type,
            'PlannedQty' => floatval($ds->PlannedQty),
            'Uom' => $ds->Uom,
            'Warehouse' => $ds->Warehouse,
            'CardCode' => get_null($ds->CardCode),
            'OriginType' => empty($ds->OriginType) ? 'M' : $ds->OriginType,
            'OriginNum' => get_null($ds->OriginNum),
            'OriginAbs' => get_null($ds->OriginAbs),
            'Comments' => get_null($ds->remark),
            'update_user' => $this->_user->uname,
            'ReleaseDate' => $ds->Status === 'R' ? now() : NULL
          );

          $this->db->trans_begin();

          if($this->production_order_model->update($code, $arr))
          {
            if($this->production_order_model->delete_details($code))
            {
              foreach($ds->rows as $row)
              {
                if($sc === FALSE) { break; }

                $arr = array(
                  'order_id' => $doc->id,
                  'order_code' => $code,
                  'LineNum' => $row->LineNum,
                  'ItemCode' => $row->ItemCode,
                  'ItemName' => $row->ItemName,
                  'BaseQty' => floatval($row->BaseQty),
                  'PlannedQty' => floatval($row->PlannedQty),
                  'IssueType' => $row->IssueType,
                  'WhsCode' => $row->WhsCode,
                  'Uom' => get_null($row->Uom),
                  'UomEntry' => get_null($row->UomEntry),
                  'UomCode' => get_null($row->UomCode),
                  'ItemType' => $row->ItemType,
                  'uid' => $row->uid
                );

                if( ! $this->production_order_model->add_detail($arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to create item row @ line {$row->LineNum}";
                }
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Failed to remove prevoius row data";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Failed to update document";
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
            if($ds->Status === 'R' OR $doc->Status == 'C')
            {
              if(is_true(getConfig('SAP_API')))
              {
                $this->load->library('sap_api');

                if( ! $this->sap_api->exportProductionOrder($code))
                {
                  $ex = 1;
                  $this->error = "Update Success But Failed to send data to SAP";
                }
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
      'message' => $this->error,
      'ex' => $ex
    );

    echo json_encode($arr);
  }


  public function view_detail($code)
  {
    $doc = $this->production_order_model->get($code);

    $ds = array(
      'doc' => $doc,
      'details' => $this->production_order_model->get_details($code),
      'transferRef' => $this->production_order_model->get_transfer_ref($code)
    );

    if( ! empty($doc))
    {
      $this->load->view('productions/production_order/production_order_view_detail', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function close_order()
  {
    $sc = TRUE;

    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->production_order_model->get($code);

      if( ! empty($doc))
      {
        if($doc->Status == 'R' OR $doc->Status == 'C')
        {
          if($doc->Status == 'R')
          {
            $arr = array(
              'Status' => 'C',
              'update_user' => $this->_user->uname
            );

            if( ! $this->production_order_model->update($code, $arr))
            {
              $sc = FALSE;
              set_error('update');
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


  public function do_export()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->production_order_model->get($code);

      if( ! empty($doc))
      {
        if($doc->Status == 'R' OR $doc->Status == 'C')
        {
          if(is_true(getConfig('SAP_API')))
          {
            $this->load->library('sap_api');

            if( ! $this->sap_api->exportProductionOrder($code))
            {
              $sc = FALSE;
              $this->error = "Send data to SAP failed : {$this->sap_api->error}";
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


  public function get_bom_data()
  {
    $sc = TRUE;
    $ds = [];
    $code = trim($this->input->post('code'));
    $planned_qty = floatval($this->input->post('planned_qty'));
    $qty = $planned_qty > 0 ? $planned_qty : 1;

    if( ! empty($code))
    {
      $bom = $this->production_order_model->get_bom($code);

      if( ! empty($bom))
      {
        $details = $this->production_order_model->get_bom_details($code);

        if( ! empty($details))
        {
          $no = 1;

          foreach($details as $rs)
          {
            $ratio = $rs->Quantity > 0 ? (round(1 / $rs->Quantity, 2)) : 1;

            $ds[] = (object) array(
              'no' => $no,
              'uid' => genUid(),
              'Type' => $rs->Type,
              'type_item' => is_selected('4', strval($rs->Type)),
              'type_resource' => is_selected('209', strval($rs->Type)),
              'type_text' => is_selected('-18', strval($rs->Type)),
              'Father' => $rs->Father,
              'ChildNum' => $rs->ChildNum,
              'Code' => $rs->Code,
              'Name' => $rs->Name,
              'Quantity' => round($rs->Quantity, 4),
              'Ratio' => $ratio > 1 ? "1/{$ratio}" : "1",
              'PlannedQty' => round($rs->Quantity * $qty, 4),
              'Warehouse' => $rs->Warehouse,
              'Issued' => 0,
              'IssueType' => $rs->IssueMthd,
              'issue_m' => is_selected('M', $rs->IssueMthd),
              'issue_b' => is_selected('B', $rs->IssueMthd),
              'Available' => number($this->stock_model->get_item_stock($rs->Code, $rs->Warehouse), 2),
              'Uom' => $rs->IUom,
              'UomEntry' => $rs->IUoMEntry,
              'UomCode' => $rs->UomCode,
              'ProJect' => $rs->Project,
              'LineText' => $rs->LineText
            );

            $no++;
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "BOM Items Not Found !";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "BOM code not found or invalid bom code";
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
      'data' => $ds
    );

    echo json_encode($arr);
  }


  public function get_customer_code_and_name()
  {
    $ds = [];

    $txt = trim($_REQUEST['term']);

    $qr = "SELECT TOP(100) CardCode, CardName FROM OCRD WHERE CardType = 'C' ";

    if($txt != '*')
    {
      $qr .= "AND (CardCode LIKE N'%{$txt}%' OR CardName LIKE N'%{$txt}%') ";
    }

    $qr .= "ORDER BY CardCode ASC";

    $qs = $this->ms->query($qr);

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $ds[] = $rs->CardCode.' | '.$rs->CardName;
      }
    }
    else
    {
      $ds[] = 'Not found';
    }

    echo json_encode($ds);
  }


  public function get_bom_code_and_name()
  {
    $ds = [];

    $txt = trim($_REQUEST['term']);

    $qr = "SELECT TOP(100) b.Code, b.Name, i.InvntryUom AS Uom FROM OITT AS b LEFT JOIN OITM AS i ON b.Code = i.ItemCode WHERE 1 = 1 ";

    if($txt != '*')
    {
      $qr .= "AND (b.Code LIKE N'%{$txt}%' OR b.Name LIKE N'%{$txt}%') ";
    }

    $qr .= "ORDER BY b.Code ASC";

    $qs = $this->ms->query($qr);

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $ds[] = $rs->Code.' | '.$rs->Name.' | '.$rs->Uom;
      }
    }
    else
    {
      $ds[] = 'Not found';
    }

    echo json_encode($ds);
  }


  public function get_warehouse_code_and_name()
  {
    $ds = [];

    $txt = trim($_REQUEST['term']);

    $qr = "SELECT TOP(100) WhsCode, WhsName FROM OWHS WHERE 1=1 ";

    if($txt != '*')
    {
      $qr .= "AND (WhsCode LIKE N'%{$txt}%' OR WhsName LIKE N'%{$txt}%') ";
    }

    $qr .= "ORDER BY WhsCode ASC";

    $qs = $this->ms->query($qr);

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $ds[] = $rs->WhsCode.' | '.$rs->WhsName;
      }
    }
    else
    {
      $ds[] = 'Not found';
    }

    echo json_encode($ds);
  }


  public function get_item_code_and_name()
  {
    $ds = [];

    $txt = trim($_REQUEST['term']);

    $qr = "SELECT ItemCode, ItemName FROM OITM WHERE ItemType = 'I' ";

    if($txt != '*')
    {
      $qr .= "AND (ItemCode LIKE N'%{$txt}%' OR ItemName LIKE N'%{$txt}%') ";
    }

    $qr .= "ORDER BY ItemCode ASC ";
    $qr .= "OFFSET 0 ROWS FETCH NEXT 100 ROWS ONLY";

    $qs = $this->ms->query($qr);

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $ds[] = $rs->ItemCode.' | '.$rs->ItemName;
      }
    }
    else
    {
      $ds[] = 'Not found';
    }

    echo json_encode($ds);
  }


  public function get_item_data()
  {
    $sc = TRUE;
    $code = $this->input->post('ItemCode');
    $whsCode = $this->input->post('WhsCode');
    $ds = [];
    $qty = 0;

    $item = $this->production_order_model->get_item_data($code);

    if( ! empty($item))
    {
      $qty = $this->stock_model->get_item_stock($code, $whsCode);
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid Item";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'item' => $item,
      'available' => number($qty, 2)
    );

    echo json_encode($arr);
  }


  public function get_sales_order()
  {
    $ds = [];
    $txt = trim($_REQUEST['term']);

    $this->ms
    ->select('DocEntry, DocNum, CardCode, CardName')
    ->where('CANCELED', 'N')
    ->where('DocStatus', 'O');

    if($txt != '*')
    {
      $this->ms->like('DocNum', $txt);
    }

    $qs = $this->ms
    ->order_by('DocEntry', 'DESC')
    ->limit(100)
    ->get('ORDR');

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $ds[] = $rs->DocNum." | ".$rs->CardCode." | ".$rs->CardName." | ".$rs->DocEntry;
      }
    }
    else
    {
      $ds[] = "Not found";
    }

    echo json_encode($ds);
  }


  public function get_available_stock()
  {
    $sc = TRUE;
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
    $prefix = getConfig('PREFIX_PRODUCTION_ORDER');
    $run_digit = getConfig('RUN_DIGIT_PRODUCTION_ORDER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->production_order_model->get_max_code($pre);

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
    $filter = array('pdo_code', 'pdo_inv_code', 'pdo_product_code', 'order_from_date', 'order_to_date', 'pdo_status', 'pdo_is_exported');

    return clear_filter($filter);
  }
} // end class
?>
