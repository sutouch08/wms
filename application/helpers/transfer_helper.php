<?php
  function transfer_action_text($is_wms = 0)
  {
    $action = [
      '-1' => 'ย้ายคลัง',
      '0' => 'ปกติ',
      '1' => 'PIONEER',
      '2' => 'SOKOCHAN'
    ];

    return empty($action[$is_wms]) ? $action['0'] : $action[$is_wms];
  }


  function transfer_status_text($status = -1)
  {
    $list = [
      '-1' => 'ยังไม่บันทึก',
      '0' => 'รออนุมัติ',
      '1' => 'สำเร็จ',
      '2' => 'ยกเลิก',
      '3' => 'WMS Process',
      '4' => 'รอยืนยัน'
    ];

    return empty($list[$status]) ? 'Unknow' : $list[$status];    
  }

 ?>
