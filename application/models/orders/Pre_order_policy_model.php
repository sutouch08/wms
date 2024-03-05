<?php
class Pre_order_policy_model extends CI_Model
{
  private $tb = "pre_order_policy";
  private $td = "pre_order_items";

  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->tb, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_item(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->td, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }



  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }



  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }


  public function delete_items($id_policy)
  {
    return $this->db->where('id_policy', $id_policy)->delete($this->td);
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_details($id)
  {
    $this->db
    ->select('ds.*')
    ->from('pre_order_items AS ds')
    ->join('products AS pd', 'ds.product_code = pd.code', 'left')
    ->join('product_color AS co', 'pd.color_code = co.code', 'left')
    ->join('product_size AS si', 'pd.size_code = si.code', 'left')
    ->where('ds.id_policy', $id)
    ->order_by('pd.style_code', 'ASC')
    ->order_by('co.code', 'ASC')
    ->order_by('si.position', 'ASC');

    $rs = $this->db->get();

    if( $rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_code($id)
  {
    $rs = $this->db
    ->select('code')
    ->where('id', $id)
    ->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }


  public function get_name($id)
  {
    $rs = $this->db
    ->select('name')
    ->where('id', $id)
    ->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('start_date >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('end_date <=', to_date($ds['to_date']));
    }

    if( isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    return $this->db->count_all_results($this->tb);

  }



  public function get_data(array $ds = array(), $limit = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('start_date >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('end_date <=', to_date($ds['to_date']));
    }

    if( isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    $rs = $this->db->order_by('code', 'DESC')->limit($limit, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_by_code($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function is_exists_item($id, $code)
  {
    $count = $this->db->where('id_policy', $id)->where('product_code', $code)->count_all_results($this->td);

    return $count > 0 ? TRUE : FALSE;
  }


  public function remove_items(array $ds = array())
  {
    //--- ds = array('id', 'id', 'id')

    if( ! empty($ds))
    {
      return $this->db->where_in('id', $ds)->delete($this->td);
    }

    return FALSE;
  }


  public function get_active_items()
  {
    $rs = $this->db
    ->select('pr.*')
    ->from('pre_order_items AS pr')
    ->join('pre_order_policy AS po', 'pr.id_policy = po.id', 'left')
    ->join('products AS pd', 'pr.product_code = pd.code', 'left')
    ->join('product_size AS si', 'pd.size_code = si.code', 'left')
    ->where('po.status', 1)
    ->where('po.start_date <=', date('Y-m-d'))
    ->where('po.end_date >=', date('Y-m-d'))
    ->group_by('pr.product_code')
    ->order_by('pd.style_code', 'ASC')
    ->order_by('pd.color_code', 'ASC')
    ->order_by('si.position', 'ASC')
    ->get();


    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_items_on_order($id)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('pre_order_detail_id', $id)
    ->where('is_cancle', 0)
    ->where('is_expired', 0)
    ->get('order_details');

    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code)
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }

} //--- end class

 ?>
