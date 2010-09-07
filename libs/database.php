<?php

class database  {
    private $pdo = null;
    function __construct() {
        global $INI;
        if (isset($INI['mysql']) && $INI['mysql']['active'] == true) {
            $this->pdo = new pdo($INI['mysql']['dsn'],$INI['mysql']['username'], $INI['mysql']['password']);
        } elseif (isset($INI['sqlite'])  && $INI['sqlite']['active'] == true) {
            $this->pdo = new pdo($INI['sqlite']['dsn']);
        } else {
            print "No database configuration provided (no mysql, no sqlite)\n";
            die();
        }
        
        if (empty($INI['cornac']['prefix'])) {
            $this->prefix = 'tokens';
        } else {
            $this->prefix = $INI['cornac']['prefix'];
        }
        
        $this->tables = array('<rapport>' => $this->prefix.'_rapport',
                              '<tokens>' => $this->prefix.'',
                              '<cache>' => $this->prefix.'_cache',
                              '<tokens_cache>' => $this->prefix.'_cache',
                              '<tags>' => $this->prefix.'_tags',
                              '<tokens_tags>' => $this->prefix.'_tags',
                              '<rapport_module>' => $this->prefix.'_rapport_module',
                              '<rapport_dot>' => $this->prefix.'_rapport_dot',
                              '<tasks>' => $this->prefix.'_tasks',
                            );
    }
    
    function query($query) {
        $query = str_replace(array_keys($this->tables), array_values($this->tables), $query);
        
        $res = $this->pdo->query($query);
        return $res;
    }
    
    function quote($string) {
        return $this->pdo->quote($string);
    }
}

?>