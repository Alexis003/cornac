<?php

class zfController extends modules {
	protected	$description = 'Liste des fonctions méthodes de contrôleur pour le ZF (*Action)';
	protected	$description_en = 'List of action method from controlers in ZF ';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $requete = <<<SQL
INSERT INTO <rapport>
SELECT 0, T1.fichier,concat(T2.class, '->',T2.code) as code, T1.id, '{$this->name}' 
FROM <tokens> T1
JOIN <tokens_tags> TT 
ON T1.id = TT.token_id AND
   TT.type='name'
JOIN <tokens> T2
ON T2.id = TT.token_sub_id AND
   T2.fichier=T1.fichier
WHERE T1.type = '_function' AND
T2.code like "%action"
ORDER BY T2.class, T2.code;
SQL;
    $this->exec_query($requete);
	}
	
	/*
	
	Extrait les fonctions qui ont du getParam mais pas de isValid.
SELECT CR.fichier, T1.class, T1.scope, 
    sum(if (CR.element in ('getParams','getParam'), 1, 0)) as getParams, 
    sum(if (CR.element in ('isValid','isErrors'), 1, 0)) as isValid
FROM caceis_rapport CR
JOIN caceis T1
ON CR.token_id = T1.id
 WHERE module='zfGetGPC'
GROUP BY class, scope
HAVING getParams > 0 and  isValid = 0
ORDER BY class, scope, element
 ;
 
 Extrait les actions des controleurs
 
 SELECT T2.class, T2.code
FROM caceis T1
JOIN caceis_tags TT 
ON T1.id = TT.token_id AND
   TT.type='name'
JOIN caceis T2
ON T2.id = TT.token_sub_id AND
   T2.fichier=T1.fichier
WHERE T1.type = '_function' AND
T2.code like "%action"
ORDER BY class, code;

	*/
}

?>