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
      $filter['doc'] = $this->receive_material_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);

      $this->load->view('inventory/receive_material/receive_material_list', $filter);
    }
  }


  public function add_new()
  {
    $this->load->view('inventory/receive_material/receive_material_add');
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

    $this->ms->select('DocNum, CardCode, CardName')->where('DocStatus', 'O');

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
        $ds[] = $rs->DocNum.' | '.$rs->CardCode.' | '.$rs->CardName;
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
