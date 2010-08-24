<?php

include_once('template.db.php');

class template_mysql extends template_db {
    protected $root = null;
    protected $database = null;
    
    function __construct($root, $fichier = null) {
        parent::__construct($root, $fichier);
        
        global $INI;
        
        $this->table = $INI['cornac']['prefix'] ?: 'tokens'; 
        $this->table_tags = $this->table.'_tags';

        if (isset($INI['mysql']) && $INI['mysql']['active'] == true) {
           $this->database = new pdo($INI['mysql']['dsn'],$INI['mysql']['username'], $INI['mysql']['password']);
        } else {
            print "No database configuration provided (no mysql)\n";
            die();
        }

        $this->database->query('DELETE FROM '.$this->table.' WHERE fichier = "'.$fichier.'"');
        $this->database->query('CREATE TABLE IF NOT EXISTS '.$this->table.' (id       INT AUTO_INCREMENT, 
                                                          droite   INT UNSIGNED, 
                                                          gauche   INT UNSIGNED,
                                                          type     CHAR(20),
                                                          code     VARCHAR(10000),
                                                          fichier  VARCHAR(255) DEFAULT "prec",
                                                          ligne    INT,
                                                          scope    VARCHAR(255),
                                                          class    VARCHAR(255),
                                                          level    TINYINT UNSIGNED,
                                                          PRIMARY KEY (`id`),
                                                          UNIQUE KEY `id` (`id`),
                                                          KEY `fichier` (`fichier`),
                                                          KEY `type` (`type`),
                                                          KEY `droite` (`droite`),
                                                          KEY `gauche` (`gauche`),
                                                          KEY `code` (`code`)
                                                          ) ENGINE=MyISAM DEFAULT CHARSET=latin1');

        $this->database->query('DELETE FROM '.$this->table_tags.' WHERE fichier = "'.$fichier.'"');
        $this->database->query('CREATE TABLE IF NOT EXISTS '.$this->table_tags.' (
  `token_id` int(10) unsigned NOT NULL,
  `token_sub_id` int(10) unsigned NOT NULL,
  `type` varchar(50) NOT NULL,
  KEY `token_id` (`token_id`),
  KEY `token_sub_id` (`token_sub_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1');

        $this->database->query('delimiter //');
        $this->database->query('CREATE TRIGGER auto_tag BEFORE DELETE ON `tokens`
FOR EACH ROW
BEGIN
DELETE FROM tokens_tags WHERE token_id = OLD.id OR token_sub_id = OLD.id;
END;
//');
        $this->database->query('delimiter ;');
        
        $this->root = $root;

    }
}

?>