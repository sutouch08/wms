<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Order_details extends PS_Controller
{
  public $menu_code = 'RAODDT';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REAUDIT';
	public $title = 'รายงานสถานะเอกสารแสดงรายละเอียด';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/audit/order_details';
    $this->load->model('report/audit/order_details_model');
    $this->load->model('masters/products_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/channels_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('masters/warehouse_model');
  }

  public function index()
  {
    $ds = array(
      'channels_list' => $this->channels_model->get_data(),
      'payment_list' => $this->payment_methods_model->get_data(),
      'warehouse_list' => $this->warehouse_model->get_sell_warehouse_list()
    );

    $this->load->view('report/audit/report_order_details', $ds);
  }



  public function get_report()
  {
    $sc = TRUE;

    if($this->input->get("json"))
    {
      $json = json_decode($this->input->get("json"));

      if( ! empty($json))
      {
        $filter = array(
          "from_date" => $json->fromDate,
          "to_date" => $json->toDate,
          "all_role" => $json->allRole,
          "role" => $json->role,
          "is_expired" => $json->isExpired,
          "all_state" => $json->allState,
          "state" => $json->state,
          "all_channels" => $json->allChannels,
          "channels" => $json->channels,
          "all_payment" => $json->allPayments,
          "payment" => $json->payment,
          "all_warehouse" => $json->allWarehouse,
          "warehouse" => $json->warehouse
        );

        $limit = 1000;
        $rows = $this->order_details_model->count_rows($filter);
        $details = $this->order_details_model->get_data($filter, $limit);
        $ds = array();

        if( ! empty($details))
        {
          $no = 1;
          foreach($details as $rs)
          {
            $rs->no = number($no);
            $rs->expired = $rs->is_expired == 1 ? 'Y' : 'N';
            $rs->date_add = thai_date($rs->date_add, FALSE, '/');
            $rs->total_amount = number($this->order_details_model->get_doc_total($rs->code), 2);
            $no++;
          }
        }

        $ds = array(
          "limit" => $limit,
          "rows" => $rows,
          "details" => $details
        );
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid request data format";
      }
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }



  public function do_export()
  {
    $token = $this->input->post('token');

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Order details Report');

    if($this->input->post('fromDate'))
    {
      $allRole = $this->input->post('allRole');
      $allWarehouse = $this->input->post('allWarehouse');
      $allChannels = $this->input->post('allChannels');
      $allPayments = $this->input->post('allPayments');
      $allState = $this->input->post('allState');
      $is_expired = $this->input->post('is_expired');
      $fromDate = $this->input->post('fromDate');
      $toDate = $this->input->post('toDate');
      $role = $this->input->post('role');
      $channels = $this->input->post('channels');
      $payments = $this->input->post('payments');
      $warehouse = $this->input->post('warehouse');
      $state = $this->input->post('state');

      $filter = array(
        "from_date" => $fromDate,
        "to_date" => $toDate,
        "all_role" => $allRole,
        "role" => $role,
        "is_expired" => $is_expired,
        "all_state" => $allState,
        "state" => $state,
        "all_channels" => $allChannels,
        "channels" => $channels,
        "all_payment" => $allPayments,
        "payment" => $payments,
        "all_warehouse" => $allWarehouse,
        "warehouse" => $warehouse
      );

      $details = $this->order_details_model->get_data($filter);

      //---  Report title
      $report_title = "รายงานสถานะเอกสารแสดงรายละเอียด";
      $role_title = "เอกสาร : ". ($allRole == 1 ? 'ทั้งหมด' : $this->get_role_title($role));
      $expire_title = "อายุเอกสาร : ".($is_expired == 'all' ? 'ทั้งหมด' : ($is_expired == 1 ? 'เฉพาะที่หมดอายุ' : 'เฉพาะที่ไม่หมดอายุ'));
      $wh_title     = 'คลัง :  '. ($allWarehouse == 1 ? 'ทั้งหมด' : $this->get_title($warehouse));
      $ch_title = "ช่องทางขาย : ". ($allChannels == 1 ? 'ทั้งหมด' : $this->get_title($channels));
      $pay_title = "การชำระเงิน : ". ($allPayments == 1 ? "ทั้งหมด" : $this->get_title($payments));
      $state_title = "สถานะ : ".($allState == 1 ? "ทั้งหมด" : $this->get_state_title($state));

      //--- set report title header
      $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
      $this->excel->getActiveSheet()->setCellValue('A2', $role_title);
      $this->excel->getActiveSheet()->setCellValue('A3', $expire_title);
      $this->excel->getActiveSheet()->setCellValue('A4', $state_title);
      $this->excel->getActiveSheet()->setCellValue('A5', $ch_title);
      $this->excel->getActiveSheet()->setCellValue('A6', $pay_title);
      $this->excel->getActiveSheet()->setCellValue('A7', $wh_title);
      $this->excel->getActiveSheet()->setCellValue('A8', 'วันที่เอกสาร : ('.thai_date($fromDate,'/') .') - ('.thai_date($toDate,'/').')');

      //--- set Table header
      $row = 9;

      $this->excel->getActiveSheet()->setCellValue("A{$row}", 'ลำดับ');
      $this->excel->getActiveSheet()->setCellValue("B{$row}", 'วันที่');
      $this->excel->getActiveSheet()->setCellValue("C{$row}", 'เลขที่');
      $this->excel->getActiveSheet()->setCellValue("D{$row}", 'มูลค่า');
      $this->excel->getActiveSheet()->setCellValue("E{$row}", 'รหัสลูกค้า');
      $this->excel->getActiveSheet()->setCellValue("F{$row}", 'ชื่อลูกค้า');
      $this->excel->getActiveSheet()->setCellValue("G{$row}", 'สถานะ');
      $this->excel->getActiveSheet()->setCellValue("H{$row}", 'หมดอายุ');
      $this->excel->getActiveSheet()->setCellValue("I{$row}", 'ช่องทางขาย');
      $this->excel->getActiveSheet()->setCellValue("J{$row}", 'การชำระเงิน');
      $this->excel->getActiveSheet()->setCellValue("K{$row}", 'รหัสคลัง');
      $this->excel->getActiveSheet()->setCellValue("L{$row}", 'ชื่อคลัง');
      $this->excel->getActiveSheet()->setCellValue("M{$row}", 'Username');
      $this->excel->getActiveSheet()->setCellValue("N{$row}", 'พนักงาน');

      $row++;

      //---- กำหนดความกว้างของคอลัมภ์
      $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
      $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
      $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
      $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
      $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(90);
      $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
      $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
      $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
      $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
      $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
      $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(40);
      $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
      $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(15);

      if( ! empty($details))
      {
        $no = 1;
        foreach($details as $rs)
        {
          $y		= date('Y', strtotime($rs->date_add));
          $m		= date('m', strtotime($rs->date_add));
          $d		= date('d', strtotime($rs->date_add));
          $date = PHPExcel_Shared_Date::FormattedPHPToExcel($y, $m, $d);

          $amount = $this->order_details_model->get_doc_total($rs->code);

          $this->excel->getActiveSheet()->setCellValue("A{$row}", $no);
          $this->excel->getActiveSheet()->setCellValue("B{$row}", $date);
          $this->excel->getActiveSheet()->setCellValue("C{$row}", $rs->code);
          $this->excel->getActiveSheet()->setCellValue("D{$row}", $amount);
          $this->excel->getActiveSheet()->setCellValue("E{$row}", $rs->customer_code);
          $this->excel->getActiveSheet()->setCellValue("F{$row}", $rs->customer_name);
          $this->excel->getActiveSheet()->setCellValue("G{$row}", $rs->state_name);
          $this->excel->getActiveSheet()->setCellValue("H{$row}", $rs->is_expired == 1 ? 'Y' : 'N');
          $this->excel->getActiveSheet()->setCellValue("I{$row}", $rs->channels_name);
          $this->excel->getActiveSheet()->setCellValue("J{$row}", $rs->payment_name);
          $this->excel->getActiveSheet()->setCellValue("K{$row}", $rs->warehouse_code);
          $this->excel->getActiveSheet()->setCellValue("L{$row}", $rs->warehouse_name);
          $this->excel->getActiveSheet()->setCellValue("M{$row}", $rs->uname);
          $this->excel->getActiveSheet()->setCellValue("N{$row}", $rs->emp_name);

          $no++;
          $row++;
        }

        $this->excel->getActiveSheet()->getStyle("B10:B{$row}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        $this->excel->getActiveSheet()->getStyle("D10:D{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
      }
    }

    setToken($token);
    $file_name = "Report Order Details.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


  public function get_title($ds = array())
  {
    $list = "";
    if(!empty($ds))
    {
      $i = 1;
      foreach($ds as $rs)
      {
        $list .= $i === 1 ? $rs : ", {$rs}";
        $i++;
      }
    }

    return $list;
  }


  private function get_role_title($ds = array())
  {
    $list = "";

    $roles = array(
      'S' => 'WO',
      'C' => 'WC',
      'N' => 'WT',
      'P' => 'WS',
      'U' => 'WU',
      'T' => 'WQ',
      'Q' => 'WV',
      'L' => 'WL'
    );

    if( ! empty($ds))
    {
      $i = 1;
      foreach($ds as $rs)
      {
        if( ! empty($roles[$rs]))
        {
          $list .= $i=== 1 ? $roles[$rs] : ", {$roles[$rs]}";
          $i++;
        }
      }
    }

    return $list;
  }


  private function get_state_title($ds = array())
  {
    $list = "";

    $states = array(
      "1" => "รอดำเนินการ",
      "2" => "รอชำระเงิน",
      "3" => "รอจัดสินค้า",
      "4" => "กำลังจัดสินค้า",
      "5" => "รอตรวจ",
      "6" => "กำลังตรวจ",
      "7" => "รอเปิดบิล",
      "8" => "เปิดบิลแล้ว",
      "9" => "ยกเลิก"
    );

    if( ! empty($ds))
    {
      $i = 1;
      foreach($ds as $rs)
      {
        if( ! empty($states[$rs]))
        {
          $list .= $i === 1 ? $states[$rs] : ", {$states[$rs]}";
          $i++;
        }
      }
    }

    return $list;
  }


} //--- end class








 ?>
