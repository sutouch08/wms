<?php
class Movement_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('stock_movement', $ds);
    }

    return FALSE;
  }



  public function drop_movement($code)
  {
    return $this->db->where('reference', $code)->delete('stock_movement');
  }

} //--- end class

?>
