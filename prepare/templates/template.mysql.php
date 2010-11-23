<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Damien Seguy <damien.seguy@gmail.com>                        |
   +----------------------------------------------------------------------+
 */

include_once('template.db.php');

class template_mysql extends template_db {
    protected $root = null;
    protected $database = null;
    
    function __construct($root, $file = null) {
        parent::__construct($root, $file);
        
        global $INI;
        
        $this->table = $INI['cornac']['prefix'] ?: 'tokens'; 
        $this->table_tags = $this->table.'_tags';

        if (isset($INI['mysql']) && $INI['mysql']['active'] == true) {
           $this->database = new pdo($INI['mysql']['dsn'],$INI['mysql']['username'], $INI['mysql']['password']);
        } else {
            print "No database configuration provided (no mysql)\n";
            die();
        }

        $this->database->query('DELETE FROM '.$this->table.' WHERE file = "'.$file.'"');
        $this->database->query('CREATE TABLE IF NOT EXISTS '.$this->table.' (
                                                          `id`       INT NOT NULL AUTO_INCREMENT, 
                                                          `left`     INT UNSIGNED, 
                                                          `right`    INT UNSIGNED,
                                                          `type`     CHAR(20),
                                                          `code`     VARCHAR(10000),
                                                          `file`     VARCHAR(255) DEFAULT "prec",
                                                          `line`     INT,
                                                          `scope`    VARCHAR(255),
                                                          `class`    VARCHAR(255),
                                                          `level`    TINYINT UNSIGNED,
                                                          PRIMARY KEY (`id`),
                                                          UNIQUE KEY `id` (`id`),
                                                          KEY `file` (`file`),
                                                          KEY `type` (`type`),
                                                          KEY `left` (`left`),
                                                          KEY `right` (`right`),
                                                          KEY `code` (`code`)
                                                          ) ENGINE=MyISAM DEFAULT CHARSET=latin1');

        $this->database->query('DELETE FROM '.$this->table_tags.' WHERE file = "'.$file.'"');
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