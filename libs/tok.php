<?php

function mon_die() {
    global $nb_tokens_courant, $nb_tokens_initial, $file, $times, $nb_cycles_final, $limite ;
    
    $message = array();
    $message['date'] = date('r');
    $message['file'] = $file;
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
        $file = $this->getInnerIterator()->current();
        $details = pathinfo($file);
        
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

function getTemplate($racine, $file, $gabarit = null) {
    if (is_null($gabarit)) {
        global $INI;
        $gabarit = $INI['templates'];
    }
    $templates = explode(',' , $gabarit);
    
    $retour = array();
    foreach($templates as $template) {
        $class = "template_".$template;
        if (!class_exists($class)) {
            include('prepare/templates/template.'.$template.'.php');
        }
        $retour[$template] = new $class($racine, $file);
    }
    return $retour;
}

function liste_directories_recursive( $path = '.', $level = 0 ){ 
    global $INI;

    $ignore_dirs = array( 'cgi-bin', '.', '..',
                          'CVS','.svn','.git','.hg', // @todo : mercurial? other vcs's special folder : please add 
                          'adodb','fpdf','fckeditor','incutio','lightbox','nusoap','odtphp','pear','phpthumb','phputf8','scriptaculous','simpletest','smarty','spyc','tiny_mce','tinymce','Zend'); 
    if (isset($INI['tokenizeur']['ignore_dirs']) && !empty($INI['tokenizeur']['ignore_dirs'])) {
        $ignore_dirs = array_merge($ignore_dirs, explode(',',$INI['tokenizeur']['ignore_dirs']));
    } else {
        // @emptyelse
    }
    
    if (isset($INI['tokenizeur']['ignore_suffixe']) && !empty($INI['tokenizeur']['ignore_suffixe'])) {
        print preg_quote($INI['tokenizeur']['ignore_suffixe'])."\n";
        $regex_suffixe = str_replace(',','|',  preg_quote($INI['tokenizeur']['ignore_suffixe']));
        $regex_suffixe = '/('.$regex_suffixe.')$/';
    } else {
        $regex_suffixe = array('.gif','.jpg','.jpeg','.xsl','.css','.js','.png');
        $regex_suffixe = '/('.join('|', $regex_suffixe).')$/';
    }

    if (isset($INI['tokenizeur']['ignore_prefixe']) && !empty($INI['tokenizeur']['ignore_prefixe'])) {
        $regex_prefixe = str_replace(',','|',  preg_quote($INI['tokenizeur']['ignore_prefixe']));
        $regex_prefixe = '/('.$regex_prefixe.')$/';
    } else {
        $regex_prefixe = array('\\.');
        $regex_prefixe = '/^('.join('|', $regex_prefixe).')/';
    }

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