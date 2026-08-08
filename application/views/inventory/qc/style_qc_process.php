<style>
  #row {
    display: flex;
    flex-wrap: wrap;
    flex-direction: row;
    margin-left: -12px;
    margin-right: -12px;
  }

  #video-box {
    width: 300px !important;
  }

  #info-box {
    flex: 1;
  }

  .table-narrow thead tr th,
  .table-narrow tbody tr td {
    font-size: 11px;
    padding: 4px;
  }

  .table-narrow thead tr th:first-child,
  .table-narrow tbody tr td:first-child {
    padding-left: 8px;
  }

  .incomplete-box {
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: white;
    padding: 5px;
  }

  .pack-item {
    position: relative;
    padding: 10px;
    background-color: #eee;
    border: solid 1px #ddd;
    border-radius: 5px;
    margin-bottom: 10px;
    font-size: 11px;
  }

  .pack-item.heighlight {
    background-color: #d1ffff;
  }

  .item-content {
    float: left;
    padding-right: 15px;
    font-size: 12px;
    margin-bottom: 5px;
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
    padding: 0 !important;
  }

  #btn-force-close {
    position: absolute;
    top: -5px;
    right: 5px;
    z-index: 1;
  }

  .tableFixHead>thead>tr>th {
    font-size: 11px !important;
    padding: 3px 5px !important;
  }

  .tableFixHead>tbody>tr>td {
    font-size: 11px !important;
    padding: 3px 5px !important;
  }

  /*--------- qc box -------------- */
  .box-control {
    position: relative;
    float: left;
    height: 60px;
    padding: 5px;
    background-color: #eee;
    border: solid 1px #ddd;
    border-radius: 5px;
    margin-left: 5px;
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
    font-size: 11px;
    height: auto;
    background-color: #eee;
    border: 0px;
  }

  .box-count {
    font-size: 16px;
    width: 60px;
    height: 60px;
    margin-top: -5px;
    border-left: solid 1px #e0e0e0;
    border-right: solid 1px #e0e0e0;
    padding-left: 10px;
    padding-right: 10px;
  }

  .box-weight {
    font-size: 16px;
    width: 80px;
    height: 60px;
    margin-top: -5px;
    border-right: solid 1px #e0e0e0;
    padding-left: 5px;
    padding-right: 5px;
  }

  .box-menu {
    width: 25px;
    height: 45px;
    margin: 0;
  }

  .qty-summary {
    width: 100%;
    height: 60px;
    margin-top: 5px;
    background-color: black;
    color: white;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .box-weight-btn {
    width: 60px;
    height: 60px;
    margin-top: -5px;
    border-right: solid 1px #e0e0e0;
    padding-left: 5px;
    padding-right: 5px;
    padding-top: 7px;
  }

  .weight-btn {
    width: 45px;
    height: 45px;
    border-radius: 5px;
  }

  #qc-box {
    display: flex;
    flex-wrap: wrap;
  }

  #weight-box {
    width: 250px !important;
  }

  #weight-content {
    width: 100%;
    font-size: 30px;
    background-color: black;
    color: white;
    padding-top: 5px;
    margin-top: 0px;
  }

  #box-row {
    flex: 1;
    min-height: 65px;
  }

  /*---------- end qc box -----------*/

  /*--------- video box -----------*/

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
    aspect-ratio: 16/9;
    background-color: black;
    border: solid 2px #000;
    object-fit: cover;
    object-position: center center;
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
    margin-top: 15px;
    color: red;
  }

  /*------- END VIDEO BOX -----------*/
</style>