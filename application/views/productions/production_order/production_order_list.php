<?php $this->load->view('include/header'); ?>
<?php $this->load->view('productions/production_order/style'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-white btn-success top-btn btn-100" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr class=""/>

<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
			<label>Document No.</label>
			<input type="text" class="form-control input-sm search" name="order_code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
			<label>SAP No.</label>
			<input type="text" class="form-control input-sm search" name="inv_code"  value="<?php echo $inv_code; ?>" />
		</div>

		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 padding-5">
			<label>Item Code</label>
		   <input type="text" class="form-control input-sm search" name="product_code" value="<?php echo $product_code; ?>" />
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Status</label>
			<select class="form-control input-sm filter" name="status">
				<option value="all">ทั้งหมด</option>
				<option value="P" <?php echo is_selected('P', $status); ?>>Planned</option>
				<option value="R" <?php echo is_selected('R', $status); ?>>Released</option>
				<option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
				<option value="D" <?php echo is_selected('D', $status); ?>>Canceled</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Interface</label>
			<select class="form-control input-sm filter" name="is_exported">
				<option value="all">ทั้งหมด</option>
				<option value="Y" <?php echo is_selected('Y', $is_exported); ?>>Yes</option>
				<option value="N" <?php echo is_selected('N', $is_exported); ?>>No</option>
				<option value="F" <?php echo is_selected('F', $is_exported); ?>>Failed</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>Order Date</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
			</div>

		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> ค้นหา</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped table-hover border-1" style="min-width:1290px;">
      <tr class="font-size-11">
				<th class="fix-width-60 text-center"></th>
        <th class="fix-width-50 text-center">#</th>
				<th class="fix-width-60 text-center">Status</th>
        <th class="fix-width-80">Order Date</th>
				<th class="fix-width-80">Due Date</th>
				<th class="fix-width-100">Document No</th>
				<th class="fix-width-80">SAP No</th>
        <th class="fix-width-250">Items Code</th>
				<th class="min-width-350">Description</th>
				<th class="fix-width-80 text-right" style="padding-right:8px !important;">Qty</th>
    		<th class="fix-width-100">User</th>
      </tr>
      <tbody>
    <?php if( !empty($data)) : ?>
    <?php $no = $this->uri->segment($this->segment) + 1; ?>
    <?php foreach($data as $rs) : ?>
      <tr class="font-size-11 pointer" id="row-<?php echo $rs->id; ?>" style="<?php echo production_order_status_color($rs->Status); ?>">
				<td class="middle" style="padding:3px !important;">
					<button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code;?>')"><i class="fa fa-eye"></i></button>
					<?php if($this->pm->can_edit && $rs->Status == 'P') : ?>
						<button type="button" class="btn btn-minier btn-warning" onclick="edit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
					<?php endif; ?>
				</td>
        <td class="middle text-center no" onclick="viewDetail('<?php echo $rs->code;?>')"><?php echo $no; ?></td>
				<td class="middle text-center" onclick="viewDetail('<?php echo $rs->code;?>')"><?php echo production_order_status_text($rs->Status); ?></td>
        <td class="middle" onclick="viewDetail('<?php echo $rs->code;?>')"><?php echo thai_date($rs->PostDate, FALSE); ?></td>
				<td class="middle" onclick="viewDetail('<?php echo $rs->code;?>')"><?php echo thai_date($rs->DueDate, FALSE); ?></td>
        <td class="middle">
					<a target="_blank" href="<?php echo $this->home; ?>/view_detail/<?php echo $rs->code; ?>" style="color:inherit;"><?php echo $rs->code; ?></a>
				</td>
				<td class="middle" onclick="viewDetail('<?php echo $rs->code;?>')"><?php echo $rs->inv_code; ?></td>
				<td class="middle" onclick="viewDetail('<?php echo $rs->code;?>')"><?php echo $rs->ItemCode; ?></td>
				<td class="middle" onclick="viewDetail('<?php echo $rs->code;?>')"><?php echo $rs->ProdName; ?></td>
				<td class="middle text-right" onclick="viewDetail('<?php echo $rs->code;?>')"><?php echo number($rs->PlannedQty, 2); ?></td>
        <td class="middle" onclick="viewDetail('<?php echo $rs->code;?>')"> <?php echo $rs->user; ?></td>
      </tr>
    <?php  $no++; ?>
    <?php endforeach; ?>
    <?php else : ?>
      <tr>
        <td colspan="11" class="text-center">--- ไม่พบข้อมูล ---</td>
      </tr>
    <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="<?php echo base_url(); ?>scripts/productions/production_order/production_order.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
