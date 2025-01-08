<style>
  #navbar {
    position: fixed;
    top:0;
    left: 0;
    width:100vw;
    z-index: 8;
  }

  #sidebar {
    margin-top:45px;
  }

  .form-horizontal .form-group {
    margin-left: 5px;
    margin-right: 5px;
  }

  .float-left {
    float:left;
  }

  #btn-increse, #btn-decrese {
    width:46px;
    height: 46px;
    margin-left: 5px;
    vertical-align: top;
  }

  .pad-title {
    position: sticky;
    top: 0;
    left: 0;
    height: 30px;
    padding: 3px;
    background-color: #fefefe;
    border-bottom: solid 1px #CCC;
    z-index: 10
  }

  .counter {
    position: fixed;
    top: 46px;
    left: 0;
    width: 100vw;
    height: 55px;
    background-color: black;
    color: white;
    padding: 10px 10px;
    font-size: 24px;
    z-index: 7;
  }

  .box-list {
    position: fixed;
    top:0px;
    right: -100vw;
    width: 100vw;
    height: 100vh;
    max-height: 100vh;    
    padding-bottom: 80px;
    overflow: auto;
    background-color: white;
    z-index: 8;
  }

  .box-item {
    position: relative;
    height: 66px;
    padding:10px 5px;
    border-bottom: solid 1px #ccc;
    background-color: #white;
  }

  .box-link {
    position: absolute;
    top: 0;
    right: 0px;
    width: 50px;
    height: 66px;
    padding:13px 20px;
    text-align: center;
    vertical-align: middle;
  }

  p.box-line {
    margin-bottom: 3px;
  }

  .incomplete-item {
    padding:5px;
    border-bottom:solid 1px #ccc;
    margin-bottom: 10px;
  }

  .incomplete-item.heighlight {
    background-color: #d1ffff;
  }

  .incomplete-box {
    position: fixed;
    top:0;
    left: 0;
    height: 100vh;
    width: 100vw;
    padding-top: 100px;
    padding-bottom: 160px;
    overflow: auto;
    background-color: white;
    z-index: 6;
  }

  .complete-item {
    padding:5px;
    border-bottom:solid 1px #ccc;
    margin-bottom: 10px;
  }

  .complete-box {
    position: fixed;
    top:0px;
    right: 0px;
    width: 100vw;
    height: 100vh;
    overflow: auto;
    padding-bottom: 80px;
    background-color: white;
    z-index: 8
  }

  .edit-box {
    position: fixed;
    top:0px;
    right: 0px;
    width: 100vw;
    height: 100vh;
    overflow: auto;
    padding-bottom: 80px;
    background-color: white;
    z-index: 8
  }

  .edit-item {
    padding:5px;
    border-bottom:solid 1px #ccc;
    margin-bottom: 10px;
  }

  .box-details {
    position: fixed;
    top:0px;
    right: 0px;
    width: 100vw;
    height: 100vh;
    overflow: auto;
    padding-bottom: 80px;
    background-color: white;
    z-index: 9
  }

  .item-in-box {
    padding:5px;
    border-bottom:solid 1px #ccc;
    margin-bottom: 10px;
  }

  button.close-box {
    position:absolute;
    bottom:80px;
    right:15px;
    background:black;
    padding:10px;
    font-size:30px;
    color:white;
    line-height:0.5;
    border:none;
    border-radius:25px;
    opacity:0.5;
  }

  #close-bar {
    position: fixed;
    bottom: 250px;
    width: 100vw;
    z-index: 7;
  }

  .move-out {
    transition: right 0.3s ease-in-out;
    right:-100vw;
  }

  .move-in {
    transition: right 0.3s ease-in-out;
     right:0px;
  }

  .item-bc {
    margin-bottom: 120px !important;
  }

  .badge-no {
    position: absolute;
    top:5px;
    right: 5px;
  }

  .badge-qty {
    position: absolute;
    top:5px;
    right: 5px;
    min-width:50px;
    text-align: center;
    font-size: 20px;
    font-weight: bold;
  }

  .hide-text {
    white-space: nowrap !important;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .extra-menu {
    position: fixed;
    bottom: 0px;
    left: 0px;
    width: 100vw;
    height: 68px;
    padding: 8px;
    line-height: 20;
    background-color: #438eb9;
    opacity: 0.9;
    z-index: 11;
  }

  .slide-out {
    transition: bottom 0.3s ease-in-out;
    bottom: 0px;
  }

  .slide-in {
    transition: bottom 0.3s ease-in-out;
    bottom: 68px;
  }

  .header-info{
    position: fixed;
    top: 46px;
    left: 0px;
    height: 45px;
    background-color: #f9f9f9;
    border-bottom: solid 1px #ccc;
    z-index: 7;
  }

  .bottom-info {
    position: fixed;
    left: 0px;
    bottom:68px;
    height: 30px;
    background-color: white;
    z-index: 7;
    padding:3px;
  }

  #control-box {
    position: fixed;
    bottom: 98px;
    left: 0px;
    width: 100%;
    padding: 5px 12px;
    background-color: white;
    box-shadow: 0px -2px 7px #f3ecec;
    z-index: 7;
  }

</style>
