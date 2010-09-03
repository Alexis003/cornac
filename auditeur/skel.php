#!/usr/bin/php
<?php

if (!isset($argv[1])) {
    die("Usage : skel [new analyzer name]\nCreates a new skeleton for analyze, in classes directory. \n");
}

$analyzer = trim($argv[1]);

if (empty($analyzer)) {
    print "'$analyzer' must be non empty.\n";
    die();
}

if (preg_match_all('/[^a-zA-Z0-9_]/', $analyzer, $r)) {
    print "'$analyzer' should be a unique name, made of letters, figures and _ (Here, ".join(', ', $r[0])." were found).\n";
    die();
}

if (file_exists('classes/'.$analyzer.'.php')) {
    print "'$analyzer' already exists.\n";
    die();
}

$code = '<?'.'php ';
$code .= "

class $analyzer extends modules {
	protected	\$title = 'Titre pour '.$analyzer;
	protected	\$description = 'Ceci est l\'analyseur '.$analyzer.' par défaut. ';

	function __construct(\$mid) {
        parent::__construct(\$mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}
	
	public function analyse() {
        \$this->clean_rapport();

// @todo of course, update this useless query. :)
	    \$query = <<<SQL
INSERT INTO <rapport>
SELECT NULL, T1.fichier, T1.code, T1.id, '{\$this->name}', 0
    FROM <tokens> T1
    WHERE code IS NULL
SQL;
        \$this->exec_query(\$query);
        
        return true;
	}
}

"
.'?'.'>';

file_put_contents('classes/'.$analyzer.'.php', $code);

$auditeur = file_get_contents('./auditeur.php');
$auditeur = str_replace("// new analyzers\n", "'$analyzer',\n// new analyzers\n", $auditeur);
file_put_contents('auditeur.php', $auditeur);
shell_exec('git add class/$analyzer.php');

print "$analyzer created ()\n";
?>