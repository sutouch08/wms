<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sales_consignment_report extends PS_Controller
{
  public $menu_code = 'RSOCMD';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'RESALE';
	public $title = 'รายงานตัดยอดฝากขายเทียม แสดงรายการสินค้า';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/sales/sales_consignment_report';
    $this->load->model('report/sales/sales_consignment_report_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/customers_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/warehouse_model');
  }

  public function index()
  {
    $this->load->model('masters/warehouse_model');
    $whList = $this->warehouse_model->get_consignment_list();
    $ds['whList'] = $whList;
    $this->load->view('report/sales/sales_consignment_report', $ds);
  }

  public function get_report()
  {
    ini_set('memory_limit', '2048M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)    
    ini_set('max_execution_time', 300); // 5 minutes

    $sc = TRUE;
    $bs = array(
      'data' => [],
      'total' => []
    );

    $ds = json_decode($this->input->get('data'));

    $filter = array(
      'fromDate' => $ds->fromDate,
      'toDate' => $ds->toDate,
      'allProduct' => $ds->allProduct,
      'pdFrom' => $ds->pdFrom,
      'pdTo' => $ds->pdTo,
      'allCustomer' => $ds->allCustomer,
      'cusFrom' => $ds->cusFrom,
      'cusTo' => $ds->cusTo,
      'allWarehouse' => $ds->allWhouse,
      'whsList' => $ds->whsList,
      'allZone' => $ds->allZone,
      'zone_code' => $ds->zoneCode
    );

    $limit = 1000;
    $offset = 0;
    $rows = $this->sales_consignment_report_model->count_rows($filter);

    if ($rows > 2000)
    {
      $sc = FALSE;
      $this->error = "ข้อมูลมีปริมาณมากเกินกว่าจะแสดงผลได้ กรุณาส่งออกข้อมูลแทนการแสดงผลหน้าจอ";
    }

    if ($rows === 0)
    {
      $bs[] = array('nodata' => 'nodata');
    }

    if ($sc === TRUE && $rows > 0)
    {
      $no = 1;
      $totalQty = 0;
      $totalDiscount = 0;
      $totalAmount = 0;
      $totalCost = 0;

      $custTemp = [];
      $whTemp = [];
      $zoneTemp = [];

      while ($rows > 0)
      {
        $result = $this->sales_consignment_report_model->get_data($filter, $limit, $offset);

        if (! empty($result))
        {
          $rows -= count($result);
          $offset += count($result);

          foreach ($result as $rs)
          {
            if (!in_array($rs->customer_code, $custTemp))
            {
              $custTemp[$rs->customer_code] = $this->customers_model->get_name($rs->customer_code);
            }

            if (!in_array($rs->warehouse_code, $whTemp))
            {
              $whTemp[$rs->warehouse_code] = $this->warehouse_model->get_name($rs->warehouse_code);
            }

            if (!in_array($rs->zone_code, $zoneTemp))
            {
              $zoneTemp[$rs->zone_code] = $this->zone_model->get_name($rs->zone_code);
            }

            $bs['data'][] = array(
              'no' => number($no),
              'date_add' => thai_date($rs->date_add, FALSE),
              'reference' => $rs->reference,
              'product_code' => $rs->product_code,
              'product_name' => $rs->product_name,
              'cost' => number($rs->cost, 2),
              'price' => number($rs->price, 2),
              'discount_label' => $rs->discount_label,
              'sell' => number($rs->sell, 2),
              'qty' => number($rs->qty),
              'total_discount' => number(($rs->discount_amount * $rs->qty), 2),
              'total_amount' => number($rs->total_amount, 2),
              'total_cost' => number($rs->total_cost, 2),
              'customer_code' => $rs->customer_code,
              'customer_name' => $custTemp[$rs->customer_code],
              'warehouse_code' => $rs->warehouse_code,
              'warehouse_name' => $whTemp[$rs->warehouse_code],
              'zone_code' => $rs->zone_code,
              'zone_name' => $zoneTemp[$rs->zone_code]
            );

            $totalQty += $rs->qty;
            $totalDiscount += ($rs->qty * $rs->discount_amount);
            $totalAmount += $rs->total_amount;
            $totalCost += $rs->total_cost;
            $no++;
          }
        }
      }

      $bs['total'] = array(
        'totalQty' => number($totalQty),
        'totalDiscount' => number($totalDiscount, 2),
        'totalAmount' => number($totalAmount, 2),
        'totalCost' => number($totalCost, 2)
      );
    }

    echo $sc === TRUE ? json_encode($bs) : $this->error;
  }

  public function count_export()
  {
    ini_set('memory_limit', '2048M');
    ini_set('max_execution_time', 600);
    $ds = json_decode(file_get_contents('php://input'));

    $filter = array(
      'fromDate' => $ds->fromDate,
      'toDate' => $ds->toDate,
      'allProduct' => $ds->allProduct,
      'pdFrom' => $ds->pdFrom,
      'pdTo' => $ds->pdTo,
      'allCustomer' => $ds->allCustomer,
      'cusFrom' => $ds->cusFrom,
      'cusTo' => $ds->cusTo,
      'allWarehouse' => $ds->allWhouse,
      'whsList' => $ds->whsList,
      'allZone' => $ds->allZone,
      'zone_code' => $ds->zoneCode
    );

    $rows = $this->sales_consignment_report_model->count_rows($filter);
    echo json_encode($rows);
  }

  public function export_chunk($total, $limit, $offset)
  {
    $ds = json_decode(file_get_contents('php://input'));

    if (empty($ds))
    {
      echo json_encode(array('error' => 'No data received'));
      return;
    }

    $filter = array(
      'fromDate' => $ds->fromDate,
      'toDate' => $ds->toDate,
      'allProduct' => $ds->allProduct,
      'pdFrom' => $ds->pdFrom,
      'pdTo' => $ds->pdTo,
      'allCustomer' => $ds->allCustomer,
      'cusFrom' => $ds->cusFrom,
      'cusTo' => $ds->cusTo,
      'allWarehouse' => $ds->allWhouse,
      'whsList' => $ds->whsList,
      'allZone' => $ds->allZone,
      'zone_code' => $ds->zoneCode
    );

    $tmpFile = FCPATH . "tmp/consignment-report-{$this->_user->id}.csv";
    $header = ["ลำดับ", "วันที่", "เลขที่", "รหัส", "สินค้า", "ทุน", "ราคา", "ขาย", "จำนวน", "ส่วนลด", "มูลค่าส่วนลด", "มูลค่ารวม", "ทุนรวม", "รหัสลูกค้า", "ชื่อลูกค้า", "รหัสคลัง", "ชื่อคลัง", "รหัสโซน", "ชื่อโซน"];
    $delimiter = ",";

    $f = fopen($tmpFile, $offset == 0 ? 'w' : 'a');
    $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputs($f, $bom);

    if ($offset == 0)
    {
      $wh_list = '';

      if (!empty($ds->whsList) && empty($ds->zoneCode))
      {
        $i = 1;
        foreach ($ds->whsList as $wh)
        {
          $wh_list .= $i === 1 ? $wh : ', ' . $wh;
          $i++;
        }
      }

      //---  Report title
      $report_title = "รายงานตัดยอดฝากขายแท้";
      $whList = $ds->allWhouse == 1 ? 'ทั้งหมด' : $wh_list;
      $zoneList = $ds->allZone == 1 ? 'ทั้งหมด' : $ds->zoneCode . " - " . $ds->zoneName;
      $productList  = $ds->allProduct == 1 ? 'ทั้งหมด' : '(' . $ds->pdFrom . ') - (' . $ds->pdTo . ')';
      $dateRange = "วันที่ " . thai_date($ds->fromDate, FALSE, '/') . ' - ' . thai_date($ds->toDate, FALSE, '/');
      $cusList = $ds->allCustomer == 1 ? 'ทั้งหมด' : "({$ds->cusFrom}) - ({$ds->cusTo})";

      fputcsv($f, [$report_title], $delimiter);
      fputcsv($f, ['Warehouse', $whList], $delimiter);
      fputcsv($f, ['Zone', $zoneList], $delimiter);
      fputcsv($f, ['Product', $productList], $delimiter);
      fputcsv($f, ['Date Range', $dateRange], $delimiter);
      fputcsv($f, ['Customer', $cusList], $delimiter);
      fputcsv($f, [], $delimiter);
      fputcsv($f, $header, $delimiter);
    }

    $chunk = $this->sales_consignment_report_model->get_data($filter, $limit, $offset);
    $count = 0;
    $no = $offset + 1;
    $custTemp = [];
    $whTemp = [];
    $zoneTemp = [];

    if (! empty($chunk))
    {
      foreach ($chunk as $rs)
      {
        if (!in_array($rs->customer_code, $custTemp))
        {
          $custTemp[$rs->customer_code] = $this->customers_model->get_name($rs->customer_code);
        }

        if (!in_array($rs->warehouse_code, $whTemp))
        {
          $whTemp[$rs->warehouse_code] = $this->warehouse_model->get_name($rs->warehouse_code);
        }

        if (!in_array($rs->zone_code, $zoneTemp))
        {
          $zoneTemp[$rs->zone_code] = $this->zone_model->get_name($rs->zone_code);
        }

        $row = [
          $no,
          thai_date($rs->date_add, FALSE, '/'),
          $rs->reference,
          $rs->product_code,
          $rs->product_name,
          $rs->cost,
          $rs->price,
          $rs->sell,
          $rs->qty,
          $rs->discount_label,
          $rs->discount_amount,
          $rs->total_amount,
          $rs->total_cost,
          $rs->customer_code,
          $custTemp[$rs->customer_code],
          $rs->warehouse_code,
          $whTemp[$rs->warehouse_code],
          $rs->zone_code,
          $zoneTemp[$rs->zone_code]
        ];

        fputcsv($f, $row, $delimiter);
        $no++;
        $count++;
      }
    }

    fclose($f);
    $exported = $offset + $count;
    echo json_encode([
      "status" => "ok",
      "offset" => $count,
      "message" => "Exporting " . (number($exported)) . " rows of " . number($total)
    ]);
  }

  public function export_finished()
  {
    $filename = "consignment-report-{$this->_user->id}.csv";
    $filepath = FCPATH . 'tmp/' . $filename;

    if (!file_exists($filepath))
    {
      echo "File not found.";
      exit;
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    unlink($filepath);
    exit;
  }


} //--- end class








 ?>
