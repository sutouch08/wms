<?php
class Auto_clear_api_logs extends CI_Controller
{
  public $home;
  public $wms;
  
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'auto/auto_clear_api_logs';
    $this->wms = $this->load->database('wms', TRUE);    
  }

  public function index()
  {
		$days = 60;
    $date = date('Y-m-d 00:00:00', strtotime("-{$days} days"));

    //--- clear WRX logs
    $this->clear_wrx_logs($date);

    //-- clear IX Logs
    $this->clear_ix_logs($date);

    //-- clear POS logs
    $this->clear_pos_logs($date);

    //-- clear SAP logs
    $this->clear_sap_logs($date);
  }


  public function clear_wrx_logs($date)
  {
    return $this->wms->where('date_upd <', $date)->delete('wrx_api_logs');
  }

  public function clear_ix_logs($date)
  {
    return $this->wms->where('date_upd <', $date)->delete('ix_api_logs');
  }

  public function clear_pos_logs($date)
  {
    return $this->wms->where('date_upd <', $date)->delete('pos_api_logs');
  }

  public function clear_sap_logs($date)
  {
    return $this->wms->where('date_upd <', $date)->delete('sap_api_logs');
  }

} //--- end class
 ?>
