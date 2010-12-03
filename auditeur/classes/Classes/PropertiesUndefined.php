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

class Classes_PropertiesUndefined extends modules {
	protected	$title = 'Propriétés non definies';
	protected	$description = 'Liste des propriétés utilisées mais pas définies';

    function __construct($mid) {
        parent::__construct($mid);
    }

	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
DROP TABLE IF EXISTS Classes_PropertiesUndefined
SQL;
        $this->exec_query($query);

        $query = <<<SQL
CREATE TEMPORARY TABLE {$this->name}_tmp
SELECT DISTINCT right(code, length(code) - 1) as code, class 
FROM <tokens> 
WHERE scope='global'  AND 
      type ='variable'
SQL;
        $this->exec_query($query);

        $query = <<<SQL
ALTER TABLE {$this->name}_tmp ADD UNIQUE (code(500), class)
SQL;
        $this->exec_query($query);

// @note only works on the same class. Doesn't take into account hierarchy
        $query = <<<SQL
SELECT NULL, T1.file, T2.code AS code, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.file = T1.file AND 
       T2.left BETWEEN T1.left AND T1.right
LEFT JOIN {$this->name}_tmp TMP 
    ON TMP.code = T2.code AND
       TMP.class = T2.class 
WHERE T1.scope!='global'  AND 
      T1.type ='property' AND 
      T2.type='literals'  AND 
      TMP.code IS NULL
SQL;
        $this->exec_query_insert('rapport',$query);

        $query = <<<SQL
DROP TABLE IF EXISTS Classes_PropertiesUndefined
SQL;
        $this->exec_query($query);
        
        return false;
	}
}

?>