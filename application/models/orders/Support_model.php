<?php
class Support_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_budget($code)
  {
    $rs = $this->ms
    ->select('(PlanAmtLC - (UndlvAmntL + CumAmntLC)) AS amount', FALSE)
    ->from('OOAT')
    ->join('OAT1', 'OOAT.AbsID = OAT1.AgrNo', 'inner')
    ->where('BpCode', $code)
		->where('OOAT.StartDate <=', now())
		->where('OOAT.EndDate >=', now())
    ->where('OOAT.Status', 'A')
    ->order_by('OOAT.AbsID', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row()->amount;
    }

    return 0;
  }



  public function get_budget_used($code)
  {
    $rs = $this->db
    ->select_sum('total_amount')
    ->from('order_details')
    ->join('orders', 'orders.code = order_details.order_code', 'left')
    ->where('orders.role', 'U')
    ->where('orders.state !=', 9)
    ->where('orders.customer_code', $code)
    ->where('order_details.is_complete', 0)
    ->where('orders.is_expired', 0)
    ->get();

    return is_null($rs->row()->total_amount) ? 0 : $rs->row()->total_amount;
  }



} //--- end class

 ?>
