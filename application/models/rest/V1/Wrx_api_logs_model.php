<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Wrx_api_logs_model extends CI_Model
{
	private $td = 'wrx_api_logs';

  public function __construct()
  {
    parent::__construct();
  }

	public function add_logs($ds = array())
	{
		return $this->wms->insert($this->td, $ds);
	}


	public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
	{
		if(!empty($ds['code']))
		{
			$this->wms->like('code', $ds['code'], 'after');
		}

		if(!empty($ds['status']) && $ds['status'] !== 'all')
		{
			$this->wms->where('status', $ds['status']);
		}

		if(! empty($ds['type']) && $ds['type'] !== 'all')
		{
			$this->wms->where('type', $ds['type']);
		}

		if(isset($ds['action']) && $ds['action'] != 'all')
		{
			$this->wms->where('action', $ds['action']);
		}

		if(!empty($ds['from_date']) && !empty($ds['to_date']))
		{
			$this->wms
			->where('date_upd >=', from_date($ds['from_date']))
			->where('date_upd <=', to_date($ds['to_date']));
		}

		$this->wms->order_by('id', 'DESC');
		$this->wms->limit($perpage, $offset);
		$rs = $this->wms->get($this->td);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function count_rows(array $ds = array())
	{
		if(!empty($ds['code']))
		{
			$this->wms->like('code', $ds['code'], 'after');
		}

		if(!empty($ds['status']) && $ds['status'] !== 'all')
		{
			$this->wms->where('status', $ds['status']);
		}

		if(! empty($ds['type']) && $ds['type'] !== 'all')
		{
			$this->wms->where('type', $ds['type']);
		}

		if(isset($ds['action']) && $ds['action'] != 'all')
		{
			$this->wms->where('action', $ds['action']);
		}

		if(!empty($ds['from_date']) && !empty($ds['to_date']))
		{
			$this->wms
			->where('date_upd >=', from_date($ds['from_date']))
			->where('date_upd <=', to_date($ds['to_date']));
		}

		return $this->wms->count_all_results($this->td);
	}


	public function get_logs($id)
	{
		$rs = $this->wms->where('id', $id)->get($this->td);

		if($rs->num_rows() == 1)
		{
			return $rs->row();
		}

		return NULL;
	}

} //---
