<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH."/third_party/Ciqrcode.php";
class Ixqrcode extends Ciqrcode {
    public function __construct() {
        parent::__construct();
    }
}

?>
