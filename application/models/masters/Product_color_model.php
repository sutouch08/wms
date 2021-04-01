<?php
class Product_color_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return  $this->db->insert('product_color', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('product_color', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('product_color');
  }


  public function count_rows(array $ds = array())
  {
    if($ds['status'] != 2)
    {
      $this->db->where('active', $ds['status']);
    }

    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if(!empty($ds['color_group']))
    {
      if($ds['color_group'] === 'NULL')
      {
        $this->db->where('id_group IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('id_group', $ds['color_group']);
      }
    }

    return $this->db->count_all_results('product_color');
  }




  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('product_color');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_name($code)
  {
    if($code === NULL OR $code === '')
    {
      return $code;
    }

    $rs = $this->db->select('name')->where('code', $code)->get('product_color');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }




  public function get_data(array $ds = array(), $perpage = '', $offset = '')
  {
    $this->db
    ->select('co.*')
    ->select('cg.code AS group_code, cg.name AS group_name')
    ->from('product_color AS co')
    ->join('product_color_group AS cg', 'co.id_group = cg.id', 'left');

    if(!empty($ds['status']) && $ds['status'] != 2)
    {
      $this->db->where('co.active', $ds['status']);
    }

    if(!empty($ds['code']))
    {
      $this->db->like('co.code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('co.name', $ds['name']);
    }

    if(!empty($ds['color_group']))
    {
      if($ds['color_group'] === 'NULL')
      {
        $this->db->where('co.id_group IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('co.id_group', $ds['color_group']);
      }
    }

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();

    return $rs->result();
  }




  public function is_exists($code, $old_code = '')
  {
    if($old_code != '')
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('product_color');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_name($name, $old_name = '')
  {
    if($old_name != '')
    {
      $this->db->where('name !=', $old_name);
    }

    $rs = $this->db->where('name', $name)->get('product_color');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function set_active($code, $active)
  {
    return $this->db->set('active', $active)->where('code', $code)->update('product_color');
  }

  public function count_members($code)
  {
    $this->db->select('active')->where('color_code', $code);
    $rs = $this->db->get('products');
    return $rs->num_rows();
  }



  public function get_color_group()
  {
    $rs = $this->db->order_by('name', 'ASC')->get('product_color_group');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function is_sap_exists($code)
  {
    $rs = $this->mc->select('Code')->where('Code', $code)->get('COLOR');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function add_sap_color(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('COLOR', $ds);
    }

    return FALSE;
  }


  public function update_sap_color($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->mc->where('Code', $code);
      return $this->mc->update('COLOR', $ds);
    }

    return FALSE;
  }


}
?>
