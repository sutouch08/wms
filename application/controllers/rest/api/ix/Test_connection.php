<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Test_connection extends REST_Controller
{
  public $error;
  public $api;

  public function __construct()
  {
    parent::__construct();
		$this->api = is_true(getConfig('IX_API'));

		if( ! $this->api)
		{
			$arr = array(
				'status' => FALSE,
				'error' => "Service Unavailable"
			);

			$this->response($arr, 503);
		}
  }

  public function index_get()
  {
    $arr = array(
      'status' => true,
      'message' => "Connected successfully"
    );

    $this->response($arr, 200);
  }
}
?>
