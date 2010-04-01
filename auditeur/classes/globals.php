<?php

class globals extends modules {
    protected    $description = 'Liste des variables globales utilisées';
    protected    $description_en = 'Global variable list';

    function __construct($mid) {
        parent::__construct($mid);
        
        $this->name = __CLASS__;
        $this->functions = array();
    }
    
    public function analyse() {
// variables marquées comme globales avec global
    
    /*
    
    select T2.code, T2.fichier
    from tokens T1
    JOIN tokens T2 
        ON T1.droite + 1 = T2.droite
    WHERE T1.type='_global' and
          T2.fichier = T1.fichier
    
    */

        $module = __CLASS__;
        $requete = <<<SQL
DELETE FROM rapport WHERE module='$module'
SQL;
        $res = $this->mid->query($requete);

        $requete = <<<SQL
INSERT INTO rapport 
        SELECT 0, T2.fichier, replace(replace(T2.code,'\$"',''),"'",'') AS code, T2.id, '$module'
    FROM tokens T1
    JOIN tokens T2 
        ON T1.droite + 1 = T2.droite
    WHERE T1.type='_global' AND
          T2.fichier = T1.fichier
SQL;
        $res = $this->mid->query($requete);
        $this->functions = array();
        while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
            $this->functions[trim($ligne['code'],'$\'"')] = $ligne['nb'];
            $this->occurrences++;
        }
        
// variables globales via $GLOBALS
       $requete = <<<SQL
INSERT INTO rapport 
SELECT 0, T2.fichier, replace(replace(T2.code,'\$"',''),"'",'') AS code, T2.id, '$module'
    FROM tokens T1
    JOIN tokens T2 
        ON T1.droite + 2 = T2.droite
    WHERE T1.code = '\$GLOBALS' AND
          T1.fichier = T2.fichier;
SQL;
        $res = $this->mid->query($requete);
        include_once('classes/rendu.php');
        $rendu = new rendu($this->mid);
        
        while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
//            print $ligne["code"]."\t".$ligne["fichier"]."\n";
            $code = $rendu->rendu($ligne['droite'], $ligne['gauche'], $ligne['fichier']);
        
            $this->functions[trim($ligne['code'],'$\'"')] = 1;
            $this->occurrences++;
        }
    }
}

?>