<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Consign_acception extends PS_Controller
{
  public $menu_code = 'RADACS';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REAUDIT';
	public $title = 'รายงานเอกสารฝากขายแท้ที่ต้องกดรับ';
  public $filter;
	public $wms;
	public $limit = 2000;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/audit/consign_acception';
    $this->load->model('report/audit/consign_acception_model');
    $this->load->helper('zone');
  }

  public function index()
  {
    $this->load->view('report/audit/report_consign_acception');
  }


  public function get_report()
  {
    ini_set('memory_limit','2048M');

    $sc = TRUE;
    $data = [];

    $ds = json_decode($this->input->post('json'));

    if( ! empty($ds))
    {
      $filter = array(
        'customer_code' => $ds->customer_code,
        'zone_code' => $ds->zone_code,
        'date_type' => $ds->date_type,
        'from_date' => $ds->from_date,
        'to_date' => $ds->to_date,
        'is_accept' => $ds->is_accept,
        'is_complete' => $ds->is_complete
      );

      $res = $this->consign_acception_model->get_list($filter);

      if( ! empty($res))
      {
        $count = count($res);

        if($count > $this->limit)
        {
          $sc = FALSE;
          $this->error = "จำนวนผลลัพธ์ทั้งหมด {$count} รายการ <br/>จึงไม่เหมาะที่จะแสดงผลบนหน้าจอได้ กรุณาส่งออกเป็นไฟล์ Excel แทน";
        }
        else
        {
          $no = 1;

          $zoneName = [];

          foreach($res as $rs)
          {
            if(empty($zoneName[$rs->zone_code]))
            {
              $zoneName[$rs->zone_code] = zone_name($rs->zone_code);
            }

            $data[] = array(
              'no' => number($no),
              'date_add' => thai_date($rs->date_add),
              'shipped_date' => thai_date($rs->shipped_date),
              'reference' => $rs->reference,
              'product_code' => $rs->product_code,
              'product_name' => $rs->product_name,
              'qty' => number($rs->qty),
              'customer_code' => $rs->customer_code,
              'customer_name' => $rs->customer_name,
              'zone_code' => $rs->zone_code,
              'zone_name' => empty($zoneName[$rs->zone_code]) ? NULL : $zoneName[$rs->zone_code],
              'is_accept' => empty($rs->is_valid) ? 'N' : 'Y',
              'is_complete' => empty($rs->inv_code) ? 'N' : 'Y',
              'inv_code' => $rs->inv_code
            );

            $no++;
          }
        }
      }
      else
      {
        $data[] = ['nodata' => 'nodata'];
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
      'data' => $data
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
    $this->excel->getActiveSheet()->setTitle("Consign Acception");
    $sheet = $this->excel->getActiveSheet();
    $sheet->getColumnDimension("A")->setAutoSize(true);
    $sheet->getColumnDimension("B")->setAutoSize(true);
    $sheet->getColumnDimension("C")->setAutoSize(true);
    $sheet->getColumnDimension("D")->setAutoSize(true);
    $sheet->getColumnDimension("E")->setAutoSize(true);
    $sheet->getColumnDimension("F")->setAutoSize(true);
    $sheet->getColumnDimension("G")->setAutoSize(true);
    $sheet->getColumnDimension("H")->setAutoSize(true);
    $sheet->getColumnDimension("I")->setAutoSize(true);
    $sheet->getColumnDimension("J")->setAutoSize(true);
    $sheet->getColumnDimension("K")->setAutoSize(true);
    $sheet->getColumnDimension("L")->setAutoSize(true);
    $sheet->getColumnDimension("M")->setAutoSize(true);

    $sheet->setCellValue('A1', $this->title);
    $sheet->mergeCells('A1:M1');

    //--- set Table header
    $sheet->setCellValue('A2', '#');
    $sheet->setCellValue('B2', 'วันที่เอกสาร');
    $sheet->setCellValue('C2', 'วันที่จัดส่ง');
    $sheet->setCellValue('D2', 'เลขที่');
    $sheet->setCellValue('E2', 'รหัสสินค้า');
    $sheet->setCellValue('F2', 'รหัสโซน');
    $sheet->setCellValue('G2', 'จำนวน');
    $sheet->setCellValue('H2', 'รับแล้ว');
    $sheet->setCellValue('I2', 'SAP');
    $sheet->setCellValue('J2', 'สินค้า');
    $sheet->setCellValue('K2', 'โซน');
    $sheet->setCellValue('L2', 'รหัสลูกค้า');
    $sheet->setCellValue('M2', 'ลูกค้า');

    if( ! empty($ds))
    {
      $filter = array(
        'customer_code' => $ds->customer_code,
        'zone_code' => $ds->zone_code,
        'date_type' => $ds->date_type,
        'from_date' => $ds->from_date,
        'to_date' => $ds->to_date,
        'is_accept' => $ds->is_accept,
        'is_complete' => $ds->is_complete
      );

      $res = $this->consign_acception_model->get_list($filter);



      $row = 3;

      if( ! empty($res))
      {
        $no = 1;

        $zoneName = [];

        foreach($res as $rs)
        {
          if(empty($zoneName[$rs->zone_code]))
          {
            $zoneName[$rs->zone_code] = zone_name($rs->zone_code);
          }

          $sheet->setCellValue("A{$row}", $no);
          $sheet->setCellValue("B{$row}", thai_date($rs->date_add));
          $sheet->setCellValue("C{$row}", thai_date($rs->shipped_date));
          $sheet->setCellValue("D{$row}", $rs->reference);
          $sheet->setCellValue("E{$row}", $rs->product_code);
          $sheet->setCellValue("F{$row}", $rs->zone_code);
          $sheet->setCellValue("G{$row}", $rs->qty);
          $sheet->setCellValue("H{$row}", empty($rs->is_valid) ? 'N' : 'Y');
          $sheet->setCellValue("I{$row}", empty($rs->inv_code) ? 'N' : 'Y');
          $sheet->setCellValue("J{$row}", $rs->product_name);
          $sheet->setCellValue("K{$row}", empty($zoneName[$rs->zone_code]) ? NULL : $zoneName[$rs->zone_code]);
          $sheet->setCellValue("L{$row}", $rs->customer_code);
          $sheet->setCellValue("M{$row}", $rs->customer_name);
          $row++;
          $no++;
        }
      }
    }

    setToken($token);
    $file_name = "Report Consign Acception.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"', true);
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }


  public function customer_code_and_name()
  {
    $txt = trim($_REQUEST['term']);

    $ds = [];

    $this->db
    ->distinct()
    ->select('customer_code AS code, customer_name AS name');

    if($txt != '*')
    {
      $this->db
      ->group_start()
      ->like('customer_code', $txt)
      ->or_like('customer_name', $txt)
      ->group_end();
    }

    $rs = $this->db
    ->order_by('customer_code', 'ASC')
    ->limit(50)
    ->get('zone_customer');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $ds[] = $rd->code.' | '.$rd->name;
      }
    }
    else
    {
      $ds[] = "not found";
    }

    echo json_encode($ds);
  }


  public function zone_customer($customer_code = NULL)
  {
    $txt = trim($_REQUEST['term']);
    $ds = [];

    $this->db
    ->distinct()
    ->select('z.code, z.name')
    ->from('zone_customer AS c')
    ->join('zone AS z', 'c.zone_code = z.code', 'left');

    if( ! empty($customer_code))
    {
      $this->db->where('c.customer_code', $customer_code);
    }

    if( $txt != '*')
    {
      $this->db
      ->group_start()
      ->like('z.code', $txt)
      ->or_like('z.name', $txt)
      ->group_end();
    }

    $rs = $this->db
    ->order_by('c.zone_code', 'ASC')
    ->limit(50)
    ->get();

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $ds[] = $rd->code.' | '.$rd->name;
      }
    }
    else
    {
      $ds[] = "not found";
    }

    echo json_encode($ds);
  }
} //--- end class








 ?>
