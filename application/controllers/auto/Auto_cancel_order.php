<?php
class Auto_cancel_order extends CI_Controller
{
  public $home;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'auto/auto_cancel_order';
    $this->load->model('orders/orders_model');
  }

  public function index()
  {
    $this->load->view('auto/auto_cancel_order');
  }


  public function auto_expire_order()
	{
		//--- จำนวนวันสูงสุดของออเดอร์ที่อยู่ในสถานะรอดำเนินการ รอชำระเงิน และ รอจัดสินค้า
		//---- ถ้าออเดอร์อยู่ใน 3 สถานะนี้นานเกิน จำนวนวันที่กำหนด จะทำให้ออเดอร์หมดอายุ
		//---- ระบบจะไม่นำยอดที่หมดอายุแล้วมาคำนวนยอดจอง ทำให้สต็อกเพิ่มขึ้น
		$limit = getConfig('ORDER_EXPIRATION');
      
		$end_date = date('Y-m-d H:i:s', strtotime("-{$limit} days"));

		$list = $this->orders_model->get_expire_list($end_date, $role);

		if(!empty($list))
		{
			foreach($list as $rs)
			{
				$this->orders_model->set_expire_order($rs->code);
        $this->orders_model->set_expire_order_details($rs->code);
			}
		}

	}


} //--- end class
 ?>
