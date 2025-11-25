<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> Add New</button>
    <div class="btn-group">
      <button data-toggle="dropdown" class="btn btn-purple btn-white dropdown-toggle margin-top-5" aria-expanded="false">
        พิมพ์สติ๊กเกอร์
        <i class="ace-icon fa fa-angle-down icon-on-right"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-right">
        <li class="primary">
          <a href="javascript:getTemplate()"><i class="fa fa-download"></i> Template</a>
        </li>
        <li class="success">
          <a href="javascript:getUploadFile()"><i class="fa fa-upload"></i> Upload</a>
        </li>
      </ul>
    </div>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
  <div class="row">
    <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
      <label class="search-label">Code</label>
      <input type="text" class="form-control input-sm text-center search-box" name="code" value="<?php echo $code; ?>" />
    </div>

    <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
      <label class="search-label">Vendor</label>
      <input type="text" class="form-control input-sm text-center search-box" name="vendor" value="<?php echo $vendor; ?>" />
    </div>

    <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
      <label class="search-label">PO No.</label>
      <input type="text" class="form-control input-sm text-center search-box" name="po_code" value="<?php echo $po_code; ?>" />
    </div>

    <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
      <label class="search-label">Invoice No.</label>
      <input type="text" class="form-control input-sm text-center search-box" name="invoice" value="<?php echo $invoice; ?>" />
    </div>

    <div class="col-lg-3 col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
      <label class="search-label">Warehouse</label>
      <select class="width-100 filter" name="warehouse" id="warehouse">
        <option value="all">All</option>
        <?php echo select_warehouse($warehouse); ?>
      </select>
    </div>

    <div class="col-lg-2 col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
      <label>User</label>
      <select class="width-100 filter" name="user" id="user">
        <option value="all">All</option>
        <?php echo select_user($user); ?>
      </select>
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>Status</label>
      <select class="width-100 filter" name="status">
        <option value="all">All</option>
        <option value="P" <?php echo is_selected('P', $status); ?>>Draft</option>
        <option value="O" <?php echo is_selected('O', $status); ?>>Pending</option>
        <option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
        <option value="D" <?php echo is_selected('D', $status); ?>>Cancelled</option>
      </select>
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>Is Export</label>
      <select class="width-100 filter" name="is_export">
        <option value="all">All</option>
        <option value="N" <?php echo is_selected('N', $is_export); ?>>No</option>
        <option value="Y" <?php echo is_selected('Y', $is_export); ?>>Yes</option>
        <option value="E" <?php echo is_selected('F', $is_export); ?>>Failed</option>
      </select>
    </div>

    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
      <label class="search-label">Date</label>
      <div class="input-daterange input-group width-100">
        <input type="text" class="form-control input-sm width-50 from-date text-center" id="fromDate" name="from_date" value="<?php echo $from_date; ?>" placeholder="From" readonly/>
        <input type="text" class="form-control input-sm width-50 to-date text-center" id="toDate" name="to_date" value="<?php echo $to_date; ?>" placeholder="To" readonly />
      </div>
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label class="search-label display-block not-show">buton</label>
      <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label class="search-label display-block not-show">buton</label>
      <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
    </div>
  </div>
</form>
<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:1260px;">
			<thead>
				<tr class="font-size-11">
          <th class="fix-width-100"></th>
          <th class="fix-width-60 text-center">สถานะ</th>
					<th class="fix-width-50 text-center">#</th>
					<th class="fix-width-100">วันที่</th>
					<th class="fix-width-100">เลขที่</th>
					<th class="fix-width-100">PO No.</th>
					<th class="fix-width-120">Invoice No.</th>
          <th class="min-width-300">Vendor</th>
					<th class="fix-width-100">Warehouse</th>
          <th class="fix-width-80">SAP No.</th>
          <th class="fix-width-150">User</th>
				</tr>
			</thead>
			<tbody>

			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(3) + 1; ?>
				<?php foreach($data as $rs) : ?>
          <?php $color = receive_material_status_color($rs->status); ?>
					<tr class="font-size-11" style="background-color:<?php echo $color; ?>">
            <td class="middle">
              <button type="button" class="btn btn-minier btn-primary" title="View Details" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
              <?php if($rs->status == 'P' OR $rs->status == 'O') : ?>
                <button type="button" class="btn btn-minier btn-danger" title="Cancle" onclick="cancel('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
              <?php endif; ?>
              <?php if($rs->status == 'P') : ?>
                <button type="button" class="btn btn-minier btn-warning" title="Edit" onclick="edit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
              <?php endif; ?>
              <?php if($rs->status == 'O') : ?>
                <button type="button" class="btn btn-minier btn-purple" title="Receive process" onclick="process('<?php echo $rs->code; ?>')"><i class="fa fa-qrcode"></i></button>
              <?php endif; ?>
            </td>
            <td class="middle text-center"><?php echo receive_material_status_label($rs->status); ?></td>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE,'/'); ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->po_code; ?></td>
						<td class="middle"><?php echo $rs->invoice_code; ?></td>
            <td class="middle"><?php echo $rs->vendor_code." | ".$rs->vendor_name; ?></td>
						<td class="middle"><?php echo $rs->warehouse_code; ?></td>
            <td class="middle"><?php echo $rs->inv_code; ?></td>
            <td class="middle"><?php echo $rs->user; ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="11" class="middle text-center">ไม่พบรายการ</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>
<?php $this->load->view('inventory/receive_material/import_modal'); ?>

<div class="modal fade" id="tempModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:800px;">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom:0px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" style="font-size: 24px; font-weight: bold; padding-bottom: 10px; color:#428bca; border-bottom:solid 2px #428bca">Temp Status</h4>
            </div>
            <div class="modal-body" style="padding-top:5px;">
            <div class="row">
              <div class="col-sm-12 col-xs-12" id="temp-table">

              </div>
            </div>

        </div>
    </div>
  </div>
</div>

<script id="temp-template" type="text/x-handlebarsTemplate">
  <input type="hidden" id="U_WEBORDER" value="{{U_WEBORDER}}"/>
  <input type="hidden" id="DocEntry" value="{{DocEntry}}" />
  <table class="table table-bordered" style="margin-bottom:0px;">
    <tbody style="font-size:16px;">
      <tr><td class="width-30">Web Order</td><td class="width-70">{{U_WEBORDER}}</td></tr>
      <tr><td class="width-30">BP Code</td><td class="width-70">{{CardCode}}</td></tr>
      <tr><td>BP Name</td><td>{{CardName}}</td></tr>
      <tr><td>Date/Time To Temp</td><td>{{F_WebDate}}</td></tr>
      <tr><td>Date/Time To SAP</td><td>{{F_SapDate}}</td></tr>
      <tr><td>Status</td><td>{{F_Sap}}</td></tr>
      <tr><td>Message</td><td>{{Message}}</td></tr>
			<tr>
				<td colspan="2">
				{{#if del_btn}}
					<button type="button" class="btn btn-sm btn-danger" onClick="removeTemp()" >Delete</button>
				{{/if}}

        <button type="button" class="btn btn-sm btn-info" onClick="viewTempDetail({{DocEntry}})" >Detail</button>
				<button type="button" class="btn btn-sm btn-default pull-right" data-dismiss="modal">Close</button>
				</td>
			</tr>
    </tbody>
  </table>
</script>

<script>
$('#warehouse').select2();
$('#user').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_material/receive_material.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
