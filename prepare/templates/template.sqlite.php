<?php

include_once('template.db.php');

class template_sqlite extends template_db {
    
    function __construct($root, $fichier = null) {
        parent::__construct($root, $fichier);
        
        global $INI;
        
        $this->table = $INI['template.sqlite']['table'] ?: 'tokens';
        $this->table_tags = $this->table.'_tags';

        if (isset($INI['sqlite']) && $INI['sqlite']['active'] == true) {
           $this->database = new pdo($INI['sqlite']['dsn']);
        } else {
            print "No database configuration provided (no sqlite)\n";
            die();
        }
        
        $this->database->query('DELETE FROM '.$this->table.' WHERE fichier = "'.$fichier.'"');
        $this->database->query('CREATE TABLE IF NOT EXISTS '.$this->table.' (id       INTEGER PRIMARY KEY AUTOINCREMENT, 
                                                          droite   INT UNSIGNED CONSTRAINT KEY DEFAULT "0",
                                                          gauche   INT UNSIGNED CONSTRAINT KEY DEFAULT "0",
                                                          type     CHAR(20) CONSTRAINT KEY DEFAULT "",
                                                          code     VARCHAR(255) CONSTRAINT KEY DEFAULT "",
                                                          fichier  VARCHAR(255) CONSTRAINT KEY DEFAULT "prec",
                                                          ligne    INT,
                                                          scope    VARCHAR(255),
                                                          class    VARCHAR(255)
                                                          )');

        $this->database->query('CREATE TABLE IF NOT EXISTS '.$this->table_tags.' (
  `token_id` int unsigned NOT NULL CONSTRAINT  KEY DEFAULT "0",
  `token_sub_id` int unsigned NOT NULL CONSTRAINT  KEY DEFAULT "0",
  `type` varchar(50) NOT NULL
)');

        $this->root = $root;
    }
}
?>