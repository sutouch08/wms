<?php
class Auto_clear_api_logs extends CI_Controller
{
  public $home;
  public $wms;
  public $logs;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'auto/auto_clear_api_logs';
    $this->wms = $this->load->database('wms', TRUE);
    $this->logs = $this->load->database('logs', TRUE);
  }

  public function clear_old_logs()
  {
		$days = 7;
    $date = date('Y-m-d 00:00:00', strtotime("-{$days} days"));

    //--- clear WRX logs
    $this->clear_wrx_logs($date);

    //-- clear IX Logs
    $this->clear_ix_api_logs($date);

    //-- clear POS logs
  }


  public function clear_wrx_logs($date)
  {
    return $this->wms->where('date_upd <', $date)->delete('wrx_api_logs');
  }

  public function clear_ix_api_logs($date)
  {
    return $this->wms->where('date_upd <', $date)->delete('ix_api_logs');
  }

  public function clear_pos_api_logs($date)
  {
    return $this->logs->where('date_upd <', $date)->delete('pos_api_logs');
  }

} //--- end class
 ?>
