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
    ini_set('memory_limit','512M');
    ini_set('max_execution_time', 600);

    $limit = 10;
    $id_sender = getConfig('SPX_ID');

    if( ! empty($id_sender))
    {
      $list = $this->orders_model->getUnsendTrackingList($id_sender, $limit);

      if( ! empty($list))
      {
        echo "found ".count($list)." orders <br/>";

        foreach($list as $rs)
        {
          $tracking = $this->orders_model->get_order_tracking($rs->code);

          $ds = array();

          if( ! empty($tracking))
          {
            foreach($tracking as $tk)
            {
              echo "{$rs->code} : {$tk->tracking_no} <br/>";
              array_push($ds, ['track_no' => $tk->tracking_no]);
            }
          }
          else
          {
            echo "No tracking on : {$rs->code} <br/>";
            $this->orders_model->update($rs->code, ['send_tracking' => 1]);
          }

          if(count($ds) > 0)
          {
            $arr = array(
              'tracking' => $ds
            );

            $result = $this->api->create_shipment($rs->reference, $arr);

            echo "Result : ". (($result == TRUE OR $result == 'true') ? 'Success' : 'Faild')."<br/>";

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

          echo "END ------------------------------------------------------------ END<br/>";
        }
      }
      else
      {
        echo "no data to send <br/>";
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


  public function test()
  {
    ini_set('memory_limit','512M');
    ini_set('max_execution_time', 600);

    $limit = 10;
    $id_sender = getConfig('SPX_ID');

    if( ! empty($id_sender))
    {
      $list[] = (object) array('code' => 'WO-240420821', 'reference' => 'W24031386111', 'tracking_no' => 'SPXTH047036533944');
      $list[] = (object) array('code' => 'WO-240420822', 'reference' => 'W24031386101', 'tracking_no' => 'SPXTH041110343204');
      $list[] = (object) array('code' => 'WO-240420823', 'reference' => 'W24031386091', 'tracking_no' => 'SPXTH048617378474');
      $list[] = (object) array('code' => 'WO-240420824', 'reference' => 'W24031386081', 'tracking_no' => 'SPXTH045516500334');
      $list[] = (object) array('code' => 'WO-240420825', 'reference' => 'W24031386071', 'tracking_no' => 'SPXTH044775383754');
      $list[] = (object) array('code' => 'WO-240420826', 'reference' => 'W24031386051', 'tracking_no' => 'SPXTH049936822164');
      $list[] = (object) array('code' => 'WO-240420827', 'reference' => 'W24031386041', 'tracking_no' => 'SPXTH048232752484');

      // $list = $this->orders_model->getUnsendTrackingList($id_sender, $limit);
      echo "<pre>";
      print_r($list);
      echo "</pre>";
      //exit();

      if( ! empty($list))
      {
        foreach($list as $rs)
        {
          //$tracking = $this->orders_model->get_order_tracking($rs->code);
          $ds = array();

          array_push($ds, ['track_no' => $rs->tracking_no]);
          // if( ! empty($tracking))
          // {
          //   foreach($tracking as $tk)
          //   {
          //     echo "{$rs->code} : {$tk->tracking_no} <br/>";
          //     array_push($ds, ['track_no' => $tk->tracking_no]);
          //   }
          // }
          // else
          // {
          //   echo "No tracking on : {$rs->code} <br/>";
          //   $this->orders_model->update($rs->code, ['send_tracking' => 1]);
          // }

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

          echo "----------------------------<br/>";
        }
      }
      else
      {
        echo "no data to send <br/>";
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

  public function getUnsendTrackingList($id_sender, $limit = 100)
  {
    $rs = $this->db
    ->select('code, reference')
    ->where('role', 'S')
    ->where('channels_code', 'WRX12')
    ->where('id_sender', $id_sender)
    ->where('send_tracking IS NULL', NULL, FALSE)
    ->where('state', 8)
    ->where('reference IS NOT NULL')
    ->where('date_add >=', '2024-04-01 00:00:00')
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
