<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Receive extends REST_Controller
{
	public $error;
  public $user;
  public $wms;
	public $test_mode = FALSE;

	public function __construct()
  {
    parent::__construct();
		$this->wms = $this->load->database('wms', TRUE); //--- Temp database

		$this->load->model('rest/V1/wms_temp_receive_model');
		$this->load->model('rest/V1/wms_error_logs_model');

		$this->test_mode = getConfig('WMS_TEST_MODE') == 1 ? TRUE : FALSE;
  }


	public function index_post()
  {

		$message = "";
		$error_mesage = array();
    //--- Get raw post data
    $data_set = json_decode(file_get_contents("php://input"));


    if(empty($data_set))
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'empty data'
      );

			$this->wms_error_logs_model->add(NULL, 'E', 'empty data');

      $this->response($arr, 400);
    }


		if(!empty($data_set))
		{
			$trans_no = $data_set->order_list_no;

			if(empty($data_set->data))
			{
				$arr = array(
	        'status' => FALSE,
	        'error' => 'empty data'
	      );

				$this->wms_error_logs_model->add(NULL, 'E', 'empty data', $trans_no);

	      $this->response($arr, 400);
			}
			else
			{

				foreach($data_set->data as $ds)
				{
					$arr = array(
						'code' => $ds->order_number,
						'reference' => get_null($ds->reference),
						'type' => $ds->type
					);

					$sc = TRUE;
					$err = "";


					$this->wms->trans_begin();

					if($this->test_mode)
					{
						$is_exists = FALSE;
					}
					else
					{
						$is_exists = $this->wms_temp_receive_model->is_exists($ds->order_number);
					}
					
					if($is_exists)
					{
						$sc = FALSE;
						$err = $ds->order_number.' already exists';
						array_push($error_mesage, array('order_number' => $ds->order_number, 'error_message' => $err));
					}
					else
					{
						$id = $this->wms_temp_receive_model->add($arr);

						if(! $id)
						{
							$sc = FALSE;
							$error = $this->wms->error();
							$err = $error['message'];
							array_push($error_mesage, array('order_number' => $ds->order_number, 'error_message' => $err));
						}
						else
						{
							$details = $ds->details;
							if(!empty($details))
							{
								foreach($details as $rs)
								{
									$arr = array(
										'id_receive' => $id,
										'receive_code' => $ds->order_number,
										'product_code' => $rs->item,
										'qty' => $rs->qty
									);

									if(! $this->wms_temp_receive_model->add_detail($arr))
									{
										$sc = FALSE;
										$error = $this->wms->error();
										$err = $error['message'];
										array_push($error_mesage, array('order_number' => $ds->order_number, 'error_message' => $err));
									}
								}
							}
							else
							{
								$sc = FALSE;
								$err = "Empty Order details";
								array_push($error_mesage, array('order_number' => $ds->order_number, 'status' => 'Empty Order details'));
							}
						}
					}


					if($sc === TRUE)
					{
						$this->wms_error_logs_model->add($ds->order_number, 'S', NULL, $trans_no);
						$this->wms->trans_commit();
					}
					else
					{
						$this->wms->trans_rollback();
						$this->wms_error_logs_model->add($ds->order_number, 'E', $err, $trans_no);
					}
				}
			}

		}

		if(!empty($error_mesage))
		{
			$arr = array(
        'status' => FALSE,
        'error' => $error_mesage
      );

			$this->response($arr, 200);
		}
		else
		{
			$arr = array(
        'status' => 'SUCCESS',
        'order_list_no' => $trans_no
      );

      $this->response($arr, 200);
		}

	}//-- end create


}

//--- end class
?>
