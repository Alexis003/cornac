<?php

class parentheses extends modules {
    protected    $description = 'Utilisation des parentheses';
    protected    $description_en = 'Usage of ()';

    function __construct($mid) {
        parent::__construct($mid);
        
        $this->name = __CLASS__;
    }
    
    public function analyse() {
        $requete = "DELETE FROM caches WHERE type='parentheses'";
        $res = $this->mid->query($requete);

        $requete = "
        INSERT INTO caches
        select fichier, id, 'parentheses',code from tokens where type = 'parentheses';";

        $res = $this->mid->query($requete);

        $requete = <<<SQL
select fichier, droite, gauche from tokens where type = 'parentheses';
SQL;
        $res = $this->mid->query($requete);
        $this->functions = array();
        include_once('classes/rendu.php');
        $rendu = new rendu($this->mid);
        

        while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
            $code = $rendu->rendu($ligne['droite'] + 1, $ligne['gauche'] - 1, $ligne['fichier']);

            $this->functions[$ligne['fichier']][$code] = 1;
            $this->occurrences++;
        }
        $this->fichiers_identifies = count($this->functions);
    }
}

?>