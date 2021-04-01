<?php
class Move_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function get_sap_move_doc($code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocStatus')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('OWTR');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function is_middle_exists($code)
  {
    $rs = $this->mc->select('DocStatus')->where('U_ECOMNO', $code)->get('OWTR');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get_middle_move_doc($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('OWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function add_sap_move_doc(array $ds = array())
  {
    if(!empty($ds))
    {
      $rs = $this->mc->insert('OWTR', $ds);
      if($rs)
      {
        return $this->mc->insert_id();
      }
    }

    return FALSE;
  }

  public function update_sap_move_doc($code, $ds = array())
  {
    if(! empty($code) && ! empty($ds))
    {
      return $this->mc->where('U_ECOMNO', $code)->update('OWTR', $ds);
    }

    return FALSE;
  }

  public function add_sap_move_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('WTR1', $ds);
    }

    return FALSE;
  }




  public function drop_sap_exists_details($code)
  {
    return $this->mc->where('U_ECOMNO', $code)->delete('WTR1');
  }


  public function drop_middle_exits_data($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('WTR1');
    $this->mc->where('DocEntry', $docEntry)->delete('OWTR');
    $this->mc->trans_complete();

    return $this->mc->trans_status();
  }




  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('move', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('move', $ds);
    }

    return FALSE;
  }



  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('move_detail', $ds);
    }

    return FALSE;
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('move');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_details($code)
  {
    $rs = $this->db
    ->select('move_detail.*, products.barcode')
    ->from('move_detail')
    ->join('products', 'products.code = move_detail.product_code', 'left')
    ->where('move_detail.move_code', $code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get('move_detail');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }
  }



  public function get_id($move_code, $product_code, $from_zone, $to_zone)
  {
    $rs = $this->db
    ->select('id')
    ->where('move_code', $move_code)
    ->where('product_code', $product_code)
    ->where('from_zone', $from_zone)
    ->where('to_zone', $to_zone)
    ->get('move_detail');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return FALSE;
  }


  public function update_qty($id, $qty)
  {
    return $this->db->set("qty", "qty + {$qty}", FALSE)->where('id', $id)->update('move_detail');
  }



  public function update_temp(array $ds = array())
  {
    if(!empty($ds))
    {
      $id = $this->get_temp_id($ds['move_code'], $ds['product_code'], $ds['zone_code']);
      if(!empty($id))
      {
        return $this->update_temp_qty($id, $ds['qty']);
      }
      else
      {
        return $this->add_temp($ds);
      }
    }
    return FALSE;
  }


  public function get_temp_id($code, $product_code, $zone_code)
  {
    $rs = $this->db
    ->select('id')
    ->where('move_code', $code)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->get('move_temp');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return FALSE;
  }


  public function get_sum_temp_stock($product_code)
  {
    $rs = $this->db->select_sum('qty')->where('product_code', $product_code)->get('move_temp');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function add_temp(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('move_temp', $ds);
    }

    return FALSE;
  }


  public function update_temp_qty($id, $qty)
  {
    return $this->db->set("qty", "qty + {$qty}", FALSE)->where('id', $id)->update('move_temp');
  }




  public function get_move_temp($code)
  {
    $rs = $this->db
    ->select('move_temp.*, products.barcode')
    ->from('move_temp')
    ->join('products', 'products.code = move_temp.product_code', 'left')
    ->where('move_code', $code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_temp_product($code, $product_code)
  {
    $rs = $this->db
    ->where('move_code', $code)
    ->where('product_code', $product_code)
    ->get('move_temp');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_temp_qty($move_code, $product_code, $zone_code)
  {
    $rs = $this->db
    ->select('qty')
    ->where('move_code', $move_code)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->get('move_temp');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }



  public function get_move_qty($move_code, $product_code, $from_zone)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('move_code', $move_code)
    ->where('product_code', $product_code)
    ->where('from_zone', $from_zone)
    ->where('valid', 0)
    ->get('move_detail');

    return intval($rs->row()->qty);
  }


  public function drop_zero_temp()
  {
    return $this->db->where('qty <', 1)->delete('move_temp');
  }


  public function drop_all_temp($code)
  {
    return $this->db->where('move_code', $code)->delete('move_temp');
  }



  public function drop_all_detail($code)
  {
    return $this->db->where('move_code', $code)->delete('move_detail');
  }


  public function drop_detail($id)
  {
    return $this->db->where('id', $id)->delete('move_detail');
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('move');
  }



  public function is_exists($code, $old_code = NULL)
  {
    if(!empty($old_code))
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('move');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_exists_detail($code)
  {
    $rs = $this->db->select('id')->where('move_code', $code)->get('move_detail');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_temp($code)
  {
    $rs = $this->db->select('id')->where('move_code', $code)->get('move_temp');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('move');
  }



  public function valid_all_detail($code, $valid)
  {
    return $this->db->set('valid', $valid)->where('move_code', $code)->update('move_detail');
  }


  public function count_rows(array $ds = array())
  {
    $this->db->where('code IS NOT NULL',NULL, FALSE);

    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(!empty($ds['from_warehouse']))
    {
      $from_warehouse = $this->get_warehouse_in($ds['from_warehouse']);
      $this->db->group_start();
      $this->db->where_in('from_warehouse', $from_warehouse);
      $this->db->or_where_in('to_warehouse', $from_warehouse);
      $this->db->group_end();
    }


    if(!empty($ds['user']))
    {
      $users = user_in($ds['user']);
      $this->db->where_in('user', $users);
    }

    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if($ds['is_export'] != 'all')
    {
      $this->db->where('is_exported', $ds['is_export']);
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results('move');
  }


  public function get_data(array $ds = array(), $perpage = '', $offset = '')
  {
    $this->db->where('code IS NOT NULL',NULL, FALSE);
    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(!empty($ds['from_warehouse']))
    {
      $from_warehouse = $this->get_warehouse_in($ds['from_warehouse']);
      $this->db->where_in('from_warehouse', $from_warehouse);
    }

    if(!empty($ds['to_warehouse']))
    {
      $to_warehouse = $this->get_warehouse_in($ds['to_warehouse']);
      $this->db->where_in('to_warehouse', $to_warehouse);
    }

    if(!empty($ds['user']))
    {
      $users = user_in($ds['user']);
      $this->db->where_in('user', $users);
    }

    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if($ds['is_export'] != 'all')
    {
      $this->db->where('is_exported', $ds['is_export']);
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    $this->db->order_by('code', 'DESC');

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get('move');
    //echo $this->db->get_compiled_select('move');
    return $rs->result();
  }



  public function get_warehouse_in($txt)
  {
    $rs = $this->db
    ->select('code')
    ->like('code', $txt)
    ->or_like('name', $txt)
    ->get('warehouse');

    $arr = array('none');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $wh)
      {
        $arr[] = $wh->code;
      }
    }

    return $arr;
  }



  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get('move');

    return $rs->row()->code;
  }


  public function exported($code)
  {
    return $this->db->set('is_exported', 1)->where('code', $code)->update('move');
  }


  public function get_un_export_list($from_date, $to_date, $limit)
  {
    $rs = $this->db
    ->select('code')
    ->where('date_add >=', $from_date)
    ->where('date_add <=', $to_date)
    ->where('status', 1)
    ->where('is_exported', 0)
    ->order_by('date_add', 'ASC')
    ->limit($limit)
    ->get('move');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('status', 1)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get('move');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_sap_doc_num($code)
  {
    $rs = $this->ms
    ->select('DocNum')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('OWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }


  public function update_inv($code, $doc_num)
  {
    return $this->db->set('inv_code', $doc_num)->where('code', $code)->update('move');
  }


}
 ?>
