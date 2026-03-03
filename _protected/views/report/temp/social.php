<style>
  

  .container-report {
    /* width: 1000px; */
    position: relative;
    display: flex;
    justify-content: space-between;
  }

  .container-report .card {
    position: relative;
    border-radius: 10px;
  }

  .container-report .card .icon {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    transition: 0.7s;
    z-index: 1;
  }

 

 


  .container-report .card .icon .fa {
    position: absolute;
    top: 60%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 80px;
    transition: 0.7s;
    color: #fff;
  }

  .container-report>i {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 80px;
    transition: 0.7s;
    color: #fff;
  }

  .container-report .card .face {
    width: 350px;
    height: 250px;
    transition: 0.5s;
  }

  .container-report .card .face.face1 {
    position: relative;
    background: #333;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1;
    transform: translateY(100px);
  }

  .container-report .card:hover .face.face1 {
    background: #ff0057;
    transform: translateY(0px);
  }

  .container-report .card .face.face1 .card-content {
    opacity: 1;
    transition: 0.5s;
  }

  .container-report .card:hover .face.face1 .card-content {
    opacity: 1;
  }

  .container-report .card .face.face1 .card-content i {
    max-width: 100px;
  }

  .container-report .card .face.face2 {
    position: relative;
    background: #fff;
    display: flex;
    /* justify-content: center; */
    /* align-items: center; */
    padding: 20px;
    box-sizing: border-box;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.8);
    transform: translateY(-150px);
  }

  .container-report .card:hover .face.face2 {
    transform: translateY(0);
  }

  .container-report .card .face.face2 .card-content p {
    margin: 0;
    padding: 0;
    text-align: center;
    color: #414141;
  }

  .container-report .card .face.face2 .card-content h3 {
    margin: 0 0 10px 0;
    padding: 0;
    color: #fff;
    font-size: 24px;
    text-align: center;
    color: #414141;
  }

  .container-report a {
    text-decoration: none;
    color: #414141;
  }

  .card-title{
    font-size: 25px;
    text-align: center;
    color: #fff;
    margin-top: 60px;
  }

  .face ul {
    font-size: 16px;
    list-style: none;
    margin: 0px;
    padding: 0px;
  }

  .face ul li{
    border-left: 3px solid;
    margin-bottom: 5px;
    transition: .2s;
    width: 310px;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .face ul li:hover{
    border-left: 3px solid #fff;
    transition: .2s;
  }

  .face ul li a{
    padding-left: 10px;
    transition: .2s;
  }

  .face ul li a:hover{
    padding-left: 15px;
    transition: .2s;
  }


  .card-red .face .card-content .icon{
    background: #e07768;
  }
  .card-red .face ul li{
    border-left-color: #e07768;
  }
  .card-red .face ul li a:hover{
    color: #e07768;
  }


  .card-blue .face .card-content .icon{
    background: #6eadd4;
  }
  .card-blue .face ul li{
    border-left-color: #6eadd4;
  }
  .card-blue .face ul li a:hover{
    color: #6eadd4;
  }

  .card-green .face .card-content .icon{
    background: #4aada9;
  }
  .card-green .face ul li{
    border-left-color: #4aada9;
  }
  .card-green .face ul li a:hover{
    color: #4aada9;
  }

  .card-ucell .face .card-content .icon{
    background: #9c27b0;
  }
  .card-ucell .face ul li{
    border-left-color: #9c27b0;
  }
  .card-ucell .face ul li a:hover{
    color: #9c27b0;
  }

  .card-orange .face .card-content .icon{
    background: #ff5722;
  }
  .card-orange .face ul li{
    border-left-color: #ff5722;
  }
  .card-orange .face ul li a:hover{
    color: #ff5722;
  }

  .card-green2 .face .card-content .icon{
    background: #4caf50;
  }
  .card-green2 .face ul li{
    border-left-color: #4caf50;
  }
  .card-green2 .face ul li a:hover{
    color: #4caf50;
  }


</style>
<div class="row">
  <div class="col-lg-10 col-lg-offset-1">
    <div class="container-report" style="margin-top: -50px;">

      <div class="card card-red" style="z-index: 1000;">
        <div class="face face1">
          <div class="card-content">
            <div class="icon">
              <p class="card-title">Material management</p>
              <i class="fa fa-cube" aria-hidden="true"></i>
            </div>
          </div>
        </div>
        <div class="face face2">
            <ul>
              <li><a href="" title="Обеспеченность по машинакомплектам по машинакомплектам">Обеспеченность по машинакомплектам по машинакомплектам</a></li>
              <li><a href="">Coverage</a></li>
              <li><a href="">Order status</a></li>
              <li><a href="">Part requirements</a></li>
              <li><a href="">Intransit material report</a></li>
              <li><a href="">Pipeline material report</a></li>
              <li><a href="">Monthly material import</a></li>
              </ul>
          </div>
        </div>

      <div class="card card-blue"  style="z-index: 1000;">
        <div class="face face1">
          <div class="card-content">
            <div class="icon">
              <p class="card-title">Inventory</p>
              <i class="fa fa-diamond" aria-hidden="true"></i>
            </div>
          </div>
        </div>
        <div class="face face2">
            <ul>
              <li><a href="">Days On Hand (DOH)</a></li>
              <li><a href="">Stocks</a></li>
            </ul>
        </div>
      </div>

      <div class="card card-green" style="z-index: 1000;">
        <div class="face face1">
          <div class="card-content">
            <div class="icon">
              <p class="card-title">Production</p>
              <i class="fa fa-cogs" aria-hidden="true"></i>
            </div>
          </div>
        </div>
        <div class="face face2">
            <ul>
              <li><a href="">Production plan</a></li>
              <li><a href="">Production target/actual</a></li>
              <li><a href="">Production result by Product line</a></li>
              <li><a href="">FTQ by Product Line</a></li>
            </ul>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-10 col-lg-offset-1">
    <div class="container-report" style="margin-top: -140px;">

      <div class="card card-ucell">
        <div class="face face1">
          <div class="card-content">
            <div class="icon">
              <p class="card-title">Sales</p>
              <i class="fa fa-truck" aria-hidden="true"></i>
            </div>
          </div>
        </div>
        <div class="face face2">
            <ul>
              <li><a href="">Finished goods invoice report</a></li>
            </ul>
        </div>
      </div>

      <div class="card card-orange">
        <div class="face face1">
          <div class="card-content">
            <div class="icon">
              <p class="card-title">Finance</p>
              <i class="fa fa-line-chart" aria-hidden="true"></i>
            </div>
          </div>
        </div>
        <div class="face face2">
            <ul>
              <li><a href="">Cash requirement for import shipments</a></li>
              <li><a href="">SPL</a></li>
              <li><a href="">Material BOM Cost</a></li>
            </ul>
        </div>
      </div>

      <div class="card card-green2">
        <div class="face face1">
          <div class="card-content">
            <div class="icon">
              <p class="card-title">Visitors</p>
              <i class="fa fa-users" aria-hidden="true"></i>
            </div>
          </div>
        </div>
        <div class="face face2">
            <ul>
              <li><a href="">All visitors</a></li>
              <li><a href="">Visitor statistics</a></li>
            </ul>
        </div>
      </div>

    </div>

  </div>
</div>