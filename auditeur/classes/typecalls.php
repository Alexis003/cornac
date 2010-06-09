<?php

class typecalls extends modules {
    protected $code = null;

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
	    if (is_array($this->in)) {
    	    $in = join("', '", $this->in);
	    } else {
    	    $in = $this->in;
	    }

        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport>
    SELECT 0, T1.fichier, T1.code AS code, T1.id, '{$this->name}'
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
