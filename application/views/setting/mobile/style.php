<style>
li {
  list-style-type: none;
}

.setting-menu {
  padding: 5px;
}

.menu-block {
  width: 100%;
  height: 40px;
  background-color: #f5f7fa;
  border: solid 1px #dddddd;
  border-radius: 5px;
  padding: 8px 8px 8px 18px;
  margin-bottom: 5px;
}

a.menu-link {
  text-decoration: none;
  position: relative;
  display: block;
  width: 100%;
  font-size: 14px;
  color: #434a54;
}

.setting-panel {
  position: fixed;
  top: 0px;
  right: 0px;
  background-color: white;
  height:100vh;
  width:100vw;
  box-shadow: #333 1px 0px 5px 1px;
  z-index: 10;
  padding-bottom: 80px;
  padding-right: 15px;
  padding-left: 15px;
  overflow: auto;
}

.move-out {
  transition: right 0.3s ease-in-out;
  right:-100vw;
}

.move-in {
  transition: right 0.3s ease-in-out;
   right:0px;
}

span.back-link {
  font-size:14px;
  position: absolute;
  left: 0;
}

.active > a , .active > a:hover {
	background: none;
}

.nav-title {
  position: fixed;
  height: 45px;
  padding-top:20px;
  margin-bottom: 10px;
  font-size:16px;
  text-align: center;
  border-bottom: solid 1px #ccc;
  background-color: white;
  width: 100%;
  z-index: 10
}

/* .nav-title {
  position: relative;
  height: 45px;
  padding-top:20px;
  margin-bottom: 10px;
  font-size:16px;
  text-align: center;
  border-bottom: solid 1px #ccc;
  background-color: white;
  width: 100%;
  z-index: 10
} */

@media (max-width:767px) {
  .fi {
    margin-bottom: 10px;
  }

  .help-block {
    margin-top: 0px;
    margin-bottom: 0px;
  }

  .tab-content {
    margin-top:-30px;
  }

  #fromDate, #toDate {
    z-index: 11;
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

  .no-border-xs {
    border:0px !important
  }

  .margin-bottom-3 {
    margin-bottom: 3px !important
  }

  .margin-top-60 {
    margin-top: 60px !important;
  }

  .pre-wrap {
    white-space: normal;
  }

  .btn-scroll-up.display {
    bottom:80px;
  }

  .move-list {
    margin-left: -20px;
    width: 100vw;
    max-height: 100vh;
    padding-bottom: 80px;
    overflow: auto;
    background-color: white;
    z-index: 8;
  }

  .move-list-item {
    position: relative;
    height: 80px;
    padding:10px 5px;
    border-bottom: solid 1px #ccc;
    background-color: #white;
  }

  .move-list-item:first-child {
    border-top:solid 1px #ccc;
  }

  .move-list-link {
    position: absolute;
    top: 0;
    right: 0px;
    width: 50px;
    height: 80px;
    padding:13px 20px;
    text-align: center;
    vertical-align: middle;
  }

  p.move-list-line {
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
  }
}
</style>
