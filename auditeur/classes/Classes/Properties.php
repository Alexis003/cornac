<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011 Alter Way Solutions (France)               |
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

class Classes_Properties extends modules {
	protected	$title = 'Properties';
	protected	$description = 'Defined properties';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_report();

        $concat = $this->concat("T2.class","'->'","T2.code");
        $query = <<<SQL
SELECT NULL, T1.file, $concat as code, T2.id, '{$this->name}' , 0
FROM <tokens> T1
  JOIN <tokens> T2 
  ON T2.file = T1.file AND 
  T2.left BETWEEN T1.left AND T1.right 
  AND T2.type='variable'
WHERE T1.class != 'global' AND T1.type='_var'
SQL;
        $this->exec_query_insert('report',$query);

        return true;
    // @todo support methods and classes
    }
}

?>