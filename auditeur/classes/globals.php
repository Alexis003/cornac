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
    

        $requete = "select T2.code, COUNT(*) as nb
    from tokens T1
    JOIN tokens T2 
        ON T1.droite + 1 = T2.droite
    WHERE T1.type='_global' and
          T2.fichier = T1.fichier
    GROUP BY T2.code";
        $res = $this->mid->query($requete);
        $this->functions = array();
        while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
            $this->functions[$ligne['code']] = $ligne['nb'];
            $this->occurrences++;
        }

// variables globales via $GLOBALS
       $requete = "select T2.code, T1.fichier, T2.gauche as gauche, T2.droite as droite, T2.fichier as fichier
    from tokens T1
    JOIN tokens T2 
        ON T1.droite + 2 = T2.droite
    where T1.code = '\$GLOBALS' and 
          T1.fichier = T2.fichier;
";
        $res = $this->mid->query($requete);
        include_once('classes/rendu.php');
        $rendu = new rendu($this->mid);
        
        while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
            $code = $rendu->rendu($ligne['droite'], $ligne['gauche'], $ligne['fichier']);
        
            $this->functions[$ligne['code']] = 1;
            $this->occurrences++;
        }
    }
}

?>