#!/usr/bin/php 
<?php
// @fer -d xdebug.profiler_enable=On

ini_set('memory_limit',234217728);

$times = array('debut' => microtime(true));
include('prepare/commun.php');
include("prepare/analyseur.php");

// @doc Reading the name of the processed application

global $FIN; 
// Collecting tokens
$FIN['debut'] = microtime(true);

include('libs/getopts.php');

// @todo : exporter les informations d'options dans une inclusion
$args = $argv;
$help = get_arg($args, '-?') ;
if ($help) { help(); }


// @doc default values, stored in a INI file
$ini = get_arg_value($args, '-I', null);
if (!is_null($ini)) {
    global $INI;
    if (file_exists('ini/'.$ini)) {
        define('INI','ini/'.$ini);
    } elseif (file_exists('ini/'.$ini.".ini")) {
        define('INI','ini/'.$ini.".ini");
    } elseif (file_exists($ini)) {
        define('INI',$ini);
    } else {
        define('INI','ini/'.'cornac.ini');
    }
    $INI = parse_ini_file(INI, true);
} else {
    define('INI',null);
    $INI = array();
}
unset($ini);
// @todo : what happens if we can't find the .INI ?
print "Directives files : ".INI."\n";

// @doc Reading constantes that are in the .INI
define('TOKENS',(bool) get_arg($args, '-t'));
define('TEST'  ,(bool) get_arg($args, '-T'));
define('STATS' ,(bool) get_arg($args, '-S', false));
define('VERBOSE', (bool) get_arg($args, '-v'));

if ($templates = get_arg_value($args, '-g', null)) {
    $templates = explode(',', $templates);

    $templates = array_unique($templates);
    
    foreach ($templates as $i => $template) {
        if (!file_exists('prepare/templates/template.'.$template.'.php')) {
            print "$id) '$template' doesn't exist. Ignoring\n";
            unset($templates[$i]);
        } else {
            print "Using template ".$template."\n";
        }
    }
    
    if (count($templates) == 0) {
        $templates = array('tree');
    }
    
    
    define('GABARIT',join(',',$templates));
} else {
    define('GABARIT','tree');
    $templates = array('tree');
}

include('prepare/templates/template.php');
foreach ($templates as $id => $template) {
    include('prepare/templates/template.'.$template.'.php');
}

define('LOG' ,(bool) get_arg($args, '-l', false));
$limite = 0 + get_arg_value($args, '-i', 0);
if ($limite) {
    print "Cycles = $limite\n";
} else {
    $limite = -1;
}

define('RECURSIVE' ,(bool) get_arg($args, '-r', false));

$dossier = get_arg_value($args, '-d', array());
if (!empty($dossier)) {
    if (substr($dossier, -1) == '/') {
        $dossier = substr($dossier, 0, -1);
    }

    if (!file_exists($dossier)) {
        print "Impossible de trouver le dossier '$dossier'\n Annulation\n";
        die();
    }

    print "Travail sur le dossier {$dossier} \n";
    
    $fichiers = glob($dossier.'/*.php');
    $fichiers = array_slice($fichiers, 1, 1);
    
    foreach($fichiers as $fichier) {
        print "./tokenizeur.php -f $fichier -g ".GABARIT. ""." -I ".INI."\n";
        print shell_exec("./tokenizeur.php  -T -i -1 -f \"".escapeshellarg($fichier)."\" -g ".GABARIT. " "." -I ".INI);
    }
    
    if (RECURSIVE) {
        $fichiers = liste_directories_recursive($dossier);

        foreach($fichiers as $fichier) {
            $code = file_get_contents($fichier);
            if (strpos($code, '<?') === false) { continue; }
            
            $commande = "./tokenizeur.php -f $fichier -g ".GABARIT." -I ".INI;
            print $commande. "\n";
            print shell_exec($commande);
        }
        print "Done\n";
        die();
    }
    print "Done\n";
    die();
} elseif ($fichier = get_arg_value($args, '-f', '')) {
    print "Working on file  '{$fichier}' \n";

    $objects = new arrayIterator(array($fichier => $fichier));
    $scriptsPHP = $objects;

} else {
    print "No files to work on\n";
    help();
}

// @section end of options

$preparations = array();
    
$extra_cols = array();
foreach($preparations as $p) {
    $cols = $p->get_cols();
    foreach($cols as $nom => $definition) {
        $extra_cols[] = "$nom $definition ";
    }
}
if (count($extra_cols) > 0) {
    $extra_cols = ', '.join(', ', $extra_cols).", ";
} else {
    $extra_cols = '';
}

$tidbits = scandir('prepare');
foreach($tidbits as $module) {
    if ($module[0] == '.') { continue; }
    if ($module == 'token.php') { continue; }
}

$FIN['fait'] = 0;
$FIN['trouves'] = 0;
foreach($scriptsPHP as $name => $object){
    $FIN['trouves']++;
    print $name."\n";
    if (!file_exists($name)) { 
        print "'$name' doesn't exist. Aborting\n";
        continue;
    }

// @doc 4177 is error_reporting for  E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR (compilations error only)
    $exec = shell_exec('php -d short_open_tag=1 -d error_reporting=4177  -l '.escapeshellarg($fichier).' '); 
    if (trim($exec) != 'No syntax errors detected in '.$name) {
        print "Script \"$name\" can't be compiled by PHP\n$exec\n";
        die();
    }
    
    global $fichier;
    $fichier = $name;

    $code = file_get_contents($name);
    
    // @doc one must leave <?php and <?xml untouched
    // @doc one must also leave <?R& (\w\W), which are binary, not PI
    // @note only take into account <?\s 
    
    if ($c = preg_match_all('/<\\?(?!php)(\w?\s)/is', $code, $r) ) { 
        if (VERBOSE) {
            print "Fixing $c opening tags\n";
        }
        $code = preg_replace('/<\\?(?!php)(\w?\s)/is', '<?php'." ".'\1', $code);
    }
    // @todo this is too simple, but it works until now (binary, beware!)
    $code = str_replace('<?=', '<?php echo ', $code);
    
    $brut = @token_get_all($code);
    if (count($brut) == 0) {
        print "No token found. Aborting\n";
        die();
    }
    $nb_tokens_initial = count($brut);
    
    if (TOKENS) {
       print_r($brut);
       $times['fin'] = microtime(true);
       mon_die();
    }
    
    $root = new Token();
    $suite = null;
    $ligne = 0;
    
    foreach($brut as $id => $b) {
        $t = new Token();

        $t->setId($id);
        if (is_array($b)) {
            $t->setToken($b[0]);
            $t->setCode($b[1]);
            $t->setLine($b[2]);
            $ligne = $b[2];
        } else {
            $t->setCode($b);
            $t->setLine($ligne);
        }
        
        if (is_null($suite)) {
            $suite = $t;
            $root = $t;
        } else {
            $suite->append($t);
            $suite = $suite->getNext();
        }
        unset($brut[$id]);
        
    }

    $t = $root;
    mon_log("\nWSC \n");
    do {
        $t = whitespace::factory($t);
        $t = commentaire::factory($t);
        
        if ($t->getId() == 0 && $t != $root) {
            mon_log("New root : ".$t."");
            $root = $t;
        }

        if (VERBOSE) {
            print "$i) ".$t->getCode()."---- \n";
            $template = getTemplate($root, $fichier, 'tree');
            $template['tree']->affiche();
            unset($template);
            print "$i) ".$t->getCode()."---- \n";
       }
    } while ($t = $t->getNext());

    $analyseur = new analyseur();

    $nb_tokens_courant = -1;
    $nb_tokens_precedent = array(-1);

    $i = 0;
    while (1) {
        $i++;
        $t = $root;
        mon_log("\nCycle : ".$i."\n");
        $nb_tokens_precedent[] = $nb_tokens_courant;
        if (count($nb_tokens_precedent) > 3) {
            array_shift($nb_tokens_precedent); 
        }
        $nb_tokens_courant = 0;
        do {
            $t = $analyseur->upgrade($t);
            if (get_class($t) == 'Token') { $nb_tokens_courant++; }
            if ($t->getId() == 0 && $t != $root) {
                mon_log("New root : ".$t."");
                $root = $t;
            }

            if (VERBOSE) {
                print "$i) ".$t->getCode()."---- \n";
                $template = getTemplate($root, $fichier, 'tree');
                $template['tree']->affiche();
                unset($template);
                print "$i) ".$t->getCode()."---- \n";
           }
        } while ($t = $t->getNext());
        
        mon_log("Remaining tokens : ".$nb_tokens_courant."");
        
        if ($nb_tokens_courant == 0) {
            break 1;
        }
        
        if ($nb_tokens_courant == $nb_tokens_precedent[0] && 
            $nb_tokens_courant == $nb_tokens_precedent[1] &&
            $nb_tokens_courant == $nb_tokens_precedent[2]
            ) { 
            print "No more update at cycle #$i \n";

            break 1;
        }
        
        if ($i == $limite) {
            break 1;
        }
    }
    $nb_cycles_final = $i;

    if (TEST) {
        if ($nb_tokens_courant == 0) {
            print "OK\n";
        } else {
            print "$nb_tokens_courant remain to be processed\n";
        }
        die();
    }

    if (VERBOSE) {
        if ($nb_tokens_courant == 0) {
            print "Some tokens were not processed\n";
        } else {
            print "$nb_tokens_courant remain to be processed\n";
        }
    }

    $token = 0;
    $loop = $root;
    $id = 0;
    while(!is_null($loop)) {
        if (get_class($loop) == "Token") {
            $token++;
        }
        $loop = $loop->getNext();
        $id++;
    }
   
    $templates = getTemplate($root, $fichier);
    foreach($templates as $template) {
        $template->affiche();
        $template->save();
    }

    if (STATS) {
        include('prepare/template.stats.php');
        $template = new template_stats($root);
        $template->affiche();
        
        print $analyseur->verifs." checks were made\n";
        $stats = array_count_values($analyseur->rates);
        asort($stats);
        print_r($stats);
    }
}

$times['fin'] = microtime(true);

$debut = $times['debut'];
unset($times['debut']);
foreach($times as $key => $valeur) {
    $times[$key] = floor(($valeur - $debut) * 1000);
}

mon_die();

function mon_die() {
    global $nb_tokens_courant, $nb_tokens_initial, $fichier, $times, $nb_cycles_final, $limite ;
    
    $message = array();
    $message['date'] = date('r');
    $message['fichier'] = $fichier;
    $message['tokens'] = $nb_tokens_initial;
    $message['reste'] = $nb_tokens_courant;
    $message['nb_cycles'] = $nb_cycles_final;
    $message['nb_cycles_autorise'] = $limite;
    $message['fin'] = $times['fin'];
    $message['memoire_max'] = memory_get_peak_usage();
    $message['memoire_finale'] = memory_get_usage();
    
    $message = join("\t", $message)."\n";
    
    $fp = fopen('analyseur.log','a');
    fwrite($fp, $message);
    fclose($fp);
    
    die();
}

function termine() {
    global $FIN;
    
    $fin = microtime(true);
    
    print "================================================\n";
    print "Duration : ".number_format(($fin - $FIN['debut']), 2)." s\n";
    print "Processed files : ".$FIN['fait']." \n";
    print "Found files : ".$FIN['trouves']." \n";
    die();
}

class PHPFilter extends FilterIterator 
{
    private $userFilter;
    public $nb;
    
    public function __construct(Iterator $iterator  )
    {
        parent::__construct($iterator);
        $this->nb = 0;
    }
    
    public function accept()
    {
    	$this->nb++;
        $fichier = $this->getInnerIterator()->current();
        $details = pathinfo($fichier);
        
        if( strpos($details['dirname'].'/', '/fckeditor/') !== false) {
            return false;
        }

        if( strpos($details['dirname'].'/', '/cssTidy/') !== false) {
            return false;
        }
        
         if( strpos($details['dirname'].'/', '/Minify/') !== false) {
            return false;
        }

         if( strpos($details['dirname'].'/', '/pear/') !== false) {
            return false;
        }

         if( strpos($details['dirname'].'/', '/jscalendar/') !== false) {
            return false;
        }

         if( strpos($details['dirname'].'/', '/jpgraph/') !== false) {
            return false;
        }

         if( strpos($details['dirname'].'/', '/fpdf/') !== false) {
            return false;
        }

         if( strpos($details['dirname'].'/', '/exif/') !== false) {
            return false;
        }
        
         if( strpos($details['dirname'].'/', '/html2pdf/') !== false) {
            return false;
        }
        
         if( strpos($details['dirname'].'/', '/fpdi/') !== false) {
            return false;
        }

         if( strpos($details['dirname'].'/', '/fonts/') !== false) {
            return false;
        }

         if( strpos($details['dirname'].'/', '/exif/') !== false) {
            return false;
        }


        if( strpos($details['dirname'], 'cligraphcrm_0.991/include') !== false) {
            return false;
        }

        if( strpos($details['dirname'], 'cligraphcrm_0.991/fonts') !== false) {
            return false;
        }

        if( strpos($details['dirname'], 'cligraphcrm_0.991/etat') !== false) {
            return false;
        }
        
        if( strpos($details['dirname'], 'cligraphcrm_0.991/themes') !== false) {
            return false;
        }

        if( isset($details['extension'] ) && ($details['extension'] == 'php' || $details['extension'] == 'inc' || $details['extension'] == 'dao' || $details['extension'] == 'lib') ) {
            return true;
        }
        return false;
    }
}

function mon_log($message) {
    global $LOG;
    
    if (!LOG) { return true; }
    
    if (!isset($LOG)) {
        $LOG =  fopen('tokenizer.log','w+');
    }
    
    if (!is_resource($LOG)) {
        die("Log file is not accessible for writing!\n");
    }
    
    fwrite($LOG, date('r')."\t$message\r");
}

function getTemplate($racine, $fichier, $gabarit = null) {
    if (is_null($gabarit)) {
        $gabarit = GABARIT;
    }
    $templates = explode(',' , $gabarit);
    
    $retour = array();
    foreach($templates as $template) {
        $classe  = "template_".$template;
        $retour[$template] = new $classe($racine, $fichier);
    }
    return $retour;
}

function help() {
    print <<<TEXT
    -d : test all .php files of the folder
    -e : also open the file in an editor
    -f : work on this file
    -g : gabarit à utiliser
    -l : activate log (in the file tokenizer.log)
    -h : This help
    -i : number of cycles. Default to 
    -I : ini file. Default to 'tokenizeur.ini'. 
    -q : quick tests.php file
    -r : mode récursif (avec -d)
    -S : display internal objects stats
    -t : display tokens produced and quit
    -T : activate test mode
    -? : this help

TEXT;
    
    die();
}

function liste_directories_recursive( $path = '.', $level = 0 ){ 
    global $INI;

    $ignore_dirs = array( 'cgi-bin', '.', '..',
                          'CVS','.svn','.git', // @todo : mercurial? other vcs's special folder : please add 
                          'adodb','fpdf','fckeditor','incutio','lightbox','nusoap','odtphp','pear','phpthumb','phputf8','scriptaculous','simpletest','smarty','spyc','tiny_mce','tinymce','Zend'); 
    if (isset($INI['tokenizeur']['ignore_dirs']) && !empty($INI['tokenizeur']['ignore_dirs'])) {
        $ignore_dirs = array_merge($ignore_dirs, explode(',',$INI['tokenizeur']['ignore_dirs']));
    } else {
        // @emptyelse
    }
    
    if (isset($INI['tokenizeur']['ignore_suffixe']) && !empty($INI['tokenizeur']['ignore_suffixe'])) {
        print preg_quote($INI['tokenizeur']['ignore_suffixe'])."\n";
        $regex_suffixe = str_replace(',','|',  preg_quote($INI['tokenizeur']['ignore_suffixe']));
    } else {
        $regex_suffixe = array('.gif','.jpg','.jpeg','.xsl','.css','.js','.png');
    }
    $regex_suffixe = '/('.join('|', $regex_suffixe).')$/';

    if (isset($INI['tokenizeur']['ignore_prefixe']) && !empty($INI['tokenizeur']['ignore_prefixe'])) {
        $regex_prefixe = str_replace(',','|',  preg_quote($INI['tokenizeur']['ignore_prefixe']));
    } else {
        $regex_prefixe = array('\\.');
    }
    $regex_prefixe = '/^('.join('|', $regex_prefixe).')/';

    $retour = array();

    $dh = opendir( $path ); 
    if (!$dh) {  print "$path\n"; return $retour; }
    while( false !== ( $file = readdir( $dh ) ) ){ 
        if( in_array( $file, $ignore_dirs ) ){ continue; }
        if( is_dir( "$path/$file" ) ){ 
            $r = liste_directories_recursive( "$path/$file", ($level+1) ); 
            $retour = array_merge($retour, $r);
        } else { 
            // @doc remove matching suffixe (aka, extensions)
            if ($regex_suffixe && preg_match($regex_suffixe, $file)) { continue; }
            // @doc remove matching prefixe (., probably)
            if ($regex_prefixe && preg_match($regex_prefixe, $file)) { continue; }
            
            // @doc The rest is accepted, until we find a PHP tag in it (see later)
            $retour[] = "$path/$file";
        } 
    } 
     
    closedir( $dh ); 
    return $retour;
} 
?>