<?php

class appelsfonctions extends modules {
	protected	$description = 'Appels d\'une fonction par une autre';
	protected	$description_en = 'Function call through the code';

	function __construct($mid) {
        parent::__construct($mid);
        
        $this->format = modules::FORMAT_DOT;
    	$this->name = __CLASS__;
    	$this->functions = array();
	}
	
	public function analyse() {
        $this->clean_rapport();
        
        $in = join("', '", modules::getPHPFunctions());
/*
        $requete = <<<SQL
INSERT INTO <rapport_dot> 
SELECT distinct T5.code, T3.code,T1.fichier, '{$this->name}'
FROM <tokens> T1
JOIN <tokens> T2
ON T1.fichier = T2.fichier AND
   T2.droite BETWEEN T1.droite AND T1.gauche AND
   T2.type = 'functioncall'
JOIN <tokens> T3
ON T1.fichier = T3.fichier AND
   T3.droite = T2.droite + 1
JOIN <tokens_tags> T4
ON T1.id = T4.token_id AND
   T4.type='name'
JOIN <tokens> T5
ON T1.fichier = T5.fichier AND
   T4.token_sub_id = T5.id
WHERE T1.type='_function'  AND
      T2.type='functioncall' AND 
      T3.code NOT IN ('$in');
SQL;
        print $this->prepare_query($requete);
        die();
        $this->exec_query($requete);
*/
    $concat1 = $this->concat("T1.class","'->'","T1.scope");
    $concat2 = $this->concat("T3.code","'->'","T4.code");
    $requete = <<<SQL
INSERT INTO <rapport_dot> 
SELECT $concat1, $concat2, T1.fichier, '{$this->name}'
  from <tokens> T1
  join <tokens_cache> T2 
    on T1.id = T2.id
  join <tokens> T3
    on T1.fichier = T3.fichier AND
       T3.droite = T1.droite + 1 AND
       T3.code != '\$this'
  join <tokens> T4
    on T1.fichier = T4.fichier AND
       T4.droite = T1.droite + 4
where 
 T1.type='method_static' ;
SQL;
        $res = $this->exec_query($requete);

        $concat1 = $this->concat("T1.class","'->'","T1.scope");
        $concat2 = $this->concat("T1.class","'->'","T4.code");
$requete = <<<SQL
INSERT INTO <rapport_dot> 
SELECT $concat1, $concat2, T1.fichier, '{$this->name}'
  from <tokens> T1
  join <tokens_cache> T2 
    on T1.id = T2.id
  join <tokens> T3
    on T1.fichier = T3.fichier AND
       T3.droite = T1.droite + 1 AND
       T3.code = '\$this'
  join <tokens> T4
    on T1.fichier = T4.fichier AND
       T4.droite = T1.droite + 4
where 
 T1.type='method' ;
SQL;
        $res = $this->exec_query($requete);

$requete = <<<SQL
SELECT T4.code AS methode, T1.class as classe
  from <tokens> T1
  join <tokens_cache> T2 
    on T1.id = T2.id
  join <tokens> T3
    on T1.fichier = T3.fichier AND
       T3.droite = T1.droite + 1 AND
       T3.code != '\$this'
  join <tokens> T4
    on T1.fichier = T4.fichier AND
       T4.droite = T1.droite + 4
where 
 T1.type='method' ;
SQL;
        $res = $this->exec_query($requete);
        
        $erreurs = 0;
        $total = 0;
        while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
$requete = <<<SQL
SELECT T1.element
  from <rapport> T1
where 
 T1.module='defmethodes' AND 
 T1.element NOT LIKE "{$ligne["classe"]}->%" AND
 T1.element LIKE "%->{$ligne["methode"]}"

 ;
SQL;
            $res2 = $this->exec_query($requete);            
            
            if ($res2->rowCount() == 0) {
                print_r($ligne);
                print $this->prepare_query($requete);
                print_r($res2->fetchall());
                $erreurs++;
            }
            $total++;
        }
//        print "$erreurs à faire\n $total trouves\n";
//            die();

    // @todo supporter les méthodes / classes
    
    /* appels internes de méthodes (classe via $this classe)
SELECT concat(T1.class,'->',T1.scope), concat(T1.class, '->', T4.code)
  from tokens T1
  join tokens_cache T2 
    on T1.id = T2.id
  join tokens T3
    on T1.fichier = T3.fichier AND
       T3.droite = T1.droite + 1 AND
       T3.code = '$this'
  join tokens T4
    on T1.fichier = T4.fichier AND
       T4.droite = T1.droite + 4
where 
 T1.type='method' ;

SELECT concat(T1.class,'::',T1.scope), concat(T3.code, '->', T4.code)
  from tokens T1
  join tokens_cache T2 
    on T1.id = T2.id
  join tokens T3
    on T1.fichier = T3.fichier AND
       T3.droite = T1.droite + 1 AND
       T3.code != '$this'
  join tokens T4
    on T1.fichier = T4.fichier AND
       T4.droite = T1.droite + 4
where 
 T1.type='method_static' ;


// cas des appels sur objets : on ne connait pas le type, on ne sait pas relayer vers quelle classe
SELECT concat(T1.class,'->',T1.scope), concat(T3.code, '->', T4.code)
  from tokens T1
  join tokens_cache T2 
    on T1.id = T2.id
  join tokens T3
    on T1.fichier = T3.fichier AND
       T3.droite = T1.droite + 1 AND
       T3.code != '$this'
  join tokens T4
    on T1.fichier = T4.fichier AND
       T4.droite = T1.droite + 4
where 
 T1.type='method' ;

    
    */
    
    }
}

?>