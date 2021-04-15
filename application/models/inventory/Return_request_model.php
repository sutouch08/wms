<?php
class Return_request_model extends CI_Model
{
	protected $tb = 'return_order_request';
	protected $td = 'return_order_request_detail';


  public function __construct()
  {
    parent::__construct();
  }


	public function get($code)
	{
		$rs = $this->db->where('code', $code)->get($this->tb);

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
			return $this->db->insert($this->tb, $ds);
		}

		return FALSE;
	}


	public function update($code, array $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->where('code', $code)->update($this->tb, $ds);
		}

		return FALSE;
	}


	public function get_detail($id)
	{
		$rs = $this->db->where('id', $id)->get($this->td);

		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


	public function get_details($code)
	{
		$rs = $this->db->where('request_code', $code)->get($this->td);
		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function get_list($ds = array(), $perpage = 20, $offset = 0)
	{
		if(!empty($ds['code']))
		{
			$this->db->like('code', trim($ds['code']));
		}

		if(!empty($ds['from_date']) && !empty($ds['to_date']))
		{
			$this->db->where('date_add >=', from_date($ds['from_date']));
			$this->db->where('date_add <=', to_date($ds['to_date']));
		}

		if(!empty($ds['remark']))
		{
			$this->db->like('remark', trim($ds['remark']));
		}

		$this->db->order_by('code', 'DESC')->limit($perpage, $offset);

		$rs = $this->db->get($this->tb);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function count_rows($ds = array())
	{
		if(!empty($ds['code']))
		{
			$this->db->like('code', trim($ds['code']));
		}

		if(!empty($ds['from_date']) && !empty($ds['to_date']))
		{
			$this->db->where('date_add >=', from_date($ds['from_date']));
			$this->db->where('date_add <=', to_date($ds['to_date']));
		}

		if(!empty($ds['remark']))
		{
			$this->db->like('remark', trim($ds['remark']));
		}

		return $this->db->count_all_results($this->tb);
	}



	public function get_sum_request_qty($code)
	{
		$rs = $this->db->select_sum('qty')->where('request_code', $code)->get($this->td);

		if($rs->num_rows() === 1)
		{
			return is_null($rs->row()->qty) ? 0 : $rs->row()->qty;
		}

		return 0;
	}


  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }




} //--- end class

 ?>
