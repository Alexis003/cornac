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

class zfElements extends modules {
	protected	$title = 'Element ZF non validés';
	protected	$description = 'Liste des elements de formulaire qui ne sont pas validés!';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();
        
        $classes = array('Zend_Form_Element_Hidden', 
                         'Zend_Form_Element_Submit', 
                         'Zend_Form_Element_Text',
                         'Zend_Form_Element_TextArea',
                         'Zend_Form_Element_Password',
                         'Zend_Form_Element_Submit');
        
        $in = join("', '", $classes);
	    $query = <<<SQL
SELECT T1.droite, T1.gauche, T1.fichier , T1.id, T2.code
FROM <tokens> T1
    JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND T2.droite BETWEEN T1.droite AND T1.gauche 
    WHERE T2.code in ('$in') AND 
          T1.type='affectation'
;
SQL;

    $res = $this->exec_query($query);
    
    while($row = $res->fetch()) {
        $droite = $row['gauche'] + 1;
        $trouve = false;
        while(!$trouve) {
	        $query = <<<SQL
SELECT T1.droite, T1.gauche, T1.fichier, SUM(if (T2.code='addElement', 1, 0)) AS addElement
FROM <tokens> T1
    JOIN <tokens> T2
    ON T2.fichier=  T1.fichier AND
       T2.droite BETWEEN T1.droite AND T1.gauche
    WHERE T1.droite = $droite AND 
          T1.fichier='{$row["fichier"]}'
    GROUP BY T1.droite, T1.gauche, T1.fichier
;
SQL;

            $res2 = $this->exec_query($query);
            $row2 = $res2->fetch();
            
            $trouve = $row2['addElement'] != 0;
            $droite = $row2['gauche'] + 1;
        
        }
        
        $query = <<<SQL
SELECT sum(if (T1.code IN ('addValidator','addFilter'), 1, 0)) AS addValidator, T1.fichier 
    FROM <tokens> T1 
    WHERE fichier = '{$row['fichier']}' AND droite BETWEEN {$row['droite']} AND {$row2['gauche']}
SQL;
        $res2 = $this->exec_query($query);
        $row2 = $res2->fetch();
        
	    $query = <<<SQL
INSERT INTO <rapport> VALUES 
    (0, '{$row2['fichier']}', '{$row['code']} : {$row2['addValidator']}' , {$row['id']}, '{$this->name}', 0 );
SQL;
        $this->exec_query($query);
        }
        
        return true;
	}
}

?>