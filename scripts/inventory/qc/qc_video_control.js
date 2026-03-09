window.addEventListener('load', () => {
  init();  
  start();
});

window.addEventListener('keydown', (event) => {
  if(event.key == 'F1') {
    event.preventDefault();
    startRecord();
  }

  if(event.key == 'Escape') {
    event.preventDefault();
    stopRecord();
  }
});

const videoDevicesSelect = document.querySelector('#video-devices');
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
const recordedPreview = document.querySelector('.recorded-preview');
const order = document.getElementById('order-code');
const audioRequired = document.getElementById('video-config').dataset.audioRequired == '1' ? true : false;
const videoAutoRecord = document.getElementById('video-config').dataset.autoRecord == '1' ? true : false;

async function uploadToServer(videoBlob) {
  const name = order.value;
  const endpoint = order.dataset.endpoint;  
  const fm = new FormData();

  fm.append('video', videoBlob, name + '.webm');
  fm.append('order', order.value);
  fm.append('role', order.dataset.role);
  fm.append('user', order.dataset.user);
  fm.append('secret', 'YXBpQHdhcnJpeDpaSzExbzE1bzE1TDEycyRwMHJ0==');
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
      showEror(error);
      console.error(error);
    })
  }
  catch (error) {
    showEror('Error during upload to server ' + error);
    console.error('Error during upload to server', error);
  }
}

async function init() {
  const cameraPermission = await navigator.permissions.query({name: 'camera'});
  const microphonePermission = await navigator.permissions.query({name: 'microphone'});

  if(cameraPermission.state === 'prompt' || microphonePermission.state === 'prompt') {
    await navigator.mediaDevices.getUserMedia({
      video: true,
      audio: true
    });
  }

  await getDevices();
}


function start() {
  if(videoAutoRecord) {
    setTimeout(() => {
      startRecord();
    }, 5000);
  }  
}


async function getDevices() {
  const mediaDevices = await navigator.mediaDevices.enumerateDevices();
  const micId = localStorage.getItem('packAudioId');
  const camId = localStorage.getItem('packCameraId');

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
    }
  }
}

let steam = null;
let mediaRecorder = null;
let blobChunks = [];

async function startCamera() {
  let videoDeviceId = {deviceId : {exact : localStorage.getItem('packCameraId')}};
  let audioDeviceId = audioRequired ? {deviceId : {exact : localStorage.getItem('packAudioId')}} : false;

  try {
    steam = await navigator.mediaDevices.getUserMedia({
      video: videoDeviceId, //{deviceId : {exact: videoDeviceId }},
      audio: audioDeviceId  //{deviceId: { exact: audioDeviceId }}
    });

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
  await startCamera();

  if(steam) {
    startButton.classList.add('hide');
    pauseButton.classList.remove('hide');

    try {
      mediaRecorder = new MediaRecorder(steam, function() {
        mimeType: 'video/webm'
      });

      mediaRecorder.addEventListener('dataavailable', (e) => {
        blobChunks.push(e.data);
      });

      timeReset();
      mediaRecorder.start(1000);
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


function stopRecord() {
  if(mediaRecorder) {
    if(mediaRecorder.state === 'recording' || mediaRecorder.state === 'paused') {
      mediaRecorder.stop();    
      timeStop();
      const recordedBlob = new Blob(blobChunks, { type: 'video/webm'});
      uploadToServer(recordedBlob);
      blobChunks = [];
      webcam.classList.remove('recording');
      pauseButton.classList.add('hide');
      resumeButton.classList.add('hide');
      startButton.classList.remove('hide');
      //stopCamera();
    }
  }
}


function selectDevices() {
  let audioOption = document.getElementById('audio-option');
  if(audioRequired) {
    audioOption.classList.remove('hide');
  }
  else {
    audioOption.classList.add('hide');
  }

  $('#devices-modal').modal('show');
}


function saveDevicesId() {  
  $('#devices-error').text('');

  let camId = $('#video-devices').val();
  let micId = audioRequired ? $('#audio-devices').val() : '';
  
  if(camId === undefined || camId == "") {
    $('#devices-error').text("Please choose camera for video record");
    return false;
  }

  if(audioRequired) {
    if(micId == undefined || micId == "") {
      $('#devices-error').text("Please choose microphone for video record");
      return false;
    }
  }

  localStorage.setItem('packCameraId', camId);
  localStorage.setItem('packAudioId', micId);

  $('#devices-modal').modal('hide');

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
