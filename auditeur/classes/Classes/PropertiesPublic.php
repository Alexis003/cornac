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

class Classes_PropertiesPublic extends modules {
	protected	$description = 'Public properties';
	protected	$title = 'List of public properties in classes. Defined as such, or used as such.';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();
        
        // @doc cas of simple public var
        $query = <<<SQL
SELECT NULL, T1.file, CONCAT(T1.class,'::',T2.code), T1.id,  '{$this->name}', 0
FROM <tokens> T1 
LEFT JOIN <tokens> T2
    ON T1.left + 1 = T2.left AND
       T1.file = T2.file 
WHERE T1.type = '_var' AND
      T2.type = 'variable'
SQL;
        $this->exec_query_insert('rapport',$query);

        // @doc cas of simple public var
        $query = <<<SQL
SELECT NULL, T1.file, CONCAT(T1.class,'::',T3.code), T1.id,  '{$this->name}', 0
FROM <tokens> T1 
LEFT JOIN <tokens> T2
    ON T1.left + 1 = T2.left AND
       T1.file = T2.file 
JOIN <tokens> T3
    ON T1.file = T2.file AND 
       T3.left = T1.left + 3 AND
       T1.file = T3.file AND
       T3.type != 'token_traite'
WHERE T1.type = '_var' AND
     T2.code = 'public'
SQL;
        $this->exec_query_insert('rapport',$query);

        $query = <<<SQL
SELECT NULL, T1.file, CONCAT(T1.class,'::',T4.code), T1.id,  '{$this->name}', 0
FROM <tokens> T1 
JOIN <tokens> T2
    ON T1.left + 1 = T2.left AND
       T1.file = T2.file AND 
       T2.type = 'token_traite'
JOIN <tokens> T3
    ON T1.file = T2.file AND 
       T3.left = T1.left + 3 AND
       T1.file = T3.file AND
       T3.type = 'token_traite'
JOIN <tokens> T4
    ON T1.file = T4.file AND 
       T4.left = T1.left + 5 AND
       T1.file = T4.file AND
       T4.type != 'token_traite'
WHERE T1.type = '_var' AND
     (T2.code = 'public' OR T3.code='public')
SQL;
        $this->exec_query_insert('rapport',$query);

    // @todo support class and methods
    // @todo support also static and var keyword
    
        return true;
    }
}

?>