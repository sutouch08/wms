<?php
class Unit_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_data()
  {
    $rs = $this->ms
    ->select('UomCode AS code')
    ->select('UomName AS name')
    ->order_by('UomCode', 'ASC')
    ->get('OUOM');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }
} //--- end class

 ?>
