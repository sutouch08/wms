<?php
class Pack_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if (isset($ds['range']) && $ds['range'] != 'all')
    {
      $this->db->where('id >', $this->get_max_id());
    }

    if(!empty($ds['order_code']))
    {
      $this->db->where('order_code', $ds['order_code']);
    }

    if(!empty($ds['pd_code']))
    {
      $this->db->where('product_code', $ds['pd_code']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_upd >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_upd <=', to_date($ds['to_date']));
    }
    
    $rs = $this->db->order_by('date_upd', 'DESC')->limit($perpage, $offset)->get('qc');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    if (isset($ds['range']) && $ds['range'] != 'all')
    {
      $this->db->where('id >', $this->get_max_id());
    }

    if(!empty($ds['order_code']))
    {
      $this->db->where('order_code', $ds['order_code']);
    }

    if(!empty($ds['pd_code']))
    {
      $this->db->where('product_code', $ds['pd_code']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_upd >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_upd <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results('qc');
  }


  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('qc');
  }

  public function get_order_state($order_code)
  {
    $rs = $this->db->select('state')->where('code', $order_code)->get('orders');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->state;
    }

    return NULL;
  }

  public function get_box_no($box_id)
  {
    $rs = $this->db->select('box_no')->where('id', $box_id)->get('qc_box');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->box_no;
    }

    return NULL;
  }

  public function get_max_id()
  {
    $limit = $this->get_limit_rows();
    $rs = $this->db->query("SELECT MAX(id) AS id FROM qc");

    if ($rs->num_rows() === 1)
    {
      return $rs->row()->id - $limit;
    }

    return $limit;
  }

  public function get_limit_rows()
  {
    $rs = $this->db->query("SELECT value FROM config WHERE code = 'FILTER_RESULT_LIMIT'");

    if ($rs->num_rows() === 1)
    {
      return intval($rs->row()->value);
    }

    return 0;
  }
}
 ?>
