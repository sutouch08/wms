<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Delivery extends REST_Controller
{
	public $error;
  public $user;
  public $wms;
	public $api_path = "rest/api/soko/delivery";
	public $logs_json = FALSE;
	public function __construct()
  {
    parent::__construct();
		$this->wms = $this->load->database('wms', TRUE); //--- Temp database

		$this->load->model('rest/V1/soko_temp_order_model');
		$this->load->model('rest/V1/soko_api_logs_model');

		$this->logs_json = is_true(getConfig('SOKOJUNG_LOG_JSON'));
  }


	public function index_post()
  {
    //--- Get raw post data
		$json = file_get_contents("php://input");
    $ds = json_decode($json);

    if(empty($ds))
    {
			$arr = array(
        'status' => FALSE,
        'error' => 'empty data'
      );

			if($this->logs_json)
			{
				$logs = array(
					'trans_id' => genUid(),
					'api_path' => $this->api_path,
					'type' =>NULL,
					'code' => NULL,
					'action' => 'shipped',
					'status' => 'failed',
					'message' => 'empty data',
					'request_json' => $json,
					'response_json' => json_encode($arr)
				);

				$this->soko_api_logs_model->add_api_logs($logs);
			}

      $this->response($arr, 400);
    }

		if( ! empty($ds))
		{

			$order_code = NULL;

			$sc = TRUE;
			$err = "";


			$this->wms->trans_begin();

			$is_completed = $this->soko_temp_order_model->is_order_completed($ds->order_number);

			if($is_completed)
			{
				$sc = FALSE;
				$this->error = "{$ds->order_number} already completed";
			}
			else
			{
				$not_complete = $this->soko_temp_order_model->get_temp_notcomplete_order($ds->order_number);

				if( ! empty($not_complete))
				{
					foreach($not_complete as $rows)
					{
						//--- drop not complete before add new data
						if(! $this->soko_temp_order_model->drop_temp_exists_data($rows->id))
						{
							$sc = FALSE;
							$this->error = "ลบข้อมูลเก่าใน Temp ไม่สำเร็จ";
						}
					}
				}

				if($sc === TRUE)
				{
					$arr = array(
						'shipped_date' => (empty($ds->shipped_date) ? now() : $ds->shipped_date),
						'code' => $ds->order_number,
						'reference' => get_null($ds->reference)
					);

					$id = $this->soko_temp_order_model->add($arr);

					if(! $id)
					{
						$sc = FALSE;
						$this->error = "Failed to insert data";
					}
					else
					{
						$details = $ds->details;


						if( ! empty($details))
						{
							foreach($details as $rs)
							{
								$arr = array(
									'id_order' => $id,
									'order_code' => $ds->order_number,
									'product_code' => $rs->item_sku,
									'qty' => $rs->qty
								);

								if(! $this->soko_temp_order_model->add_detail($arr))
								{
									$sc = FALSE;
									$this->error = "Failed to insert item rows : {$ds->order_number} : {$rs->item_sku}";
								}
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "Items rows not found";
						}

            if( ! empty($ds->shipping_details))
            {
              foreach($ds->shipping_details as $rs)
              {
                $arr = array(
                  'id_order' => $id,
                  'order_code' => $ds->order_number,
                  'product_code' => $rs->sku,
                  'carton_code' => $rs->carton_number,
                  'tracking_no' => $rs->tracking_number,
                  'courier_code' => $ds->courier_code,
                  'courier_name' => $ds->courier_name,
                  'qty' => empty($rs->qty) ? 1 : $rs->qty
                );

                $this->soko_temp_order_model->add_tracking($arr);
              }
            }
					}
				}
			}

			if($sc === TRUE)
			{
				$this->soko_api_logs_model->add($ds->order_number, 'S', NULL, $ds->reference);
				$this->wms->trans_commit();
			}
			else
			{
				$this->wms->trans_rollback();
				$this->soko_api_logs_model->add($ds->order_number, 'E', $this->error, $ds->reference);
			}
		}

		if($sc === TRUE)
		{
			$arr = array(
				'status' => 'success',
				'order_number' => $ds->order_number
			);

			if($this->logs_json)
			{
				$logs = array(
					'trans_id' => genUid(),
					'api_path' => $this->api_path,
					'type' => $ds->order_type,
					'code' => $ds->order_number,
					'action' => 'shipped',
					'status' => 'success',
					'message' => 'success',
					'request_json' => $json,
					'response_json' => json_encode($arr)
				);

				$this->soko_api_logs_model->add_api_logs($logs);
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
					'type' => $ds->order_type,
					'code' => $ds->order_number,
					'action' => 'shipped',
					'status' => 'failed',
					'message' => $this->error,
					'request_json' => $json,
					'response_json' => json_encode($arr)
				);

				$this->soko_api_logs_model->add_api_logs($logs);
			}

			$this->response($arr, 200);
		}
	}//-- end create
}

//--- end class
?>
