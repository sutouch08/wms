<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Backorder_item extends PS_Controller
{
  public $menu_code = 'RADBKO';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REAUDIT';
	public $title = 'รายงาน Back Order แสดงรายการสินค้า';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/audit/backorder_item';
    $this->load->model('report/audit/backorder_model');
  }

  public function index()
  {
    $this->load->view('report/audit/report_backorder_item');
  }


  public function get_report()
  {
    ini_set('memory_limit','2048M');

    $sc = TRUE;
    $ds = json_decode($this->input->post('json'));
    $res = [];

    if( ! empty($ds))
    {
      $filter = array(
        'from_date' => $ds->from_date,
        'to_date' => $ds->to_date
      );

      $result = $this->backorder_model->get_report($filter);

      if( ! empty($result))
      {
        $no = 1;

        foreach($result as $rs)
        {
          $res[] = array(
            'no' => number($no),
            'date_upd' => thai_date($rs->date_upd),
            'order_code' => $rs->order_code,
            'product_code' => $rs->product_code,
            'order_qty' => number($rs->order_qty),
            'available_qty' => number($rs->available_qty)
          );

          $no++;
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $res : NULL
    );

    echo json_encode($arr);
  }



  public function do_export()
  {
    ini_set('memory_limit','2048M');

    $token = $this->input->post('token');
    $ds = json_decode($this->input->post('filter'));

    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle("Back order items");
    $sheet = $this->excel->getActiveSheet();
    $sheet->getColumnDimension("A")->setAutoSize(true);
    $sheet->getColumnDimension("B")->setAutoSize(true);
    $sheet->getColumnDimension("C")->setAutoSize(true);
    $sheet->getColumnDimension("D")->setAutoSize(true);
    $sheet->getColumnDimension("E")->setAutoSize(true);
    $sheet->getColumnDimension("F")->setAutoSize(true);

    $sheet->setCellValue('A1', $this->title);
    $sheet->mergeCells('A1:F1');

    //--- set Table header
    $sheet->setCellValue('A2', '#');
    $sheet->setCellValue('B2', 'วันที่');
    $sheet->setCellValue('C2', 'ออเดอร์');
    $sheet->setCellValue('D2', 'รหัสสินค้า');
    $sheet->setCellValue('E2', 'Order Qty');
    $sheet->setCellValue('F2', 'Available Qty');

    if( ! empty($ds))
    {
      $filter = array(
        'from_date' => $ds->from_date,
        'to_date' => $ds->to_date
      );

      $res = $this->backorder_model->get_report($filter);

      $row = 3;

      if( ! empty($res))
      {
        $no = 1;

        foreach($res as $rs)
        {
          $sheet->setCellValue("A{$row}", $no);
          $sheet->setCellValue("B{$row}", thai_date($rs->date_upd));
          $sheet->setCellValue("C{$row}", $rs->order_code);
          $sheet->setCellValue("D{$row}", $rs->product_code);
          $sheet->setCellValue("E{$row}", $rs->order_qty);
          $sheet->setCellValue("F{$row}", $rs->available_qty);
          $row++;
          $no++;
        }
      }
    }

    setToken($token);
    $file_name = "Report Back Order Items.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"', true);
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }

} //--- end class








 ?>
