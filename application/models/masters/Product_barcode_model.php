<?php
class Product_barcode_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }




  public function addEan13(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('barcode_ean13', $ds);
    }

    return FALSE;
  }



  public function addLocal(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('barcode_local', $ds);
    }

    return FALSE;
  }





  public function is_exists($barcode, $id = NULL)
  {
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

    $count = $this->db->where('barcode', $barcode)->count_all_results('products');

    return $count > 0 ? TRUE : FALSE;
  }



  public function get_last_barcode()
  {
    $rs = $this->db->select_max('barcode')->get('barcode_local');
    return $rs->row()->barcode;
  }


  public function get_last_ean_barcode()
  {
    $rs = $this->db->select_max('running')->get('barcode_ean13');
    return $rs->row()->running;
  }



}
?>
