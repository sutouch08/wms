<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Qc extends REST_Controller
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

  public function index_post()
  {
    $sc = TRUE;

    $file = $_FILES['video']; //$this->input->post('video');
    //$code = $this->input->post('order_code');
    $path = $this->config->item('upload_path').'video/';

    if( ! empty($file))
    {
      $fileName = $file['name'];

      $config = array(
        "allowed_types" => "*",
        "upload_path" => $path,
        "file_name"	=> $fileName, // name canbe change
        "max_size" => 102400, //100 MB in KB base on php.ini setting
        "overwrite" => TRUE
      );

      $this->load->library("upload", $config);

      if( ! $this->upload->do_upload('video'))
      {
        $sc = FALSE;
        $this->error = $this->upload->display_errors();
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    if($sc === TRUE)
    {
      $this->response($arr, 200);
    }
  }
}
?>
