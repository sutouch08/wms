<?php
class View_stock extends PS_Controller
{
  public $title = 'เช็คสต็อก';
	public $menu_code = 'SOVIEW';
	public $menu_group_code = 'SO';
	public $pm;
	public function __construct()
	{
		parent::__construct();
		_check_login();
		$this->pm = new stdClass();
		$this->pm->can_view = 1;

    $this->load->model('masters/products_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/product_tab_model');
    $this->load->model('stock/stock_model');

    $this->load->helper('order');
    $this->load->helper('warehouse');
    $this->load->helper('product_tab');

    $this->filter = getConfig('STOCK_FILTER');

	}


	public function index()
	{
		$this->load->view('view_stock');
	}

	public function available_details($item_code, $warehouse)
	{
		$this->load->model('orders/reserv_stock_model');
		$this->load->model('orders/orders_model');
		$this->load->model('stock/stock_model');

		$sell_stock = $this->stock_model->get_sell_stock($item_code, $warehouse);
		$ordered = $this->orders_model->get_reserv_stock($item_code, $warehouse);
		$reserv_stock = $this->reserv_stock_model->get_reserv_stock($item_code, $warehouse);
		$availableStock = $sell_stock - $ordered - $reserv_stock;
		$data = array(
			'item_code' => $item_code,
			'warehouse' => $warehouse,
			'sell_stock' => $sell_stock,
			'ordered' => $ordered,
			'reserv_stock' => $reserv_stock,
			'availableStock' => $availableStock
		);

		$this->load->view('view_available_stock', $data);
	}

	

}
 ?>
