<?php
class Order_status extends CI_Controller
{
  private $api_key = "adbd64b022fbb54d22e4d59437338741";
  public $error;
  private $user = "api@sokochan";

  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    $sc = TRUE;
    $api_key = $this->input->get('api_key');
    $code = $this->input->get('order_number');
    $status = $this->input->get('status');
    $date = date('Y-m-d H:i:s', $this->input->get('update_time'));

    if( ! $api_key)
    {
      $sc = FALSE;
      $this->error = "Missing required parameter : api_key";
    }

    if($sc === TRUE && $api_key != $this->api_key)
    {
      $sc = FALSE;
      $this->error = "Invalid Credential";
    }

    if($sc === TRUE && (empty($code) OR empty($status)))
    {
      $sc = FALSE;
      $this->error = "Missing require parameter : order_number or status";
    }

    if($sc === TRUE)
    {
      $this->load->model('orders/orders_model');
      $this->load->model('orders/order_state_model');

      $order = $this->orders_model->get($code);

      if( ! empty($order))
      {
        $stateList = array(
          'Pending' => 30,
          'Processing' => 31,
          'Processed' => 32,
          'Picked' => 33,
          'Packed' => 34,
          'Shipped' => 35,
          'Cancelled' => 36,
          'pending' => 30,
          'processing' => 31,
          'processed' => 32,
          'picked' => 33,
          'packed' => 34,
          'shipped' => 35,
          'cancelled' => 36
        );

        if( ! empty($stateList[$status]))
        {
          $state = $stateList[$status];

          $arr = array(
            'order_code' => $order->code,
            'state' => $state,
            'update_user' => $this->user,
            'date_upd' => $date
          );

          if( ! $this->order_state_model->add_wms_state($arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update order status";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid order status";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid order number";
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : FALSE,
      'message' => $sc === TRUE ? 'Order status updated' : $this->error
    );

    echo json_encode($arr);
  }
}

 ?>
