<?php
class Document_audit_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_outbound_data(array $ds = array())
	{
		if(!empty($ds))
		{
			$this->db
			->select('o.date_add, o.code AS order_code, o.role, o.state, o.channels_code, o.inv_code, o.wms_export')
			->select('tmp.reference AS temp_code')
			->from('warrix_sap.orders AS o')
			->join('warrix_wms_temp.wms_temp_order AS tmp', 'o.code = tmp.code', 'left')
			->where('o.is_wms', 1)
			->where_in('o.role', $ds['role'])
			->where_in('o.state', $ds['state'])
			->where('o.date_add >=', $ds['fromDate'])
			->where('o.date_add <=', $ds['toDate']);

			if($ds['allDoc'] != 1 && !empty($ds['docForm']) && !empty($ds['docTo']))
			{
				$this->db->where('o.code >=', $ds['docFrom'])->where('o.code <=', $ds['docTo']);
			}

			if($ds['channels'] != "all")
			{
				$this->db->where('o.channels_code', $ds['channels']);
			}

			$this->db->order_by('o.date_add', 'ASC');
			$this->db->order_by('o.code', 'ASC');

			$rs = $this->db->get();

			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}
		}

		return NULL;
	}

} //--- end class

?>
