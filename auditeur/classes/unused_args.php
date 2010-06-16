<?php

class unused_args extends modules {
	protected	$description = 'Liste des arguments inutilisés dans une fonction/method';
	protected	$description_en = 'List of action method from controlers in ZF ';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();


	    $requete = <<<SQL
SELECT T1.id, T1.code, T1.fichier, TT.type, TT.token_sub_id  
FROM <tokens> T1
JOIN <tokens_tags> TT
ON TT.token_id = T1.id 
WHERE T1.type = '_function' AND TT.type in ('args','block','abstract');
SQL;
    $res = $this->exec_query($requete);
    
    $fonctions = array();
    while($ligne = $res->fetch()) {
        $fonctions[$ligne['id']][$ligne['type']] = $ligne['token_sub_id'];
        $fonctions[$ligne['id']]['function'] = $ligne['code'];
        $fonctions[$ligne['id']]['fichier'] = $ligne['fichier'];
    }
    
    foreach($fonctions as $id => $infos) {
        extract($infos);
        if ($args == 0) { continue; }
        if (isset($abstract)) { unset($abstract); continue; }
        
        // exterminer les abstract !
        
	    $requete = <<<SQL
 SELECT T2.code FROM <tokens> T1
 JOIN <tokens> T2
 ON T2.fichier = T1.fichier and T2.droite between T1.droite and T1.gauche AND T2.type = 'variable'
 where T1.id = $args AND T2.code NOT IN (
    SELECT T2.code FROM <tokens> T1
     JOIN <tokens> T2
     ON T2.fichier = T1.fichier AND T2.droite BETWEEN T1.droite AND T1.gauche AND T2.type = 'variable'
     WHERE T1.id = $block );
SQL;

    $res = $this->exec_query($requete);
    if ($res->rowCount() > 0) {
        $ligne = $res->fetch(PDO::FETCH_ASSOC);
        $vars = join(', ', $ligne);
        
	    $requete = <<<SQL
INSERT INTO <rapport> VALUES ( 0, '$fichier', '$function($vars)', $id, '{$this->name}' )
SQL;
        $this->exec_query($requete);
     }
        
    }
    


	}
}

?>