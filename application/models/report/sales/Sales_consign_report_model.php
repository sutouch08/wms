<?php
class Sales_consign_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function count_rows(array $ds = array())
  {
    $this->db->where('role', 'M');

    if (! empty($ds['fromDate']) && ! empty($ds['toDate']))
    {
      $this->db
        ->group_start()
        ->where('date_add >=', from_date($ds['fromDate']))
        ->where('date_add <=', to_date($ds['toDate']))
        ->group_end();
    }

    if (empty($ds['allProduct']) && ! empty($ds['pdFrom']) && ! empty($ds['pdTo']))
    {
      $this->db
        ->group_start()
        ->where('product_style >=', $ds['pdFrom'])
        ->where('product_style <=', $ds['pdTo'])
        ->group_end();
    }

    if (empty($ds['allCustomer']) && ! empty($ds['cusFrom']) && ! empty($ds['cusTo']))
    {
      $this->db
        ->group_start()
        ->where('customer_code >=', $ds['cusFrom'])
        ->where('customer_code <=', $ds['cusTo'])
        ->group_end();
    }

    if (empty($ds['allWarehouse']) && ! empty($ds['warehouse_code']))
    {
      $this->db->where_in('warehouse_code', $ds['warehouse_code']);
    }


    if (empty($ds['allZone']) && ! empty($ds['zone_code']))
    {
      $this->db->where('zone_code', $ds['zone_code']);
    }
    
    return $this->db->count_all_results('order_sold');    
  }

  public function get_data(array $ds = array(), $limit = 1000, $offset = 0)
  {
    if( ! empty($ds))
    {      
      $this->db
      ->select('date_add, reference, product_code, product_name')
      ->select('cost, price, sell, qty, discount_label, discount_amount')
      ->select('total_amount, total_cost, customer_code, warehouse_code, zone_code')
      ->from('order_sold')
      ->where('role', 'M');
            
      if( ! empty($ds['fromDate']) && ! empty($ds['toDate']))
      {
        $this->db
        ->group_start()
        ->where('date_add >=', from_date($ds['fromDate']))
        ->where('date_add <=', to_date($ds['toDate']))
        ->group_end();
      }

      if(empty($ds['allProduct']) && ! empty($ds['pdFrom']) && ! empty($ds['pdTo']))
      {
        $this->db
        ->group_start()
        ->where('product_style >=', $ds['pdFrom'])
        ->where('product_style <=', $ds['pdTo'])
        ->group_end();
      }

      if(empty($ds['allCustomer']) && ! empty($ds['cusFrom']) && ! empty($ds['cusTo']))
      {
        $this->db
        ->group_start()
        ->where('customer_code >=', $ds['cusFrom'])
        ->where('customer_code <=', $ds['cusTo'])
        ->group_end();
      }

      if(empty($ds['allWarehouse']) && ! empty($ds['warehouse_code']))
      {
        $this->db->where_in('warehouse_code', $ds['warehouse_code']);
      }


      if(empty($ds['allZone']) && ! empty($ds['zone_code']))
      {
        $this->db->where('zone_code', $ds['zone_code']);
      }

      $rs = $this->db
      ->order_by('date_add', 'ASC')
      ->order_by('reference', 'ASC')
      ->limit($limit, $offset)
      ->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }
} //--- end class

 ?>
