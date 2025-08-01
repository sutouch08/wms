<style>
li {
  list-style-type: none;
}

.setting-menu {
  padding: 5px;
}

.menu-block {
  width: 100%;
  /*height: 100px;*/
  background-color: #f5f7fa;
  border: solid 1px #dddddd;
  border-radius: 5px;
  margin-bottom: 5px;
}

a.menu-link {
  text-align: center;
  padding: 8px 8px 8px 18px;
  text-decoration: none;
  position: relative;
  display: block;
  width: 100%;
  font-size: 14px;
  color: #434a54;
}

.v-box {
  position: relative;
  float: right !important;
  text-align: center;
  color: #333333;
  font-size: 14px;
}

.state-div {
  padding-left: 15px;
  padding-right: 15px;
  padding-bottom: 15px;
}

.state-table tbody > tr > td {
  padding: 4px;
  border-bottom: solid 1px #ccc;
}

.state-value {
  font-size:14px;
  padding:5px;
  border-bottom: solid 1px #dddddd;
}

.divider {
  margin-top:0px !important;
  margin-bottom: 10px !important;
  border-bottom: solid 1px #dddddd;
}

.active > a , .active > a:hover {
	background: none;
}

.toggle-header {
  position: absolute;
  top: 20px;
  right: 15px;
}

.nav-title {
  position: relative;
  height: 45px;
  padding:10px 5px;
  font-size:16px;
  text-align: center;
  border-bottom: solid 1px #ccc;
  background-color: white;
  width: 100%;
  z-index: 10
}

.setting-panel {
  position: fixed;
  top: 0px;
  right: 0px;
  background-color: white;
  height:100vh;
  width:400px;
  max-width: 100vw;
  box-shadow: #333 1px 0px 5px 1px;
  z-index: 10;
  padding-bottom: 80px;
  padding-right: 15px;
  padding-left: 15px;
  overflow: auto;
}

.move-out {
  transition: right 0.3s ease-in-out;
  right:-400px;
}

.move-in {
  transition: right 0.3s ease-in-out;
   right:0px;
}
</style>
