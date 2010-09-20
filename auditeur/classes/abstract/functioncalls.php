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

class functioncalls extends modules {
    protected $not = false; 
    protected $functions = array();

    function __construct($mid) {
        parent::__construct($mid);
    }
    
    public function analyse() {
        if (!is_array($this->functions) || empty($this->functions)) {
            print "No function name provided for class ".get_class($this)." Aborting.\n";
            die();
        }
        $in = join("','", $this->functions);
        $this->functions = array();

        if ($this->not) {
            $not = ' not ';
        } else {
            $not = '';
        }
        
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport> 
    SELECT NULL, T1.fichier, T2.code AS code, T1.id, '{$this->name}', 0
    FROM <tokens> T1 
    JOIN <tokens> T2
        ON T2.droite = T1.droite + 1 AND
           T2.fichier = T1.fichier
    WHERE T1.type='functioncall' AND T2.code $not in ('$in')
SQL;
        $this->exec_query($query);
    }
}

?>