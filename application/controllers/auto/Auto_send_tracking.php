<?php
class Auto_send_tracking extends CI_Controller
{
	public $error;

  public function __construct()
  {
    parent::__construct();
    $this->load->model('orders/orders_model');
    $this->load->library('api');
  }

  public function index()
  {
    $limit = 2;
    $id_sender = getConfig('SPX_ID');

    if( ! empty($id_sender))
    {
      $list = $this->orders_model->getUnsendTrackingList($id_sender, $limit);

      if( ! empty($list))
      {
        foreach($list as $rs)
        {
          $tracking = $this->orders_model->get_order_tracking($rs->code);

          $ds = array();

          if( ! empty($tracking))
          {
            foreach($tracking as $tk)
            {
              array_push($ds, ['track_no' => $tk->tracking_no]);
            }
          }

          if(count($ds) > 0)
          {
            $arr = array(
              'tracking' => $ds
            );

            $result = $this->api->create_shipment($rs->reference, $arr);
            echo $result;

            if($result === TRUE || $result == 'true')
            {
              $this->add_logs(['status' => 'success']);
              $this->orders_model->update($rs->code, ['send_tracking' => 1]);
            }
            else
            {
              $this->add_logs(['status' => 'failed', 'message' => $result]);
              $this->orders_model->update($rs->code, ['send_tracking' => 3, 'send_tracking_error' => $result]);              
            }
          }
        }
      }
      else
      {
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

  public function add_logs(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('po_export_logs', $ds);
    }

    return FALSE;
  }

} //-- end class
 ?>
