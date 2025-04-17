<?php
class Transport_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('address_transport', $ds);
    }

    return FALSE;
  }


  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('address_transport', $ds);
    }

    return FALSE;
  }



  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('address_transport');
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('address_transport');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_name($id)
  {
    $rs = $this->db->where('id', $id)->get('address_transport');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }



  public function is_exists($customer_code, $id = NULL)
  {
    if(! empty($id))
    {
      $rs = $this->db->where('customer_code', $customer_code)->where('id !=',$id)->get('address_transport');
    }
    else
    {
      $rs = $this->db->where('customer_code', $customer_code)->get('address_transport');
    }

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('address_transport AS t')
    ->join('customers AS c', 't.customer_code = c.code', 'left');

    if( ! empty($ds['name']))
    {
      $this->db
      ->group_start()
      ->like('t.customer_code', $ds['name'])
      ->or_like('c.name', $ds['name'])
      ->group_end();
    }

    if(isset($ds['sender']) && $ds['sender'] != 'all')
    {
      $this->db
      ->group_start()
      ->where('main_sender', $ds['sender'])
      ->or_where('second_sender', $ds['sender'])
      ->or_where('third_sender', $ds['sender'])
      ->group_end();
    }

    return $this->db->count_all_results();
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('t.*, c.name AS customer_name')
    ->from('address_transport AS t')
    ->join('customers AS c', 't.customer_code = c.code', 'left');

    if( ! empty($ds['name']))
    {
      $this->db
      ->group_start()
      ->like('t.customer_code', $ds['name'])
      ->or_like('c.name', $ds['name'])
      ->group_end();
    }

    if(isset($ds['sender']) && $ds['sender'] != 'all')
    {
      $this->db
      ->group_start()
      ->where('t.main_sender', $ds['sender'])
      ->or_where('t.second_sender', $ds['sender'])
      ->or_where('t.third_sender', $ds['sender'])
      ->group_end();
    }

    $rs = $this->db->limit($perpage, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


}
 ?>
