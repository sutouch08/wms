<?php
class Auto_send_tracking extends CI_Controller
{
	public $error;
	public $isApi = FALSE;
	public $wms;

  public function __construct()
  {
    parent::__construct();
    $this->load->model('orders/orders_model');
    $this->load->library('wrx_web_api');
		$this->isApi = getConfig('WRX_WEB_TRACKING_API') == 1 ? TRUE : FALSE;
  }

	public function index($show = 0)
	{
		if($this->isApi)
		{
			$this->wms = $this->load->database('wms', TRUE); //--- Temp database

			ini_set('memory_limit','512M');
			ini_set('max_execution_time', 600);

			$id_sender = getConfig('SPX_ID');

			if( ! empty($id_sender))
			{
				$limit = getConfig('WEB_TRACKING_PER_ROUND');
				$limit = $limit > 0 ? $limit : 10;

				$list = $this->getUnsendTrackingList($id_sender, $limit);

				if( ! empty($list))
				{
					if($show == 1)
					{
						echo "found ".count($list)." orders <br/>";
					}

					foreach($list as $rs)
					{
						if( ! empty($rs->tracking))
						{
							if( ! $this->wrx_web_api->create_shipment($rs->reference, $rs->tracking))
							{
								if($show)
								{
									echo "{$rs->code} : Failed <br/>";
								}

								$this->add_logs(['status' => 'failed', 'message' => $this->wrx_web_api->error]);
								$this->orders_model->update($rs->code, ['send_tracking' => 3, 'send_tracking_error' => $this->wrx_web_api->error]);
							}
							else
							{
								if($show)
								{
									echo "{$rs->code} : Success <br/>";
								}

								$this->add_logs(['status' => 'failed', 'message' => $this->wrx_web_api->error]);
								$this->orders_model->update($rs->code, ['send_tracking' => 1, 'send_tracking_error' => NULL]);
							}
						}
						else
						{
							if($show)
							{
								echo "No tracking on : {$rs->code} <br/>";
							}
						}

						if($show == 1)
						{
							echo "END ------------------------------------------------------------ END<br/>";
						}
					}
				}
				else
				{
					if($show)
					{
						echo "no data to send <br/>";
					}

					$this->add_logs(['status' => 'OK', 'message' => "no data to send"]);
				}
			}
			else
			{
				$arr = array(
				'status' => 'failed',
				'message' => 'No SPX ID'
				);

				$this->add_logs($arr);
			}
		}
	}


  public function add_logs(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('po_export_logs', $ds);
    }

    return FALSE;
  }


  public function getUnsendTrackingList($id_sender, $limit = 100)
  {
		$days = 30;
		$date = date('Y-m-d 00:00:00', strtotime("-{$days} days"));

    $rs = $this->db
    ->select('code, reference, shipping_code AS tracking')
    ->where('role', 'S')
    ->where('channels_code', 'WRX12')
    ->where('id_sender', $id_sender)
		->group_start()
    ->where('send_tracking IS NULL', NULL, FALSE)
		->or_where('send_tracking', 3)
		->group_end()
    ->where('state', 8)
    ->where('reference IS NOT NULL')
		->where('date_add >=', $date)
    ->order_by('code', 'ASC')
    ->limit($limit)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
} //-- end class
 ?>
