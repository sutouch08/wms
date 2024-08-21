<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class CN extends REST_Controller
{
  public $error;
  public $ms;
  public $mc;
  public $user;
  public $logs;
  public $logs_json = FALSE;
  public $api = FALSE;
  public $create_status;
  private $path = "/rest/api/pos/CN/";

  public function __construct()
  {
    parent::__construct();
    $this->api = is_true(getConfig('POS_API'));
    $this->create_status = getConfig('POS_API_CN_CREATE_STATUS') == 1 ? 1 : 0;

    if($this->api)
    {
      $this->ms = $this->load->database('ms', TRUE);
      $this->mc = $this->load->database('mc', TRUE);
      $this->logs = $this->load->database('logs', TRUE); //--- api logs database
      $this->logs_json = is_true(getConfig('POS_LOG_JSON'));
      $this->user = "pos@warrix.co.th";

      $this->load->model('inventory/return_order_model');
      $this->load->model('inventory/movement_model');
      $this->load->model('masters/zone_model');
      $this->load->model('masters/customers_model');
      $this->load->model('masters/products_model');
      $this->load->model('rest/V1/pos_api_logs_model');
    }
    else
    {
      $this->response(['status' => FALSE, 'message' => "Access denied"], 400);
    }
  }


  public function create_post()
  {
    $api_path = $this->path."create";

    $sc = TRUE;

    //--- Get raw post data
    $json = file_get_contents("php://input");

    $data = json_decode($json);

    if(empty($data))
    {
      $this->error = "Missing required parameters";

      $arr = array(
        'status' => FALSE,
        'message' => 'Missing required parameters'
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'CN',
          'code' => NULL,
          'action' => 'create',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }

    $sc = $this->verify_data($data);

		//---- if any error return
    if($sc === FALSE)
    {
      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'CN',
          'code' => $data->pos_ref,
          'action' => 'create',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }


    if($this->return_order_model->is_exists_pos_ref($data->pos_ref))
    {
      $sc = FALSE;
      $this->error = "pos_ref {$data->pos_ref} already exists";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'CN',
          'code' => $data->pos_ref,
          'action' => 'create',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }

    $customer = $this->customers_model->get($data->customer_code);

    if(empty($customer))
    {
      $sc = FALSE;
      $this->error = "Invalid Customer code : {$data->customer_code}";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'CN',
          'code' => $data->pos_ref,
          'action' => 'create',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }

    $zone = $this->zone_model->get($data->zone_code);

    if(empty($zone))
    {
      $sc = FALSE;
      $this->error = "Invalid Zone code : {$data->zone_code}";
      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'CN',
          'code' => $data->pos_ref,
          'action' => 'create',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }

    if( ! $this->zone_model->is_exists_customer($zone->code, $customer->code))
    {
      $sc = FALSE;
      $this->error = "No matching records found, Customer and Zone missmatch";
      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'CN',
          'code' => $data->pos_ref,
          'action' => 'create',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }

    //--- check each item code
    if(empty($data->items))
    {
			$sc = FALSE;
			$this->error = "Missing required parameter : Items";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'CN',
          'code' => $data->pos_ref,
          'action' => 'create',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }

    //---- check valid items data
    foreach($data->items as $rs)
    {
      //---- check valid items
      $item = $this->products_model->get($rs->product_code);

      if(empty($item))
      {
        $sc = FALSE;
        $this->error = "Invalid Product code : {$rs->product_code}";
        break;
      }
      else
      {
        $rs->item = $item;
      }
    }

    //---- if any error return
    if($sc === FALSE)
    {
      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'CN',
          'code' => $data->pos_ref,
          'action' => 'create',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }

    //---- all data validated
    if($sc === TRUE)
    {
      $date = empty($data->date) ? date('Y-m-d H:i:s') : db_date($data->date, TRUE);
      $minDate  = date_create('2024-02-01');
      $date_add = date_create($date) > $minDate ? $date : date('Y-m-d H:i:s');
      $code = $this->get_new_code($date_add);
      $bookcode = getConfig('BOOK_CODE_RETURN_ORDER');
      $vat_rate = getConfig('SALE_VAT_RATE');

      $arr = array(
        'code' => $code,
        'bookcode' => $bookcode,
        'customer_code' => $customer->code,
        'warehouse_code' => $zone->warehouse_code,
        'zone_code' => $zone->code,
        'remark' => empty($data->remark) ? NULL : get_null($data->remark),
        'date_add' => $date_add,
        'shipped_date' => $date_add,
        'user' => $this->user,
        'status' => $this->create_status,
        'is_complete' => 1, //-- no need to send to wms so set complete to 1 for not waiting for wms interface
        'is_approve' => $this->create_status == 1 ? 1 : 0,
        'approver' => $this->create_status == 1 ? $this->user : NULL,
        'pos_ref' => $data->pos_ref,
        'bill_code' => $data->bill_code,
        'is_pos_api' => 1
      );

      $this->db->trans_begin();

      if( ! $this->return_order_model->add($arr))
      {
        $sc = FALSE;
        $this->error = "Failed to Create Document Please try again later";
      }

      if($sc === TRUE)
      {
        foreach($data->items as $rs)
        {
          if($sc === FALSE)
          {
            break;
          }

          //--- add new row
          $item = $rs->item;

          $arr = array(
            'return_code' => $code,
            'invoice_code' => NULL,
            'order_code' => NULL,
            'product_code' => $item->code,
            'product_name' => $item->name,
            'sold_qty' => $rs->qty,
            'qty' => $rs->qty,
            'receive_qty' => $rs->qty,
            'price' => $rs->price,
            'discount_percent' => $rs->discount_percent, //-- discount percent without % example 40 (mean 40%)
            'amount' => $rs->line_total,
            'vat_amount' => empty($rs->vat_amount) ? get_vat_amount($rs->line_total,$vat_rate) : $rs->vat_amount,
            'valid' => 1,
            'pos_ref' => $data->pos_ref,
            'bill_code' => $data->bill_code
          );

          if( ! $this->return_order_model->add_detail($arr))
          {
            $sc = FALSE;
            $this->error = "Faild to add item : {$item->code}, {$rs->bill_code}";
          }

          if($sc === TRUE && $this->create_status == 1)
          {
            //--- update movement
            $arr = array(
              'reference' => $code,
              'warehouse_code' => $zone->warehouse_code,
              'zone_code' => $zone->code,
              'product_code' => $item->code,
              'move_in' => $rs->qty,
              'move_out' => 0,
              'date_add' => $date_add
            );

            if( ! $this->movement_model->add($arr))
            {
              $sc = FALSE;
              $this->error = 'Failed to add stock movement';
            }
          }
        } //--- foreach items

        if($sc === TRUE)
        {
          $this->db->trans_commit();
        }
        else
        {
          $this->db->trans_rollback();
        }

        if($sc === TRUE && $this->create_status == 1)
        {
          $this->load->library('export');
          $this->export->export_return($code);
        }

        if($sc === TRUE)
        {
          $arr = array(
            'status' => TRUE,
            'message' => 'success',
            'code' => $code
          );

          if($this->logs_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'api_path' => $api_path,
              'type' =>'CN',
              'code' => $code,
              'action' => 'create',
              'status' => 'success',
              'message' => 'success',
              'request_json' => $json,
              'response_json' => json_encode($arr)
            );

            $this->pos_api_logs_model->add_api_logs($logs);
          }

          $this->response($arr, 200);
        }
        else
        {
          $arr = array(
            'status' => FALSE,
            'message' => $this->error
          );

          if($this->logs_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'api_path' => $api_path,
              'type' =>'CN',
              'code' => $data->pos_ref,
              'action' => 'create',
              'status' => 'failed',
              'message' => $this->error,
              'request_json' => $json,
              'response_json' => json_encode($arr)
            );

            $this->pos_api_logs_model->add_api_logs($logs);
          }

          $this->response($arr, 200);
        }
      }
    }
  } //-- end function create


  public function cancel_post()
  {
    $api_path = $this->path."cancel";

    $sc = TRUE;
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    if(empty($data))
    {
      $sc = FALSE;
      $this->error = "Missing required parameters";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'CN',
          'code' => NULL,
          'action' => 'cancel',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
    }

    if(empty($data->code))
    {
      $sc = FALSE;
      $this->error = "Missing required parameter: code";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'CN',
          'code' => NULL,
          'action' => 'cancel',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
    }

    if(empty($data->cancel_reason))
    {
      $sc = FALSE;
      $this->error = "Missing required parameter: cancel_reason";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'CN',
          'code' => NULL,
          'action' => 'cancel',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
    }

    $code = $data->code;
    $reason = trim($data->cancel_reason);
    $doc = $this->return_order_model->get($code);

    if( empty($doc))
    {
      $sc = FALSE;
      $this->error = "Invalid document number : {$code}";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'CN',
          'code' => $code,
          'action' => 'cancel',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }

    if($doc->is_pos_api != 1)
    {
      $sc = FALSE;
      $this->error = "The document was not created by the POS system. It cannot be canceled via the API.";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'CN',
          'code' => $code,
          'action' => 'cancel',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }

    if($doc->status != 2)
    {
      $sap = $this->return_order_model->get_sap_doc_num($code);

      if( ! empty($sap))
      {
        $sc = FALSE;
        $this->error = "Unable to cancel : {$code} already imported into SAP. Please cancel this document in SAP before and try again";

        $arr = array(
          'status' => FALSE,
          'message' => $this->error
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => genUid(),
            'api_path' => $api_path,
            'type' =>'CN',
            'code' => $code,
            'action' => 'cancel',
            'status' => 'failed',
            'message' => $this->error,
            'request_json' => $json,
            'response_json' => json_encode($arr)
          );

          $this->pos_api_logs_model->add_api_logs($logs);
        }

        $this->response($arr, 200);
      }

      if($doc->status == 1)
      {
        //--- drop middle details
        $middle = $this->return_order_model->get_middle_return_doc($code);

        if( ! empty($middle))
        {
          foreach($middle as $rows)
          {
            if( ! $this->return_order_model->drop_middle_exits_data($rows->DocEntry))
            {
              $sc = FALSE;
              $this->error = "Failed to delete SAP Temp";
            }
          }
        }
      }

      if($sc === FALSE)
      {
        $arr = array(
          'status' => FALSE,
          'message' => $this->error
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => genUid(),
            'api_path' => $api_path,
            'type' =>'CN',
            'code' => $code,
            'action' => 'cancel',
            'status' => 'failed',
            'message' => $this->error,
            'request_json' => $json,
            'response_json' => json_encode($arr)
          );

          $this->pos_api_logs_model->add_api_logs($logs);
        }

        $this->response($arr, 200);
      }

      $this->db->trans_begin();

      $arr = array(
        'status' => 2,
        'cancle_reason' => $reason,
        'cancle_user' => $this->user,
        'cancle_date' => now()
      );

      if( ! $this->return_order_model->update($code, $arr))
      {
        $sc = FALSE;
        $this->error = "Failed to update document status";
      }

      if($sc === TRUE)
      {
        if( ! $this->return_order_model->update_details($code, array('is_cancle' => 1)))
        {
          $sc = FALSE;
          $this->error = "Failed to update items status";
        }

        if($sc === TRUE)
        {
          //--- remove movement
          if( ! $this->movement_model->drop_movement($code))
          {
            $sc = FALSE;
            $this->error = "Failed to delete movement";
          }
        }
      }

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
        'status' => TRUE,
        'message' => "{$code} canceled successful",
        'code' => $code
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'CN',
          'code' => $code,
          'action' => 'cancel',
          'status' => 'success',
          'message' => 'success',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }
    else
    {
      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'CN',
          'code' => $code,
          'action' => 'cancel',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RETURN_ORDER');
    $run_digit = getConfig('RUN_DIGIT_RETURN_ORDER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->return_order_model->get_max_code($pre);

    if(!empty($code))
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


  public function verify_data($data)
	{
    if( ! property_exists($data, 'pos_ref') OR empty($data->pos_ref))
    {
      $this->error = 'Missing required parameter : pos_ref';
      return FALSE;
    }

    if( ! property_exists($data, 'bill_code') OR empty($data->bill_code))
    {
      $this->error = "Missing required parameter : bill_code";
      return FALSE;
    }

    if(! property_exists($data, 'customer_code') OR empty($data->customer_code))
    {
      $this->error = 'Missing required parameter : customer_code';
			return FALSE;
    }


		if(! property_exists($data, 'zone_code') OR empty($data->zone_code))
		{
			$this->error = "Missing required parameter : zone_code";
			return FALSE;
		}

		return TRUE;
	}


} //--- end class
?>
