<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Products extends REST_Controller
{
  public $error;
  public $user;
  public $api = FALSE;

  public function __construct()
  {
    parent::__construct();
    $this->api = is_true(getConfig('POS_API'));

    if($this->api)
    {
      $this->load->model('masters/products_model');
      $this->user = 'api@warrix';
    }
    else
    {
      $this->response(['status' => FALSE, 'error' => "Access denied"], 400);
    }
  }

  public function getItem_get()
  {
    $json = file_get_contents("php://input");
		$data = json_decode($json);

    if(empty($data))
    {
      $arr = array(
        'status' => FALSE,
        'error' => "Item code is required"
      );

      $this->response($arr, 400);
    }

		$item = $this->products_model->get_attribute($data->item_code);

		if( ! empty($item))
		{
			$ds = array(
				'status' => TRUE,
				'item' => array(
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
          'collection_code' => $item->collection_code,
          'collection_name' => $item->collection_name,
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


  //---- for POS and Website
  public function countUpdateItems_get()
	{
		$json = file_get_contents("php://input");
		$data = json_decode($json);

		if(! empty($data))
		{
			$last_sync = empty($data->date) ? '2020-01-01 00:00:00' : $data->date;

			$rs = $this->db
      ->where('count_stock', 1)
      ->group_start()
      ->where('date_add >', $last_sync)
      ->or_where('date_upd >', $last_sync)
      ->group_end()
      ->count_all_results('products');

			$arr = array(
				'status' => TRUE,
				'count' => $rs
			);

			$this->response($arr, 200);
		}
		else
		{
			$arr = array(
				'status' => FALSE,
				'error' => 'Missing required parameter'
			);

			$this->response($arr, 400);
		}

	}

  //---- for POS and Website
	public function getUpdateItems_get()
	{
		$json = file_get_contents("php://input");
		$ds = json_decode($json);

		if(! empty($ds))
		{
			$date = $ds->date;
			$limit = $ds->limit;
			$offset = $ds->offset;

			$rs = $this->db
      ->select('id, code, name, barcode, style_code, cost, price')
      ->select('color_code, size_code, group_code, main_group_code')
      ->select('sub_group_code, category_code, kind_code, type_code')
      ->select('brand_code, year, unit_code, active')
      ->where('count_stock', 1)
      ->group_start()
      ->where('date_add >', $date)
      ->or_where('date_upd >', $date)
      ->group_end()
			->limit($limit, $offset)
			->get('products');

			if($rs->num_rows() > 0)
			{
        $arr = array(
          'status' => TRUE,
          'count' => $rs->num_rows(),
          'items' => $rs->result()
        );
			}
      else
      {
        $arr = array(
          'status' => TRUE,
          'count' => 0,
          'items' => NULL
        );
      }

      $this->response($arr, 200);
		}
		else
		{
			$arr = array(
				'status' => FALSE,
				'error' => 'Missing required parameter'
			);

			$this->response($arr, 400);
		}
	}


  public function getProductProperties_get()
  {
    $ds = [];

    $prop = array(
      'product_color',
      'product_size',
      'product_brand',
      'product_category',
      'product_group',
      'product_main_group',
      'product_sub_group',
      'product_kind',
      'product_type',
      'product_collection'
    );

    if( ! empty($prop))
    {
      foreach($prop as $tb)
      {
        $rs = $this->db->select('code, name')->get($tb);

        if( ! empty($rs))
        {
          $ds[$tb] = $rs->result_array();
        }
      }
    }

    $arr = array(
      'status' => TRUE,
      'props' => $ds
    );

    $this->response($arr, 200);
  }


} //--- end class
