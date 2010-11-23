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

class gpc_affectations extends modules {
	protected	$title = 'Assignations de GPC';
	protected	$description = 'Liste des variables GPC qui se voient assigné une valeur (mauvaise pratique)';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array('affectations_variables');
	}

	public function analyse() {
        $this->clean_rapport();

        $gpc_regexp = '(\\\\'.join('|\\\\',modules::getPHPGPC()).')';

        $query = <<<SQL
SELECT NULL, TR1.fichier, TR1.element, TR1.id, '{$this->name}', 0
FROM <rapport> TR1
WHERE TR1.module = 'affectations_variables' AND 
      BINARY TR1.element REGEXP '^$gpc_regexp'
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>