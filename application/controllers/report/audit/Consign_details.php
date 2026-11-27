<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Consign_details extends PS_Controller
{
  public $menu_code = 'RAWTDT';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REAUDIT';
	public $title = 'รายงานเอกสารฝากขายแท้แสดงรายละเอียด';
 
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/audit/consign_details';
    $this->load->model('report/audit/consign_details_model');
    $this->load->model('masters/products_model');
    $this->load->model('orders/orders_model');    
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/customers_model');
    $this->load->helper('warehouse');
  }

  public function index()
  {    
    $ds = array(      
      'whsList' => $this->warehouse_model->get_consign_list()
    );

    $this->load->view('report/audit/report_consign_details', $ds);
  }

 
  private function warehouse_array()
  {
    $ds = array();
    $rs = $this->db->get('warehouse');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $ds[$rd->code] = $rd->name;
      }
    }

    return $ds;
  }

  private function state_array()
  {
    $ds = array(
      '1' => 'รอดำเนินการ',
      '2' => 'รอชำระเงิน',
      '3' => 'รอจัดสินค้า',
      '4' => 'กำลังจัดสินค้า',
      '5' => 'รอตรวจสินค้า',
      '6' => 'กำลังตรวจสินค้า',
      '7' => 'รอการจัดส่ง',
      '8' => 'จัดส่งแล้ว',
      '9' => 'ยกเลิก'
    );

    return $ds;
  }

  private function cancel_reason($code)
  {
    $rs = $this->db->where('code', $code)->order_by('id', 'DESC')->limit(1)->get('order_cancle_reason');

    if($rs->num_rows() == 1)
    {
      return $rs->row()->reason;
    }

    return NULL;
  }


  public function get_report()
  {
    ini_set('memory_limit','2048M');
    ini_set('max_execution_time', 600);

    $sc = TRUE;
    $ds = json_decode($this->input->post('data') ?? '{}');
    $res = [];
   
    if( ! empty($ds))
    {
      $filter = array(
        'from_date' => $ds->from_date,
        'to_date' => $ds->to_date,        
        'fromWhsCode' => $ds->fromWhsCode,
        'all_warehouse' => $ds->allWarehouse,
        'toWhsList' => $ds->warehouseList
      );

      $count = $this->consign_details_model->count_rows($filter);
      
      if($count > 2000)
      {
        echo 'จำนวนรายการเกิน 2000 รายการ กรุณา export เป็นไฟล์ Excel แทน';
        exit;
      }

      $limit = 2000;
      $offset = 0;
      $data = $this->consign_details_model->get_data($filter, $limit, $offset);

      if( ! empty($data))
      {
        $no = 1;
        
        foreach($data as $rs)
        {
          $res[] = array(
            'no' => $no,
            'date_add' => thai_date($rs->date_add, FALSE),
            'code' => $rs->reference,
            'inv_code' => $rs->inv_code,
            'customer' => $rs->customer_code.' | '.$rs->customer_name,
            'product' => $rs->product_code,
            'from_warehouse' => $rs->warehouse_code,
            'to_zone' => $rs->zone_code.' | '.$rs->zone_name,
            'qty' => $rs->qty
          );

          $no++;
        }
      }
      else 
      {
        $res[] = ['nodata' => 'nodata'];
      }
    }
    else 
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'error',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $res : NULL
    );

    echo json_encode($arr);
  }



  public function do_export()
  {
    ini_set('memory_limit','2048M');
    ini_set('max_execution_time', 600);
    $token = $this->input->post('token');
    $ds = json_decode($this->input->post('data') ?? '{}');

    if( ! empty($ds))
    {
      $filter = array(
        'from_date' => $ds->from_date,
        'to_date' => $ds->to_date,
        'fromWhsCode' => $ds->fromWhsCode,
        'all_warehouse' => $ds->allWarehouse,
        'toWhsList' => $ds->warehouseList
      );

      $count = $this->consign_details_model->count_rows($filter);
      $limit = 1000;
      $rows = 0;
      $offset = 0;
      $max_loop = 1000;
      $no = 1;

      $this->load->library('excel');
      $this->excel->setActiveSheetIndex(0);
      $excel = $this->excel->getActiveSheet();

      $report_title = "รายงานเอกสารฝากขายแท้แสดงรายละเอียด";
      $date_title = "วันที่ : " . thai_date($ds->from_date, FALSE) . " - " . thai_date($ds->to_date, FALSE);
      $fromWhs_title = "คลังต้นทาง : " . $ds->fromWhsCode . ' | ' . warehouse_name($ds->fromWhsCode);
      $toWhs_title = "คลังปลายทาง : " . ($ds->allWarehouse == 1 ? 'ทั้งหมด' : $this->get_title($ds->warehouseList));

      $excel->setCellValue('A1', $report_title);
      $excel->setCellValue('A2', $date_title);
      $excel->setCellValue('A3', $fromWhs_title);
      $excel->setCellValue('A4', $toWhs_title);

      $row = 5; // starting row for data

      $excel->setCellValue("A{$row}", "#");
      $excel->setCellValue("B{$row}", "วันที่");
      $excel->setCellValue("C{$row}", "เลขที่เอกสาร");
      $excel->setCellValue("D{$row}", "SAP No");
      $excel->setCellValue("E{$row}", "รหัสลูกค้า");
      $excel->setCellValue("F{$row}", "ชื่อลูกค้า");
      $excel->setCellValue("G{$row}", "รหัสสินค้า");
      $excel->setCellValue("H{$row}", "ชื่อสินค้า");
      $excel->setCellValue("I{$row}", "จำนวน");
      $excel->setCellValue("J{$row}", "ต้นทาง");
      $excel->setCellValue("K{$row}", "ปลายทาง");

      $excel->getColumnDimension('A')->setWidth(5);
      $excel->getColumnDimension('B')->setWidth(12);
      $excel->getColumnDimension('C')->setWidth(15);
      $excel->getColumnDimension('D')->setWidth(15);
      $excel->getColumnDimension('E')->setWidth(15);
      $excel->getColumnDimension('F')->setWidth(50);
      $excel->getColumnDimension('G')->setWidth(30);
      $excel->getColumnDimension('H')->setWidth(50);
      $excel->getColumnDimension('I')->setWidth(10);
      $excel->getColumnDimension('J')->setWidth(15);
      $excel->getColumnDimension('K')->setWidth(60);

      $row++;

      if($count > 0)
      {        
        while($rows < $count && $max_loop > 0)
        {
          $data = $this->consign_details_model->get_data($filter, $limit, $offset);

          if( !empty($data))
          {
            foreach($data as $rs)
            {
              $excel->setCellValue("A{$row}", $no);
              $excel->setCellValue("B{$row}", db_date($rs->date_add, FALSE));
              $excel->setCellValue("C{$row}", $rs->reference);
              $excel->setCellValue("D{$row}", $rs->inv_code);
              $excel->setCellValue("E{$row}", $rs->customer_code);
              $excel->setCellValue("F{$row}", $rs->customer_name);
              $excel->setCellValue("G{$row}", $rs->product_code);
              $excel->setCellValue("H{$row}", $rs->product_name);
              $excel->setCellValue("I{$row}", round($rs->qty ?? 0, 2));
              $excel->setCellValue("J{$row}", $rs->warehouse_code);
              $excel->setCellValue("K{$row}", $rs->zone_code.' | '.(empty($rs->zone_name) ? $rs->zone_code : $rs->zone_name));
              $row++;
              $no++;
              $rows++;
            }
          }

          $max_loop--;
          $offset += $limit;
        }

        $this->excel->getActiveSheet()->getStyle("B5:B{$row}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        $this->excel->getActiveSheet()->getStyle("I5:I{$row}")->getNumberFormat()->setFormatCode('#,##0.00');       
      }

      setToken($token);
      $file_name = "Report Consign Details.xlsx";
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
      header('Content-Disposition: attachment;filename="' . $file_name . '"');
      $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
      $writer->save('php://output');
      exit;
    }

    echo get_error('required');
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
