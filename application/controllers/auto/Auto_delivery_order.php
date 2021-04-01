<?php
class Auto_delivery_order extends CI_Controller
{
  public $home;
  public $mc;
  public $ms;
  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
    $this->home = base_url().'auto/auto_delivery_order';
    $this->load->model('orders/orders_model');
  }

  public function index()
  {
    $this->load->view('auto/auto_delivery_order');
  }


  public function get_delivery_list()
  {
    $from_date = from_date($this->input->get('from_date'));
    $to_date = to_date($this->input->get('to_date'));
    $limit = $this->input->get('limit');

    $qs = $this->db
    ->where('state', 7)
    ->where_in('role', array('S', 'C', 'N', 'P', 'U', 'L'))
    ->where('date_add >=', $from_date)
    ->where('date_add <=', $to_date)
    ->limit($limit)
    ->get('orders');

    if($qs->num_rows() > 0)
    {
      $list = $qs->result();
    }

    if(!empty($list))
    {
      $ds = array();
      foreach($list as $rs)
      {
        $ds[] = $rs->code;
      }

      echo json_encode($ds);
    }
    else
    {
      echo 'not_found';
    }
  }


  public function resend()
  {
    $this->load->view('auto/resend');
  }


} //--- end class
 ?>
