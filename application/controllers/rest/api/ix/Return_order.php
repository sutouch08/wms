<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Return_order extends REST_Controller
{
  public $error;
  public $user;
  public $ms;
  public $mc;
  public $wms;
	public $api_path = "rest/api/ix/return";
	public $logs;
	public $log_json = FALSE;
	public $api = FALSE;

  public function __construct()
  {
    parent::__construct();
		$this->api = is_true(getConfig('IX_API'));

		if($this->api)
		{
      $this->wms = $this->load->database('wms', TRUE); //--- Temp database
      $this->ms = $this->load->database('ms', TRUE);
      $this->mc = $this->load->database('mc', TRUE);
      $this->load->model('rest/V1/ix_api_logs_model');
      $this->load->model('inventory/return_order_model');
      $this->load->model('orders/orders_model');
      $this->load->model('masters/warehouse_model');
      $this->load->model('masters/zone_model');
      $this->load->model('masters/customers_model');
      $this->load->model('masters/products_model');

	    $this->user = 'api@warrix';
			$this->logs_json = is_true(getConfig('IX_LOG_JSON'));
		}
		else
		{
			$arr = array(
				'status' => FALSE,
				'error' => "Access denied"
			);

			$this->response($arr, 400);
		}
  }


  public function create_post()
  {
    $sc = TRUE;

    $this->api_path = $this->api_path."/create";
    //--- Get raw post data
    $json = file_get_contents("php://input");

    if( ! $this->api)
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'API Not Enabled'
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => 'RETURN',
          'code' => NULL,
          'action' => 'create',
          'status' => 'failed',
          'message' => 'API Not Enabled',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $data = json_decode($json);

    if(empty($data))
    {
      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => 'RETURN',
          'code' => NULL,
          'action' => 'create',
          'status' => 'failed',
          'message' => 'empty data',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $arr = array(
      'status' => FALSE,
      'error' => 'empty data'
      );

      $this->response($arr, 400);
    }

    if(empty($data->order_number))
    {
      $this->error = 'order_number is required';

      $arr = array(
      'status' => FALSE,
      'error' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
        'trans_id' => genUid(),
        'api_path' => $this->api_path,
        'type' => 'RETURN',
        'code' => NULL,
        'action' => 'create',
        'status' => 'failed',
        'message' => $this->error,
        'request_json' => $json,
        'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    //--- check each item code
    if(empty($data->details))
    {
      $sc = FALSE;
      $this->error = "Return items not found";

      $arr = array(
        'status' => FALSE,
        'error' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => 'RETURN',
          'code' => $data->order_number,
          'action' => 'create',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $order = $this->orders_model->get_order_by_reference($data->order_number);

    if(empty($order))
    {
      $sc = FALSE;
      $this->error = "Invalid order number";

      $arr = array(
        'status' => FALSE,
        'error' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => 'RETURN',
          'code' => $data->order_number,
          'action' => 'create',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    if($order->state != 8)
    {
      $sc = FALSE;
      $this->error = "Invalid order status";

      $arr = array(
        'status' => FALSE,
        'error' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => 'RETURN',
          'code' => $data->order_number,
          'action' => 'create',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $invoice = $this->return_order_model->get_invoice_detail_by_order_item($order->code, $data->details[0]->item);

    if(empty($invoice))
    {
      $sc = FALSE;
      $this->error = "Invoice not found";

      $arr = array(
        'status' => FALSE,
        'error' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => 'RETURN',
          'code' => $data->order_number,
          'action' => 'create',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $ds = (object) array(
      'date_add' => now(),
      'invoice' => $invoice->code,
      'warehouse_code' => getConfig('IX_RETURN_WAREHOUSE'),
      'zone_code' => getConfig('IX_RETURN_ZONE'),
      'customer_code' => $invoice->customer_code,
      'remark' => $data->remark,
      'details' => array()
    );

    if($sc === TRUE)
    {
      foreach($data->details as $rs)
      {
        if($sc === FALSE)
        {
          break;
        }

        $invoice = $this->return_order_model->get_invoice_detail_by_order_item($order->code, $rs->item);

        if( ! empty($invoice))
        {
          if($invoice->qty >= $rs->qty)
          {
            $invoice->price = round(add_vat($invoice->price), 2);
            $amount = round((get_price_after_discount($invoice->price, $invoice->discount) * $rs->qty), 2);
            $vat_amount = round(get_vat_amount($amount), 2);

            $ds->details[] = (object) array(
              'invoice_code' => $invoice->code,
              'order_code' => $order->code,
              'product_code' => $invoice->product_code,
              'product_name' => $invoice->product_name,
              'sold_qty' => round($invoice->qty, 2),
              'return_qty' => $rs->qty,
              'price' => $invoice->price,
              'discount_percent' => round($invoice->discount, 2),
              'amount' => $amount,
              'vat_amount' => $vat_amount
            );
          }
          else
          {
            $sc = FALSE;
            $this->error = "Return quantity ({$rs->qty}) exceed invoice quantity ({intval($invoice->qty)}) for item {$rs->item}";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invoice not exists for {$order->code} : {$item_code}";
        }
      }
    } //--- end $sc == TRUE


    //---- if any error return
    if($sc === FALSE)
    {
      $arr = array(
      'status' => FALSE,
      'error' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
        'trans_id' => genUid(),
        'api_path' => $this->api_path,
        'type' => 'RETURN',
        'code' => $data->order_number,
        'action' => 'create',
        'status' => 'failed',
        'message' => $this->error,
        'request_json' => $json,
        'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    //---- new code start
    if($sc === TRUE)
    {
      $this->db->trans_begin();

      $bookcode = getConfig('BOOK_CODE_RETURN_ORDER');
      $code = $this->get_new_code($ds->date_add);

      $arr = array(
        'code' => $code,
        'bookcode' => $bookcode,
        'invoice' => $ds->invoice,
        'customer_code' => $ds->customer_code,
        'warehouse_code' => $ds->warehouse_code,
        'zone_code' => $ds->zone_code,
        'user' => $this->user,
        'date_add' => $ds->date_add,
        'remark' => $ds->remark,
        'status' => 0
      );

      if( ! $this->return_order_model->add($arr))
      {
        $sc = FALSE;
        $this->error = "Failed to create document";
      }

      if($sc === TRUE)
      {
        if( ! empty($ds->details))
        {
          foreach($ds->details as $rs)
          {
            $arr = array(
              'return_code' => $code,
              'invoice_code' => $rs->invoice_code,
              'order_code' => get_null($rs->order_code),
              'product_code' => $rs->product_code,
              'product_name' => $rs->product_name,
              'sold_qty' => $rs->sold_qty,
              'qty' => $rs->return_qty,
              'price' => $rs->price,
              'discount_percent' => $rs->discount_percent,
              'amount' => $rs->amount,
              'vat_amount' => $rs->vat_amount
            );

            if( ! $this->return_order_model->add_detail($arr))
            {
              $sc = FALSE;
              $this->error = "Failed to insert item row @ {$rs->product_code} : {$rs->order_code}";
            }
          } //-- end foreach
        } // end if
      } // endif

      if($sc === TRUE)
      {
        $this->db->trans_commit();
      }
      else
      {
        $this->db->trans_rollback();
      }
    }

    if($sc === TRUE)
    {
      $arr = array(
        'status' => 'success',
        'message' => 'success',
        'order_code' => $code
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' =>'RETRUN',
          'code' => $data->order_number,
          'action' => 'create',
          'status' => 'success',
          'message' => 'success',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 200);
    }
    else
    {
      $arr = array(
        'status' => FALSE,
        'error' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' =>'RETURN',
          'code' => $data->order_number,
          'action' => 'create',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 200);
    }
  } //--- create_post


  public function get_new_code($date, $prefix = 'SM', $run_digit = 5)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $prefix = empty($prefix) ? getConfig('PREFIX_RETURN_ORDER') : $prefix;
    $run_digit = empty($run_digit) ? getConfig('RUN_DIGIT_RETURN_ORDER') : $run_digit;

    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->return_order_model->get_max_code($pre);

    if(! is_null($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }

} //--- end class
