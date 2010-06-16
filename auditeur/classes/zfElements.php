<?php

class zfElements extends modules {
	protected	$description = 'Liste des elements de formulaire qui ne sont pas validés!';
	protected	$description_en = 'List of ZF elements that are not validated';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
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
	    $requete = <<<SQL
SELECT T1.droite, T1.gauche, T1.fichier , T1.id, T2.code
FROM <tokens> T1
    JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND T2.droite BETWEEN T1.droite AND T1.gauche 
    WHERE T2.code in ('$in') AND 
          T1.type='affectation'
;
SQL;

    $res = $this->exec_query($requete);
    
    while($ligne = $res->fetch()) {
        $droite = $ligne['gauche'] + 1;
        $trouve = false;
        while(!$trouve) {
	        $requete = <<<SQL
SELECT T1.droite, T1.gauche, T1.fichier, SUM(if (T2.code='addElement', 1, 0)) AS addElement
FROM <tokens> T1
    JOIN <tokens> T2
    ON T2.fichier=  T1.fichier AND
       T2.droite BETWEEN T1.droite AND T1.gauche
    WHERE T1.droite = $droite AND 
          T1.fichier='{$ligne["fichier"]}'
    GROUP BY T1.droite, T1.gauche, T1.fichier
;
SQL;

            $res2 = $this->exec_query($requete);
            $ligne2 = $res2->fetch();
            
            $trouve = $ligne2['addElement'] != 0;
            $droite = $ligne2['gauche'] + 1;
        
        }
        
        $requete = <<<SQL
SELECT sum(if (T1.code IN ('addValidator','addFilter'), 1, 0)) AS addValidator, T1.fichier 
    FROM <tokens> T1 
    WHERE fichier = '{$ligne['fichier']}' AND droite BETWEEN {$ligne['droite']} AND {$ligne2['gauche']}
SQL;
        $res2 = $this->exec_query($requete);
        $ligne2 = $res2->fetch();
        
	    $requete = <<<SQL
INSERT INTO <rapport> VALUES (0, '{$ligne2['fichier']}', '{$ligne['code']} : {$ligne2['addValidator']}' , {$ligne['id']}, '{$this->name}' );
SQL;
        $this->exec_query($requete);
        }
	}
}

?>