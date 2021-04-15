<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Wms_temp_receive_model extends CI_Model
{
	private $tb = "wms_temp_receive";  //---- table nmae
	private $td = "wms_temp_receive_detail"; //---- table name

	public function __construct()
	{
		parent::__construct();
	}


	public function add(array $ds = array())
	{
		if(!empty($ds))
		{
			return $this->wms->insert($this->tb, $ds);
		}

		return FALSE;
	}


	public function add_detail(array $ds = array())
	{
		if(!empty($ds))
		{
			return $this->wms->insert($this->td, $ds);
		}

		return FALSE;
	}


	public function get($code)
	{
		$rs = $this->wms->where('code', $code)->get($this->tb);

		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}



	public function get_details($code)
	{
		$rs = $this->wms->where('receive_code', $code)->get($this->td);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}



	public function get_unprocess($code)
	{
		$rs = $this->wms->where('status', 0)->where('code', $code)->get($this->tb);

		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


	public function get_unprocess_list($limit = 100)
	{
		$rs = $this->wms->where('status', 0)->limit($limit)->get($this->tb);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function get_error_list($limit = 100)
	{
		$rs = $this->wms->where('status', 3)->limit($limit)->get($this->tb);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function update_status($code, $status, $message = NULL)
	{
		$arr = array('status' => $status, 'message' => $message);

		$this->wms->trans_start();
		$ds = $this->wms->set('status', $status)->where('receive_code', $code)->update($this->td);
		$od = $this->wms->where('code', $code)->update($this->tb, $arr);
		$this->wms->trans_complete();
	}

} //--- end model
?>
