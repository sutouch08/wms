<div class="modal fade" id="cameras-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:300px; max-width:95%; margin-left:auto; margin-right:auto;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Choose Camera</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label><i class="fa fa-video-camera"></i>&nbsp; Choose Camera</label>
            <select class="form-control input-sm focus" id="video-devices">
              <option value="">Select Video Device</option>
            </select>
          </div>
          <div class="divider-hidden"></div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hide" id="audio-option">
            <label><i class="fa fa-microphone"></i>&nbsp; Choose Microphone</label>
            <select class="form-control input-sm focus" id="audio-devices">
              <option value="">Select Audio Device</option>
            </select>
          </div>
          <div class="divider-hidden"></div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label><i class="fa fa-sliders"></i>&nbsp; Resolution</label>
            <select class="form-control input-sm focus" id="video-resolution">
              <option value="">Select Resolution</option>
              <option value="1920x1080">Full HD (1080p)</option>
              <option value="1280x720">HD (720p)</option>
              <option value="640x480">VGA (480p)</option>
            </select>
          </div>
        </div>
        <div class="err-label" id="cameras-error"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success btn-100" onclick="saveDevicesId()">Save</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="remove-camera-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:300px; max-width:95%; margin-left:auto; margin-right:auto;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Remove Camera</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label><i class="fa fa-video-camera"></i>&nbsp; Choose Camera</label>
            <input type="text" class="form-control input-sm" id="remove-video-devices" readonly />
          </div>          
        </div>        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-danger btn-100" onclick="removeCamera()">Remove</button>
      </div>
    </div>
  </div>
</div>