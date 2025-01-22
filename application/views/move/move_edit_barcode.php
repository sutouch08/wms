<?php $this->load->view('include/header'); ?>
<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
<div class="row">
	<div class="col-sm-3">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-9">
    	<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		    <?php if($doc->status == 1) : ?>
		      <button type="button" class="btn btn-sm btn-info" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
		    <?php endif; ?>
		    <?php if($doc->status == 1 && $this->pm->can_add OR $this->pm->can_edit) : ?>
		      <?php if($doc->status == 0) : ?>
		        <button type="button" class="btn btn-sm btn-primary" onclick="goUseKeyboard()">คีย์มือ</button>
		      <?php endif; ?>

					<?php if($doc->status == 0 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
		          <button type="button" class="btn btn-sm btn-success" onclick="save()"><i class="fa fa-save"></i> บันทึก</button>
					<?php endif; ?>
		    <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr/>
<?php
	$this->load->view('move/move_edit_header');

  $this->load->view('move/move_detail_barcode');
  
?>

	<script id="moveTableTemplate" type="text/x-handlebars-template">
	{{#each this}}
		{{#if nodata}}
		<tr>
			<td colspan="7" class="text-center"><h4>ไม่พบรายการ</h4></td>
		</tr>
		{{else}}
			{{#if @last}}
				<tr>
					<td colspan="5" class="text-right"><strong>รวม</strong></td>
					<td class="middle text-center" id="total">{{ total }}</td>
					<td></td>
				</tr>
			{{else}}
			<tr class="font-size-12" id="row-{{id}}">
				<td class="middle text-center no">{{ no }}</td>
				<td class="middle">{{ barcode }}</td>
				<td class="middle">{{ products }}</td>
				<td class="middle">{{ from_zone }}</td>
				<td class="middle">{{{ to_zone }}}</td>
				<td class="middle text-center qty">{{ qty }}</td>
				<td class="middle text-center">{{{ btn_delete }}}</td>
			</tr>
			{{/if}}
		{{/if}}
	{{/each}}
	</script>

<?php else : ?>
<?php $this->load->view('deny_page'); ?>
<?php endif; ?>
<script src="<?php echo base_url(); ?>scripts/move/move.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_detail.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
