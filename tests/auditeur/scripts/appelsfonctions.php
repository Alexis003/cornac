<?php

a();
e();


function a() {
    b();
    c();
}

function b() {

}

function c() {
    d();
}

function d() {
    if (e()) {
        b();
    } else {
        c();
    }
//    a();
}

function f() {
//    a();
}

?>
