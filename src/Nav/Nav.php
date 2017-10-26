<?php

namespace InnStudio\Prober\Nav;

use InnStudio\Prober\Events\Api as Events;

class Nav
{
    private $ID = 'nav';

    public function __construct()
    {
        Events::on('script', array($this, 'filterScript'));
        Events::on('style', array($this, 'filterStyle'));
    }

    public function filterStyle()
    {
        ?>
<style>
.nav {
    position: fixed;
    bottom: 0;
    background: #333;
    padding: 0 1rem;
    left: 0;
    right: 0;
    text-align: center;
    z-index: 10;
}
    .nav a{
        display: inline-block;
        color: #eee;
        padding: .3rem .5rem;
        border-left: 1px solid rgba(255,255,255,.05);
    }
    .nav a:first-child{
        border: none;
    }
    .nav a:hover,
    .nav a:focus,
    .nav a:active{
        background: #f8f8f8;
        color: #333;
    }

.nav .long-title{
    display: none;
}
.nav .tiny-title{
    display: block;
}
@media (min-width: 579px) {
    .nav .tiny-title{
        display: none;
    }
    .nav .long-title{
        display: block;
    }
    .nav a{
        padding: .3rem 1rem;
    }
}
</style>
        <?php
    }

    public function filterScript()
    {
        ?>
<script>
(function(){
var fieldsets = document.querySelectorAll('fieldset');

if (! fieldsets.length) {
    return;
}

var nav = document.createElement('div');
nav.className = 'nav';


for(var i = 0; i < fieldsets.length; i++) {
    var fieldset = fieldsets[i];
    var a = document.createElement('a');
    a.href = '#' + encodeURIComponent(fieldset.id);
    a.innerHTML = fieldset.querySelector('legend').innerHTML;
    nav.appendChild(a);
}

document.body.appendChild(nav);
})()
</script>
        <?php
    }
}
