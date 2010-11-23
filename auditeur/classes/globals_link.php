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

class globals_link extends modules {
	protected	$title = 'Réseau des globales';
	protected	$description = 'Liste des dépendances de globales entre les files';

	function __construct($mid) {
        parent::__construct($mid);
        
        $this->format = modules::FORMAT_DOT;
	}
	
	public function analyse() {
        $this->clean_rapport();
        
        $query = <<<SQL
INSERT INTO <rapport_dot>
SELECT DISTINCT TR1.file, TR2.file, TR1.element, '{$this->name}'
FROM <rapport> TR1
JOIN <rapport> TR2
    ON TR1.element = TR2.element AND
       TR2.module='globals'
WHERE TR1.module='globals'
SQL;
        $res = $this->exec_query($query);
	}
}

?>