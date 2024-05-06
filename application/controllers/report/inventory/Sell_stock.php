<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sell_stock extends PS_Controller
{
  public $menu_code = 'RICSST';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REINVT';
	public $title = 'รายงานสินค้าคงเหลือ(หักยอดจอง)';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/inventory/sell_stock';
    $this->load->model('report/inventory/inventory_report_model');
    $this->load->model('masters/products_model');
    $this->load->model('orders/orders_model');
  }

  public function index()
  {
    $this->load->model('masters/warehouse_model');
    $whList = $this->warehouse_model->get_sell_warehouse_list();
    $ds['whList'] = $whList;
    $this->load->view('report/inventory/report_sell_stock', $ds);
  }


  public function get_report()
  {
    $limit = 2000;
    $allProduct = $this->input->get('allProduct');
    $pdFrom = $this->input->get('pdFrom');
    $pdTo = $this->input->get('pdTo');
    $allWhouse = $this->input->get('allWhouse');
    $warehouse = $this->input->get('warehouse');


    $wh_list = '';
    if(!empty($warehouse))
    {
      $i = 1;
      foreach($warehouse as $wh)
      {
        $wh_list .= $i === 1 ? $wh : ', '.$wh;
        $i++;
      }
    }

    //---  Report title
    $sc['reportDate'] = thai_date(date('Y-m-d'),FALSE, '/');
    $sc['whList']   = $allWhouse == 1 ? 'ทั้งหมด' : $wh_list;
    $sc['productList']   = $allProduct == 1 ? 'ทั้งหมด' : '('.$pdFrom.') - ('.$pdTo.')';

    $result = $this->inventory_report_model->get_current_stock_balance($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse);



    $bs = array();

    if(!empty($result))
    {
      $count = count($result);

      if($count > $limit)
      {
        echo 'ผลลัพธ์ของรายงานมีมากกว่า '.number($limit).' รายการ กรุณาส่งออกเป็นไฟล์ Excel แทนการแสดงผลหน้าจอ';
        exit;
      }

      $no = 1;
      $totalQty = 0;
      $totalAmount = 0;

      foreach($result as $rs)
      {
        $item = $this->products_model->get_item($rs->product_code);
        if(!empty($item))
        {
          $reserv_stock = $this->inventory_report_model->get_reserv_stock($item->code, $warehouse);
          $availableStock = $rs->qty - $reserv_stock;

          $arr = array(
            'no' => number($no),
            'pdCode' => $item->code,
            'oldCode' => $item->old_code,
            'pdName' => $item->name,
            'cost' => number($item->cost, 2),
            'qty' => number($availableStock),
            'amount' => number($item->cost * $availableStock, 2)
          );

          array_push($bs, $arr);
          $no++;

          $totalQty += $availableStock;
          $totalAmount += ($availableStock * $item->cost);
        }

      } //--- end foreach

      $arr = array(
        'totalQty' => number($totalQty),
        'totalAmount' => number($totalAmount, 2)
      );

      array_push($bs, $arr);
    }
    else
    {
      $arr = array('nodata' => 'nodata');
      array_push($bs, $arr);
    }

    $sc['bs'] = $bs;

    echo json_encode($sc);
  }


  public function countStockItems()
  {
    $count = 0;

    $option = json_decode($this->input->post('filter'));

    if( ! empty($option))
    {
      $qr = "SELECT COUNT(DISTINCT ItemCode) AS numrows FROM OITW WHERE OnHand > 0 ";

      if($option->allProduct == 0 && ! empty($option->pdFrom) && ! empty($option->pdTo))
      {
        $qr .= "AND OITW.ItemCode >= '{$option->pdFrom}' ";
        $qr .= "AND OITW.ItemCode <= '{$option->pdTo}' ";
      }

      if($option->allWhouse == 0 && ! empty($option->whsList))
      {
        $whsCode = "";

        $i = 1;

        foreach($option->whsList as $whs)
        {
          $whsCode .= $i == 1 ? "'{$whs}'" : ", '{$whs}'";
          $i++;
        }

        $qr .= "AND WhsCode IN({$whsCode}) ";
      }

      $qs = $this->ms->query($qr);

      if($qs->num_rows() === 1)
      {
        $count = $qs->row()->numrows;
      }

    }

    echo $count;
  }


  public function getStock()
  {
    $sc = TRUE;
    $ds = [];
    $option = json_decode($this->input->post('filter'));
    $limit = $this->input->post('limit');
    $offset = $this->input->post('offset');
    $no = $offset + 1;

    if( ! empty($option))
    {
      $result = $this->inventory_report_model->getStock($option, $limit, $offset);

      if( ! empty($result))
      {
        foreach($result as $rs)
        {
          $item = $this->products_model->get_item($rs->ItemCode);

          if( ! empty($item))
          {
            $whsList = empty($option->whsList) ? NULL : $option->whsList;
            $reserv_stock = $this->inventory_report_model->get_reserv_stock($rs->ItemCode, $whsList);
            $availableStock = $rs->OnHand - $reserv_stock;

            $arr = array(
              'no' => $no,
              'pdCode' => $item->code,
              'oldCode' => $item->old_code,
              'pdName' => $item->name,
              'cost' => round($item->cost, 2),
              'qty' => round($rs->OnHand, 2),
              'reserv' => round($reserv_stock, 2),
              'availableStock' => round($availableStock, 2),
              'amount' => round($availableStock * $item->cost, 2)
            );

            array_push($ds, $arr);

            $no++;
          }
        }
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'rows' => $sc === TRUE ? count($ds) : 0,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function do_export()
  {
    $allProduct = $this->input->post('allProduct');
    $pdFrom = $this->input->post('pdFrom');
    $pdTo = $this->input->post('pdTo');
    $allWhouse = $this->input->post('allWhouse');
    $warehouse = $this->input->post('warehouse');
    $token = $this->input->post('token');


    $wh_list = '';
    if(!empty($warehouse))
    {
      $i = 1;
      foreach($warehouse as $wh)
      {
        $wh_list .= $i === 1 ? $wh : ', '.$wh;
        $i++;
      }
    }


    //---  Report title
    $report_title = 'รายงานสินค้าคงเหลือ(หักยอดจอง) ณ วันที่  '.thai_date(date('Y-m-d'), '/');
    $wh_title     = 'คลัง :  '. ($allWhouse == 1 ? 'ทั้งหมด' : $wh_list);
    $pd_title     = 'สินค้า :  '. ($allProduct == 1 ? 'ทั้งหมด' : '('.$pdFrom.') - ('.$pdTo.')');

    $result = $this->inventory_report_model->get_current_stock_balance($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse);

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Sell Stock Report');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->mergeCells('A1:G1');
    $this->excel->getActiveSheet()->setCellValue('A2', $wh_title);
    $this->excel->getActiveSheet()->mergeCells('A2:G2');
    $this->excel->getActiveSheet()->setCellValue('A3', $pd_title);
    $this->excel->getActiveSheet()->mergeCells('A3:G3');

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue('A4', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B4', 'รหัส');
    $this->excel->getActiveSheet()->setCellValue('C4', 'รหัสเก่า');
    $this->excel->getActiveSheet()->setCellValue('D4', 'สินค้า');
    $this->excel->getActiveSheet()->setCellValue('E4', 'ทุน');
    $this->excel->getActiveSheet()->setCellValue('F4', 'จำนวน');
    $this->excel->getActiveSheet()->setCellValue('G4', 'มูลค่า');

    $row = 5;
    if(!empty($result))
    {

      $no = 1;
      foreach($result as $rs)
      {
        $item = $this->products_model->get_item($rs->product_code);
        if(!empty($item))
        {
          $reserv_stock = $this->inventory_report_model->get_reserv_stock($item->code, $warehouse);
          $availableStock = $rs->qty - $reserv_stock;

          $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
          $this->excel->getActiveSheet()->setCellValue('B'.$row, $item->code);
          $this->excel->getActiveSheet()->setCellValue('C'.$row, $item->old_code);
          $this->excel->getActiveSheet()->setCellValue('D'.$row, $item->name);
          $this->excel->getActiveSheet()->setCellValue('E'.$row, $item->cost);
          $this->excel->getActiveSheet()->setCellValue('F'.$row, $availableStock);
          $this->excel->getActiveSheet()->setCellValue('G'.$row, '=E'.$row.'*F'.$row);
          $no++;
          $row++;
        }

      }

      $res = $row -1;

      $this->excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
      $this->excel->getActiveSheet()->mergeCells('A'.$row.':E'.$row);
      $this->excel->getActiveSheet()->setCellValue('F'.$row, '=SUM(F5:F'.$res.')');
      $this->excel->getActiveSheet()->setCellValue('G'.$row, '=SUM(G5:G'.$res.')');

      $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('B5:B'.$res)->getNumberFormat()->setFormatCode('0');
      $this->excel->getActiveSheet()->getStyle('F5:G'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('F5:F'.$row)->getNumberFormat()->setFormatCode('#,##0');
      $this->excel->getActiveSheet()->getStyle('G5:G'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
    }

    setToken($token);
    $file_name = "Report Sell Stock.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


  // public function do_export()
  // {
  //   $allProduct = $this->input->post('allProduct');
  //   $pdFrom = $this->input->post('pdFrom');
  //   $pdTo = $this->input->post('pdTo');
  //   $allWhouse = $this->input->post('allWhouse');
  //   $warehouse = $this->input->post('warehouse');
  //   $token = $this->input->post('token');
  //
  //
  //   $wh_list = '';
  //   if(!empty($warehouse))
  //   {
  //     $i = 1;
  //     foreach($warehouse as $wh)
  //     {
  //       $wh_list .= $i === 1 ? $wh : ', '.$wh;
  //       $i++;
  //     }
  //   }
  //
  //
  //   //---  Report title
  //   $report_title = 'รายงานสินค้าคงเหลือ(หักยอดจอง) ณ วันที่  '.thai_date(date('Y-m-d'), '/');
  //   $wh_title     = 'คลัง :  '. ($allWhouse == 1 ? 'ทั้งหมด' : $wh_list);
  //   $pd_title     = 'สินค้า :  '. ($allProduct == 1 ? 'ทั้งหมด' : '('.$pdFrom.') - ('.$pdTo.')');
  //
  //   $result = $this->inventory_report_model->get_current_stock_balance($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse);
  //
  //   //--- load excel library
  //   $this->load->library('excel');
  //
  //   $this->excel->setActiveSheetIndex(0);
  //   $this->excel->getActiveSheet()->setTitle('Sell Stock Report');
  //
  //   //--- set report title header
  //   $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
  //   $this->excel->getActiveSheet()->mergeCells('A1:G1');
  //   $this->excel->getActiveSheet()->setCellValue('A2', $wh_title);
  //   $this->excel->getActiveSheet()->mergeCells('A2:G2');
  //   $this->excel->getActiveSheet()->setCellValue('A3', $pd_title);
  //   $this->excel->getActiveSheet()->mergeCells('A3:G3');
  //
  //   //--- set Table header
  //   $this->excel->getActiveSheet()->setCellValue('A4', 'ลำดับ');
  //   $this->excel->getActiveSheet()->setCellValue('B4', 'รหัส');
  //   $this->excel->getActiveSheet()->setCellValue('C4', 'รหัสเก่า');
  //   $this->excel->getActiveSheet()->setCellValue('D4', 'สินค้า');
  //   $this->excel->getActiveSheet()->setCellValue('E4', 'ทุน');
  //   $this->excel->getActiveSheet()->setCellValue('F4', 'จำนวน');
  //   $this->excel->getActiveSheet()->setCellValue('G4', 'มูลค่า');
  //
  //   $row = 5;
  //   if(!empty($result))
  //   {
  //
  //     $no = 1;
  //     foreach($result as $rs)
  //     {
  //       $item = $this->products_model->get_item($rs->product_code);
  //       if(!empty($item))
  //       {
  //         $reserv_stock = $this->inventory_report_model->get_reserv_stock($item->code, $warehouse);
  //         $availableStock = $rs->qty - $reserv_stock;
  //
  //         $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
  //         $this->excel->getActiveSheet()->setCellValue('B'.$row, $item->code);
  //         $this->excel->getActiveSheet()->setCellValue('C'.$row, $item->old_code);
  //         $this->excel->getActiveSheet()->setCellValue('D'.$row, $item->name);
  //         $this->excel->getActiveSheet()->setCellValue('E'.$row, $item->cost);
  //         $this->excel->getActiveSheet()->setCellValue('F'.$row, $availableStock);
  //         $this->excel->getActiveSheet()->setCellValue('G'.$row, '=E'.$row.'*F'.$row);
  //         $no++;
  //         $row++;
  //       }
  //
  //     }
  //
  //     $res = $row -1;
  //
  //     $this->excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
  //     $this->excel->getActiveSheet()->mergeCells('A'.$row.':E'.$row);
  //     $this->excel->getActiveSheet()->setCellValue('F'.$row, '=SUM(F5:F'.$res.')');
  //     $this->excel->getActiveSheet()->setCellValue('G'.$row, '=SUM(G5:G'.$res.')');
  //
  //     $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
  //     $this->excel->getActiveSheet()->getStyle('B5:B'.$res)->getNumberFormat()->setFormatCode('0');
  //     $this->excel->getActiveSheet()->getStyle('F5:G'.$row)->getAlignment()->setHorizontal('right');
  //     $this->excel->getActiveSheet()->getStyle('F5:F'.$row)->getNumberFormat()->setFormatCode('#,##0');
  //     $this->excel->getActiveSheet()->getStyle('G5:G'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
  //   }
  //
  //   setToken($token);
  //   $file_name = "Report Sell Stock.xlsx";
  //   header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
  //   header('Content-Disposition: attachment;filename="'.$file_name.'"');
  //   $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
  //   $writer->save('php://output');
  //
  // }


} //--- end class








 ?>
