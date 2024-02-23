<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class WM extends REST_Controller
{
  public $error;
  public $ms;
  public $mc;
  public $user;
  public $logs;
  public $log_json = FALSE;
  public $api = FALSE;

  public function __construct()
  {
    parent::__construct();
    $this->api = is_true(getConfig('POS_API'));

    if($this->api)
    {
      $this->ms = $this->load->database('ms', TRUE);
      $this->mc = $this->load->database('mc', TRUE);
      $this->logs = $this->load->database('logs', TRUE); //--- api logs database
      $this->log_json = is_true(getConfig('POS_LOG_JSON'));
      $this->user = "pos@warrix.co.th";
      $this->load->model('account/consign_order_model');
      $this->load->model('inventory/delivery_order_model');
      $this->load->model('inventory/invoice_model');
      $this->load->model('inventory/movement_model');
      $this->load->model('masters/zone_model');
      $this->load->model('masters/warehouse_model');
      $this->load->model('masters/products_model');
      $this->load->model('masters/customers_model');
      $this->load->model('rest/V1/order_api_logs_model');
      $this->load->helper('discount');
    }
    else
    {
      $this->response(['status' => FALSE, 'error' => "Access denied"], 400);
    }
  }


  public function create_post()
  {
    $sc = TRUE;

    //--- Get raw post data
    $json = file_get_contents("php://input");

    $data = json_decode($json);

    if(empty($data))
    {
      $this->error = "Missing required parameters";

      $this->add_logs('WM', 'create', 'error', $this->error, NULL);
      $this->response(['status' => FALSE, 'error' => 'Missing required parameters'], 400);
    }

    $sc = $this->verify_data($data);

		//---- if any error return
    if($sc === FALSE)
    {
      $this->add_logs('WM', 'create', 'error', $this->error, NULL);
      $this->response(['status' => FALSE, 'error' => $this->error], 400);
    }


    if($this->consign_order_model->is_exists_pos_ref($data->pos_ref))
    {
      $sc = FALSE;
      $this->error = "pos_ref {$data->pos_ref} already exists";
      $this->add_logs('WM', 'create', 'error', $this->error, $json);
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    $customer = $this->customers_model->get($data->customer_code);

    if(empty($customer))
    {
      $sc = FALSE;
      $this->error = "Invalid Customer code : {$data->customer_code}";
      $this->add_logs('WM', 'create', 'error', $this->error, $json);
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    $zone = $this->zone_model->get($data->zone_code);

    if(empty($zone))
    {
      $sc = FALSE;
      $this->error = "Invalid Zone code : {$data->zone_code}";
      $this->add_logs('WM', 'create', 'error', $this->error, $json);
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    if( ! $this->zone_model->is_exists_customer($zone->code, $customer->code))
    {
      $sc = FALSE;
      $this->error = "No matching records found, Customer and Zone missmatch";
      $this->add_logs('WM', 'create', 'error', $this->error, $json);
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    //--- check each item code
    if(empty($data->items))
    {
			$sc = FALSE;
			$this->error = "Missing required parameter : Items";
      $this->add_logs('WM', 'create', 'error', $this->error, $json);
      $this->response(array('status' => FALSE, 'message' => $this->error), 200);
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
      $this->add_logs('WM', 'create', 'error', $this->error, $json);
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    //---- all data validated
    if($sc === TRUE)
    {
      $date = empty($data->date) ? date('Y-m-d H:i:s') : db_date($data->date, TRUE);
      $minDate  = date_create('2024-02-01');
      $date_add = date_create($date) > $minDate ? $date : date('Y-m-d H:i:s');
      $code = $this->get_new_code($date_add);
      $bookcode = getConfig('BOOK_CODE_CONSIGN_SOLD');

      $arr = array(
        'code' => $code,
        'bookcode' => $bookcode,
        'customer_code' => $customer->code,
        'customer_name' => $customer->name,
        'zone_code' => $zone->code,
        'zone_name' => $zone->name,
        'warehouse_code' => $zone->warehouse_code,
        'remark' => empty($data->remark) ? NULL : get_null($data->remark),
        'date_add' => $date_add,
        'shipped_date' => $date_add,
        'user' => $this->user,
        'status' => 1,
        'pos_ref' => $data->pos_ref,
        'is_api' => 1
      );

      $this->db->trans_begin();

      if( ! $this->consign_order_model->add($arr))
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
            'consign_code' => $code,
            'style_code' => $item->style_code,
            'product_code' => $item->code,
            'product_name' => $item->name,
            'cost' => $item->cost,
            'price' => $rs->price,
            'qty' => $rs->qty,
            'discount' => $rs->discount_label, //-- discount label per item
            'discount_amount' => $rs->discount_amount * $rs->qty,
            'amount' => $rs->line_total,
            'status' => 1,
            'pos_ref' => $data->pos_ref,
            'bill_ref' => $rs->bill_ref,
            'input_type' => 4
          );

          $id = $this->consign_order_model->add_detail($arr);

          if( ! $id)
          {
            $sc = FALSE;
            $this->error = "Faild to add item : {$item->code}, {$rs->bill_ref}";
          }

          if($sc === TRUE)
          {
            $final_price = $rs->line_total/$rs->qty;

            //--- ข้อมูลสำหรับบันทึกยอดขาย
            $arr = array(
              'reference' => $code,
              'role'   => 'M',
              'product_code'  => $item->code,
              'product_name'  => $item->name,
              'product_style' => $item->style_code,
              'cost'  => $item->cost,
              'price'  => $rs->price,
              'sell'  => $final_price,
              'qty'   => $rs->qty,
              'discount_label'  => $rs->discount_label,
              'discount_amount' => $rs->discount_amount * $rs->qty,
              'total_amount'   => $rs->line_total,
              'total_cost'   => $item->cost * $rs->qty,
              'margin'  =>  ($final_price * $rs->qty) - ($item->cost * $rs->qty),
              'id_policy'   => NULL,
              'id_rule'     => NULL,
              'customer_code' => $customer->code,
              'customer_ref' => NULL,
              'sale_code'   => NULL,
              'user' => $this->user,
              'date_add'  => $date_add,
              'zone_code' => $zone->code,
              'warehouse_code'  => $zone->warehouse_code,
              'update_user' => $this->user,
              'order_detail_id' => $id
            );

            //--- บันทึกขาย
            if( ! $this->delivery_order_model->sold($arr))
            {
              $sc = FALSE;
              $this->error = 'Sales record failed';
            }

            if($sc === TRUE)
            {
              //--- update movement
              $arr = array(
                'reference' => $code,
                'warehouse_code' => $zone->warehouse_code,
                'zone_code' => $zone->code,
                'product_code' => $item->code,
                'move_in' => 0,
                'move_out' => $rs->qty,
                'date_add' => $date_add
              );

              if(! $this->movement_model->add($arr))
              {
                $sc = FALSE;
                $this->error = 'Failed to add stock movement';
              }
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

        if($sc === TRUE)
        {
          $this->load->library('export');
          $this->export->export_consign_order($code);
        }

        if($sc === TRUE)
        {
          //--- add logs
          $this->add_logs($code, 'create', 'success', 'success', $json);

          $arr = array(
            'status' => TRUE,
            'message' => 'success',
            'code' => $code
          );

          $this->add_logs($code, 'create', 'response', 'success', json_encode($arr));

          $this->response($arr, 200);
        }
        else
        {
          //--- add logs
          $this->add_logs('WM', 'create', 'error', $this->error, $json);
        }
      }
    }
  } //-- end function create


  public function cancle_post()
  {
    $sc = TRUE;
    $json = file_get_contents('php://input');

    $data = json_decode($json);

    if(empty($data))
    {
      $sc = FALSE;
      $this->error = "Missing required parameters";
      $this->add_logs('WM', 'cancel', $this->error, $json);
      $this->response(['status' => FALSE, 'error' => $this->error], 400);
    }

    if(empty($data->code))
    {
      $sc = FALSE;
      $this->error = "Missing required parameter: code";
      $this->add_logs('WM', 'cancel', $this->error, $json);
      $this->response(['status' => FALSE, 'error' => $this->error], 400);
    }

    if(empty($data->cancel_reason))
    {
      $sc = FALSE;
      $this->error = "Missing required parameter: cancel_reason";
      $this->add_logs('WM', 'cancel', $this->error, $json);
      $this->response(['status' => FALSE, 'error' => $this->error], 400);
    }

    $code = $data->code;
    $reason = trim($data->cancel_reason);
    $doc = $this->consign_order_model->get($code);

    if(empty($doc))
    {
      $sc = FALSE;
      $this->error = "Invalid document number : {$code}";
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }

    if($doc->status != 2)
    {
      $do = $this->delivery_order_model->get_sap_delivery_order($code);

      if( ! empty($do))
      {
        $sc = FALSE;
        $this->error = "Unable to cancel : {$code} already imported into SAP. Please cancel this document in SAP before and try again";
        $this->response(['status' => FALSE, 'message' => $this->error], 200);
      }

      if($doc->status == 1)
      {
        //--- drop middle details
        $middle = $this->delivery_order_model->get_middle_delivery_order($code);

        if( ! empty($middle))
        {
          foreach($middle as $rows)
          {
            if( ! $this->delivery_order_model->drop_middle_exits_data($rows->DocEntry))
            {
              $sc = FALSE;
              $this->error = "Failed to delete temp data";
            }
          }
        }
      }

      if($sc === FALSE)
      {
        $this->response(['status' => FALSE, 'message' => $this->error], 200);
      }

      $this->db->trans_begin();

      //--- remove movement
      if( ! $this->movement_model->drop_movement($code))
      {
        $sc = FALSE;
        $this->error = "Failed to delete movement";
      }

      //--- Remove sold data
      if($sc === TRUE)
      {
        if( ! $this->invoice_model->drop_all_sold($code))
        {
          $sc = FALSE;
          $this->error = "Failed to delete sales records";
        }
      }

      if($sc === TRUE)
      {
        if( ! $this->consign_order_model->update_details($code, ['status' => 2]))
        {
          $sc = FALSE;
          $this->error = "Failed to update document items";
        }
      }

      if($sc === TRUE)
      {
        $arr = array(
          'status' => 2,
          'cancle_reason' => $reason,
          'cancle_user' => $this->user
        );

        if(! $this->consign_order_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = "Failed to cancel document";
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
      $this->add_logs('WM', 'cancel', 'success', $json);
    }
    else
    {
      $this->add_logs('WM', 'cancel', $this->error, $json);
    }

    if($sc === TRUE)
    {
      $this->response(['status' => TRUE, 'message' => "{$code} canceled successful"], 200);
    }
    else
    {
      $this->response(['status' => FALSE, 'message' => $this->error], 200);
    }
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_CONSIGN_SOLD');
    $run_digit = getConfig('RUN_DIGIT_CONSIGN_SOLD');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->consign_order_model->get_max_code($pre);
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


  public function add_logs($code = 'WM', $action = 'create', $status = 'error', $message = NULL, $json = NULL)
  {
    if($this->log_json)
    {
      $log = array(
        'trans_id' => genUid(),
        'code' => $code,
        'action' => $action,
        'status' => $status,
        'message' => $message,
        'json_text' => ($this->log_json ? $json : NULL)
      );

      $this->order_api_logs_model->logs_pos($log);
    }

    return TRUE;
  }


  public function verify_data($data)
	{
    if( ! property_exists($data, 'pos_ref') OR empty($data->pos_ref))
    {
      $this->error = 'Missing required parameter : pos_ref';
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
