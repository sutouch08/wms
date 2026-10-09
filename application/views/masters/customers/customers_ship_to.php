<div class='row'>
  <div class="col-sm-12">
    <div class="table-responsive">
      <table class='table table-bordered' style="margin-bottom:0px;">
        <thead>
          <tr>
            <td colspan="6" align="center">ที่อยู่สำหรับจัดส่ง
              <p class="pull-right top-p">
                <button type="button" class="btn btn-info btn-xs" onClick="addNewAddress()"> เพิ่มที่อยู่ใหม่</button>
              </p>
            </td>
          </tr>
          <tr style="font-size:12px;">
            <td class="fix-width-100"></td>
            <td class="fix-width-150">ชื่อเรียก</td>
            <td class="fix-width-150">ผู้รับ</td>
            <td class="min-width-350">ที่อยู่</td>
            <td class="fix-width-150">อีเมล์</td>
            <td class="fix-width-150">โทรศัพท์</td>
          </tr>
        </thead>
        <tbody id="adrs">
          <?php if (!empty($addr)) : ?>
            <?php foreach ($addr as $rs) : ?>
              <tr class="font-size-11" id="<?php echo $rs->id; ?>">
                <td>
                  <button type="button" class="btn btn-minier btn-address <?php echo $rs->is_default == 1 ? 'btn-success' : ''; ?>" id="btn-<?php echo $rs->id; ?>" onClick="setDefaultShippingAddress('<?php echo $ds->code; ?>', '<?php echo $rs->id; ?>')">
                    <i class="fa fa-check"></i>
                  </button>
                  <button type="button" class="btn btn-minier btn-warning" onClick="editAddress(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
                  <button type="button" class="btn btn-minier btn-danger" onClick="removeAddress(<?php echo $rs->id; ?>)"><i class="fa fa-trash"></i></button>
                </td>
                <td align="center"><?php echo $rs->alias; ?></td>
                <td><?php echo $rs->name; ?></td>
                <td><?php echo $rs->address . ' ' . $rs->sub_district . ' ' . $rs->district . ' ' . $rs->province . ' ' . $rs->postcode; ?></td>
                <td><?php echo $rs->email; ?></td>
                <td><?php echo $rs->phone; ?></td>

              </tr>
            <?php endforeach; ?>
          <?php else : ?>
            <tr>
              <td colspan="6" class="font-size-11 text-center">ไม่พบที่อยู่</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div><!-- /row-->


<!--  Add New Address Modal  --------->
<div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title-site text-center">เพิ่ม/แก้ไข ที่อยู่สำหรับจัดส่ง</h4>
      </div>
      <div class="modal-body">
        <form id="addAddressForm">
          <input type="hidden" name="id_address" id="id_address" />
          <input type="hidden" name="customer_ref" id="customer_ref" value="<?php echo $ds->code; ?>" />
          <div class="row">
            <div class="col-sm-12 col-xs-12">
              <label class="input-label">ชื่อ</label>
              <input type="text" class="form-control input-sm" name="Fname" id="Fname" placeholder="ชื่อผู้รับ (จำเป็น)" />
            </div>
            <div class="col-sm-12 col-xs-12">
              <label class="input-label">ที่อยู่</label>
              <input type="text" class="form-control input-sm" name="address" id="address1" placeholder="เลขที่, หมู่บ้าน, ถนน (จำเป็น)" />
            </div>

            <div class="col-sm-6 col-xs-12">
              <label class="input-label">ตำบล/แขวง</label>
              <input type="text" class="form-control input-sm" name="sub_district" id="sub_district" placeholder="ตำบล" />
            </div>
            <div class="col-sm-6 col-xs-12">
              <label class="input-label">อำเภอ/เขต</label>
              <input type="text" class="form-control input-sm" name="district" id="district" placeholder="อำเภอ (จำเป็น)" />
            </div>
            <div class="col-sm-6 col-xs-12">
              <label class="input-label">จังหวัด</label>
              <input type="text" class="form-control input-sm" name="province" id="province" placeholder="จังหวัด (จำเป็น)" />
            </div>
            <div class="col-sm-6 col-xs-12">
              <label class="input-label">รหัสไปรษณีย์</label>
              <input type="text" class="form-control input-sm" name="postcode" id="postcode" placeholder="รหัสไปรษณีย์" />
            </div>
            <div class="col-sm-6 col-xs-12">
              <label class="input-label">เบอร์โทรศัพท์</label>
              <input type="text" class="form-control input-sm" name="phone" id="phone" placeholder="000 000 0000" />
            </div>
            <div class="col-sm-6 col-xs-12">
              <label class="input-label">อีเมล์</label>
              <input type="text" class="form-control input-sm" name="email" id="email" placeholder="someone@somesite.com" />
            </div>
            <div class="col-sm-6 col-xs-12">
              <label class="input-label">ชื่อเรียก</label>
              <input type="text" class="form-control input-sm" name="alias" id="alias" placeholder="ใช้เรียกที่อยู่ เช่น บ้าน, ที่ทำงาน (จำเป็น)" />
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success" onClick="saveShipTo()"><i class="fa fa-save"></i> บันทึก</button>
      </div>
    </div>
  </div>
</div>

<script id="addressTemplate" type="text/x-handlebars-template">
  <tr class="font-size-11" id="{{ id }}">
    <td>
      <button type="button" class="btn btn-minier btn-address {{#if default}}btn-success{{/if}}" id="btn-{{ id }}" onClick="setDefault('{{customer_code}}','{{ id }}')"><i class="fa fa-check"></i></button>        
      <button type="button" class="btn btn-minier btn-warning" onClick="editAddress('{{ id }}')"><i class="fa fa-pencil"></i></button>
      <button type="button" class="btn btn-minier btn-danger" onClick="removeShipTo('{{ id }}')"><i class="fa fa-trash"></i></button>
    </td>
    <td>{{ alias }}</td>
    <td>{{ name }}</td>
    <td>{{ address }}</td>
    <td>{{ email }}</td>
    <td>{{ phone }}</td>
  </tr>
</script>



<script id="addressTableTemplate" type="text/x-handlebars-template">
  {{#each this}}
    <tr class="font-size-11" id="{{ id }}">
      <td>
        <button type="button" class="btn btn-minier btn-address {{#if default}}btn-success{{/if}}" id="btn-{{ id }}" onClick="setDefault('{{customer_code}}','{{ id }}')"><i class="fa fa-check"></i></button>
        <button type="button" class="btn btn-minier btn-warning" onClick="editAddress('{{ id }}')"><i class="fa fa-pencil"></i></button>
        <button type="button" class="btn btn-minier btn-danger" onClick="removeShipTo('{{ id }}')"><i class="fa fa-trash"></i></button>
      </td>
      <td>{{ alias }}</td>
      <td>{{ name }}</td>
      <td>{{ address }}</td>
      <td>{{ email }}</td>
      <td>{{ phone }}</td>
    </tr>
  {{/each}}
</script>