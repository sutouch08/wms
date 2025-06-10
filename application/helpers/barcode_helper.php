<?php

function generateEAN($ean)
{
  //---- ตัดช่องว่างหน้า-หลังออก
  $ean = trim($ean);

  //--- ถ้าเจอ string ที่ไม่ใช่ตัวเลข แจ้ง error
  if(preg_match("/[^0-9]/i", $ean))
  {
    die("Invalid EAN-Code");
  }

  //--- ถ้าจำนวน digit น้อยกว่า 12 หรือ มากกว่า 13 แจ้ง error
  if(strlen($ean) < 12 OR strlen($ean) > 13)
  {
    die("Invalid EAN13 Code (must have 12/13 numbers)");
  }

  //--- ตัดให้เหลือ แค่ 12 digit แรกเท่านั้น
  $ean = substr($ean, 0, 12);

  //--- get check digit
  $checkdigit = ean_checkdigit($ean);

  //---- ประกอบรา่ง
  $ean = $ean . $checkdigit;

  return $ean;
}


function ean_checkdigit($code)
{
  //--- ถ้ารหัสมาไม่ครบ 12 digit ให้เติม 0 ด้ายซ้ายให้ครบ 12 digit
  $code = str_pad($code, 12, "0", STR_PAD_LEFT);

  $sum = 0;

  //--- วนลูป คำนวนยอดรวมผลคูณของแต่ละหลัก โดย
  //--- จำนวนหลักเลขคี่ 1, 3, .. คูณด้วย 1
  //--- จำนวนหลักเลขคู่ 2, 4, .. คูณด้วย 3
  //--- นำผลคูณของแต่ละหลักรวมกันทั้งหมด
  for( $i = (strlen($code)-1); $i>=0; $i--)
  {
    $sum += (($i % 2) * 2 + 1 ) * $code[$i];
  }

  //--- เอาผลรวมมาเป็นตัวลบ โดยตังต้้งจะต้องเป็นจำนวนเต็ม 10 ที่ใกล้เคียงหรือเท่ากับ ผลรวม
  //--- เช่น ผลรวมได้ 57 จะเอา 60 เป็นตัวตั้ง ลบกันจะได้ 3 เป็นตัว check digit ได้เลย
  //--- หรือ เอา 10 ตั้ง แล้วลบด้วย ผลรวม mod 10
  //--- เช่น 57 mod 10 = 7 | 10 - 7 = 3 | 3 = check digit
  $rs = (10 - ($sum % 10));

  return $rs == 10 ? 0 : $rs;
}

 ?>
