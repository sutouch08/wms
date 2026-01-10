<?php
class Stock
{
  function __construct()
  {
    $this->ci =& get_instance();
    $this->ci->load->model('stock/stock_model');
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('orders/reserv_stock_model');
  }

  public function get_available_stock($item_code, $warehouse_code)
  {
    $sell_stock = $this->ci->stock_model->get_sell_stock($item_code, $warehouse_code);
    $ordered = $this->ci->orders_model->get_reserv_stock($item_code, $warehouse_code);
    $reserv_stock = $this->ci->reserv_stock_model->get_reserv_stock($item_code, $warehouse_code);

    $available = $sell_stock - $ordered - $reserv_stock;

    return $available < 0 ? 0 : $available;
  }
} //--- end class 

?>
