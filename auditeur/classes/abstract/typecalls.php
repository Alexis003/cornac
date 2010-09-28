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

class typecalls extends modules {
    protected $code = null;

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
	    if (is_array($this->type)) {
    	    $in = '"'.join('", "', $this->type).'"';
	    } else {
    	    $in = $this->type;
	    }

        $this->clean_rapport();

        $query = <<<SQL
    SELECT NULL, T1.fichier, T1.code AS code, T1.id, '{$this->name}', 0
    FROM <tokens> T1 
    WHERE T1.type IN ($in)
SQL;
        if (!is_null($this->code) && is_array($this->code) && count($this->code) > 0) {
            $in = "'".join("', '", $this->code)."'";
            $query .= " AND T1.code IN ($in)";
        }
        
        $this->exec_query_insert('rapport', $query);
	}
}

?>