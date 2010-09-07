<?php

// read option in command line

/**
  * Read options with an expected value
  */
function get_arg_value(&$args, $option=null, $default_value=null) {
    if ($id = array_search($option, $args)) {
        if (!isset($args[$id + 1])) { 
            unset($args[$id]);
            return $default_value;
        }
        $return = $args[$id + 1];
        unset($args[$id]);
        unset($args[$id + 1]);
    } else {
        $return = $default_value;
    }
    return $return; 
}

/**
  * Read options with no value
  */
function get_arg(&$args, $option) {
    if ($id = array_search($option, $args)) {
        unset($args[$id]);
        $return = true;
    } else {
        $return = false;
    }
    return $return; 
}

function help() {
    global $INI;
    $options = $INI['help_options'];
    
    $r = 'Usage : '.$_SERVER['SCRIPT_NAME'];
    
    $list = '';
    foreach($options as $name => $option) {
        $list .= "-{$option['option']} : {$option['help']}\n";
        if ($option['compulsory']) {
            $r .= " -{$option['option']} <$name>";
        }
    }
    $r .= "\n\n$list\n";
    
    return $r;
}

if (isset($options)) {
    $INI = array('help_options' => $options);

    $args = $argv;    
    
    if (get_arg($args, '-?')) { print help(); die(); }
    
    foreach($options as $name => $option) {
        if (array_key_exists('get_arg_value', $option)) {
            $INI[$name] = get_arg_value($args, '-'.$option['option'], $option['get_arg_value']);
            if (empty($INI[$name])) {
                $INI[$name] = $option['get_arg_value'];
            }
        } else {
            $INI[$name] = get_arg($args, '-'.$option['option']);
        }

        
        if ($option['compulsory'] && empty($INI[$name])) {
            print("Option -{$option['option']} <$name> is compulsory. \n");
            print help($options);
            die();
        }
    }
    
    if (!is_null($INI['ini'])) {
        if (file_exists('ini/'.$INI['ini'])) {
            $ini = parse_ini_file(dirname(dirname(__FILE__)).'/ini/'.$INI['ini'], true);
            $ini['cornac']['ini'] = substr($INI['ini'], 0, -4); // @note minus .ini
        } elseif (file_exists(dirname(dirname(__FILE__)).'/ini/'.$INI['ini'].".ini")) {
            $ini = parse_ini_file(dirname(dirname(__FILE__)).'/ini/'.$INI['ini'].".ini", true);
            $ini['cornac']['ini'] = $INI['ini']; // @note good name
        } elseif (file_exists($INI['ini'])) {
            $ini = parse_ini_file($INI['ini'], true);
            $ini['cornac']['ini'] = substr(basname($INI['ini']), 0, -4); // @note file name minus .ini (loosing dir name?)
        } else {
            $ini = parse_ini_file(dirname(dirname(__FILE__)).'/ini/'.'cornac.ini', true);
            $ini['cornac']['ini'] = 'cornac'; // @note default value. Probably wrong.
        }
    } else {
    // @todo this is probably wrong
        $ini = array();
    }

/*    
    // @doc commandline option replace INI values
    foreach($ini as $section => $inis) {
        foreach($inis as $name => $value) {
            if (array_key_exists($name, $INI)) {
                $ini[$section][$name] = $INI[$name];
                unset($INI[$name]);
            }
        }
    }
    
    print_r($INI);die();
        */
    $INI = array_merge($INI, $ini);
    unset($ini);
}

?>