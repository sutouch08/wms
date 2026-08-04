<?php
class Main_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_search_order($txt, $warehouse_code = NULL, $limit = NULL)
  {    
    $this->db
      ->select('o.code, o.customer_name, o.user, o.state, od.product_code, od.qty')      
      ->from('order_details AS od')
      ->join('orders AS o', 'od.order_code = o.code', 'left')      
      ->where('o.state <=', 8, FALSE)
      ->where('od.is_complete', 0)
      ->where('od.is_expired', 0)
      ->where('od.is_cancle', 0)
      ->where('od.product_code', $txt);

    if(!empty($warehouse_code))
    {
      $this->db->where('o.warehouse_code', $warehouse_code);
    }

    $this->db
    ->order_by('od.product_code', 'ASC')
    ->order_by('o.code', 'ASC');

    if(!empty($limit))
    {
      $this->db->limit($limit);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function search_items_list($txt, $limit = NULL)
  {
    $this->db
    ->select('pd.code, pd.name')
    ->from('products AS pd')
    ->join('product_style AS style', 'pd.style_code = style.code', 'left')
    ->join('product_color AS co', 'pd.color_code = co.code', 'left')
    ->join('product_size AS size', 'pd.size_code = size.code', 'left')
    ->like('pd.code', $txt, 'after');   

    $this->db
    ->order_by('style.code', 'ASC')
    ->order_by('pd.color_code', 'ASC')
    ->order_by('size.position', 'ASC');

    if(!empty($limit))
    {
      $this->db->limit($limit);
    }

		$rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end class


 ?>
