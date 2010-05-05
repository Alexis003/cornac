<?php

class typecalls extends modules {
    protected $code = null;

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
	    $in = $this->type;
        $this->functions = array();

        $module = __CLASS__;
        $requete = <<<SQL
DELETE FROM <rapport> WHERE module='{$this->name}'
SQL;
        $this->exec_query($requete);

        $requete = <<<SQL
INSERT INTO <rapport>
    SELECT 0, T1.fichier, T1.code AS code, T1.id, '{$this->name}'
    FROM <tokens> T1 
    WHERE T1.type = '$in'
SQL;
        if (!is_null($this->code) && is_array($this->code) && count($this->code) > 0) {
            $requete .= " AND T1.code in ('".join("', '", $this->code)."')";
        }

        $this->exec_query($requete);

//        $this->updateCache();
        $this->functions = array();
	}
}

?>
