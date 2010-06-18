<?php

include_once('template.db.php');

class template_sqlite extends template_db {
    
    function __construct($root, $fichier = null) {
        parent::__construct($root, $fichier);
        
        global $INI;
        
        $this->table = $INI['template.mysql']['table'] ?: 'tokens';
        $this->table_tags = $this->table.'_tags';

        $this->mysql = new pdo("sqlite:/tmp/tokenizeur.sq3");
        
        $this->mysql->query('DELETE FROM '.$this->table.' WHERE fichier = "'.$fichier.'"');
/*        $this->mysql->query('CREATE TABLE IF NOT EXISTS '.$this->table.' (id       INT AUTO_INCREMENT, 
                                                          droite   INT UNSIGNED, 
                                                          gauche   INT UNSIGNED,
                                                          type     CHAR(20),
                                                          code     VARCHAR(255),
                                                          fichier  VARCHAR(255) DEFAULT "prec",
                                                          ligne    INT,
                                                          scope    VARCHAR(255),
                                                          class    VARCHAR(255),
                                                          PRIMARY KEY (`id`),
                                                          UNIQUE KEY `id` (`id`),
                                                          KEY `fichier` (`fichier`),
                                                          KEY `type` (`type`),
                                                          KEY `droite` (`droite`),
                                                          KEY `gauche` (`gauche`),
                                                          KEY `code` (`code`)
                                                          )');
*/
        $this->mysql->query('CREATE TABLE IF NOT EXISTS '.$this->table.' (id       INTEGER PRIMARY KEY AUTOINCREMENT, 
                                                          droite   INT UNSIGNED CONSTRAINT KEY DEFAULT "0",
                                                          gauche   INT UNSIGNED CONSTRAINT KEY DEFAULT "0",
                                                          type     CHAR(20) CONSTRAINT KEY DEFAULT "",
                                                          code     VARCHAR(255) CONSTRAINT KEY DEFAULT "",
                                                          fichier  VARCHAR(255) CONSTRAINT KEY DEFAULT "prec",
                                                          ligne    INT,
                                                          scope    VARCHAR(255),
                                                          class    VARCHAR(255)
                                                          )');
//        print_r($this->mysql->errorInfo());

        $this->mysql->query('DELETE FROM '.$this->table.'_rapport WHERE fichier = "'.$fichier.'"');
        $this->mysql->query('CREATE TABLE IF NOT EXISTS '.$this->table.'_rapport 
  (id       INTEGER PRIMARY KEY   AUTOINCREMENT  , 
  `fichier` varchar(500) NOT NULL,
  `element` varchar(500) NOT NULL,
  `token_id` int unsigned NOT NULL,
  `module` varchar(50) NOT NULL
)');
        
        $this->mysql->query('DELETE FROM '.$this->table.'_rapport_dot WHERE cluster = "'.$fichier.'"');
       $this->mysql->query('CREATE TABLE IF NOT EXISTS '.$this->table.'_rapport_dot (
  `a` varchar(255) NOT NULL,
  `b` varchar(255) NOT NULL,
  `cluster` varchar(255) NOT NULL DEFAULT \'\',
  `module` varchar(255) NOT NULL
)');

        $this->mysql->query('CREATE TABLE IF NOT EXISTS '.$this->table.'_rapport_module (
  `module` varchar(255) NOT NULL PRIMARY KEY,
  `fait` datetime NOT NULL,
  `format` varchar(255) NOT NULL
)');

        $this->mysql->query('CREATE TABLE IF NOT EXISTS '.$this->table_tags.' (
  `token_id` int unsigned NOT NULL CONSTRAINT  KEY DEFAULT "0",
  `token_sub_id` int unsigned NOT NULL CONSTRAINT  KEY DEFAULT "0",
  `type` varchar(50) NOT NULL
)');

/*
        $this->mysql->query('delimiter //');
        $this->mysql->query('CREATE TRIGGER auto_tag BEFORE DELETE ON `tokens`
FOR EACH ROW
BEGIN
DELETE FROM tokens_tags WHERE token_id = OLD.id OR token_sub_id = OLD.id;
END;
//');
        $this->mysql->query('delimiter ;');
        */
        $this->root = $root;

    }

}

?>