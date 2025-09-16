<?php
class Auto_check_shopee_status extends CI_Controller
{
  public $home;
  public $mc;
  public $ms;
  public $isViewer = FALSE;
  public $notibars = FALSE;
  public $menu_code = NULL;
  public $menu_group_code = NULL;
  public $pm;
  public $error;
  public $default_shop_id = "32706050";
  public $statusList = [
    'UNPAID' => 'Order is created, buyer has not paid yet',
    'READY_TO_SHIP' => 'Seller can arrange shipment',
    'PROCESSED' => 'Seller has arranged shipment online and got tracking number from 3PL',
    'RETRY_SHIP' => '3PL pickup parcel fail Need to re arrange shipment',
    'SHIPPED' => 'The parcel has been drop to 3PL or picked up by 3PL',
    'TO_CONFIRM_RECEIVE' => 'The order has been received by buyer',
    'IN_CANCEL' => 'The order\'s cancelation is under processing',
    'CANCELLED' => 'The order has been canceled',
    'TO_RETURN' => 'The buyer requested to return the order and order\'s return is processing',
    'COMPLETED' => 'The order has been completed',
  ]

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'auto/auto_check_shopee_status';
    $this->load->model('orders/orders_model');
    $this->pm = new stdClass();
    $this->pm->can_view = 1;
  }

  public function index($show = NULL)
  {
    if($show) { echo "start : " . now() . "<br/>";}

    $list = $this->get_orders_list([3,4,5,6]);

    if( ! empty($list))
    {
      $this->load->library('wrx_shopee_api');

      foreach($list as $rs)
      {
        $shop_id = empty($rs->shop_id) ? $this->default_shop_id : $rs->shop_id;

        $order_status = $this->wrx_shopee_api->get_order_status($rs->reference, $shop_id);

        if($show) { echo "{$rs->code} : {$this->statusList[$order_status]} <br/>"; }

        if($order_status == 'CANCELLED')
        {
          $this->orders_model->update($rs->code, ['is_cancled' => 1, 'last_check' => now()]);
        }
        else
        {
          $this->orders_model->update($rs->code, ['last_check' => now()]);
        }
      }
    }

    if($show) { echo "end : " . now(); }
  }


  public function pick($show = NULL)
  {
    if($show) { echo "start : " . now() . "<br/>";}

    $list = $this->get_orders_list([3, 4]);

    if( ! empty($list))
    {
      $this->load->library('wrx_shopee_api');

      foreach($list as $rs)
      {
        $shop_id = empty($rs->shop_id) ? $this->default_shop_id : $rs->shop_id;

        $order_status = $this->wrx_shopee_api->get_order_status($rs->reference, $shop_id);

        if($show) { echo "{$rs->code} : {$this->statusList[$order_status]} <br/>"; }

        if($order_status == 'CANCELLED')
        {
          $this->orders_model->update($rs->code, ['is_cancled' => 1, 'last_check' => now()]);
        }
        else
        {
          $this->orders_model->update($rs->code, ['last_check' => now()]);
        }
      }
    }

    if($show) { echo "end : " . now(); }
  }


  public function pack($show = NULL)
  {
    if($show) { echo "start : " . now() . "<br/>";}

    $list = $this->get_orders_list([5, 6]);

    if( ! empty($list))
    {
      $this->load->library('wrx_shopee_api');

      foreach($list as $rs)
      {
        $shop_id = empty($rs->shop_id) ? $this->default_shop_id : $rs->shop_id;

        $order_status = $this->wrx_shopee_api->get_order_status($rs->reference, $shop_id);

        if($show) { echo "{$rs->code} : {$this->statusList[$order_status]} <br/>"; }

        if($order_status == 'CANCELLED')
        {
          $this->orders_model->update($rs->code, ['is_cancled' => 1, 'last_check' => now()]);
        }
        else
        {
          $this->orders_model->update($rs->code, ['last_check' => now()]);
        }
      }
    }

    if($show) { echo "end : " . now(); }
  }


  public function get_max_order_id()
  {
    $rs = $this->db->select_max('id')->get('orders');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return 1000000;
  }


  public function get_orders_list(array $state = array())
  {
    $max_id = $this->get_max_order_id();

    $id = $max_id > 100000 ? $max_id - 10000 : $id;

    $rs = $this->db
    ->select('code, reference, shop_id')
    ->where('id >', $id)
    ->where('role', 'S')
    ->where('channels_code', 'SHOPEE')
    ->where('is_cancled', 0)
    ->where_in('state', $state)
    ->order_by('last_check', 'ASC')
    ->order_by('id', 'ASC')
    ->limit(100)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end class
 ?>
