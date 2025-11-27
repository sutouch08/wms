<?php
class Auto_renew_sap_token extends CI_Controller
{
  public $error;

  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    $this->load->library('sap_api');

    return $this->sap_api->renewToken();
  }


  public function renewToken()
  {
    $sc = TRUE;

    $this->load->library('sap_api');

    if( ! $this->sap_api->renewToken())
    {
      $sc = FALSE;
      $this->error = $this->sap_api->error;
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'token' => $sc === TRUE ? getConfig('SAP_API_CREDENTIAL') : NULL
    );

    echo json_encode($arr);
  }

} //--- end class
 ?>
