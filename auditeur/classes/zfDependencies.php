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
class zfDependencies extends modules {
	protected	$title = 'ZF : Zend Framework dependance';
	protected	$description = 'Dependances toward ZF  : by heritage or composition, those classes from the ZF are needed.';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}

	public function analyse() {
        $this->clean_rapport();

// @note heritage
        $in = modules::getZendFrameworkClasses();
//        $in = array_slice($in, 0, 4);
        $in = join('", "', $in);
	    $query = <<<SQL
SELECT NULL, T1.fichier, T2.code AS code, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens_tags> TT 
ON T1.id = TT.token_id AND
   TT.type='extends'
JOIN <tokens> T2
ON T2.id = TT.token_sub_id AND
   T2.fichier=T1.fichier
WHERE T1.type = '_class' AND
T2.code IN ("$in")
SQL;
        $this->exec_query_insert('rapport', $query);

// @note direct instantiation with new
        $query = <<<SQL
SELECT NULL, T1.fichier, T2.code AS code, T2.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND
       T2.droite = T1.droite + 1
WHERE T1.type='_new' AND
      T2.code IN ("$in")
SQL;
        $this->exec_query_insert('rapport', $query);

// @note static usage
        $query = <<<SQL
SELECT NULL, T1.fichier, T2.code AS code, T2.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND
       T2.droite = T1.droite + 1
WHERE T1.type='method_static' AND
      T2.code IN ("$in")
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>