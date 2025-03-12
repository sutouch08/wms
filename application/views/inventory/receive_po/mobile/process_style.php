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

  .btn-qty {
    width:46px;
    height: 46px;
    vertical-align: top;
  }

  .receive-table {
    margin-bottom: 0 !important;
  }
  .receive-table>tbody>tr>td{
    padding:0px !important;
    border:0px !important;
  }

  .receive-item {
    padding:5px;
    border-bottom:solid 1px #ccc;
    margin-bottom: 10px;
  }

  .receive-item.heighlight {
    background-color: #d1ffff;
  }

  .incomplete-box {
    position: fixed;
    top:0;
    left: 0;
    height: 100vh;
    width: 100vw;
    padding-top: 100px;
    padding-bottom: 200px;
    overflow: auto;
    background-color: white;
    z-index: 6;
  }

  .complete-pad {
    position: fixed;
    top:0px;
    right: 0px;
    width: 100vw;
    height: 100vh;
    overflow: auto;
    padding-bottom: 80px;
    background-color: white;
    z-index: 101
  }

  .goback {
    position: fixed;
    top:0px;
    left: 0px;
    z-index: 8;
  }

  .goback-icon {
    position: relative;
    padding:10px;
    height: 45px;
    color:white;
  }

  .toggle-header {
    position: fixed;
    top: 0px;
    right: 0px;
    z-index: 9;
  }

  .toggle-header-icon {
    position: relative;
    display: block;
    padding:10px;
    height: 45px;
    color: white;
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
    background-color: #d0d0d0;
    border-radius: 4px;
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
    background-color: #000;
    color: #fff;
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

  .control-box {
    position: fixed;
    bottom: 98px;
    left: 0px;
    width: 100%;
    padding: 5px 12px;
    background-color: white;
    box-shadow: 0px -2px 7px #f3ecec;
    z-index: 7;
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

  .counter-input {
    height: 35px;
    border:0 !important;
    padding: 0px 8px !important;
    background-color: transparent !important;
    color:white !important;
    font-size:24px !important;
  }

  #control-box {
    position: fixed;
    bottom: 65px;
    left: 0px;
    width: 100%;
    padding: 5px 12px 12px 12px;
    background-color: white;
    box-shadow: 0px -2px 7px #f3ecec;
    z-index: 7;
  }

  #close-bar {
    position: fixed;
    bottom:180px;
    padding:30px;
  }

</style>
