#!/usr/bin/php
<?php

include('../libs/getopts.php');
include('../libs/write_ini_file.php');

$args = $argv;

$help = get_arg($args, '-?') ;
if ($help) { help(); }

$output_file = get_arg_value($args, '-o','inventory');
if (preg_match('#[^a-zA-Z0-9_/\.\-]#', $output_file)) {
    print "Invalid output file '$output_file'. Aborting\n";
    die();
}
if (strtolower(substr($output_file, 0, -4)) != '.ods') {
    $output_file .= ".ods";
}

if (file_exists($output_file)) {
    print "$output_file already exists. Aborting\n";
    die();
}

// @todo : check $format for values
// @todo : check output for being a folder

// @question : should options be constants?
// default values, stored in a INI file
$ini = get_arg_value($args, '-I', null);
if (!is_null($ini)) {
    global $INI;
    if (file_exists('../ini/'.$ini)) {
        define('INI','../ini/'.$ini);
    } elseif (file_exists('../ini/'.$ini.".ini")) {
        define('INI','../ini/'.$ini.".ini");
    } elseif (file_exists($ini)) {
        define('INI',$ini);
    } else {
        if (!file_exists('../ini/'.'tokenizeur.ini')) {
            die("No configuration file available ($ini nor tokenizeur.ini)\n");
        }
        define('INI','../ini/'.'tokenizeur.ini');
    }
    $INI = parse_ini_file(INI, true);
} else {
    define('INI',null);
    $INI = array("reader" => array( 'fichier' => '', ));
}
unset($ini);

$INI['reader']['dependences'] = (bool) get_arg_value($args, '-d', false);
$INI['reader']['module'] = get_arg_value($args, '-a', @$INI['reader']['module']);
$INI['reader']['file']   = get_arg_value($args, '-f', ''  );
$INI['reader']['output'] = get_arg_value($args, '-o', @$INI['reader']['output']);
$INI['reader']['format'] = get_arg_value($args, '-F', @$INI['reader']['format']);

// validations
if (empty($INI['reader']['format'])) {
    print "Output format is needed (option -F) : xml or html\n";
    help();
}


// @todo : support later
//$summary = getOption($args, '-s', OPT_NO_VALUE, null);

if (isset($INI['mysql']) && $INI['mysql']['active'] == true) {
    $database = new pdo($INI['mysql']['dsn'],$INI['mysql']['username'], $INI['mysql']['password']);
//    print "MySQL\n";
} elseif (isset($INI['sqlite'])  && $INI['sqlite']['active'] == true) {
    $database = new pdo($INI['sqlite']['dsn']);
//    print "sqlite\n";
} else {
    print "No database configuration provided (no mysql, no sqlite)\n";
    die();
}

if (isset($INI['cornac']['prefix'])) {
    $prefix = $INI['cornac']['prefix'];
} else {
    $prefix = 'tokens';
}

$headers = array('Variables' => 'SELECT COUNT(DISTINCT element) FROM '.$prefix.'_rapport WHERE module="variables"',
                 'Fichiers'  => 'SELECT COUNT(DISTINCT fichier) FROM '.$prefix.'_rapport',
                 'Classes'   => 'SELECT COUNT(DISTINCT element) FROM '.$prefix.'_rapport WHERE module="classes"',
                 'Constantes'   => 'SELECT COUNT(DISTINCT element) FROM '.$prefix.'_rapport WHERE module="defconstantes"',
                 'Utilise Zend Framework'   => 'SELECT COUNT(DISTINCT element) FROM '.$prefix.'_rapport WHERE module="zfClasses"',
                 );

$stats = array();
foreach($headers as $name => $sql) {
    $res = $database->query($sql);
    $row = $res->fetch();
    $stats[] = array($name, $row[0]);
}

$tables = '';
$headers = array('Libelle','Compte');

// @doc summary table 

    $cells = '';
    foreach($headers as $header) {
        $cells .= <<<XML
					<table:table-cell table:style-name="ce1" office:value-type="string">
						<text:p>$header</text:p>
					</table:table-cell>
XML;
    }

        $rows = <<<XML
				<table:table-row table:style-name="ro1">
				    $cells
				</table:table-row>
XML;

    foreach($stats as $row) {
        $cells = '';
        foreach($row as $col) {
            $col = ods_protect($col);
            
            $cells .= <<<XML
					<table:table-cell office:value-type="string">
						<text:p>{$col}</text:p>
					</table:table-cell>
XML;
        }
        
        $rows .= <<<XML
				<table:table-row table:style-name="ro1">
				    $cells
				</table:table-row>
XML;
    }



$tables .= <<<XML
			<table:table table:name="Sommaire" table:style-name="ta1" table:print="false">
				<table:table-column table:style-name="co1" table:number-columns-repeated="2" table:default-cell-style-name="Default" />
				$rows
			</table:table>
XML;




// @attention : should also support _dot reports
$names = array("Modules PHP" => array('query' => 'SELECT DISTINCT element FROM '.$prefix.'_rapport WHERE module="php_modules" ORDER BY element',
                                  'headers' => array('Extension'),
                                  'columns' => array('element')),
               "Constantes" => array('query' => 'SELECT element, COUNT(*) as NB FROM '.$prefix.'_rapport WHERE module="defconstantes" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Constant','Number'),
                                    'columns' => array('element','NB')),
               "Classes" => array('query' => 'SELECT element, fichier FROM '.$prefix.'_rapport WHERE module="classes" ORDER BY element',
                                  'headers' => array('Classe','File'),
                                  'columns' => array('element','fichier')),
               "Interfaces" => array('query' => 'SELECT element, COUNT(*) as NB FROM '.$prefix.'_rapport WHERE module="interface" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Interface','Number'),
                                    'columns' => array('element','NB')),
               "Méthodes" => array('query' => 'SELECT DISTINCT class, scope AS method, fichier FROM '.$prefix.' WHERE class != "" AND scope != "global" ORDER BY class, scope',
                                    'headers' => array('Class','Method','File'),
                                    'columns' => array('class','method','fichier')),
               "Fonctions" => array('query' => 'SELECT element, fichier FROM '.$prefix.'_rapport WHERE module="deffunctions" ORDER BY element',
                                    'headers' => array('Functions','Number'),
                                    'columns' => array('element','fichier')),
               "Paramètres" => array('query' => 'SELECT element, COUNT(*) as NB FROM '.$prefix.'_rapport WHERE module="gpc_variables" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Variable','Number'),
                                    'columns' => array('element','NB')),
               "Sessions" => array('query' => 'SELECT element, COUNT(*) as NB FROM '.$prefix.'_rapport WHERE module="session_variables" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Variable','Number'),
                                    'columns' => array('element','NB')),
               "Variables" => array('query' => 'SELECT element, COUNT(*) as NB FROM '.$prefix.'_rapport WHERE module="variables" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Variable','Number'),
                                    'columns' => array('element','NB')),
               "Fichiers" => array('query' => 'SELECT DISTINCT fichier FROM '.$prefix.'_rapport GROUP BY fichier ORDER BY fichier DESC',
                                    'headers' => array('Fichier'),
                                    'columns' => array('fichier')),
                                  
                                  //, "variables","constantes","deffunctions","interfaces"
                                  
                                  );
foreach($names as $name => $conf) {
    extract($conf);
    
    $confs = array('query','headers','columns');
    foreach($confs as $conf) {
        if (!isset($$conf)) { 
            print "Missing '$conf' info in configuration for '$name' : aborting\n";
            continue; 
        }
    }
    
    $res = $database->query($query);
    
    $cells = '';
    foreach($headers as $header) {
        $cells .= <<<XML
					<table:table-cell table:style-name="ce1" office:value-type="string">
						<text:p>$header</text:p>
					</table:table-cell>
XML;
    }

        $rows = <<<XML
				<table:table-row table:style-name="ro1">
				    $cells
				</table:table-row>
XML;

    while($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $cells = '';
        foreach($columns as $col) {
            $row[$col] = ods_protect($row[$col]);
            
            $cells .= <<<XML
					<table:table-cell office:value-type="string">
						<text:p>{$row[$col]}</text:p>
					</table:table-cell>
XML;
        }
        
        $rows .= <<<XML
				<table:table-row table:style-name="ro1">
				    $cells
				</table:table-row>
XML;
    }

    $tables .= <<<XML
			<table:table table:name="$name" table:style-name="ta1" table:print="false">
				<table:table-column table:style-name="co1" table:number-columns-repeated="2" table:default-cell-style-name="Default" />
				$rows
			</table:table>
XML;
}

$document = file_get_contents('skel/content.xml');
$document = str_replace('<tables>',$tables,$document);

$zip = new ZipArchive();
$filename = "./$output_file";

if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
    exit("cannot open <$filename>\n");
}

$files = rglob('skel/*');

foreach($files as $file) {
    if ($file == 'content.xml') {continue; }

    $destination = "".str_replace("skel/",'',$file);
    if (substr($file, -1) == '/') {
        $zip->addEmptyDir(substr($destination, 0, -1));    
    } else {
        $zip->addFile("./$file",$destination);    
    }
}

$zip->addFromString("content.xml", $document);

if ($zip->close()) {
    print "Done\n";
} else {
    print "Failed\n";
}

function rglob($path) {
    $files = glob($path);
    
    $files2 = array();
    foreach($files as $id => $file) {
        if (is_dir($file)) {
            $files2[] = $file."/";
            $files2 = array_merge($files2, rglob($file."/*"));
        } else {
            $files2[] = $file;
        }
    }
    
    return $files2;
}


function help() {
    print <<<SHELL
Usage : ./inventaire.php

Options : 
-?     : this help
-I     : .INI file of configuration
-o     : outputfile 

SHELL;
    die();
}

function ods_protect($text) {
    $in = array("\t");
    $out = array('<text:tab />');
    
    return $text;
}

?>