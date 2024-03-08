<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Wms_temp_tracking_model extends CI_Model
{
	private $tb = "wms_tracking_detail";  //---- table nmae

	public function __construct()
	{
		parent::__construct();
	}


	public function get_tracking_list_by_order_code($order_code)
	{
		$rs = $this->wms->where('order_code', $order_code)->get($this->tb);

		if( $rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}

	public function count_rows(array $ds = array())
	{
		if( ! empty($ds['code']))
		{
			$this->wms->like('order_code', $ds['code']);
		}

		if( ! empty($ds['product_code']))
		{
			$this->wms->like('product_code', $ds['product_code']);
		}

		if( ! empty($ds['carton_code']))
		{
			$this->wms->like('carton_code', $ds['carton_code']);
		}

		if( ! empty($ds['tracking_no']))
		{
			$this->wms->like('tracking_no', $ds['tracking_no']);
		}

		if( ! empty($ds['courier']))
		{
			$this->wms
			->group_start()
			->like('courier_code', $ds['courier'])
			->or_like('courier_name', $ds['courier'])
			->group_end();
		}

		if( ! empty($ds['from_date']))
		{
			$this->wms->where('date_add >=', from_date($ds['from_date']));
		}

		if( ! empty($ds['to_date']))
		{
			$this->wms->where('date_add <=', to_date($ds['to_date']));
		}

		return $this->wms->count_all_results($this->tb)		;
	}


	public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
	{
		if( ! empty($ds['code']))
		{
			$this->wms->like('order_code', $ds['code']);
		}

		if( ! empty($ds['product_code']))
		{
			$this->wms->like('product_code', $ds['product_code']);
		}

		if( ! empty($ds['carton_code']))
		{
			$this->wms->like('carton_code', $ds['carton_code']);
		}

		if( ! empty($ds['tracking_no']))
		{
			$this->wms->like('tracking_no', $ds['tracking_no']);
		}

		if( ! empty($ds['courier']))
		{
			$this->wms
			->group_start()
			->like('courier_code', $ds['courier'])
			->or_like('courier_name', $ds['courier'])
			->group_end();
		}

		if( ! empty($ds['from_date']))
		{
			$this->wms->where('date_add >=', from_date($ds['from_date']));
		}

		if( ! empty($ds['to_date']))
		{
			$this->wms->where('date_add <=', to_date($ds['to_date']));
		}

		$this->wms->order_by('id', 'DESC')->limit($perpage, $offset);

		// echo $this->wms->get_compiled_select($this->tb);
		$rs = $this->wms->get($this->tb);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return  NULL;
	}

} //--- end model

?>
