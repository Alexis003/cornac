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

class method_without_ppp extends modules {
	protected	$title = 'Methods without PPP';
	protected	$description = 'Spot methods that do not bear any of the ppp visibility attribute : they shoud be mentions explicitly. ';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}

	public function analyse() {
        $this->clean_rapport();

// @todo of course, update this useless query. :)
	    $query = <<<SQL
SELECT NULL, T1.fichier, T1.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
LEFT JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND
       T2.type = 'token_traite' AND
       (T2.droite = T1.droite + 1 OR 
        T2.droite = T1.droite + 3 OR 
        T2.droite = T1.droite + 5
        )
WHERE T1.type='_function' AND
      T1.class!= ''
GROUP BY T1.class, T1.code
HAVING  SUM(IF(T2.code IN ('protected','private','public'), 1, 0)) = 0
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>