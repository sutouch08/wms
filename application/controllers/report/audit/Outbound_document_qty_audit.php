<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Outbound_document_qty_audit extends PS_Controller
{
  public $menu_code = 'RAIXDQ';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REAUDIT';
	public $title = 'รายงาน กระทบยอดเอกสารขาออก IX-WMS-SAP';
  public $filter;
	public $wms;
	public $limit = 2000;

  public function __construct()
  {
    parent::__construct();
		$this->wms = $this->load->database('wms', TRUE);
    $this->home = base_url().'report/audit/outbound_document_qty_audit';
    $this->load->model('report/audit/document_audit_model');
		$this->load->model('masters/channels_model');
  }

  public function index()
  {
		$this->load->helper('channels');
    $this->load->view('report/audit/outbound_document_qty_audit');
  }


  public function get_report()
  {
    $sc = array();
		$roleName = array(
			"S" => "WO",
			"C" => "WC",
			"N" => "WT",
			"P" => "WS",
			"U" => "WU",
			"L" => "WL",
			"T" => "WQ",
			"Q" => "WV"
		);

		$SapDoc = array(
			"S" => "DO",
			"C" => "DO",
			"N" => "TR",
			"P" => "DO",
			"U" => "DO",
			"L" => "TR",
			"T" => "TR",
			"Q" => "TR"
		);

		$stateName = array(
			'1' => "รอดำเนินการ",
			'2' => "รอชำระเงิน",
			'3' => "รอจัดสินค้า",
			'4' => "กำลังจัด",
			'5' => "รอตรวจ",
			'6' => "กำลังตรวจ",
			'7' => "รอเปิดบิล",
			'8' => "เปิดบิลแล้ว",
			'9' => "ยกเลิก"
		);

		$channelsName = $this->channels_model->get_channels_array();

		$channels = $this->input->get('channels');

    $is_wms = $this->input->get('is_wms');

    $fromDate = $this->input->get('fromDate');

    $toDate = $this->input->get('toDate');

    $allRole = $this->input->get('allRole');

    $role = $this->input->get('role');

		$allState = $this->input->get('allState');

    $state = $this->input->get('state');

		// 	$state = array("1", "2", "3", "7", "8", "9");

    $arr = array(
      'is_wms' => $is_wms,
      'allRole' => $allRole,
      'role' => $role,
      'allState' => $allState,
			'state' => $state,
      'fromDate' => $fromDate,
      'toDate' => $toDate,
			'channels' => $channels
    );

    $result = $this->document_audit_model->get_ix_order($arr);

    if(! empty($result))
    {
			if(count($result) > $this->limit)
			{
				echo "จำนวนรายการมากกว่า {$this->limit} รายการ กรุณาส่งออกเป็นไฟล์แทนการแสดงผลหน้าจอ";
				exit;
			}

      $no = 1;

      foreach($result as $rs)
      {
				$sap = NULL;
        $rs->order_qty = $this->document_audit_model->get_order_qty($rs->code);
        $temp = $this->document_audit_model->get_wms_temp_qty($rs->code, $is_wms);


				if($rs->state == 8 && ($rs->role == 'S' OR $rs->role == 'C' OR $rs->role == 'P' OR $rs->role == 'U'))
				{
					$sap = $this->document_audit_model->get_do_code_and_qty($rs->code);
				}

				if($rs->state == 8 && ($rs->role == 'N' OR $rs->role == 'N' OR $rs->role == 'L' OR $rs->role == 'T' OR $rs->role == 'Q'))
				{
					$sap = $this->document_audit_model->get_tr_code_and_qty($rs->code);
				}

				$sap_qty = (empty($sap) ? 0 : number($sap->qty));

        $rs->temp_qty = (empty($temp) ? 0 : number($temp->qty));
        $rs->temp_code = (empty($temp) ? NULL : $temp->reference);

				$hilight = "";

				if($rs->state == 8)
				{
					if($rs->order_qty != $rs->temp_qty OR $rs->order_qty != $sap_qty)
					{
						$hilight = "red";
					}
				}

        $ds = array(
          'no' => number($no),
          'date' => thai_date($rs->date_add, FALSE, '/'),
          'ix_code' => $rs->code,
					'ix_type' => $roleName[$rs->role],
					'wms_code' => $rs->temp_code,
					'wms_type' => 'OB',
					'sap_code' => (empty($sap) ? "" : $sap->DocNum),
					'sap_type' => $SapDoc[$rs->role],
					'ix_state' => $stateName[$rs->state],
					'channels' => (empty($rs->channels_code) ? "" : $channelsName[$rs->channels_code]),
					'ix_qty' => number($rs->order_qty),
					'wms_qty' => number($rs->temp_qty),
					'sap_qty' => $sap_qty,
					'hilight' => $hilight
        );

        array_push($sc, $ds);

        $no++;
      }
    }
    else
    {
      $arr = array('nodata' => 'nodata');
      array_push($sc, $arr);
    }

    echo json_encode($sc);
  }





  public function do_export()
  {
    ini_set('memory_limit','2048M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    set_time_limit(1800); // limit time change to 30 mins.
    $start_time = now();
    $token = $this->input->post('token');

		$roleName = array(
			"S" => "WO",
			"C" => "WC",
			"N" => "WT",
			"P" => "WS",
			"U" => "WU",
			"L" => "WL",
			"T" => "WQ",
			"Q" => "WV"
		);

		$SapDoc = array(
			"S" => "DO",
			"C" => "DO",
			"N" => "TR",
			"P" => "DO",
			"U" => "DO",
			"L" => "TR",
			"T" => "TR",
			"Q" => "TR"
		);

		$stateName = array(
			'1' => "รอดำเนินการ",
			'2' => "รอชำระเงิน",
			'3' => "รอจัดสินค้า",
			'4' => "กำลังจัด",
			'5' => "รอตรวจ",
			'6' => "กำลังตรวจ",
			'7' => "รอเปิดบิล",
			'8' => "เปิดบิลแล้ว",
			'9' => "ยกเลิก"
		);

    $is_wms = $this->input->post('is_wms');

		$channelsName = $this->channels_model->get_channels_array();

		$channels = $this->input->post('channels');

    $fromDate = $this->input->post('fromDate');

    $toDate = $this->input->post('toDate');

    $allRole = $this->input->post('allRole');
    $role = $this->input->post('role');
		$role_in = "";
		if($allRole != 1)
		{
			$i = 1;
			foreach($role as $ro)
			{
				$role_in .= $i === 1 ? $roleName[$ro] : ", ".$roleName[$ro];
				$i++;
			}
		}
    else
    {
      $role = array('S', 'C', 'N', 'P', 'U', 'L', 'T', 'Q');
    }

		$allState = $this->input->post('allState');
    $state = $this->input->post('state');
    $state_in = "";
		if($allState != 1)
		{
			$i = 1;
			foreach($state as $st)
			{
				$state_in .= $i === 1 ? $stateName[$st] : ", ".$stateName[$st];
				$i++;
			}
		}
    else
    {
      $state = array('1', '2', '3', '7', '8', '9');
    }

    $state = $this->input->post('state');

    $arr = array(
      'is_wms' => $is_wms,
      'allRole' => $allRole,
      'role' => $role,
      'allState' => $allState,
      'state' => $state,
      'fromDate' => $fromDate,
      'toDate' => $toDate,
      'channels' => $channels
    );

    $wh = $is_wms == 2 ? "SOKO" : "PLC";

    $title = "รายงาน กระทบยอดเอกสารขาออก IX-{$wh}-SAP ";
    $dateTitle = "วันที่ (".thai_date($fromDate, FALSE, '/').") - (".thai_date($toDate, FALSE, '/').")";
    $roleTitle = $allRole == 1 ? 'ทั้งหมด' : $role_in;
    $stateTitle = $allState == 1 ? 'ทั้งหมด' : $state_in;

    $header = ["ลำดับ", "วันที่", "IX", "{$wh}", "SAP", "QTY(IX)", "QTY({$wh})", "QTY(SAP)", "สถานะ (IX)", "ช่องทางขาย"];


    $result = $this->document_audit_model->get_outbound_data_qty($arr);

    // Create a file pointer
    $f = fopen('php://memory', 'w');
    $delimiter = ",";
    fputs($f, $bom = ( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
    fputcsv($f, [$title], $delimiter);
    fputcsv($f, [$dateTitle], $delimiter);
    fputcsv($f, [$roleTitle], $delimiter);
    fputcsv($f, [$stateTitle], $delimiter);
    fputcsv($f, $header, $delimiter);

    if(!empty($result))
    {
      $no = 1;

      foreach($result as $rs)
      {
				$sap = NULL;
        // $rs->order_qty = $this->document_audit_model->get_order_qty($rs->code);
        // $temp = $this->document_audit_model->get_wms_temp_qty($rs->code, $is_wms);
        // $rs->temp_qty = (empty($temp) ? 0 : number($temp->qty));
        // $rs->temp_code = (empty($temp) ? NULL : $temp->reference);

				if($rs->state == 8 && ($rs->role == 'S' OR $rs->role == 'C' OR $rs->role == 'P' OR $rs->role == 'U'))
				{
					$sap = $this->document_audit_model->get_do_code_and_qty($rs->order_code);
				}

				if($rs->state == 8 && ($rs->role == 'N' OR $rs->role == 'N' OR $rs->role == 'L' OR $rs->role == 'T' OR $rs->role == 'Q'))
				{
					$sap = $this->document_audit_model->get_tr_code_and_qty($rs->order_code);
				}

        $row = array(
          $no,
          thai_date($rs->date_add, FALSE, '/'),
          $rs->order_code,
          $rs->temp_code,
          (empty($sap) ? "" : $sap->DocNum),
          $rs->order_qty,
          $rs->temp_qty,
          (empty($sap) ? 0 : $sap->qty),
          $stateName[$rs->state],
          empty($rs->channels_code) ? "" : $channelsName[$rs->channels_code]
        );

        fputcsv($f, $row, $delimiter);
        $no++;
      }

      $end_time = now();

      $arr = array("begin : {$start_time}", "end : {$end_time}");
      fputcsv($f, $arr, $delimiter);

      $memuse = (memory_get_usage() / 1024) / 1024;
      $arr = array('memory usage', round($memuse, 2).' MB');
      fputcsv($f, $arr, $delimiter);
    }

    //--- Move to begin of file
    fseek($f, 0);

    setToken($token);
    $date = date('Ymd');
    $file_name = "รายงานกระทบยอดที่เอกสาร IX-{$wh}-SAP {$date}.csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="'.$file_name.'"');

    //output all remaining data on a file pointer
    fpassthru($f); ;

    exit();
  }


} //--- end class








 ?>
