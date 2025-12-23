<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Order_reference extends PS_Controller
{
  public $menu_code = 'RAOREF';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REAUDIT';
	public $title = 'รายงาน ตรวจสอบเลขอ้างอิงออเดอร์';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/audit/order_reference';
  }

  public function index()
  {
    $this->load->view('report/audit/report_order_reference');
  }


  public function get_order()
  {
    $sc = TRUE;
    $code = trim($this->input->post('code'));
    $ds = [];

    if( ! empty($code))
    {
      $this->load->model('orders/orders_model');
      $this->load->helper('channels');

      $order = $this->orders_model->get_order_by_tracking($code);

      if(empty($order))
      {
        $order = $this->orders_model->get_order_by_reference($code);
      }

      if(empty($order))
      {
        $order = $this->orders_model->get($code);
      }

      if(empty($order))
      {
        $order = $this->orders_model->get_order_in_qc_box($code);
      }

      if( ! empty($order))
      {
        $ds = array(
          'id' => genUid(),
          'order_code' => $order->code,
          'reference' => get_null($order->reference),
          'tracking_no' => get_null($order->shipping_code),
          'channels_code' => get_null($order->channels_code),
          'channels_name' => channels_name($order->channels_code),
          'customer_code' => get_null($order->customer_code),
          'customer_name' => empty($order->customer_ref) ? get_null($order->customer_name) : $order->customer_ref,
          'carton_qty' => $this->count_order_box($order->code)
        );
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function count_order_box($order_code)
  {
    return $this->db->where('order_code', $order_code)->count_all_results('qc_box');
  }

  public function do_export()
  {
    ini_set('memory_limit','2048M');

    $token = $this->input->post('token');
    $ds = json_decode($this->input->post('data'));

    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle("order ref");
    $sheet = $this->excel->getActiveSheet();
    $sheet->getColumnDimension("A")->setAutoSize(true);
    $sheet->getColumnDimension("B")->setAutoSize(true);
    $sheet->getColumnDimension("C")->setAutoSize(true);
    $sheet->getColumnDimension("D")->setAutoSize(true);
    $sheet->getColumnDimension("E")->setAutoSize(true);
    $sheet->getColumnDimension("F")->setAutoSize(true);
    $sheet->getColumnDimension("G")->setAutoSize(true);

    //--- set Table header
    $sheet->setCellValue('A1', '#');
    $sheet->setCellValue('B1', 'ออเดอร์');
    $sheet->setCellValue('C1', 'อ้างอิง');
    $sheet->setCellValue('D1', 'Tracking');
    $sheet->setCellValue('E1', 'ลูกค้า');
    $sheet->setCellValue('F1', 'ช่องทาง');
    $sheet->setCellValue('G1', 'กล่อง');

    if( ! empty($ds))
    {
      $no = 1;
      $row = 2;

      foreach($ds as $rs)
      {
        $sheet->setCellValue("A{$row}", $no);
        $sheet->setCellValueExplicit("B{$row}", $rs->order_code, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("C{$row}", $rs->reference, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("D{$row}", $rs->tracking_no, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("E{$row}", $rs->customer, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("F{$row}", $rs->channels, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->setCellValue("G{$row}", $rs->carton);
        $row++;
        $no++;
      }
    }

    setToken($token);
    $file_name = "Report Order Ref.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"', true);
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }

} //--- end class








 ?>
