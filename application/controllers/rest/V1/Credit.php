<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Credit extends REST_Controller
{
  public $ms;
  public $error;

  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE);

    $this->load->model('masters/customers_model');
    $this->load->model('orders/orders_model');
  }

  //---- get credit balance;
  public function index_get($code = '')
  {
    if($code != '')
    {
      $customer = $this->customers_model->get($code);
      //--- creadit used
      $credit_used = $this->orders_model->get_sum_not_complete_amount($code);
      //--- credit balance from sap
      $credit_balance = $this->customers_model->get_credit($code);

      $balance = $credit_balance - $credit_used;

      if(!empty($credit_balance))
      {
        $ds = array(
          'status' => 'SUCCESS',
          'data' => array(
            'code' => $customer->code,
            'name' => $customer->name,
            'balance' => $balance,
            'status' => $customer->active == 1 ? 'active' : 'suspended'
          )
        );

        $this->response($ds, 200);
      }
      else
      {
        $ds = array(
          'status' => 'FALSE',
          'message' => 'Invalid Customer code'
        );

        $this->response($ds, 200);
      }

    }
    else
    {
      $ds = array(
        'status' => 'FALSE',
        'message' => 'Missing required parameter : customer_code'
      );

      $this->response($ds, 400);
    }
  }
} //--- end class


?>
