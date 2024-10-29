<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class WW extends REST_Controller
{
  public $error;
  public $ms;
  public $mc;
  public $logs;
  public $user;
  public $api = FALSE;
  public $logs_json = FALSE;
  private $path = "/rest/api/pos/WW/";

  public function __construct()
  {
    parent::__construct();

    $this->api = is_true(getConfig('POS_API'));

    if($this->api)
    {
      $this->ms = $this->load->database('ms', TRUE);
      $this->mc = $this->load->database('mc', TRUE);
      $this->logs = $this->load->database('logs', TRUE); //--- api logs database
      $this->logs_json = is_true(getConfig('POS_LOG_JSON'));
      $this->user = "pos@warrix.co.th";

      $this->load->model('inventory/transfer_model');
      $this->load->model('inventory/movement_model');
      $this->load->model('masters/products_model');
      $this->load->model('masters/warehouse_model');
      $this->load->model('masters/zone_model');
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
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WW',
          'code' => NULL,
          'action' => 'create',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
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
          'type' =>'WW',
          'code' => $data->pos_ref,
          'action' => 'create',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
    }


    if($this->transfer_model->is_exists_pos_ref($data->pos_ref))
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
          'type' =>'WW',
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

    $fWhs = $this->warehouse_model->get($data->from_warehouse);
    $tWhs = $this->warehouse_model->get($data->to_warehouse);
    $fZone = $this->zone_model->get($data->from_zone);
    $tZone = $this->zone_model->get($data->to_zone);

    if(empty($fWhs) OR empty($tWhs))
    {
      $sc = FALSE;
      $this->error = "Invalid Warehouse code : ".(empty($fWhs) ? $data->from_warehouse : $data->to_warehouse);

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WW',
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


    if(empty($fZone) OR empty($tZone))
    {
      $sc = FALSE;
      $this->error = "Invalid Zone code : ".(empty($fZone) ? $data->from_zone : $data->to_zone);

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WW',
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


    if( $fZone->warehouse_code != $data->from_warehouse OR $tZone->warehouse_code != $data->to_warehouse)
    {
      $sc = FALSE;
      $this->error = "Warehouse and Zone missmatch !";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WW',
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
          'type' =>'WW',
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
      if($rs->qty > 0)
      {
        //---- check valid items
        $item = $this->products_model->get($rs->product_code);

        if(empty($item))
        {
          $sc = FALSE;
          $this->error = "Invalid Product code : {$rs->product_code}";
        }
        else
        {
          $rs->item = $item;
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Quantity must be greater than 0 : {$rs->product_code} : {$rs->qty}";
      }

      if($sc === FALSE)
      {
        break;
      }
    } //--- end foreach

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
          'type' =>'WW',
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
      $minDate  = date_create('2024-10-01');
      $date_add = date_create($date) > $minDate ? $date : date('Y-m-d H:i:s');
      $code = $this->get_new_code($date_add);
      $bookcode = getConfig('BOOK_CODE_TRANSFER');

      $arr = array(
        'code' => $code,
        'bookcode' => $bookcode,
        'from_warehouse' => $fWhs->code,
        'to_warehouse' => $tWhs->code,
        'remark' => get_null(trim($data->remark)),
        'user' => $this->user,
        'date_add' => $date_add,
        'status' => 1,
        'valid' => 1,
        'is_pos' => 1,
        'pos_ref' => $data->pos_ref
      );

      $this->db->trans_begin();

      if( ! $this->transfer_model->add($arr))
      {
        $sc = FALSE;
        $this->error = "Failed to Create Document Please try again later";
      }

      if($sc === TRUE)
      {
        foreach($data->items as $rs)
        {
          //--- add new row
          $item = $rs->item;
          $arr = array(
            'transfer_code' => $code,
            'product_code' => $item->code,
            'product_name' => $item->name,
            'from_zone' => $fZone->code,
            'to_zone' => $tZone->code,
            'qty' => $rs->qty,
            'valid' => 1
          );

          if( ! $this->transfer_model->add_detail($arr))
          {
            $sc = FALSE;
            $this->error = "Insert data failed";
          }
          else
          {
            //--- update movement
            $move_out = array(
              'reference' => $code,
              'warehouse_code' => $fWhs->code,
              'zone_code' => $fZone->code,
              'product_code' => $item->code,
              'move_in' => 0,
              'move_out' => $rs->qty,
              'date_add' => $date_add
            );

            //--- move out
            if(! $this->movement_model->add($move_out))
            {
              $sc = FALSE;
              $this->error = 'Failed to create outgoing movement logs';
            }
            else
            {
              $move_in = array(
                'reference' => $code,
                'warehouse_code' => $tWhs->code,
                'zone_code' => $tZone->code,
                'product_code' => $item->code,
                'move_in' => $rs->qty,
                'move_out' => 0,
                'date_add' => $date_add
              );

              //--- move in
              if(! $this->movement_model->add($move_in))
              {
                $sc = FALSE;
                $this->error = 'Failed to create incomming movement logs';
              }
            }
          }

          if($sc === FALSE)
          {
            break;
          }
        } //--- foreach items

      } //--- create success

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

        if($this->export->export_transfer($code))
        {
          $this->transfer_model->set_export($code, 1);
        }
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
            'type' =>'WW',
            'code' => $data->pos_ref,
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
            'type' =>'WW',
            'code' => $data->pos_ref,
            'action' => 'create',
            'status' => 'failed',
            'message' => "Internal Error : ".$this->error,
            'request_json' => $json,
            'response_json' => json_encode($arr)
          );

          $this->pos_api_logs_model->add_api_logs($logs);
        }

        $this->response($arr, 200);
      }
    }
  } //-- end function create


  public function cancel_post()
  {
    $api_path = $this->path."cancel";

    $sc = TRUE;

    //--- Get raw post data
    $json = file_get_contents("php://input");

    $code = NULL;

    $data = json_decode($json);

    if(empty($data))
    {
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
          'type' =>'WW',
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

    if(empty($data->code) OR empty($data->cancel_reason))
    {
      $this->error = "Missing required parameters : ".(empty($data->code) ? 'code' : 'cancel_reason');

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WW',
          'code' => empty($data->code) ? NULL : $data->code,
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

    //---- all data validated
    if($sc === TRUE)
    {
      $doc = $this->transfer_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status != 2)
        {
          if($doc->status == 1)
          {
            $sap = $this->transfer_model->get_sap_transfer_doc($code);

            if( ! empty($sap))
            {
              $sc = FALSE;
              $this->error = "Status error : Document already completed, cancellation cannot be completed";
            }

            if($sc === TRUE)
            {
              $middle = $this->transfer_model->get_middle_transfer_doc($code);

              if( ! empty($middle))
              {
                foreach($middle as $rs)
                {
                  $this->transfer_model->drop_middle_exits_data($rs->DocEntry);
                }
              }
            }
          } //--- if doc->status == 1

          if($sc === TRUE)
          {
            $this->db->trans_begin();

            //--- clear temp
            if( ! $this->transfer_model->drop_all_temp($code))
            {
              $sc = FALSE;
              $this->error = "Internal transection error : Failed to delete transfer temp";
            }

            //--- delete detail
            if( ! $this->transfer_model->drop_all_detail($code))
            {
              $sc = FALSE;
              $this->error = "Internal transection error : Failed to delete transfer rows";
            }

            //--- drop movement
            if( ! $this->movement_model->drop_movement($code))
            {
              $sc = FALSE;
              $this->error = "Internal transection error : Failed to delete movement";
            }

            if($sc === TRUE)
            {
              //--- change status to 2 (cancled)
              $arr = array(
                'status' => 2,
                'inv_code' => NULL,
                'cancle_reason' => $data->cancel_reason,
                'cancle_user' => $this->user
              );

              if( ! $this->transfer_model->update($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Internal transection error : Failed to update document status";
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
          } //--- $sc === TRUE
        } //--- $doc->status != 2
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }

      if($sc === TRUE)
      {
        $arr = array(
          'status' => TRUE,
          'message' => "The cancellation was successful",
          'code' => $code
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => genUid(),
            'api_path' => $api_path,
            'type' =>'WW',
            'code' => $code,
            'action' => 'cancel',
            'status' => 'success',
            'message' => 'The cancellation was successful',
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
            'type' =>'WW',
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
  } //-- end function create


  //--- for POS
	public function get_get($code = NULL, $test = FALSE)
	{
    $api_path = $this->path."get";

    $sc = TRUE;
    $rows = array();

    if(empty($code))
    {
      $sc = FALSE;
      $this->error = "Missing required parameter : document code";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WW',
          'code' => NULL,
          'action' => 'get',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }

    $order = $this->transfer_model->get($code);

    if(empty($order))
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
          'type' =>'WW',
          'code' => $code,
          'action' => 'get',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }


    if($order->status != 1)
    {
      $sc = FALSE;
      $this->error = "Invalid document status";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WW',
          'code' => $code,
          'action' => 'get',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }

    $details = $this->transfer_model->get_details($code);

    if( empty($details))
    {
      $sc = FALSE;
      $this->error = "No item in document.";

      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WW',
          'code' => $code,
          'action' => 'get',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->pos_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 200);
    }
    else
    {
      foreach($details as $rs)
      {
        $qty = ($order->is_wms == 1 && $order->api == 1) ? $rs->wms_qty : $rs->qty;
        $row = new stdClass();
        $row->product_code = $rs->product_code;
        $row->product_name = $rs->product_name;
        $row->unit_code = $rs->unit_code;
        $row->qty = $qty;
        $row->from_zone = $rs->from_zone;
        $row->to_zone = $rs->to_zone;

        array_push($rows, $row);
      }
    }

    $ds = array(
      'code' => $order->code,
      'from_warehouse' => $order->from_warehouse,
      'to_warehouse' => $order->to_warehouse,
      'doc_date' => $order->date_add,
      'shipped_date' => $order->shipped_date,
      'rows' => $rows
    );

    $arr = array(
      'status' => TRUE,
      'message' => 'success',
      'data' => $ds
    );

    if($this->logs_json)
    {
      $logs = array(
        'trans_id' => genUid(),
        'api_path' => $api_path,
        'type' =>'WW',
        'code' => $code,
        'action' => 'get',
        'status' => 'success',
        'message' => $this->error,
        'request_json' => $json,
        'response_json' => json_encode($arr)
      );

      $this->pos_api_logs_model->add_api_logs($logs);
    }

    $this->response($arr, 200);
	}


  public function verify_data($data)
	{
    if( ! property_exists($data, 'pos_ref') OR empty($data->pos_ref))
    {
      $this->error = 'Missing required parameter : pos_ref';
      return FALSE;
    }

    if(! property_exists($data, 'from_warehouse') OR empty($data->from_warehouse))
    {
      $this->error = 'Missing required parameter : from_warehouse';
			return FALSE;
    }

    if(! property_exists($data, 'to_warehouse') OR empty($data->to_warehouse))
    {
      $this->error = 'Missing required parameter : to_warehouse';
			return FALSE;
    }


		if(! property_exists($data, 'from_zone') OR empty($data->from_zone))
		{
			$this->error = "Missing required parameter : from_zone";
			return FALSE;
		}

    if(! property_exists($data, 'to_zone') OR empty($data->to_zone))
		{
			$this->error = "Missing required parameter : to_zone";
			return FALSE;
		}

		return TRUE;
	}


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_TRANSFER');
    $run_digit = getConfig('RUN_DIGIT_TRANSFER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->transfer_model->get_max_code($pre);
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
?>
