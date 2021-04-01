<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    	<h3 class="title" >
        <?php echo $this->title; ?>
      </h3>
	</div>
    <div class="col-sm-6 col-xs-6 padding-5">
      	<p class="pull-right top-p">
			    <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<?php if($doc->status == 1 && empty($doc->issue_code)) : ?>
					<button type="button" class="btn btn-sm btn-success" onclick="send_to_sap()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
				<?php endif; ?>
        </p>
    </div>
</div>
<hr class="padding-5" />

<div class="row">
    <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
    </div>
		<div class="col-sm-1 col-xs-6 padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" id="date_add" value="<?php echo thai_date($doc->date_add) ?>" readonly disabled/>
    </div>
		<div class="col-sm-1 col-1-harf col-xs-12 padding-5">
			<label>อ้างถึง</label>
			<input type="text" class="form-control input-sm text-center" id="reference" value="<?php echo $doc->reference; ?>" disabled />
		</div>
		<div class="col-sm-2 col-xs-4 padding-5">
			<label>โซนแปรสภาพ</label>
			<input type="text" class="form-control input-sm text-center" id="zone" value="<?php echo $doc->from_zone; ?>" disabled />
		</div>
		<div class="col-sm-4 col-4-harf col-xs-4 padding-5">
			<label class="not-show">โซนแปรสภาพ</label>
			<input type="text" class="form-control input-sm text-center" id="zoneName" value="<?php echo $doc->zone_name; ?>" disabled />
		</div>
		<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
			<label>Goods Issue</label>
			<input type="text" class="form-control input-sm text-center" id="issue_code" value="<?php echo $doc->issue_code; ?>" disabled />
		</div>

		<div class="col-sm-3 col-xs-12 padding-5">
			<label>พนักงาน</label>
			<input type="text" class="form-control input-sm" id="user" value="<?php echo $doc->user_name; ?>" disabled />
		</div>
		<div class="col-sm-9 col-xs-12 padding-5">
	   	<label>หมายเหตุ</label>
	    <input type="text" class="form-control input-sm" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled/>
	  </div>
    <input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
</div>

<hr class="margin-top-15 margin-bottom-15 padding-5"/>

<?php
if($doc->status == 2)
{
  $this->load->view('cancle_watermark');
}
?>
<div class="row">
  <div class="col-sm-12 paddint-5 first last">
    <p class="pull-right top-p">
		<?php if(! empty($doc->issue_code)) : ?>
      <span class="red">** เอกสารเข้าระบบ SAP แล้วไม่สามารถแก้ไขได้</span>
		<?php endif; ?>
    </p>
  </div>
  <div class="col-sm-12 padding-5">
    <table class="table table-striped border-1">
      <thead>
        <tr>
          <th class="width-5 text-center">ลำดับ</th>
          <th class="width-20">รหัสสินค้า</th>
          <th class="">สินค้า</th>
          <th class="width-10 text-center">จำนวน</th>
        </tr>
      </thead>
      <tbody id="detail-table">
<?php if(!empty($details)) : ?>
<?php   $no = 1;    ?>
<?php 	$total = 0; ?>
<?php   foreach($details as $rs) : ?>
      <tr class="font-size-12 rox" id="row-<?php echo $rs->id; ?>">
        <td class="middle text-center no">
          <?php echo $no; ?>
        </td>
        <td class="middle">
          <?php echo $rs->product_code; ?>
        </td>
        <td class="middle">
          <?php echo $rs->product_name; ?>
        </td>

        <td class="middle text-center">
          <?php echo number($rs->qty); ?>
        </td>
      </tr>
<?php     $no++; ?>
<?php 		$total += $rs->qty; ?>
<?php   endforeach; ?>
			<tr>
				<td colspan="3" class="middle text-right">รวม</td>
				<td class="middle text-center"><?php echo number($total); ?></td>
			</tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>


<script src="<?php echo base_url(); ?>scripts/inventory/adjust_transform/adjust_transform.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust_transform/adjust_transform_add.js"></script>
<?php $this->load->view('include/footer'); ?>
