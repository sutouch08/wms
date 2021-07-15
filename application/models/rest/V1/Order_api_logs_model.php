<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Order_api_logs_model extends CI_Model
{

	private $table = 'orders_api_logs';

  public function __construct()
  {
    parent::__construct();
  }


	public function log_json($json)
	{
		$arr = array(
			'json_text' => $json
		);

		return $this->logs->insert('json_logs', $arr);
	}


	public function logs($code, $status, $error)
	{
		$arr = array(
			'code' => $code,
			'status' => $status,
			'error_message' => $error
		);

		return $this->logs->insert($this->table, $arr);
	}

} //---
