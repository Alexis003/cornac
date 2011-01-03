#!/usr/bin/env php
<?php


if (!file_exists('../../inventory.ods')) {
    die("'../../inventory.ods' doesn't exists\n Aborting\n");
}

shell_exec('rm -rf extract');
mkdir('extract',0777);
copy('../../inventory.ods','./extract/inventory.zip');
shell_exec('cd extract; unzip inventory.zip; bbedit content.xml');
shell_exec('rm -rf extract');

?>