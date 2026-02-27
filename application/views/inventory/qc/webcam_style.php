<style>
.wraper {
  display: flex;;
  /* width: 100%; */
}

.left-column {
  width:400px;
  min-width: 400px;
  height: 350px;
  /* float: left; */
}

.right-column {
  /* width: calc(100vw - 700px);
  float: left; */
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

#box-row {
  min-height: 65px;
  width: 100%;
  padding: 5px;
}

.box-control {
  position: relative;
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

</style>
