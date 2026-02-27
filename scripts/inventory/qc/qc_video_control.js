window.addEventListener('load', () => {
  init();
  startCamera();
});


const videoDevicesSelect = document.querySelector('#video-devices');
const audioDevicesSelect = document.querySelector('#audio-devices');
const cameraButton = document.querySelector('#start-camera');
const webcam = document.querySelector('.webcam');
const videoElem = document.querySelector('#video');
const startButton = document.querySelector('#start-record');
const pauseButton = document.querySelector('#pause-record');
const resumeButton = document.querySelector('#resume-record');
const stopButton = document.querySelector('#stop-record');
const recordedPreview = document.querySelector('.recorded-preview');
const order = document.getElementById('order-code');

async function uploadToServer(videoBlob) {
  const name = order.value;
  //const endpoint = order.dataset.endpoint;
  const endpoint = 'http://127.0.0.1/wms/rest/api/ix/qc/';
  const fm = new FormData();

  fm.append('video', videoBlob, name + '.webm');
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
  let videoDeviceId = localStorage.getItem('packCameraId');
  let audioDeviceId = localStorage.getItem('packAudioId');

  try {
    steam = await navigator.mediaDevices.getUserMedia({
      video:{
        deviceId: { exact: videoDeviceId }
      },
      audio:false //{
        //deviceId: { exact: audioDeviceId }
      //}
    });

    videoElem.srcObject = steam;

  } catch (e) {
    if(e.message.includes('Permission')) {
      console.log('Permission denied');
      return
    }
    else {
      console.log('Cound not connect to media devices');
    }
  }
}


async function startRecord() {
  if(steam === null) {
    await startCamera();
  }

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
  if(mediaRecorder.state === 'recording' || mediaRecorder.state === 'paused') {
    mediaRecorder.stop();
    videoElem.pause();
    timeStop();
    const recordedBlob = new Blob(blobChunks, { type: 'video/webm'});
    uploadToServer(recordedBlob);
    blobChunks = [];
    webcam.classList.remove('recording');
    pauseButton.classList.add('hide');
    resumeButton.classList.add('hide');
    startButton.classList.remove('hide');
  }
}




function selectDevices() {
  $('#devices-modal').modal('show');
}


function saveDevicesId() {
  $('#devices-error').text('');

  let camId = $('#video-devices').val();
  let micId = $('#audio-devices').val();
  console.log(camId);
  console.log(micId);

  if(camId === undefined || camId == "") {
    $('#devices-error').text("Please choose camera for video record");
    return false;
  }

  if(micId == undefined || micId == "") {
    $('#devices-error').text("Please choose microphone fo video record");
    return false;
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
