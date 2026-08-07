<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prepare_list_model extends CI_Model
{
  private $tb = "prepare";

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

    if (! empty($ds['from_date']))
    {
      $this->db->where('date_upd >=', from_date($ds['from_date']));
    }

    if (! empty($ds['to_date']))
    {
      $this->db->where('date_upd <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['order_code']))
    {
      $this->db->where('order_code', $ds['order_code']);
    }

    if( ! empty($ds['pd_code']))
    {
      $this->db->where('product_code', $ds['pd_code']);
    }

    if( isset($ds['warehouse_code']) && $ds['warehouse_code'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse_code']);
    }

    if( ! empty($ds['zone_code']))
    {
      $this->db->where('zone_code', $ds['zone_code']);
    }

    if( isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }    

    $this->db->order_by('date_upd', 'DESC');

    $rs = $this->db->limit($perpage, $offset)->get($this->tb);

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

    if (! empty($ds['from_date']))
    {
      $this->db->where('date_upd >=', from_date($ds['from_date']));
    }

    if (! empty($ds['to_date']))
    {
      $this->db->where('date_upd <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['order_code']))
    {
      $this->db->where('order_code', $ds['order_code']);
    }

    if( ! empty($ds['pd_code']))
    {
      $this->db->where('product_code', $ds['pd_code']);
    }

    if( isset($ds['warehouse_code']) && $ds['warehouse_code'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse_code']);
    }

    if( ! empty($ds['zone_code']))
    {
      $this->db->where('zone_code', $ds['zone_code']);
    }

    if( isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }    

    return $this->db->count_all_results($this->tb);
  }


  public function get_export_data(array $ds = array())
  {
    if (isset($ds['range']) && $ds['range'] != 'all')
    {
      $this->db->where('id >', $this->get_max_id());
    }

    if (! empty($ds['from_date']))
    {
      $this->db->where('date_upd >=', from_date($ds['from_date']));
    }

    if (! empty($ds['to_date']))
    {
      $this->db->where('date_upd <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['order_code']))
    {
      $this->db->where('order_code', $ds['order_code']);
    }

    if( ! empty($ds['pd_code']))
    {
      $this->db->where('product_code', $ds['pd_code']);
    }

    if( isset($ds['warehouse_code']) && $ds['warehouse_code'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse_code']);
    }

    if( ! empty($ds['zone_code']))
    {
      $this->db->where('zone_code', $ds['zone_code']);
    }

    if( isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }    

    $this->db->order_by('date_upd', 'DESC');

    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_order_state($code)
  {
    $rs = $this->db->select('state')->where('code', $code)->get('orders');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->state;
    }

    return NULL;
  }


  public function state_name_array()
  {
    $state = [];
    $rs = $this->db->get('order_state');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $ro)
      {
        $state[$ro->state] = $ro->name;
      }
    }

    return $state;
  }


  public function channels_name_array()
  {
    $channels = [];
    $rs = $this->db->get('channels');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $ro)
      {
        $channels[$ro->code] = $ro->name;
      }
    }

    return $channels;
  }


  public function get_order($code)
  {
    $rs = $this->db
    ->select('code, reference, channels_code, state, date_add')
    ->where('code', $code)
    ->get('orders');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_max_id()
  {
    $limit = $this->get_limit_rows();
    $rs = $this->db->query("SELECT MAX(id) AS id FROM prepare");

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
} //-- end class

?>
