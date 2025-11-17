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
            $row = $this->receive_po_model->get_po_row($rs->baseEntry, $rs->baseLine);

            if( ! empty($row))
            {
              $diff = $row->Quantity - $row->OpenQty;
              $rs->backlogs = 100; //$row->OpenQty;
              $rs->limit = 100; //($row->Quantity + ($row->Quantity * $rate)) - $diff;
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
          'batch_details' => $this->receive_material_model->get_batch_details($doc->code),
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
                'price_label' => number($rs->Price, 4),
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
