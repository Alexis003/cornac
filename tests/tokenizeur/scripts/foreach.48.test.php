<?php

foreach ($adminTab->_includeVars AS $var => $value)
	$adminTab->$var = $this->$value;

?>