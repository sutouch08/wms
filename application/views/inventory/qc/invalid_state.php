<?php $this->load->view('include/header'); ?>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
        <h3 class="title"><?php echo $this->title; ?></h3>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
        <button type="button" class="btn btn-white btn-primary top-btn" onclick="goBack()"><i class="fa fa-chevron-left"></i> รอตรวจ</button>
        <button type="button" class="btn btn-white btn-info top-btn" onclick="viewProcess()"><i class="fa fa-cube"></i> กำลังตรวจ</button>        
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5" style="margin-top:50px;">
        <center>
            <h1><i class="fa fa-frown-o"></i></h1>
        </center>
        <center>
            <h3>Oops.. Something went wrong.</h3>
        </center>
        <center>
            <h4>สถานะออเดอร์ ไม่อยู่ในสถานะที่สามารถดำเนินการได้ โปรดตรวจสอบสถานะของออเดอร์</h4>
        </center>
    </div>
</div>
<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>