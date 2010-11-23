<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Damien Seguy <damien.seguy@gmail.com>                        |
   +----------------------------------------------------------------------+
 */

class classes_undefined extends modules {
	protected	$title = 'Classes non définies';
	protected	$description = 'Liste des classes du code qui ne sont pas déclarées, mais qui sont utilisées. Les classes PHP sont omises.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array('classes','_new');
	}
	
	public function analyse() {
        $this->clean_rapport();

        $in = "'".join("','", modules::getPHPClasses())."'";
        $query = <<<SQL
SELECT NULL, TR1.file, TR1.element AS code, TR1.id, '{$this->name}', 0
    FROM <rapport>  TR1
    LEFT JOIN <rapport>  TR2 
        ON TR1.element = TR2.element AND TR2.module='classes' 
    WHERE TR1.module = '_new' AND 
          TR2.element IS NULL AND
          TR1.element NOT IN ($in)
SQL;
        $this->exec_query_insert('rapport', $query);
        return true;
	}
}

?>