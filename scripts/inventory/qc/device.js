let portOpen = false; // tracks whether a port is corrently open
let portPromise = Promise.resolve(); // promise used to wait until port succesfully closed
let holdPort = null; // use this to park a SerialPort object when we change settings so that we don't need to ask the user to select it again
let port = null; // current SerialPort object
let reader = null; // current port reader object so we can call .cancel() on it to interrupt port reading
let test = 0;
const resultMessage = document.getElementById('result-message');
const weightUpdate = document.getElementById('weight');
// This function is bound to the "Open" button, which also becomes the "Close" button
// and it detects which thing to do by checking the portOpen variable
async function connect() {
  resultMessage.value = "กำลังเชื่อมต่อเครื่องชั่ง...";
  weightUpdate.value = "";

  if (portOpen) return;

  let setting = {
    'baudRate': parseInt($('#device-baud-rate').val()),
    'dataBits': parseInt($('#device-data-bits').val()),
    'stopBits': parseInt($('#device-stop-bits').val()),
    'parity': $('#device-parity').val()
  }

  try {
    const ports = await navigator.serial.getPorts();
    port = ports.length > 0 ? ports[0] : await navigator.serial.requestPort();

    try {
      await port.open(setting);

      // เคลียร์ buffer ก่อนเริ่มอ่านจริง
      await drainPort();

      reader = port.readable.getReader();
      portOpen = true;
      mesg = resultMessage.value + "\r\nเชื่อมต่อ Serial Port สำเร็จ";
      resultMessage.value = mesg;

      readLoop(); // Start reading from the port
    }
    catch (e) {
      console.error("Failed to open port with settings:", setting, e);
      mesg = resultMessage.value + "\r\nไม่สามารถเปิด Serial Port ได้ กรุณาตรวจสอบการเชื่อมต่อและลองใหม่อีกครั้ง";
      resultMessage.value = mesg;
      return;
    }
  }
  catch (e) {
    console.error("connect() error:", e);
    resultMessage.value = "ไม่พบ Serial Port กรุณาเชื่อมต่อเครื่องชั่งและลองใหม่อีกครั้ง";
    return;
  }
}

let lastestWeight = null; // store lastest weight to avoid duplicate weight reading
let lastestUnit = null; // store lastest unit to avoid duplicate weight reading

async function readLoop() {
  let buffer = "";

  while (portOpen) {
    try {
      const { value, done } = await reader.read();
      if (done) break;
      if (!value) continue;

      const cheunk = new TextDecoder().decode(value);
      buffer += cheunk;
      if (test) console.log("read bytes: " + cheunk);

      // match weight format
      // format1: =0000.92(kg)
      let format1 = buffer.match(/=([\d\.]+)\((kg|g)\)/);
      // แบบใหม่: ST,GS,+  0.253kg      
      let format2 = buffer.match(/ST,GS,\+\s*([\d\.]+)\s*(kg|g)/);

      if (format1) {
        lastestWeight = format1[1];
        lastestUnit = format1[2];
        buffer = "";
        weightUpdate.value = lastestWeight + " " + lastestUnit;
        if (test) console.log("Weight detected: " + lastestWeight + " " + lastestUnit);
        
      } else if (format2) {
        lastestWeight = format2[1];
        lastestUnit = format2[2];
        buffer = "";
        weightUpdate.value = lastestWeight + " " + lastestUnit;
        if (test) console.log("Weight detected: " + lastestWeight + " " + lastestUnit);
      }

      // ป้องกัน buffer โตเกินไป
      if (buffer.length > 200) {
        buffer = "";
      }
    }
    catch (err) {
      let mesg = resultMessage.value;

      if (err.name === "FramingError") {
        resultMessage.value = mesg + "\r\nError : Serial settings do not match the weighing scale (Baud Rate not match)";
        console.error(err);
      } else if (err.name === "OverrunError") {
        resultMessage.value = mesg + "\r\nError : Buffer Overrun (Data received too fast or reader not closed properly)";
        console.error(err);
      } else if (err.name === "ParityError") {
        resultMessage.value = mesg + "\r\nParity Error : Check parity settings";
        console.error(err);
      } else {
        resultMessage.value = mesg + `\r\nSerial Error : ${err.name}: ${err.message}`;
        console.error(`Serial Error : ${err.name}: ${err.message}`);
      }

      disconnect(); // disconnect if no valid data after 20 reads

      return;
    }
  }
}


async function changeSettings() {
  await disconnect(); // close the port first
  await connect(); // open the port again (it will grab the new settings while opening the port)
}

// ถ้าต้องการปิดพอร์ตจริง ๆ (เช่นตอนออกจากหน้า)
async function disconnect() {
  if (!portOpen) return;
  try { await reader.cancel(); } catch { }
  try { reader.releaseLock(); } catch { }
  try { await port.close(); } catch { }
  reader = null;
  port = null;
  portOpen = false;
  await new Promise(resolve => setTimeout(resolve, 200)); // wait a bit to ensure port is closed
  resultMessage.value = resultMessage.value + "\r\nปิดการเชื่อมต่อ Serial Port เรียบร้อยแล้ว";
  $('#weight').val("");
}


function disconectPort(modalName) {
  if (portOpen) {
    disconnect();
  }

  closeModal(modalName);
}

async function drainPort() {
  try {
    const drainReader = port.readable.getReader();

    const timeout = Date.now() + 150; // อ่านทิ้ง 150ms
    while (Date.now() < timeout) {
      const { value, done } = await drainReader.read();
      if (done) break;
    }

    await drainReader.cancel();
    drainReader.releaseLock();
  } catch { }
}


function saveAndClose() {
  const ds = {
    'id' : $('#device-id').val(),
    'deviceId' : $('#device-uid').val(),
    'deviceCode' : $('#device-code').val().trim(),
    'deviceName' : $('#device-name').val().trim(),
    'deviceUnit' : $('#device-unit').val(),
    'deviceBaudRate' : parseInt($('#device-baud-rate').val()),
    'devicePort' : $('#device-port').val(),
    'deviceDataBits' : parseInt($('#device-data-bits').val()),
    'deviceStopBits' : parseInt($('#device-stop-bits').val()),
    'deviceParity' : $('#device-parity').val()
  }  
    
  localStorage.setItem('WrxActiveDevice', JSON.stringify(ds));
  
  disconectPort('add-modal');

  setTimeout(() => {
    swal({
      title: 'บันทึกเครื่องชั่งเรียบร้อยแล้ว',
      type: 'success',
      timer: 1000
    });
  }, 200);
}

function getDeviceList() {  
  load_in();

  $.ajax({
    url: `${HOME}get_device_list`,
    type:'GET',    
    success:function(rs) {
      load_out();
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          let source = $('#device-template').html();
          let output = $('#device-table');
          render(source, ds.data, output);

          $('#devices-modal').modal('show');
        }
        else {
          showError(ds.message);
        }
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  });  
}


function selectDevice(id) {
  let data = $(`#device-data-${id}`);

  $('#devices-modal').modal('hide');

  $('#device-id').val(data.data('id'));
  $('#device-uid').val(data.data('uid'));
  $('#device-code').val(data.data('code'));
  $('#device-name').val(data.data('name'));
  $('#device-unit').val(data.data('unit'));
  $('#device-baud-rate').val(data.data('baudrate'));
  $('#device-data-bits').val(data.data('databits'));
  $('#device-stop-bits').val(data.data('stopbits'));
  $('#device-parity').val(data.data('parity'));
  $('#device-port').val(data.data('port'));  

  $('#add-modal').modal('show');
}


function showActiveDevice() {
  let device = localStorage.getItem('WrxActiveDevice');
  if(device) {
    device = JSON.parse(device);
    $('#device-id').val(device.id);
    $('#device-uid').val(device.deviceId);
    $('#device-code').val(device.deviceCode);
    $('#device-name').val(device.deviceName);
    $('#device-unit').val(device.deviceUnit);
    $('#device-baud-rate').val(device.deviceBaudRate);
    $('#device-data-bits').val(device.deviceDataBits);
    $('#device-stop-bits').val(device.deviceStopBits);
    $('#device-parity').val(device.deviceParity);
    $('#device-port').val(device.devicePort);
    $('#del-btn').removeClass('hide');
    $('#add-modal').modal('show');
  }
  else {
    showInfo("ไม่พบเครื่องชั่งที่บันทึกไว้ กรุณาเลือกเครื่องชั่งจากรายการเครื่องชั่ง");
  }
}

//---- ลบเครื่องชั่งที่บันทึกไว้
function removeDevice() {
  localStorage.removeItem('WrxActiveDevice');
  $('#add-modal').modal('hide');
  swal({
    title:'ลบเครื่องชั่งเรียบร้อยแล้ว',
    type:'success',
    timer:1000
  });

  $('#device-id').val("");
  $('#del-btn').addClass('hide');
}

window.addEventListener('load', () => {
  let camera = localStorage.getItem('packCameraId');
  
  if(camera) {
    cameraInit();
  }
});
//--- for video on pack
const videoDevicesSelect = document.querySelector('#video-devices');
const videoResolutionSelect = document.querySelector('#video-resolution');
const audioDevicesSelect = document.querySelector('#audio-devices');
const config = document.getElementById('video-config');
const audioRequired = config.dataset.audioRequired == '1' ? true : false;

async function cameraInit() {
  const cameraPermission = await navigator.permissions.query({ name: 'camera' });
  const microphonePermission = await navigator.permissions.query({ name: 'microphone' });
  console.log('camera permission:', cameraPermission.state);

  if (cameraPermission.state === 'prompt' || (audioRequired && microphonePermission.state === 'prompt')) {
    await navigator.mediaDevices.getUserMedia({
      video: true,
      audio: audioRequired
    });
  }

  await getCamera();
}

async function getCamera() {
  const mediaDevices = await navigator.mediaDevices.enumerateDevices();
  const micId = localStorage.getItem('packAudioId');
  const camId = localStorage.getItem('packCameraId');
  let resolution = localStorage.getItem('packResolution');

  for (const device of mediaDevices) {
    const optionElement = document.createElement('option');
    optionElement.value = device.deviceId;
    optionElement.innerText = device.label;

    if (device.kind === 'audioinput') {
      if (device.deviceId === micId) {
        optionElement.defaultSelected = true;
      }

      audioDevicesSelect.appendChild(optionElement);
    }

    if (device.kind === 'videoinput') {
      if (device.deviceId == camId) {
        optionElement.defaultSelected = true;
      }

      videoDevicesSelect.appendChild(optionElement);
      videoResolutionSelect.value = resolution ? resolution : '1280x720';
    }
  }
}

function selectCameras() {  
  cameraInit();

  let audioOption = document.getElementById('audio-option');

  if (audioRequired) {
    audioOption.classList.remove('hide');
  }
  else {
    audioOption.classList.add('hide');
  }

  $('#cameras-modal').modal('show');
}


function saveDevicesId() {
  $('#cameras-error').text('');

  let camId = $('#video-devices').val();
  let micId = audioRequired ? $('#audio-devices').val() : '';
  let resolution = $('#video-resolution').val();

  if (camId === undefined || camId == "") {
    $('#cameras-error').text("Please choose camera for video record");
    return false;
  }

  if (audioRequired) {
    if (micId == undefined || micId == "") {
      $('#cameras-error').text("Please choose microphone for video record");
      return false;
    }
  }

  if (resolution == undefined || resolution == "") {
    resolution = '1280x720';
  }

  localStorage.setItem('packCameraId', camId);
  localStorage.setItem('packAudioId', micId);
  localStorage.setItem('packResolution', resolution);

  $('#cameras-modal').modal('hide');  
}

async function showActiveCamera() {
  const mediaDevices = await navigator.mediaDevices.enumerateDevices();  
  const camId = localStorage.getItem('packCameraId');

  if( ! camId) {
    showInfo("ไม่พบกล้องที่บันทึกไว้ กรุณาเลือกกล้องจากรายการกล้อง");
    return;
  }

  const removeVideoDeviceInput = document.getElementById('remove-video-devices');
  for (const device of mediaDevices) {    
    if (device.kind === 'videoinput') {
      if (device.deviceId == camId) {
        removeVideoDeviceInput.value = device.label;
      }
    }
  }

  $('#remove-camera-modal').modal('show');
}


function removeCamera() {
  localStorage.removeItem('packCameraId');
  localStorage.removeItem('packAudioId');
  localStorage.removeItem('packResolution');

  $('#remove-camera-modal').modal('hide');
  swal({
    title: 'ลบกล้องเรียบร้อยแล้ว',
    type: 'success',
    timer: 1000
  });  
}

