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

class undeffunctions extends modules {
	protected	$title = 'Undefined functions';
	protected	$description = 'List of function without defintions nor declaration. They may be actually forgotten (dead code), native to PHP (unusual ext), or included in standard library (__autoload and PEAR).';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
SELECT NULL, TR1.fichier, TR1.element, TR1.id, '{$this->name}', 0
FROM <rapport> TR1
LEFT JOIN <rapport> TR2 
  ON TR1.element = TR2.element AND TR2.module='deffunctions'
WHERE TR1.module='functionscalls' AND
      TR2.element IS NULL;
SQL;
        $this->exec_query_insert('rapport',$query);

        return true;
	}
}

?>