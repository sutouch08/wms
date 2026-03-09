<?php
class Item_group_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_data()
  {
    $rs = $this->ms
    ->select('ItmsGrpCod AS code, ItmsGrpNam AS name')
    ->where('Locked', 'N')
    ->get('OITB');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
} //--- end class

 ?>
