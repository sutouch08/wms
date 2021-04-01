<?php
class Order_channels_items_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_data(array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db
      ->select('o.code AS code, o.date_add, o.reference, o.warehouse_code')
      ->select('o.shipping_code, o.shipping_fee, o.service_fee')
      ->select('o.customer_ref, o.id_address, c.name AS channels')
      ->select('pm.name AS payment, st.name AS state')
      ->select('od.product_code, od.price')
      ->select('od.qty, od.discount_amount, od.total_amount')
      ->from('order_details AS od')
      ->join('orders AS o', 'od.order_code = o.code', 'left')
      ->join('channels AS c', 'o.channels_code = c.code', 'left')
      ->join('payment_method AS pm', 'o.payment_code = pm.code', 'left')
      ->join('order_state AS st', 'o.state = st.state', 'left')
      ->where('o.date_add >=', from_date($ds['fromDate']))
      ->where('o.date_add <=', to_date($ds['toDate']))
      ->where('o.role', 'S')
      ->where('o.status', 1)
      ->where('o.is_expired', 0);

      if(empty($ds['allChannels']) && !empty($ds['channels']))
      {
        $this->db->where_in('o.channels_code', $ds['channels']);
      }

      if(empty($ds['allPayments']) && !empty($ds['payments']))
      {
        $this->db->where_in('o.payment_code', $ds['payments']);
      }

      if(empty($ds['allWarehouse']) && !empty($ds['warehouse']))
      {
        $this->db->where_in('o.warehouse_code', $ds['warehouse']);
      }

      if(!empty($ds['state']))
      {
        $this->db->where_in('o.state', $ds['state']);
      }

      if(!empty($ds['pdFrom']) && !empty($ds['pdTo']))
      {
        $this->db
        ->where('od.product_code >=', $ds['pdFrom'])
        ->where('od.product_code <=', $ds['pdTo']);
      }



      $this->db->order_by('o.code', 'ASC')->order_by('od.product_code', 'ASC');

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
