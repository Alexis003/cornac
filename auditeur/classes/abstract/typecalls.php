<?php

class typecalls extends modules {
    protected $code = null;

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
	    if (is_array($this->type)) {
    	    $in = join("', '", $this->type);
	    } else {
    	    $in = $this->type;
	    }

        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, T1.fichier, T1.code AS code, T1.id, '{$this->name}'
    FROM <tokens> T1 
    WHERE T1.type IN ('$in')
SQL;
        if (!is_null($this->code) && is_array($this->code) && count($this->code) > 0) {
            $requete .= " AND T1.code in ('".join("', '", $this->code)."')";
        }

        $this->exec_query($requete);
	}
}

?>
