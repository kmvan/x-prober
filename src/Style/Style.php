<?php

namespace InnStudio\Prober\Style;

use InnStudio\Prober\Events\EventsApi;

class Style
{
    private $ID = 'style';

    public function __construct()
    {
        EventsApi::on('style', array($this, 'filter'));
    }

    public function filter()
    {
        $this->styleProgress();
        $this->styleGlobal();
        $this->stylePoiContainer();
        $this->stylePoiGrid();
        $this->styleTitle();
    }

    private function styleTitle()
    {
        ?>
<style>
.long-title{
    text-transform: capitalize;
}
.tiny-title{
    display: none;
}
</style>
        <?php
    }

    private function styleProgress()
    {
        ?>
<style>
.progress-container{
    position: relative;
}
.progress-container .percent,
.progress-container .number{
    position: absolute;
    right: 1rem;
    bottom: 0;
    z-index: 1;
    font-weight: bold;
    color: #fff;
    text-shadow: 0 1px 1px #000;
    line-height: 2rem;
}
.progress-container .percent{
    left: 1rem;
    right: auto;
}
.progress {
    position: relative;
    display: block;
    width: 100%;
    height: 2rem;
    background: #444;
    border-radius: 1rem;
    box-shadow: inset 0px 10px 20px rgba(0,0,0,0.3);
  }
  .progress .progress-value{
    position: absolute;
    top: .35rem;
    bottom: .35rem;
    left: .35rem;
    right: .35rem;
    -webkit-transition: 2s all;
    transition: 2s all;
    border-radius: 1rem;
    background: #00cc00;
    box-shadow: inset 0 -5px 10px rgba(0,0,0,0.4), 0 5px 10px 0px rgba(0,0,0,0.3)
  }
  .progress.medium-low .progress-value{
    background: #009999;
  }
  .progress.medium .progress-value{
    background: #f07746;
  }
  .progress.high .progress-value{
    background: #ef2d2d;
  }
</style>
        <?php
    }

    private function styleGlobal()
    {
        ?>
<style>
*{
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    vertical-align: middle;
}

html{
    font-size: 75%;
    background: #333;
}
body{
    background: #f8f8f8;
    color: #666;
    font-family: "Microsoft YaHei UI", "Microsoft YaHei", sans-serif;
    border: 10px solid #333;
    margin: 0;
    border-radius: 2rem;
    line-height: 2rem;
}
a{
    cursor: pointer;
    color: #333;
    text-decoration: none;
}
    a:hover,
    a:active{
        color: #999;
        text-decoration: underline;
    }
.ini-ok{
    color: green;
    font-weight: bold;
}
.ini-error{
    color: red;
    font-weight: bold;
}
h1{
    text-align: center;
    font-size: 1rem;
    background: #333;
    border-radius: 0 0 10rem 10rem;
    width: 60%;
    line-height: 1.5rem;
    margin: 0 auto 1rem;
}
    h1 *{
        display: block;
        color: #fff;
        padding: 0 0 10px;
    }
    h1 *:hover{
        color: #fff;
    }
.form-group{
    overflow: hidden;
    display: table;
    width: 100%;
    border-bottom: 1px solid #eee;
    min-height: 2rem;
}
    .form-group:hover{
        background: rgba(0,0,0,.03);
    }
.group-label,
.group-content{
    display: table-cell;
    padding: .2rem .5rem;
}
.group-label{
    width: 8rem;
    text-align: left;
    font-weight: normal;
}
.group-label a{
    display: block;
}
.group-content a{
    line-height: 1;
    display: block;
}

@media (min-width:768px){
    .group-label{
        width: 15rem;
    }
    .group-label,
    .group-content{
        display: table-cell;
        padding: .5rem 1rem;
    }
}

fieldset{
    position: relative;
    border: 5px solid #eee;
    border-radius: .5rem;
    padding: 0;
    background: rgba(255,255,255,.5);
    margin-bottom: 1rem;
    padding: .5rem 0;
}
    legend{
        background: #333;
        margin-left: 1rem;
        padding: .5rem 2rem;
        border-radius: 5rem;
        color: #fff;
        margin: 0 auto;
    }
    p{
        margin: 0 0 1rem;
    }
    .description{
        margin: 0;
        padding-left: 1rem;
        font-style: italic;
    }
</style>
        <?php
    }

    private function stylePoiContainer()
    {
        ?>
<style>
@media (min-width:768px){.poi-container{margin-left:auto;margin-right:auto;padding-left:.5rem;padding-right:.5rem}}
@media (min-width:579px){.poi-container{width:559px}}
@media (min-width:768px){.poi-container{width:748px}}
@media (min-width:992px){.poi-container{width:940px;padding-left:1rem;padding-right:1rem}}
@media (min-width:1200px){.poi-container{width:1180px}}
@media (min-width:992px){.row{margin-left:-.5rem;margin-right:-.5rem}}
</style>
        <?php
    }

    private function stylePoiGrid()
    {
        ?>
<style>
.row:after{
    content: '';
    display: block;
    clear: both;
}
.row>*{max-width:100%;float:left;width:100%;box-sizing:border-box;padding-left:.25rem;padding-right:.25rem;min-height: 2rem;}
@media (min-width:992px){.row>*{padding-left:.5rem;padding-right:.5rem}}
.poi-g-1-1{width:100%}.poi-g-1-2{width:50%}.poi-g-2-2{width:100%}.poi-g-1-3{width:33.33333%}.poi-g-2-3{width:66.66667%}.poi-g-3-3{width:100%}.poi-g-1-4{width:25%}.poi-g-2-4{width:50%}.poi-g-3-4{width:75%}.poi-g-4-4{width:100%}.poi-g-1-5{width:20%}.poi-g-2-5{width:40%}.poi-g-3-5{width:60%}.poi-g-4-5{width:80%}.poi-g-5-5{width:100%}.poi-g-1-6{width:16.66667%}.poi-g-2-6{width:33.33333%}.poi-g-3-6{width:50%}.poi-g-4-6{width:66.66667%}.poi-g-5-6{width:83.33333%}.poi-g-6-6{width:100%}.poi-g-1-7{width:14.28571%}.poi-g-2-7{width:28.57143%}.poi-g-3-7{width:42.85714%}.poi-g-4-7{width:57.14286%}.poi-g-5-7{width:71.42857%}.poi-g-6-7{width:85.71429%}.poi-g-7-7{width:100%}.poi-g-1-8{width:12.5%}.poi-g-2-8{width:25%}.poi-g-3-8{width:37.5%}.poi-g-4-8{width:50%}.poi-g-5-8{width:62.5%}.poi-g-6-8{width:75%}.poi-g-7-8{width:87.5%}.poi-g-8-8{width:100%}.poi-g-1-9{width:11.11111%}.poi-g-2-9{width:22.22222%}.poi-g-3-9{width:33.33333%}.poi-g-4-9{width:44.44444%}.poi-g-5-9{width:55.55556%}.poi-g-6-9{width:66.66667%}.poi-g-7-9{width:77.77778%}.poi-g-8-9{width:88.88889%}.poi-g-9-9{width:100%}.poi-g-1-10{width:10%}.poi-g-2-10{width:20%}.poi-g-3-10{width:30%}.poi-g-4-10{width:40%}.poi-g-5-10{width:50%}.poi-g-6-10{width:60%}.poi-g-7-10{width:70%}.poi-g-8-10{width:80%}.poi-g-9-10{width:90%}.poi-g-10-10{width:100%}.poi-g-1-11{width:9.09091%}.poi-g-2-11{width:18.18182%}.poi-g-3-11{width:27.27273%}.poi-g-4-11{width:36.36364%}.poi-g-5-11{width:45.45455%}.poi-g-6-11{width:54.54545%}.poi-g-7-11{width:63.63636%}.poi-g-8-11{width:72.72727%}.poi-g-9-11{width:81.81818%}.poi-g-10-11{width:90.90909%}.poi-g-11-11{width:100%}.poi-g-1-12{width:8.33333%}.poi-g-2-12{width:16.66667%}.poi-g-3-12{width:25%}.poi-g-4-12{width:33.33333%}.poi-g-5-12{width:41.66667%}.poi-g-6-12{width:50%}.poi-g-7-12{width:58.33333%}.poi-g-8-12{width:66.66667%}.poi-g-9-12{width:75%}.poi-g-10-12{width:83.33333%}.poi-g-11-12{width:91.66667%}.poi-g-12-12{width:100%}@media (min-width:579px){.poi-g-sm-1-1{width:100%}.poi-g-sm-1-2{width:50%}.poi-g-sm-2-2{width:100%}.poi-g-sm-1-3{width:33.33333%}.poi-g-sm-2-3{width:66.66667%}.poi-g-sm-3-3{width:100%}.poi-g-sm-1-4{width:25%}.poi-g-sm-2-4{width:50%}.poi-g-sm-3-4{width:75%}.poi-g-sm-4-4{width:100%}.poi-g-sm-1-5{width:20%}.poi-g-sm-2-5{width:40%}.poi-g-sm-3-5{width:60%}.poi-g-sm-4-5{width:80%}.poi-g-sm-5-5{width:100%}.poi-g-sm-1-6{width:16.66667%}.poi-g-sm-2-6{width:33.33333%}.poi-g-sm-3-6{width:50%}.poi-g-sm-4-6{width:66.66667%}.poi-g-sm-5-6{width:83.33333%}.poi-g-sm-6-6{width:100%}.poi-g-sm-1-7{width:14.28571%}.poi-g-sm-2-7{width:28.57143%}.poi-g-sm-3-7{width:42.85714%}.poi-g-sm-4-7{width:57.14286%}.poi-g-sm-5-7{width:71.42857%}.poi-g-sm-6-7{width:85.71429%}.poi-g-sm-7-7{width:100%}.poi-g-sm-1-8{width:12.5%}.poi-g-sm-2-8{width:25%}.poi-g-sm-3-8{width:37.5%}.poi-g-sm-4-8{width:50%}.poi-g-sm-5-8{width:62.5%}.poi-g-sm-6-8{width:75%}.poi-g-sm-7-8{width:87.5%}.poi-g-sm-8-8{width:100%}.poi-g-sm-1-9{width:11.11111%}.poi-g-sm-2-9{width:22.22222%}.poi-g-sm-3-9{width:33.33333%}.poi-g-sm-4-9{width:44.44444%}.poi-g-sm-5-9{width:55.55556%}.poi-g-sm-6-9{width:66.66667%}.poi-g-sm-7-9{width:77.77778%}.poi-g-sm-8-9{width:88.88889%}.poi-g-sm-9-9{width:100%}.poi-g-sm-1-10{width:10%}.poi-g-sm-2-10{width:20%}.poi-g-sm-3-10{width:30%}.poi-g-sm-4-10{width:40%}.poi-g-sm-5-10{width:50%}.poi-g-sm-6-10{width:60%}.poi-g-sm-7-10{width:70%}.poi-g-sm-8-10{width:80%}.poi-g-sm-9-10{width:90%}.poi-g-sm-10-10{width:100%}.poi-g-sm-1-11{width:9.09091%}.poi-g-sm-2-11{width:18.18182%}.poi-g-sm-3-11{width:27.27273%}.poi-g-sm-4-11{width:36.36364%}.poi-g-sm-5-11{width:45.45455%}.poi-g-sm-6-11{width:54.54545%}.poi-g-sm-7-11{width:63.63636%}.poi-g-sm-8-11{width:72.72727%}.poi-g-sm-9-11{width:81.81818%}.poi-g-sm-10-11{width:90.90909%}.poi-g-sm-11-11{width:100%}.poi-g-sm-1-12{width:8.33333%}.poi-g-sm-2-12{width:16.66667%}.poi-g-sm-3-12{width:25%}.poi-g-sm-4-12{width:33.33333%}.poi-g-sm-5-12{width:41.66667%}.poi-g-sm-6-12{width:50%}.poi-g-sm-7-12{width:58.33333%}.poi-g-sm-8-12{width:66.66667%}.poi-g-sm-9-12{width:75%}.poi-g-sm-10-12{width:83.33333%}.poi-g-sm-11-12{width:91.66667%}.poi-g-sm-12-12{width:100%}}@media (min-width:768px){.poi-g-md-1-1{width:100%}.poi-g-md-1-2{width:50%}.poi-g-md-2-2{width:100%}.poi-g-md-1-3{width:33.33333%}.poi-g-md-2-3{width:66.66667%}.poi-g-md-3-3{width:100%}.poi-g-md-1-4{width:25%}.poi-g-md-2-4{width:50%}.poi-g-md-3-4{width:75%}.poi-g-md-4-4{width:100%}.poi-g-md-1-5{width:20%}.poi-g-md-2-5{width:40%}.poi-g-md-3-5{width:60%}.poi-g-md-4-5{width:80%}.poi-g-md-5-5{width:100%}.poi-g-md-1-6{width:16.66667%}.poi-g-md-2-6{width:33.33333%}.poi-g-md-3-6{width:50%}.poi-g-md-4-6{width:66.66667%}.poi-g-md-5-6{width:83.33333%}.poi-g-md-6-6{width:100%}.poi-g-md-1-7{width:14.28571%}.poi-g-md-2-7{width:28.57143%}.poi-g-md-3-7{width:42.85714%}.poi-g-md-4-7{width:57.14286%}.poi-g-md-5-7{width:71.42857%}.poi-g-md-6-7{width:85.71429%}.poi-g-md-7-7{width:100%}.poi-g-md-1-8{width:12.5%}.poi-g-md-2-8{width:25%}.poi-g-md-3-8{width:37.5%}.poi-g-md-4-8{width:50%}.poi-g-md-5-8{width:62.5%}.poi-g-md-6-8{width:75%}.poi-g-md-7-8{width:87.5%}.poi-g-md-8-8{width:100%}.poi-g-md-1-9{width:11.11111%}.poi-g-md-2-9{width:22.22222%}.poi-g-md-3-9{width:33.33333%}.poi-g-md-4-9{width:44.44444%}.poi-g-md-5-9{width:55.55556%}.poi-g-md-6-9{width:66.66667%}.poi-g-md-7-9{width:77.77778%}.poi-g-md-8-9{width:88.88889%}.poi-g-md-9-9{width:100%}.poi-g-md-1-10{width:10%}.poi-g-md-2-10{width:20%}.poi-g-md-3-10{width:30%}.poi-g-md-4-10{width:40%}.poi-g-md-5-10{width:50%}.poi-g-md-6-10{width:60%}.poi-g-md-7-10{width:70%}.poi-g-md-8-10{width:80%}.poi-g-md-9-10{width:90%}.poi-g-md-10-10{width:100%}.poi-g-md-1-11{width:9.09091%}.poi-g-md-2-11{width:18.18182%}.poi-g-md-3-11{width:27.27273%}.poi-g-md-4-11{width:36.36364%}.poi-g-md-5-11{width:45.45455%}.poi-g-md-6-11{width:54.54545%}.poi-g-md-7-11{width:63.63636%}.poi-g-md-8-11{width:72.72727%}.poi-g-md-9-11{width:81.81818%}.poi-g-md-10-11{width:90.90909%}.poi-g-md-11-11{width:100%}.poi-g-md-1-12{width:8.33333%}.poi-g-md-2-12{width:16.66667%}.poi-g-md-3-12{width:25%}.poi-g-md-4-12{width:33.33333%}.poi-g-md-5-12{width:41.66667%}.poi-g-md-6-12{width:50%}.poi-g-md-7-12{width:58.33333%}.poi-g-md-8-12{width:66.66667%}.poi-g-md-9-12{width:75%}.poi-g-md-10-12{width:83.33333%}.poi-g-md-11-12{width:91.66667%}.poi-g-md-12-12{width:100%}}@media (min-width:992px){.poi-g-lg-1-1{width:100%}.poi-g-lg-1-2{width:50%}.poi-g-lg-2-2{width:100%}.poi-g-lg-1-3{width:33.33333%}.poi-g-lg-2-3{width:66.66667%}.poi-g-lg-3-3{width:100%}.poi-g-lg-1-4{width:25%}.poi-g-lg-2-4{width:50%}.poi-g-lg-3-4{width:75%}.poi-g-lg-4-4{width:100%}.poi-g-lg-1-5{width:20%}.poi-g-lg-2-5{width:40%}.poi-g-lg-3-5{width:60%}.poi-g-lg-4-5{width:80%}.poi-g-lg-5-5{width:100%}.poi-g-lg-1-6{width:16.66667%}.poi-g-lg-2-6{width:33.33333%}.poi-g-lg-3-6{width:50%}.poi-g-lg-4-6{width:66.66667%}.poi-g-lg-5-6{width:83.33333%}.poi-g-lg-6-6{width:100%}.poi-g-lg-1-7{width:14.28571%}.poi-g-lg-2-7{width:28.57143%}.poi-g-lg-3-7{width:42.85714%}.poi-g-lg-4-7{width:57.14286%}.poi-g-lg-5-7{width:71.42857%}.poi-g-lg-6-7{width:85.71429%}.poi-g-lg-7-7{width:100%}.poi-g-lg-1-8{width:12.5%}.poi-g-lg-2-8{width:25%}.poi-g-lg-3-8{width:37.5%}.poi-g-lg-4-8{width:50%}.poi-g-lg-5-8{width:62.5%}.poi-g-lg-6-8{width:75%}.poi-g-lg-7-8{width:87.5%}.poi-g-lg-8-8{width:100%}.poi-g-lg-1-9{width:11.11111%}.poi-g-lg-2-9{width:22.22222%}.poi-g-lg-3-9{width:33.33333%}.poi-g-lg-4-9{width:44.44444%}.poi-g-lg-5-9{width:55.55556%}.poi-g-lg-6-9{width:66.66667%}.poi-g-lg-7-9{width:77.77778%}.poi-g-lg-8-9{width:88.88889%}.poi-g-lg-9-9{width:100%}.poi-g-lg-1-10{width:10%}.poi-g-lg-2-10{width:20%}.poi-g-lg-3-10{width:30%}.poi-g-lg-4-10{width:40%}.poi-g-lg-5-10{width:50%}.poi-g-lg-6-10{width:60%}.poi-g-lg-7-10{width:70%}.poi-g-lg-8-10{width:80%}.poi-g-lg-9-10{width:90%}.poi-g-lg-10-10{width:100%}.poi-g-lg-1-11{width:9.09091%}.poi-g-lg-2-11{width:18.18182%}.poi-g-lg-3-11{width:27.27273%}.poi-g-lg-4-11{width:36.36364%}.poi-g-lg-5-11{width:45.45455%}.poi-g-lg-6-11{width:54.54545%}.poi-g-lg-7-11{width:63.63636%}.poi-g-lg-8-11{width:72.72727%}.poi-g-lg-9-11{width:81.81818%}.poi-g-lg-10-11{width:90.90909%}.poi-g-lg-11-11{width:100%}.poi-g-lg-1-12{width:8.33333%}.poi-g-lg-2-12{width:16.66667%}.poi-g-lg-3-12{width:25%}.poi-g-lg-4-12{width:33.33333%}.poi-g-lg-5-12{width:41.66667%}.poi-g-lg-6-12{width:50%}.poi-g-lg-7-12{width:58.33333%}.poi-g-lg-8-12{width:66.66667%}.poi-g-lg-9-12{width:75%}.poi-g-lg-10-12{width:83.33333%}.poi-g-lg-11-12{width:91.66667%}.poi-g-lg-12-12{width:100%}}@media (min-width:1200px){.poi-g-xl-1-1{width:100%}.poi-g-xl-1-2{width:50%}.poi-g-xl-2-2{width:100%}.poi-g-xl-1-3{width:33.33333%}.poi-g-xl-2-3{width:66.66667%}.poi-g-xl-3-3{width:100%}.poi-g-xl-1-4{width:25%}.poi-g-xl-2-4{width:50%}.poi-g-xl-3-4{width:75%}.poi-g-xl-4-4{width:100%}.poi-g-xl-1-5{width:20%}.poi-g-xl-2-5{width:40%}.poi-g-xl-3-5{width:60%}.poi-g-xl-4-5{width:80%}.poi-g-xl-5-5{width:100%}.poi-g-xl-1-6{width:16.66667%}.poi-g-xl-2-6{width:33.33333%}.poi-g-xl-3-6{width:50%}.poi-g-xl-4-6{width:66.66667%}.poi-g-xl-5-6{width:83.33333%}.poi-g-xl-6-6{width:100%}.poi-g-xl-1-7{width:14.28571%}.poi-g-xl-2-7{width:28.57143%}.poi-g-xl-3-7{width:42.85714%}.poi-g-xl-4-7{width:57.14286%}.poi-g-xl-5-7{width:71.42857%}.poi-g-xl-6-7{width:85.71429%}.poi-g-xl-7-7{width:100%}.poi-g-xl-1-8{width:12.5%}.poi-g-xl-2-8{width:25%}.poi-g-xl-3-8{width:37.5%}.poi-g-xl-4-8{width:50%}.poi-g-xl-5-8{width:62.5%}.poi-g-xl-6-8{width:75%}.poi-g-xl-7-8{width:87.5%}.poi-g-xl-8-8{width:100%}.poi-g-xl-1-9{width:11.11111%}.poi-g-xl-2-9{width:22.22222%}.poi-g-xl-3-9{width:33.33333%}.poi-g-xl-4-9{width:44.44444%}.poi-g-xl-5-9{width:55.55556%}.poi-g-xl-6-9{width:66.66667%}.poi-g-xl-7-9{width:77.77778%}.poi-g-xl-8-9{width:88.88889%}.poi-g-xl-9-9{width:100%}.poi-g-xl-1-10{width:10%}.poi-g-xl-2-10{width:20%}.poi-g-xl-3-10{width:30%}.poi-g-xl-4-10{width:40%}.poi-g-xl-5-10{width:50%}.poi-g-xl-6-10{width:60%}.poi-g-xl-7-10{width:70%}.poi-g-xl-8-10{width:80%}.poi-g-xl-9-10{width:90%}.poi-g-xl-10-10{width:100%}.poi-g-xl-1-11{width:9.09091%}.poi-g-xl-2-11{width:18.18182%}.poi-g-xl-3-11{width:27.27273%}.poi-g-xl-4-11{width:36.36364%}.poi-g-xl-5-11{width:45.45455%}.poi-g-xl-6-11{width:54.54545%}.poi-g-xl-7-11{width:63.63636%}.poi-g-xl-8-11{width:72.72727%}.poi-g-xl-9-11{width:81.81818%}.poi-g-xl-10-11{width:90.90909%}.poi-g-xl-11-11{width:100%}.poi-g-xl-1-12{width:8.33333%}.poi-g-xl-2-12{width:16.66667%}.poi-g-xl-3-12{width:25%}.poi-g-xl-4-12{width:33.33333%}.poi-g-xl-5-12{width:41.66667%}.poi-g-xl-6-12{width:50%}.poi-g-xl-7-12{width:58.33333%}.poi-g-xl-8-12{width:66.66667%}.poi-g-xl-9-12{width:75%}.poi-g-xl-10-12{width:83.33333%}.poi-g-xl-11-12{width:91.66667%}.poi-g-xl-12-12{width:100%}}
</style>
        <?php
    }
}
