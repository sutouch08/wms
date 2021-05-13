<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Item extends REST_Controller
{
  public $error;
  public $user;

  public function __construct()
  {
    parent::__construct();

    $this->load->model('masters/products_model');
    $this->user = 'api@warrix';
  }


  public function index_get($code)
  {
    if(empty($code))
    {
      $arr = array(
        'status' => FALSE,
        'error' => "Item code is required"
      );

      $this->response($arr, 400);
    }

		$item = $this->products_model->get_attribute($code);

		if(!empty($item))
		{
			$ds = array(
				'status' => 'success',
				'data' => array(
					'code' => $item->code,
					'name' => $item->name,
					'barcode' => $item->barcode,
					'price' => $item->price,
					'unit_code' => $item->unit_code,
					'count_stock' => $item->count_stock,
					'style_code' => $item->style_code,
					'color_code' => $item->color_code,
					'color_name' => $item->color_name,
					'size_code' => $item->size_code,
					'size_name' => $item->size_name,
					'group_code' => $item->group_code,
					'group_name' => $item->group_name,
					'sub_group_code' => $item->sub_group_code,
					'sub_group_name' => $item->sub_group_name,
					'category_code' => $item->category_code,
					'category_name' => $item->category_name,
					'kind_code' => $item->kind_code,
					'kind_name' => $item->kind_name,
					'type_code' => $item->type_name,
					'brand_code' => $item->brand_code,
					'brand_name' => $item->brand_name,
					'year' => $item->year,
					'active' => $item->active == 1 ? 'Y' : 'N'
				)
			);

			$this->response($ds, 200);
		}
		else
		{
			$arr = array(
        'status' => FALSE,
        'error' => "Item code not found"
      );

      $this->response($arr, 400);
		}

  }



} //--- end class
