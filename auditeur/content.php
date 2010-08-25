#!/usr/bin/php
<?php

shell_exec('rm -rf extract');
mkdir('extract',0777);
copy('inventaire.ods','extract/inventaire.zip');
shell_exec('cd extract; unzip inventaire.zip; bbedit content.xml');
//shell_exec('rm -rf extract');

?>