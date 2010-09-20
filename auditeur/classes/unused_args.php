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

class unused_args extends modules {
	protected	$title = 'Arguments inutilisés';
	protected	$description = 'Liste des arguments inutilisés dans une fonction/method';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();
        
        // @todo if block uses func_get_args and co, ignore this
        // @todo display class/method 

	    $query = <<<SQL
SELECT T1.id, T1.code, T1.fichier, TT.type, TT.token_sub_id , TC.code AS signature
FROM <tokens> T1
JOIN <tokens_tags> TT
    ON TT.token_id = T1.id 
JOIN <tokens_cache> TC
    ON T1.id = TC.id 
WHERE T1.type = '_function' AND 
      TT.type in ('args','block','abstract');
SQL;
        $res = $this->exec_query($query);
    
        $fonctions = array();
        while($ligne = $res->fetch()) {
            $fonctions[$ligne['id']][$ligne['type']] = $ligne['token_sub_id'];
            $fonctions[$ligne['id']]['function'] = $ligne['code'];
            $fonctions[$ligne['id']]['fichier'] = $ligne['fichier'];
            $fonctions[$ligne['id']]['signature'] = $ligne['signature'];
        }
        
        foreach($fonctions as $id => $infos) {
            extract($infos);
            if ($args == 0) { continue; }
    
            // @doc don't keep abstract properties
            if (isset($abstract)) { unset($abstract); continue; }
        
        	$query = <<<SQL
SELECT T2.code FROM <tokens> T1
JOIN <tokens> T2
    ON T2.fichier = T1.fichier and T2.droite between T1.droite and T1.gauche AND T2.type = 'variable'
WHERE T1.id = $args AND T2.code NOT IN (
    SELECT T2.code FROM <tokens> T1
    JOIN <tokens> T2
        ON T2.fichier = T1.fichier AND 
           T2.droite BETWEEN T1.droite AND T1.gauche AND
           T2.type = 'variable'
     WHERE T1.id = $block 
     );
SQL;
    
           $res = $this->exec_query($query);
           if ($res->rowCount() > 0) {
              $ligne = $res->fetch(PDO::FETCH_ASSOC);
              $vars = join(', ', $ligne);
        
              $query = <<<SQL
INSERT INTO <rapport> 
    VALUES ( 0, '$fichier', '$signature', $id, '{$this->name}', 0 )
SQL;
              $this->exec_query($query);
          }
       }
    }
}

?>