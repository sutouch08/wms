<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8" />
  <title>Video Player ด้วย Video.js</title>

  <!-- Video.js CSS (CDN) -->
  <link
    href="https://vjs.zencdn.net/8.10.0/video-js.css"
    rel="stylesheet" />

  <style>
    /* จัดหน้าให้วิดีโออยู่กลางจอ */
    body {
      margin: 0;
      padding: 0;
      font-family: sans-serif;
      background: #111;
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .player-wrapper {
      width: 80%;
      max-width: 900px;
    }

    h1 {
      text-align: center;
      margin-bottom: 16px;
      font-size: 1.6rem;
    }

    /* ปรับสไตล์ของ video.js เพิ่มเล็กน้อย */
    .video-js {
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
    }
  </style>
</head>

<body>
  <div class="player-wrapper">
    <h1>ตัวอย่าง Video Player ด้วย Video.js</h1>

    <!-- วิดีโอ -->
    <video
      id="my-video"
      class="video-js vjs-default-skin"
      controls
      preload="auto"
      width="640"
      height="360"
      poster="https://vjs.zencdn.net/v/oceans.png"
      data-setup="{}">
      <!-- เปลี่ยน src เป็นไฟล์วิดีโอของคุณเองได้ -->
      <source src="https://vjs.zencdn.net/v/oceans.mp4" type="video/mp4" />
      <source src="https://vjs.zencdn.net/v/oceans.webm" type="video/webm" />
      <p class="vjs-no-js">
        กรุณาเปิดใช้งาน JavaScript เพื่อดูวิดีโอนี้
      </p>
    </video>
  </div>

  <!-- Video.js JS (CDN) -->
  <script src="https://vjs.zencdn.net/8.10.0/video.min.js"></script>

  <script>
    // ถ้าต้องการตั้งค่าเพิ่มเติม สามารถกำหนดผ่าน videojs() ได้
    const player = videojs('my-video', {
      playbackRates: [0.5, 1, 1.5, 2],
      controls: true,
      fluid: true
    });

    // ตัวอย่าง event
    player.on('play', () => {
      console.log('เริ่มเล่นวิดีโอ');
    });
  </script>
</body>

</html>