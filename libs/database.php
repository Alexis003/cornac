<?php

class database  {
    // @todo : must expose more functions

    private $pdo = null;
    
    function __construct($INI = null) {
        if (is_null($INI)) {
            global $INI;
        } 
        
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
    
    function setup_query($query) {
        $query = str_replace(array_keys($this->tables), array_values($this->tables), $query);
        
        return $query;
    }
    
    function query($query) {
        $this->last_query = $this->setup_query($query);
        
        $res = $this->pdo->query($this->last_query);
        
        $this->errorInfo(true);
        return $res;
    }
    
    function quote($string) {
        return $this->pdo->quote($string);
    }
    
    function errorInfo($print = false) {
        if ($print) {
            $errorInfo = $this->pdo->errorInfo();
            if (!$errorInfo[1] * 1) { return true; }
            print "<p style=\"border: 1px\"><div style=\"font-family: courier\">".$this->last_query."</div><br />";
            
            print $errorInfo[2];
            print "</p>";
            
            return true;
        } else {
            return $this->pdo->errorInfo();
        }
    }
}

?>