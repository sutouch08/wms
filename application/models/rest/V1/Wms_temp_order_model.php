<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Wms_temp_order_model extends CI_Model
{
	private $tb = "wms_temp_order";  //---- table nmae
	private $td = "wms_temp_order_detail"; //---- table name

	public function __construct()
	{
		parent::__construct();
	}


	public function get($id)
	{
		$rs = $this->wms->where('id', $id)->get($this->tb);
		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


	public function add(array $ds = array())
	{
		if(!empty($ds))
		{
			if( $this->wms->insert($this->tb, $ds))
			{
				return $this->wms->insert_id();
			}
		}

		return FALSE;
	}


	public function update($id, array $ds = array())
	{
		if(!empty($ds))
		{
			return $this->wms->where('id', $id)->update($this->tb, $ds);
		}

		return FALSE;
	}


	public function is_exists($code)
	{
		$rs = $this->wms->where('code', $code)->count_all_results($this->tb);
		if($rs > 0)
		{
			return TRUE;
		}

		return FALSE;
	}


	public function drop_temp_exists_data($id)
	{
		$this->wms->trans_start();
		$this->wms->where('id_order', $id)->delete($this->td);
		$this->wms->where('id', $id)->delete($this->tb);
		$this->wms->trans_complete();

		return $this->wms->trans_status();
	}


	public function get_temp_notcomplete_order($code)
	{
		$rs = $this->wms->where('code', $code)->where_in('status', array(0, 3))->get($this->tb);
		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function is_order_completed($code)
	{
		$rs = $this->wms->where('code', $code)->where('status', 1)->get($this->tb);
		if($rs->num_rows() > 0)
		{
			return TRUE;
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


	public function get_details($id_order)
	{
		$rs = $this->wms->where('id_order', $id_order)->get($this->td);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function get_details_by_code($order_code)
	{
		$rs = $this->wms->where('order_code', $order_code)->where('status', 1)->get($this->td);
		
		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function processing_status(array $ds = array())
	{
		if( ! empty($ds))
		{
			return $this->wms->set('status', 2)->where_in('code', $ds)->update($this->tb);
		}

		return FALSE;
	}


	public function get_unprocess_list($limit = 100)
	{
		$date = $this->last_minute();

		$rs = $this->wms
		->where('status', 0)
		->where('temp_date <=', $date)
		->order_by('temp_date', 'ASC')
		->limit($limit)
		->get($this->tb);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function last_minute()
	{
		$i = date('i');
		$h = date('H');
		if($i == 0)
		{
			if($h != 0)
			{
				$i = 59;
				$h--;
			}
		}
		else
		{
			$i--;
		}

		return  date("Y-m-d {$h}:{$i}:s");
	}


	public function update_status($code, $status, $message = NULL)
	{
		$arr = array('status' => $status, 'message' => $message);

		$this->wms->trans_start();
		$ds = $this->wms->set('status', $status)->where('order_code', $code)->update($this->td);
		$od = $this->wms->where('code', $code)->update($this->tb, $arr);
		$this->wms->trans_complete();
	}


	public function count_rows(array $ds = array())
	{
		if(!empty($ds['code']))
		{
			$this->wms->like('code', $ds['code']);
		}

		if(!empty($ds['reference']))
		{
			$this->wms->like('reference', $ds['reference']);
		}

		if($ds['status'] !== 'all')
		{
			$this->wms->where('status', $ds['status']);
		}

		if(!empty($ds['shipped_from_date']) && !empty($ds['shipped_to_date']))
		{
			$this->wms->where('shipped_date >=', from_date($ds['shipped_from_date']));
			$this->wms->where('shipped_date <=', to_date($ds['shipped_to_date']));
		}

		if(!empty($ds['from_date']) && !empty($ds['to_date']))
		{
			$this->wms->where('temp_date >=', from_date($ds['from_date']));
			$this->wms->where('temp_date <=', to_date($ds['to_date']));
		}

		return $this->wms->count_all_results($this->tb)		;
	}


	public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
	{
		if(!empty($ds['code']))
		{
			$this->wms->like('code', $ds['code']);
		}

		if(!empty($ds['reference']))
		{
			$this->wms->like('reference', $ds['reference']);
		}

		if($ds['status'] !== 'all')
		{
			$this->wms->where('status', $ds['status']);
		}

		if(!empty($ds['shipped_from_date']) && !empty($ds['shipped_to_date']))
		{
			$this->wms->where('shipped_date >=', from_date($ds['shipped_from_date']));
			$this->wms->where('shipped_date <=', to_date($ds['shipped_to_date']));
		}

		if(!empty($ds['from_date']) && !empty($ds['to_date']))
		{
			$this->wms->where('temp_date >=', from_date($ds['from_date']));
			$this->wms->where('temp_date <=', to_date($ds['to_date']));
		}

		$this->wms->order_by('id', 'DESC')->limit($perpage, $offset);

		$rs = $this->wms->get($this->tb);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return  NULL;
	}


	public function delete($id)
	{
		$this->wms->trans_begin();
		$rd = $this->wms->where('id_order', $id)->delete('wms_temp_order_detail');
		$rs = $this->wms->where('id', $id)->delete('wms_temp_order');
		$rt = $this->wms->where('id_order', $id)->delete('wms_tracking_detail');

		if($rd && $rs)
		{
			$this->wms->trans_commit();
			return TRUE;
		}
		else
		{
			$this->wms->trans_rollback();
			return FALSE;
		}

		return FALSE;
	}


	public function add_tracking(array $ds = array())
	{
		if( ! empty($ds))
		{
			return $this->wms->insert('wms_tracking_detail', $ds);
		}

		return FALSE;
	}


} //--- end model
?>
