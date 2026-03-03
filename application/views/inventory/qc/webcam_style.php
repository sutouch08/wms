<style>
.wraper {
  display: flex;;  
}

.left-column {
  width:400px;
  min-width: 400px;
  height: 350px;  
}

.right-column {  
  flex: 1;
  width: auto;
  margin-left: 20px;
}

.webcam {
  width: 100%;
  position: relative;
}

.webcam::after {
  content: "";
  display: none;
  width: 15px;
  height: 15px;
  background-color: red;
  border-radius: 50%;
  position: absolute;
  top: 15px;
  right: 15px;
}

.webcam.recording video {
  border: 2px solid red;
}

.webcam.recording::after {
  display: block;
}

video {
  width: 100%;
  aspect-ratio: 4/3;
  background-color: black;
  border: solid 2px #000;
  object-fit: cover;
  object-position: center center;
  transform: scale(-1, 1); /* mirror the view*/
}

#stop-watch {
  width: 100%;
  text-align: center;
  font-size: 20px;
  color: white;
  background-color: black;
  position: absolute;
  bottom: 5px;
  opacity: 0.5;
}

.err-label {
  margin-top:15px;
  color:red;
}

#qc-box {
  min-height: 100px;  
  padding: 0;
}

#box-row { 
  width: 100%;
  min-height: 65px;
  padding: 5px;  
}

.box-control {
  position: relative;
  display: flex;  
  float: left;
  height: 60px;
  padding: 5px;
  background-color: #eee;
  border: solid 1px #ddd;
  border-radius:5px;
  margin-left:5px;
  margin-bottom: 5px;
}

.box-label {
  float: left;
  margin-bottom: 0;
  padding-right: 10px;
}

.box-package {
  margin-top: 2px;
  display: block;
  font-size:11px;
  height: auto;
  background-color: #eee;
  border: 0px;
}

.box-count {
  font-size:16px;
  width:60px;
  height: 60px;
  margin-top:-5px;
  border-left:solid 1px #e0e0e0;
  border-right:solid 1px #e0e0e0;
  padding-left:10px;
  padding-right:10px;
}

.box-menu {
  width: 25px;
  height: 45px;
  margin: 0;
}

.qty-summary {
  width: 100%;
  height:60px;
  margin-top: 5px;
  background-color:black;
  color:white;
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
}
/********* END QC BOX *****************/

.incomplete-box {
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: white;
  padding:5px;
}

.pack-item {
  position: relative;
  padding: 10px;
  background-color: #eee;
  border:solid 1px #ddd;
  border-radius: 5px;
  margin-bottom: 10px;
  font-size: 11px;
}

.pack-item.heighlight {
  background-color: #d1ffff;
}

.item-content {
  float:left;
  padding-right: 15px;
  font-size: 12px;
  margin-bottom:5px;
}

button.must-edit {
  width: 35px;
  height: 35px;
  border-radius: 5px;
  position: absolute;
  top: 5px;
  right: 5px;
}

.btn.btn-link {
  padding:0 !important;
}

#btn-force-close {
  position: absolute;
  top: -5px;
  right: 5px;
  z-index: 1;
}

.tableFixHead > thead > tr > th {
  font-size: 11px !important;
  padding: 3px 5px !important;
}

.tableFixHead > tbody > tr > td {
  font-size: 11px !important;
  padding: 3px 5px !important
}
</style>
