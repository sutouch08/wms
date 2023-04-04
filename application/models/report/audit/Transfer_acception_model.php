<?php
class Transfer_acception_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get($doc, $ds)
  {
    $tb = "";

    switch($doc)
    {
      case "WW" :
        $tb = "transfer";
        break;
      case "MV" :
        $tb = "move";
        break;
      case "RT" :
        $tb = "receive_transform";
        break;
      case "RN" :
        $tb = "return_lend";
        break;
      case "WR" :
        $tb = "receive_product";
        break;
      case "SM" :
        $tb = "return_order";
        break;
    }

    return $this->get_data($tb, $ds);
  }


  public function get_data($tb, $ds = array())
  {
    $this->db
    ->select("t.*, u.name AS display_name")
    ->from("{$tb} AS t")
    ->join("user AS u", "t.accept_by = u.uname", "left")
    ->where("t.must_accept", 1);

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db
      ->where('t.date_add >=', from_date($ds['from_date']))
      ->where('t.date_add <=', to_date($ds['to_date']));
    }

    if( $ds['is_accept'] != 'all')
    {
      $this->db->where('t.is_accept', $ds['is_accept']);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_transfer_accept_list($code)
  {
    $list = "";

    $rs = $this->db
    ->select("u.name AS display_name")
    ->from("transfer_detail AS t")
    ->join("user AS u", "t.accept_by = u.uname", "left")
    ->where("t.transfer_code", $code)
    ->where("t.must_accept", 1)
    ->where("t.is_accept", 1)
    ->group_by("t.accept_by")
    ->get();

    if($rs->num_rows() > 0)
    {
      $i = 1;

      foreach($rs->result() as $rd)
      {
        $list .= $i === 1 ? $rd->display_name : ", ".$rd->display_name;
        $i++;
      }
    }
    return $list;
  }


  public function get_move_accept_list($code)
  {
    $list = "";

    $rs = $this->db
    ->select("u.name AS display_name")
    ->from("move_detail AS t")
    ->join("user AS u", "t.accept_by = u.uname", "left")
    ->where("t.move_code", $code)
    ->where("t.must_accept", 1)
    ->where("t.is_accept", 1)
    ->group_by("t.accept_by")
    ->get();

    if($rs->num_rows() > 0)
    {
      $i = 1;

      foreach($rs->result() as $rd)
      {
        $list .= $i === 1 ? $rd->display_name : ", ".$rd->display_name;
        $i++;
      }
    }
    return $list;
  }

} //-- end class

 ?>
