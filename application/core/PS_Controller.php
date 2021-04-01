<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PS_Controller extends CI_Controller
{
  public $pm;
  public $home;
  public $ms;
  public $mc;
  public $cn;
  public $close_system;
  public $notibars;
  public $WC;
  public $WT;
  public $WS;
  public $WU;
  public $WQ;
  public $WV;
  public $RR;
  public $WL;
  public $isViewer;

  public function __construct()
  {
    parent::__construct();


    //--- check is user has logged in ?
    _check_login();

    $this->close_system   = getConfig('CLOSE_SYSTEM'); //--- ปิดระบบทั้งหมดหรือไม่

    if($this->close_system == 1)
    {
      redirect('setting/maintenance');
    }

    $uid = get_cookie('uid');

    $this->isViewer = $this->user_model->is_viewer($uid);

    $this->notibars = getConfig('NOTI_BAR');

    //--- get permission for user
    $this->pm = get_permission($this->menu_code, $uid, get_cookie('id_profile'));

    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
    $this->cn = $this->load->database('cn', TRUE); //--- consign Database
    //$this->is = $this->load->database('is', TRUE); //---- Ecom database

    if($this->notibars == 1 && $this->isViewer === FALSE)
    {
      $this->WC = get_permission('SOCCSO', $uid);
  		$this->WT = get_permission('SOCCTR', $uid);
  		$this->WS = get_permission('SOODSP', $uid);
  		$this->WU = get_permission('ICSUPP', $uid);
  		$this->WQ = get_permission('ICTRFM', $uid);
      $this->WV = get_permission('ICTRFS', $uid);
      $this->RR = get_permission('ICRQRC', $uid);
      $this->WL = get_permission('ICLEND', $uid);
    }
  }
}

?>
