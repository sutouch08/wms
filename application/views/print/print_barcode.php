<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">

<?php if(! empty($list)) : ?>
  <?php foreach($list as $rs) : ?>
    <div class="border-1 padding-5 text-center" style="width:80mm; height:80mm;">
      <image src="<?php echo base_url().$rs->file; ?>" style="width:60mm;"/>
      <image src="<?php echo base_url().'assets/barcode/barcode.php?text='.$rs->code; ?>" style="width:75mm;" />
    </div>
  <?php endforeach; ?>
<?php endif; ?>
  </div>
</div>
<?php $this->load->view('include/footer'); ?>
