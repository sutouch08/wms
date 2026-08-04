let deviceList = null; // use to store device list object that get from localStorage 'WrxDeviceData' as array of object
let activeDevice = null; // use to store active device data to make connection to serial port (localStorage: WrxActiveDevice);
let reader = null; // current port reader object so we can call .cancel() on it to interrupt port reading
let portOpen = false; // tracks whether a port is corrently open
let port; // current SerialPort object
let lastestWeight = null; // store lastest weight to avoid duplicate weight reading
let lastestUnit = null; // store lastest unit to avoid duplicate weight reading
let test = 0;

const deviceLabel = document.getElementById('device-label'); // use to store active device label to display on request form

navigator.serial.addEventListener("connect", (e) => {
  swal({
    title:'Port Connected',
    type:'success',
    timer:1000
  });

  setTimeout(() => {
    window.location.reload();
  }, 1200);
});

navigator.serial.addEventListener("disconnect", (e) => {
  swal({
    title:'Port Disconnected',
    type:'warning'
  }, function() {
    window.location.reload();
  });
})

window.addEventListener('load', async () => {
  //--- 1. get active device from localStorage name 'WrxActiveDevice'
  activeDevice = await getActiveDevice();
  //------ if active device not exists  lets user pick one on device list
  if(activeDevice === null || activeDevice === undefined) {
    deviceLabel.value = 'ไม่พบเครื่องชั่ง กรุณาเพิ่มเครื่องชั่งในระบบ';
    return;
  }
  else {        
    if(activeDevice.devicePort === 'RS-232') {
      openPort();
    }
  }
});


function getActiveDevice() {
  let device = localStorage.getItem('WrxActiveDevice');

  if(device !== null && device !== undefined) {
    let ds = JSON.parse(device);

    if(ds) {
      return ds;
    }
  }

  return null;
}


async function openPort() {
  if (portOpen) return;

  let setting = {
    'baudRate': parseInt(activeDevice.deviceBaudRate),
    'dataBits': parseInt(activeDevice.deviceDataBits),
    'stopBits': parseInt(activeDevice.deviceStopBits),
    'parity': activeDevice.deviceParity
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
      deviceLabel.value = `${activeDevice.deviceName} Connected`;

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
        if (test) console.log("Weight detected: " + lastestWeight + " " + lastestUnit);

      } else if (format2) {
        lastestWeight = format2[1];
        lastestUnit = format2[2];
        buffer = "";        
        if (test) console.log("Weight detected: " + lastestWeight + " " + lastestUnit);
      }

      // ป้องกัน buffer โตเกินไป
      if (buffer.length > 200) {
        buffer = "";
      }
    }
    catch (err) {      
      if (err.name === "FramingError") {        
        console.error("FramingError: Check baud rate and data format settings", err);
      } 
      else if (err.name === "OverrunError") {        
        console.error("OverrunError: Buffer Overrun (Data received too fast or reader not closed properly)", err);
      } 
      else if (err.name === "ParityError") {        
        console.error("ParityError: Check parity settings", err);
      } 
      else {        
        console.error(`Serial Error : ${err.name}: ${err.message}`);
      }

      showError(`Serial Error : ${err.name}: ${err.message}`);
      disconnect(); // disconnect if no valid data after 20 reads

      return;
    }
  }
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

async function getWeight() {    
  if(lastestWeight) { 
    const boxId = $('#id_box').val();
    addWeight(boxId, lastestWeight, lastestUnit);
  }
  else {
    console.log("No weight data available. Please ensure the device is connected and sending data.");
  }
}

function updateWeightDisplay() {
  const el = document.getElementById("latest-weight");
  if (!el) return;        // ป้องกัน error ถ้า div ยังไม่อยู่บนหน้า
  if (!lastestWeight) {    // ยังไม่มีข้อมูลน้ำหนัก
    el.textContent = "0.00";
    return;
  }

  const weight = lastestUnit === "g" ? (parseFloat(lastestWeight) / 1000).toFixed(2) : parseFloat(lastestWeight).toFixed(2);
  el.textContent = `${weight}`;
}

setInterval(updateWeightDisplay, 500); // update every 500ms


