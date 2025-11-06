<style>
.head-line {
  font-size: clamp(2rem, 2.5rem, 6rem);
  text-align: center;
  font-weight: bolder;
  vertical-align:middle !important;
  /* writing-mode: vertical-lr; */
}

.h-box {
  width: 10%; 
  text-align: center;
  color: #333333;
  font-size: clamp(1.5rem, 2.5rem, 8rem);
  font-weight: bolder;
  vertical-align:middle !important;
}

.v-box {
  position: relative;
  min-height: 200px !important;
  text-align: center;
  color: #333333;
  font-size: clamp(2rem, 2.5rem, 6rem);
  font-weight: bolder;
  vertical-align:middle !important;
}

.pre-load {
  font-size: 18px;
  margin:0;
  position: absolute;
  top: 50%;
  right: 0;
  -ms-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
}

.load-out {
  transition: opacity 0.5s ease-in-out;
  opacity: 0;
}

.load-in {
  transition: opacity 0.5s ease-in-out;
  opacity: 0.6;
}

.h-box.i-0 { background-color: #cccccc;}
.h-box.i-3 { background-color: #a59df3;}
.h-box.i-4 { background-color: #FBB57F;}
.h-box.i-5 { background-color: #d990ef;}
.h-box.i-6 { background-color: #13b161;}
.h-box.i-7 { background-color: #e7a9cd;}
.h-box.i-8 { background-color: #92cd88;}

.v-box.i-0 { background-color: #dddddd;}
.v-box.i-3 { background-color: #d3cffb;}
.v-box.i-4 { background-color: #ffd3b1;}
.v-box.i-5 { background-color: #e7bdf3;}
.v-box.i-6 { background-color: #89e1b5;}
.v-box.i-7 { background-color: #e9c9dc;}
.v-box.i-8 { background-color: #cfedca;}

.v-box.i-02 { background-color: #dddddd;}
.v-box.i-32 { background-color: #ADA9D4;}
.v-box.i-42 { background-color: #efba92;}
.v-box.i-52 { background-color: #e1adef;}
.v-box.i-62 { background-color: #6ed9a3;}
.v-box.i-72 { background-color: #e3b4d0;}
.v-box.i-82 { background-color: #bce1b6;}
.total { background-color: #3f3e43; color: white;}

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

.toggle-header {
  position: absolute;
  top: 10px;
  right: 15px;
}
/*
.toggle-header-icon {
  position: relative;
  display: block;
  padding:10px;
  height: 45px;
  color: white;
} */

.setting-panel {
  position: fixed;
  top: 10px;
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

.form-horizontal .form-group {
  margin-left: 5px;
  margin-right: 5px;
}
</style>
