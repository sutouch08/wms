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
    ->select('u.UomEntry AS id, u.UomCode AS code, u.UomName AS name')
    ->select('g.UgpEntry AS group_id')
    ->from('OUOM AS u')
    ->join('OUGP AS g', 'g.BaseUom = u.UomEntry', 'left')
    ->order_by('u.UomCode', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
} //--- end class

 ?>
