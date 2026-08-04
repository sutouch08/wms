<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <?php if ($this->pm->can_add) : ?>
      <button type="button" class="btn btn-white btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> Add New</button>
    <?php endif; ?>
  </div>
</div><!-- End Row -->
<hr class="" />
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
  <div class="row">
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>Device id</label>
      <input type="text" class="form-control input-sm search-box" name="device_id" value="<?php echo $device_id; ?>" />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>Code</label>
      <input type="text" class="form-control input-sm search-box" name="code" value="<?php echo $code; ?>" />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>Name</label>
      <input type="text" class="form-control input-sm search-box" name="name" value="<?php echo $name; ?>" />
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
      <label>Status</label>
      <select class="form-control input-sm filter" name="active">
        <option value="all">ทั้งหมด</option>
        <option value="1" <?php echo is_selected('1', $active); ?>>Active</option>
        <option value="0" <?php echo is_selected('0', $active); ?>>Inactive</option>
      </select>
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label class="display-block not-show">search</label>
      <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label class="display-block not-show">reset</label>
      <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
    </div>
  </div>
</form>
<hr class="" />
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-stripped table-narrow border-1" style="min-width:1200px;">
      <thead>
        <tr>
          <th class="fix-width-80"></th>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-100">Device Id</th>
          <th class="fix-width-100">Code</th>
          <th class="fix-width-200">Name</th>
          <th class="fix-width-100 text-center">Port</th>
          <th class="fix-width-80 text-center">Baud Rate</th>
          <th class="fix-width-80 text-center">Data Bits</th>
          <th class="fix-width-80 text-center">Stop Bits</th>
          <th class="fix-width-80 text-center">Parity</th>
          <th class="fix-width-80 text-center">Unit</th>
          <th class="fix-width-80 text-center">Status</th>
          <th class="fix-width-150">Modified by</th>
          <th class="fix-width-150">Modified date</th>
        </tr>
      </thead>
      <tbody id="device-table">
        <?php if (!empty($data)) : ?>
          <?php $no = $this->uri->segment($this->segment) + 1; ?>
          <?php foreach ($data as $rs) : ?>
            <tr id="row-<?php echo $rs->id; ?>">
              <td>
                <?php if ($this->pm->can_edit) : ?>
                  <button type="button" class="btn btn-minier btn-warning" onclick="edit('<?php echo $rs->id; ?>')"><i class="fa fa-pencil"></i></button>
                <?php endif; ?>
                <?php if ($this->pm->can_delete) : ?>
                  <button type="button" class="btn btn-minier btn-danger" onclick="remove('<?php echo $rs->id; ?>', '<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
                <?php endif; ?>
              </td>
              <td class="text-center no"><?php echo $no; ?></td>
              <td><?php echo $rs->device_id; ?></td>
              <td><?php echo $rs->code; ?></td>
              <td><?php echo $rs->name; ?></td>
              <td class="text-center"><?php echo $rs->port; ?></td>
              <td class="text-center"><?php echo $rs->baud_rate; ?></td>
              <td class="text-center"><?php echo $rs->data_bit; ?></td>
              <td class="text-center"><?php echo $rs->stop_bit; ?></td>
              <td class="text-center"><?php echo $rs->parity; ?></td>
              <td class="text-center"><?php echo $rs->unit; ?></td>
              <td class="text-center"><?php echo is_active($rs->active); ?></td>
              <td><?php echo empty($rs->update_by) ? $rs->create_by : $rs->update_by; ?></td>
              <td><?php echo thai_date($rs->update_date, TRUE); ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="14" class="text-center">
              <h4>No device</h4>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog" style="width:400px; max-width:90vw;">
    <div class="modal-content">
      <div class="modal-header" style="border-bottom: 1px solid #e5e5e5;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title-site text-center">เชื่อมต่อเครื่องชั่งใหม่</h4>
        <input type="hidden" id="device-id" value="" />
        <input type="hidden" id="device-uid" value="" />
        <input type="hidden" id="action" value="add" />
      </div>
      <div class="modal-body">
        <div class="row" style="margin-left:0; margin-right:0;">
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
            <label class="label-top">รหัสเครื่องชั่ง</label>
            <input type="text" class="form-control input-sm r" maxlength="20" id="device-code" value="" />
          </div>
          <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5">
            <label class="label-top">ชื่อเครื่องชั่ง</label>
            <input type="text" class="form-control input-sm r" maxlength="100" id="device-name" value="" />
          </div>
          <div class="divider-hidden"></div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
            <label class="fix-width-100">
              <input type="radio" class="ace" name="active" value="1" checked>
              <span class="lbl"> Active</span>
            </label>
            <label class="fix-width-100">
              <input type="radio" class="ace" name="active" value="0">
              <span class="lbl"> Inactive</span>
            </label>
          </div>

          <div class="divider"></div>

          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-5 hide">
            <label class="label-top">Port</label>
            <select class="form-control input-sm" id="device-port">
              <option value="RS-232" data-name="Serial">RS-232</option>
              <option value="USB" data-name="USB">USB</option>
            </select>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-5">
            <label class="label-top">Unit</label>
            <select class="form-control input-sm" id="device-unit">
              <option value="kg" data-name="กิโลกรัม">KG</option>
              <option value="g" data-name="กรัม">G</option>
            </select>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-5">
            <label class="label-top">Baud Rate</label>
            <select class="form-control input-sm" id="device-baud-rate">
              <option value="110">110</option>
              <option value="200">200</option>
              <option value="300">300</option>
              <option value="600">600</option>
              <option value="1200">1200</option>
              <option value="1800">1800</option>
              <option value="2400">2400</option>
              <option value="4800">4800</option>
              <option value="7200">7200</option>
              <option value="9600" selected>9600</option>
              <option value="1440">1440</option>
              <option value="1920">1920</option>
            </select>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-5">
            <label class="label-top">Data Bits</label>
            <select class="form-control input-sm" id="device-data-bits">
              <option value="7">7</option>
              <option value="8" selected>8</option>
            </select>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-5">
            <label class="label-top">Stop Bits</label>
            <select class="form-control input-sm" id="device-stop-bits">
              <option value="1" selected>1</option>
              <option value="2">2</option>
            </select>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-5">
            <label class="label-top">Parity</label>
            <select class="form-control input-sm" id="device-parity">
              <option value="none" selected>None</option>
              <option value="even">Even</option>
              <option value="odd">Odd</option>
            </select>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-5">
            <label class="label-top display-block not-show">submit</label>
            <button type="button" class="btn btn-xs btn-primary btn-block" id="open-close-btn" onclick="connect()">Connect</button>
            <button type="button" class="btn btn-xs btn-primary btn-block hide" id="reopen-btn" onclick="changeSettings()">Connect</button>
          </div>


          <div class="divider-hidden"></div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
            <label class="label-top">Result Message</label>
            <textarea class="form-control text-center" id="result-message" rows="5" disabled></textarea>
          </div>
          <div class="divider-hidden"></div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
            <label class="label-top">Weight</label>
            <input type="text" class="form-control text-center" id="weight" disabled />
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-xs btn-default btn-100" onclick="disconectPort('add-modal')">Close</button>
        <button type="button" class="btn btn-xs btn-success btn-100" id="save-btn" onclick="save()">Add</button>
      </div>
    </div>
  </div>
</div>


<script id="rows-template" type="text/x-handlebarsTemplate">
  <tr id="row-{{id}}">
    <td>
    <?php if ($this->pm->can_edit) : ?>
      <button type="button" class="btn btn-minier btn-warning" onclick="edit('{{id}}')"><i class="fa fa-pencil"></i></button>
    <?php endif; ?>
    <?php if ($this->pm->can_delete) : ?>
      <button type="button" class="btn btn-minier btn-danger" onclick="remove('{{id}}', '{{code}}')"><i class="fa fa-trash"></i></button>
    <?php endif; ?>
    </td>
    <td class="text-center no">{{no}}</td>
    <td>{{deviceId}}</td>
    <td>{{deviceCode}}</td>
    <td>{{deviceName}}</td>
    <td class="text-center">{{devicePort}}</td>
    <td class="text-center">{{deviceBaudRate}}</td>
    <td class="text-center">{{deviceUnit}}</td>
    <td class="text-center">{{deviceDataBits}}</td>
    <td class="text-center">{{deviceStopBits}}</td>
    <td class="text-center">{{deviceParity}}</td>
    <td class="text-center">{{{active}}}</td>
    <td>{{uname}}</td>
    <td>{{date_upd}}</td>
  </tr>  
</script>

<script id="edit-rows-template" type="text/x-handlebarsTemplate">
  <td>
    <?php if ($this->pm->can_edit) : ?>
      <button type="button" class="btn btn-minier btn-warning" onclick="edit('{{id}}')"><i class="fa fa-pencil"></i></button>
    <?php endif; ?>
    <?php if ($this->pm->can_delete) : ?>
      <button type="button" class="btn btn-minier btn-danger" onclick="remove('{{id}}', '{{code}}')"><i class="fa fa-trash"></i></button>
    <?php endif; ?>
  </td>
  <td class="text-center no">{{no}}</td>
  <td>{{deviceId}}</td>
  <td>{{deviceCode}}</td>
  <td>{{deviceName}}</td>
  <td class="text-center">{{devicePort}}</td>
  <td class="text-center">{{deviceBaudRate}}</td>
  <td class="text-center">{{deviceUnit}}</td>
  <td class="text-center">{{deviceDataBits}}</td>
  <td class="text-center">{{deviceStopBits}}</td>
  <td class="text-center">{{deviceParity}}</td>
  <td class="text-center">{{{active}}}</td>
  <td>{{uname}}</td>
  <td>{{date_upd}}</td> 
</script>

<script src="<?php echo base_url(); ?>scripts/masters/weighing_machine/weighing_machine.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/masters/weighing_machine/device.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>