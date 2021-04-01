<?php
class Sender_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('address_sender', $ds);
    }

    return FALSE;
  }


  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('address_sender', $ds);
    }

    return FALSE;
  }



  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('address_sender');
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('address_sender');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }





  public function get_sender($id)
  {
    $rs = $this->db->where('id', $id)->get('address_sender');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_name($id)
  {
    $rs = $this->db->where('id', $id)->get('address_sender');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }



  public function is_exists($name, $id = NULL)
  {
    if(! empty($id))
    {
      $rs = $this->db->where('name', $name)->where('id !=',$id)->get('address_sender');
    }
    else
    {
      $rs = $this->db->where('name', $name)->get('address_sender');
    }

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function count_rows(array $ds = array())
  {
    if(!empty($ds))
    {
      $qr = "SELECT count(*) AS row FROM address_sender WHERE id > 0 ";

      if(!empty($ds['name']))
      {
        $qr .= "AND name LIKE '%{$ds['name']}%' ";
      }

      if(!empty($ds['addr']))
      {
        $qr .= "AND (address1 LIKE '%{$ds['addr']}%' OR address2 LIKE '%{$ds['addr']}%') ";
      }

      if(!empty($ds['phone']))
      {
        $qr .= "AND phone LIKE '%{$ds['phone']}%' ";
      }

      if($ds['type'] != 'all')
      {
        $qr .= "AND type = '{$ds['type']}' ";
      }

      $rs = $this->db->query($qr);

      return $rs->row()->row;
    }

    return 0;
  }


  public function get_list(array $ds = array(), $perpage, $offset)
  {
    if(!empty($ds))
    {
      $qr = "SELECT * FROM address_sender WHERE id > 0 ";

      if(!empty($ds['name']))
      {
        $qr .= "AND name LIKE '%{$ds['name']}%' ";
      }

      if(!empty($ds['addr']))
      {
        $qr .= "AND (address1 LIKE '%{$ds['addr']}%' OR address2 LIKE '%{$ds['addr']}%') ";
      }

      if(!empty($ds['phone']))
      {
        $qr .= "AND phone LIKE '%{$ds['phone']}%' ";
      }

      if($ds['type'] != 'all')
      {
        $qr .= "AND type = '{$ds['type']}' ";
      }

      if(empty($offset))
      {
        $offset = 0;
      }

      $qr .= "LIMIT {$perpage} OFFSET {$offset}";

      $rs = $this->db->query($qr);

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

    }

    return FALSE;
  }



  public function search($txt)
  {
    $qr = "SELECT id FROM address_sender WHERE name LIKE '%".$txt."%'";
    $rs = $this->db->query($qr);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }
    else
    {
      return array();
    }

  }

}
 ?>
