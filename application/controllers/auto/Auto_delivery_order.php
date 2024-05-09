<?php
class Auto_delivery_order extends CI_Controller
{
  public $home;
  public $mc;
  public $ms;
  public $title = "Send To SAP";
  public $isViewer = FALSE;
  public $notibars = FALSE;
  public $menu_code = NULL;
  public $menu_group_code = NULL;
  public $pm;
  public $error;

  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
    $this->home = base_url().'auto/auto_delivery_order';
    $this->load->model('orders/orders_model');
		$this->load->library('export');
    $this->pm = new stdClass();
    $this->pm->can_view = 1;
  }

  public function index()
  {
    $ds['data'] = NULL;
    $all = $this->db->where('status !=', 1)->count_all_results('auto_send_to_sap_order');
    $rs = $this->db->where('status !=', 1)->limit(100)->get('auto_send_to_sap_order');

    $ds['count'] = $rs->num_rows();
    $ds['all'] = $all;
    $ds['data'] = $rs->result();
    
    $this->load->view('auto/auto_delivery_order', $ds);
  }


  private function export_order($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_order($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }


  private function export_transfer_order($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_transfer_order($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }


  private function export_transfer_draft($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_transfer_draft($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }


  private function export_transform($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_transform($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }


  //--- manual export by client
  public function do_export($code)
  {
    $sc = TRUE;

    $order = $this->orders_model->get($code);

    if(!empty($order))
    {
      switch($order->role)
      {
        case 'C' : //--- Consign (SO)
          $sc = $this->export_order($code);
          break;

        case 'L' : //--- Lend
          $sc = $this->export_transfer_order($code);
          break;

        case 'N' : //--- Consign (TR)
          $sc = $this->export_transfer_draft($code);
          break;

        case 'P' : //--- Sponsor
          $sc = $this->export_order($code);
          break;

        case 'Q' : //--- Transform for stock
          $sc = $this->export_transform($code);
          break;

        case 'S' : //--- Sale order
          $sc = $this->export_order($code);
          break;

        case 'T' : //--- Transform for sell
          $sc = $this->export_transform($code);
          break;

        case 'U' : //--- Support
          $sc = $this->export_order($code);
          break;

        default : ///--- sale order
          $sc = $this->export_order($code);
          break;
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร {$code}";
    }

    return $sc;
  }



  public function send_to_sap($code)
  {
    $rs = $this->do_export($code);

    if($rs === FALSE)
    {
      $arr = array(
        'status' => 3,
        'message' => $this->error
      );

      $this->update_status($code, $arr);
    }
    else
    {
      $arr = array(
        'status' => 1
      );

      $this->update_status($code, $arr);
    }

    echo $rs === TRUE ? 'success' : $this->error;
  }


	private function update_status($code, array $ds = array())
	{
		return $this->db->where('code', $code)->update('auto_send_to_sap_order', $ds);
	}

} //--- end class
 ?>
