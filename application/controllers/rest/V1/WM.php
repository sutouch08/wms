<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class WM extends REST_Controller
{
  public $error;
  public $ms;
  public $mc;
  public $user;
  public $ms;
  public $path;
  public $logs;
  public $log_json = FALSE;
  public $api = FALSE;

  public function __construct()
  {
    parent::__construct();
    $this->api = is_true(getConfig('MAGENTO_API'));

    $this->load->model('inventory/invoice_model');
    $this->load->model('inventory/transfer_model');
    $this->load->model('orders/orders_model');

    $this->ms = $this->load->database('ms', TRUE);
    $this->mc = $this->load->database('mc', TRUE);
    $this->user = "pos@warrix.co.th";
  }

  //--- for check stock
	public function get_get($code = NULL)
	{
    $sc = TRUE;
    $ds = array(
      'document' => NULL,
      'rows' => NULL
    );

    if(empty($code))
    {
      $sc = FALSE;
      $this->error = "Missing required parameter : document code";
    }

		if($sc = TRUE && ! empty($code))
		{
      $order = $this->orders_model->get($code);

      if( ! empty($order))
      {
        if($order->role == 'N')
        {
          if($order->state == 8)
          {
            $ds['document'] = array(
              'code' => $order->code,
              'customer_code' => $order->customer_code,
              'zone_code' => $order->zone_code,
              'is_received' => $order->is_valid == 1 ? 'Y' : 'N'
            );

            $ds['rows'] = $this->invoice_model->get_details_summary_group_by_item($code);

          }
          else
          {
            $sc = FALSE;
            $this->error = "Invalid document status : document not shipping";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid document type : {$code}";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid document code : {$code}";
      }
		}

    if($sc === TRUE)
    {
      $ds['status'] = TRUE;
      $this->response($ds, 200);
    }
    else
    {
      $arr = array(
        'status' => FALSE,
        'error' => $this->error
      );

      $this->response($arr, 400);
    }
	}


  public function confirm_post()
  {
    $sc = TRUE;

    $json = file_get_contents("php://input");
		$ds = json_decode($json);

    if( ! empty($ds) && ! empty($ds->code))
    {
      //--- check ว่ามีเลขที่เอกสารนี้ใน transfer draft หรือไม่
      $draft = $this->transfer_model->get_transfer_draft($ds->code);

      if( ! empty($draft))
      {
        if(empty($draft->F_Receipt) OR $draft->F_Receipt == 'N' OR $draft->F_Receipt == 'D')
        {
          //---- ยืนยันรับสินค้า
          if($this->transfer_model->confirm_draft_receipted($draft->DocEntry))
          {
            $this->orders_model->valid_transfer_draft($code);
          }
          else
          {
            $sc = FALSE;
            $this->error = "ยืนยันการรับสินค้าใน Transfer Draft ไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสารถูกยืนยันไปแล้ว";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameters";
    }

    if($sc === TRUE)
    {
      $arr = array(
        'status' => TRUE,
        'message' => 'success'
      );

      $this->response($arr, 200);
    }
    else
    {
      $arr = array(
        'status' => FALSE,
        'error' => $this->error
      );
    }
  }

  //---- for check stock
	public function getUpdateItem_get()
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
      ->where('barcode IS NOT NULL', NULL, FALSE)
      ->where('barcode !=', '')
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
          'rows' => $rs->num_rows(),
          'items' => $rs->result()
        );
			}
      else
      {
        $arr = array(
          'status' => TRUE,
          'rows' => 0,
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
          'rows' => $rs->num_rows(),
          'items' => $rs->result()
        );
			}
      else
      {
        $arr = array(
          'status' => TRUE,
          'rows' => 0,
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
      'product_type'
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
?>
