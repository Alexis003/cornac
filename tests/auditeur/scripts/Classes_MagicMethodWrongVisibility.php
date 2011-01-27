<?php

class x_static {
    static function __get($x) {}
    static function __set($x, $y) {}
    static function __call($x, $y) {}
    static function __isset($x) {}
    static function __unset($x) {}
}

class x_protected {
    protected function __get($x) {}
    protected function __set($x, $y) {}
    protected function __call($x, $y) {}
    protected function __isset($x) {}
    protected function __unset($x) {}
}

class x_private {
    private function __get($x) {}
    private function __set($x, $y) {}
    private function __call($x, $y) {}
    private function __isset($x) {}
    private function __unset($x) {}
}

class x_private_static {
    private static function __get($x) {}
    private static function __set($x, $y) {}
    private static function __call($x, $y) {}
    private static function __isset($x) {}
    private static function __unset($x) {}
}

class x_protected_static {
    protected static function __get($x) {}
    protected static function __set($x, $y) {}
    protected static function __call($x, $y) {}
    protected static function __isset($x) {}
    protected static function __unset($x) {}
}

class x_final {
    final function __get($x) {}
    final function __set($x, $y) {}
    final function __call($x, $y) {}
    final function __isset($x) {}
    final function __unset($x) {}
}

class x_public {
    public function __get($x) {}
    public function __set($x, $y) {}
    public function __call($x, $y) {}
    public function __isset($x) {}
    public function __unset($x) {}
}


?>