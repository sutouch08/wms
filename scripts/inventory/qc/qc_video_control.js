window.addEventListener('load', () => {
  let camera = localStorage.getItem('packCameraId');
  if(camera) {
    cameraInit();
    start();
  }  
});

window.addEventListener('keydown', (event) => {
  if(event.key == 'F8') {
    event.preventDefault();
    if( ! mediaRecorder) {
      startRecord();
    }
    else {
      if(mediaRecorder) {
        if(mediaRecorder.state === 'recording') {
          pauseRecord();
        }
        else {
          resumeRecord();
        }
      }
    }
  }

  if (event.key == 'F9') {
    if (!steam) {
      startCamera();
    }
    else {
      if (videoElem.srcObject != null) {
        stopCamera();
      }
      else {
        startCamera();
      }
    }
  }

  if(event.key == 'F10') {
    event.preventDefault();
    stopRecord();
  }
});

const videoDevicesSelect = document.querySelector('#video-devices');
const videoResolutionSelect = document.querySelector('#video-resolution');
const audioDevicesSelect = document.querySelector('#audio-devices');
const cameraButton = document.querySelector('#start-camera');
const webcam = document.querySelector('.webcam');
const videoElem = document.querySelector('#video');
const startCameraButton = document.querySelector('#start-camera');
const stopCameraButton = document.querySelector('#stop-camera');
const startButton = document.querySelector('#start-record');
const pauseButton = document.querySelector('#pause-record');
const resumeButton = document.querySelector('#resume-record');
const stopButton = document.querySelector('#stop-record');
const order = document.getElementById('order-code');
const config = document.getElementById('video-config');
const audioRequired = config.dataset.audioRequired == '1' ? true : false;
const videoAutoRecord = config.dataset.autoRecord == '1' ? true : false;
const format = 'webm';
const owner = config.dataset.owner;
const endpoint = config.dataset.endpoint.endsWith('/') ? config.dataset.endpoint : config.dataset.endpoint + '/';
const secret = config.dataset.secret;

let videoMimeType = 'video/webm;codecs=h264' + (audioRequired ? ',opus' : ''); //format === 'mp4' ? 'video/mp4' : 'video/webm';

async function uploadToServer(videoBlob) {
  const name = order.value;  
  const fm = new FormData();

  fm.append('video', videoBlob, name + '.' + format);
  fm.append('order', order.value);
  fm.append('role', order.dataset.role);
  fm.append('user', order.dataset.user);
  fm.append('owner', owner);
  fm.append('secret', secret);
  load_in('บันทึกวีดีโอไปยังเซิร์ฟเวอร์...');

  try {
    const requestOptions = {
      method: "POST",
      body:fm
    };

    fetch(endpoint, requestOptions)
    .then(res => res.text())
    .then(data => {
      load_out();

      if(isJson(data)) {
        let ds = JSON.parse(data);

        if(ds.status === 'success') {          
          addVideoList(ds.data);
        }

        if(ds.status !== 'success') {
          showError('Cannot upload video to Server : ' + ds.message);
        }
      }
      else {
        showError(data);
      }

      console.log(data);
    })
    .catch(error => {
      showError(error);
      console.error(error);
    })
  }
  catch (error) {
    showError('Error during upload to server ' + error);
    console.error('Error during upload to server', error);
  }
}

async function cameraInit() {
  const cameraPermission = await navigator.permissions.query({name: 'camera'});
  const microphonePermission = await navigator.permissions.query({name: 'microphone'});

  if(cameraPermission.state === 'prompt' || (audioRequired && microphonePermission.state === 'prompt')) {
    await navigator.mediaDevices.getUserMedia({
      video: true,
      audio: audioRequired
    });
  }

  await getDevices();
}

function start() {
  if(videoAutoRecord) {
    setTimeout(() => {
      startRecord();
    }, 200);
  }
}

async function getDevices() {
  const mediaDevices = await navigator.mediaDevices.enumerateDevices();
  const micId = localStorage.getItem('packAudioId');
  const camId = localStorage.getItem('packCameraId');
  let resolution = localStorage.getItem('packResolution');

  for(const device of mediaDevices) {
    const optionElement = document.createElement('option');
    optionElement.value = device.deviceId;
    optionElement.innerText = device.label;

    if(device.kind === 'audioinput') {
      if(device.deviceId === micId) {
        optionElement.defaultSelected = true;
      }

      audioDevicesSelect.appendChild(optionElement);
    }

    if(device.kind === 'videoinput') {
      if(device.deviceId == camId) {
        optionElement.defaultSelected = true;
      }

      videoDevicesSelect.appendChild(optionElement);
      videoResolutionSelect.value = resolution ? resolution : '1280x720';
    }    
  }
}

let steam = null;
let mediaRecorder = null;
let blobChunks = [];

async function startCamera() {
  let resolution = localStorage.getItem('packResolution');
  let width = resolution ? parseInt(resolution.split('x')[0]) : 1280;
  let height = resolution ? parseInt(resolution.split('x')[1]) : 720;
  let constraints = {
    video: {
      deviceId: { exact: localStorage.getItem('packCameraId') },
      width: { exact: width },
      height: { exact: height },
      aspectRatio: { exact: 1.7777777778 }, //--- 16:9 aspect ratio
      frameRate: { ideal: 24 }, //--- 30 fps
    },
    audio: audioRequired
  };
  
  try {
    steam = await navigator.mediaDevices.getUserMedia(constraints);

    videoElem.srcObject = steam;
    startCameraButton.classList.add('hide');
    stopCameraButton.classList.remove('hide');

  } catch (e) {
    if(e.message.includes('Permission')) {
      swal({
        title:'Error!',
        text:'Permission denied',
        type:'error'
      });

      return false;
    }
    else {
      swal({
        title:'Warning',
        text:'Cloud not connect to media devices',
        type:'info'
      });
    }
  }
}

function stopCamera() {
  if(mediaRecorder && mediaRecorder.state !== 'inactive') {
    return false;
  }
  
  const activeSteam = videoElem.srcObject;

  if(activeSteam) {
    //-- get all track (video and audio) in steam
    const tracks = activeSteam.getTracks();

    //stock each track
    tracks.forEach((track) => {
      track.stop();
    });

    // remove steam from video element
    videoElem.srcObject = null;
    stopCameraButton.classList.add('hide');
    startCameraButton.classList.remove('hide');
  }
}

async function startRecord() {
  blobChunks = [];
  blobChunks.length = 0;

  await startCamera();

  if (!MediaRecorder.isTypeSupported(videoMimeType)) {
    videoMimeType = 'video/webm;codecs=vp9' + (audioRequired ? ',opus' : '');
  }
  else if (!MediaRecorder.isTypeSupported(videoMimeType)) {
    videoMimeType = 'video/webm;codecs=vp8' + (audioRequired ? ',opus' : '');
  }
  
  // ตัวเลือกโครงสร้างเพื่อจำกัดขนาดไฟล์ให้เล็กที่สุด
  const recorderOptions = {
    mimeType: videoMimeType,
    // บิตเรตวิดีโอ 1,000,000 bits/sec (1Mbps) ภาพ HD ชัดกำลังดีและไฟล์เล็กมาก
    videoBitsPerSecond: 1000000
  }

  if(audioRequired) {
    // บิตเรตเสียง 64,000 bits/sec (64kbps) เสียงคุยทั่วไปชัดเจน ประหยัดพื้นที่
    recorderOptions.audioBitsPerSecond = 64000;    
    // เปิดโหมดเสียงแปรผัน (Variable Bitrate) ช่วยประหยัดเนื้อที่ช่วงที่เงียบไม่มีเสียงพูด
    recorderOptions.audioBitrateMode = 'variable';
  }

  if(steam) {
    startButton.classList.add('hide');
    pauseButton.classList.remove('hide');
    
    try {
      mediaRecorder = new MediaRecorder(steam, recorderOptions);

      mediaRecorder.addEventListener('dataavailable', (e) => {
        if(e.data.size > 0) {
          blobChunks.push(e.data);
        }
      });

      timeReset();
      mediaRecorder.start(500);
      timeStart();

      webcam.classList.add('recording');
    }
    catch (error) {
      console.error('Error accessing webcam', error);
    }
  }
}

function pauseRecord() {
  if(mediaRecorder.state === 'recording') {
    mediaRecorder.pause();
    timeStop();
    webcam.classList.remove('recording');
    pauseButton.classList.add('hide');
    resumeButton.classList.remove('hide');
  }
}

function resumeRecord() {
  if(mediaRecorder.state === 'paused') {
    mediaRecorder.resume();
    timeStart();
    webcam.classList.add('recording');
    resumeButton.classList.add('hide');
    pauseButton.classList.remove('hide');
  }
}

async function stopRecord() {
  if(mediaRecorder) {
    if(mediaRecorder.state === 'recording' || mediaRecorder.state === 'paused') {
      mediaRecorder.requestData();
      mediaRecorder.stop();
      timeStop();      
      const recordedBlob = new Blob(blobChunks, { type: 'video/webm' });
      uploadToServer(recordedBlob);
      blobChunks = [];
      webcam.classList.remove('recording');
      pauseButton.classList.add('hide');
      resumeButton.classList.add('hide');
      startButton.classList.remove('hide');
      stopCamera();
    }
  }
}

function selectCameras() {
  let audioOption = document.getElementById('audio-option');
  if(audioRequired) {
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

  if(camId === undefined || camId == "") {
    $('#cameras-error').text("Please choose camera for video record");
    return false;
  }

  if(audioRequired) {
    if(micId == undefined || micId == "") {
      $('#cameras-error').text("Please choose microphone for video record");
      return false;
    }
  }

  if(resolution == undefined || resolution == "") {
    resolution = '1280x720';
  }

  localStorage.setItem('packCameraId', camId);
  localStorage.setItem('packAudioId', micId);
  localStorage.setItem('packResolution', resolution);

  $('#cameras-modal').modal('hide');

  startCamera();
}

// for video duration
let ms = 0;
let sec = 0;
let min = 0;
let hrs = 0;
let timeDuration = document.getElementById('stop-watch');
let timeInterval = null;

function timeStart() {
  //- set intval every 100 ms
  timeInterval = setInterval(() => {
    ms++;

    if(ms == 10) {
      sec++;
      ms = 0;
    }

    if(sec == 60) {
      min++;
      sec = 0;
    }

    if(min == 60) {
      hrs++;
      min = 0;
    }

    timeDuration.innerText = `${zeroPad(hrs)}:${zeroPad(min)}:${zeroPad(sec)}`;
  }, 100);
}

function timeStop() {
  clearInterval(timeInterval);
}

function timeReset() {
  ms = 0;
  sec = 0;
  min = 0;
  hrs = 0;
  timeDuration.innerText = '00:00:00';
}

//-- make 0 perfix times
function zeroPad(num) {
  return String(num).padStart(2, '0');
}

async function addVideoList(videoData) {
  $.ajax({
    url:`${HOME}add_video_log`,
    type:'POST',
    cache:false,
    data:{
      'data':JSON.stringify(videoData)
    },
    success:function(rs) {
      if(rs.trim() !== 'success') {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  });
}