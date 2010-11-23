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

class doubledefclass extends modules {
	protected	$description = 'Liste des défintions doubles de classes';
	protected	$title = 'Double définitions de classes : des classes définies plusieurs fois au cours du code';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	function dependsOn() {
        return array('classes');	
	}

	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
SELECT NULL, file, TR.element,  TR.token_id, '{$this->name}', 0
    FROM <rapport> TR
    WHERE module='classes'                                  AND
         TR.element IN (SELECT element FROM <rapport> TR
                            WHERE module='classes'
                            GROUP BY element 
                            HAVING count(*) > 1);
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>