<?php

class sommaire {
	function __construct() {

	}

    function add($module) {
        $this->modules[] = $module;
    }

    function sauve() {
        $html = '';
        $fichiers = scandir('export');
        sort($fichiers);
        setlocale(LC_TIME, "fr_FR");
        $date = strftime("%A %d %B %Y %H:%M:%S ");
        
        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                              "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <head>
         <title>Audit de code PHP</title>
         <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        </head>
        <body>
      <!--  <p><a href="#">Code source à télécharger</a></p> -->
        <p><a href="liste.txt">Liste des fichiers</a></p>
        
        <table>';
        $html .= "<tr>
            <td>Production</td>
            <td>$date</td>
        </tr>
        </table>";
        
        $html .= "
        <table>
        <tr>
            <td>Nom</td>
            <td>Nombre</td>
            <td>Description</td>
        </tr>
        ";
        
        
        foreach($this->modules as $module) {
            
            $f = $module->getfilename();
            if ($f[0] == '.') { continue; }
            if (in_array($f[0], array('index.html'))) { continue; }
        
            $n = $module->getnombre();
            $d = $module->getdescription();
        
            $html .= "<tr>
            <td><a href=\"$f\">$f</a></td>
            <td>$n</td>
            <td>$d</td>
        </tr>";
        }
/*
    foreach($modules_evites as $module) {
            $f = $module->getfilename();
            $n = $module->getnombre();
            $d = $module->getdescription();
        
            $html .= "<tr>
            <td><a href=\"$f\">$f</a></td>
            <td>$n</td>
            <td>$d</td>
        </tr>";
        }
        */
        
        $html .= '</table></body></html>';
        file_put_contents('export/index.html', $html);
        
    }
	
/*	
	function getdescription() {
	    if (isset($this->description_en)) {
    		return $this->description_en ;
	    } elseif (isset($this->description)) {
    		return $this->description ;
	    } else {
    		return "Description : ".__CLASS__ ;    
	    }
	}

	function getnombre() {
		return $this->occurrences;
	} 
	
	function init_file() {
		setlocale(LC_TIME, "fr_FR");
		$date = strftime("%A %d %B %Y %H:%M:%S ");
		
		$this->export = "<html><body>
		<table>
			<tr><td><a href=\"index.html\">Index</td><td>&nbsp;</td></tr>
			<tr><td>Production</td><td>$date</td></tr>
			<tr><td>Nombre de fichiers</td><td>{$this->total_de_fichiers}</td></tr>
			<tr><td>Nombre de fichiers identifi&eacute;s</td><td>{$this->fichiers_identifies}</td></tr>
			<tr><td>Nombre d'occurrences</td><td>{$this->occurrences}</td></tr>
		</table>
		<p>&nbsp;</p>
		";
	}

	function finish_file() {
		$this->export .= "
		<table>
			<tr><td><a href=\"index.html\">Index</td><td>&nbsp;</td></tr>
		</table>
		<p>&nbsp;</p>
</body></html>";
	}
	
	function save_file($name) {
		file_put_contents('export/'.$name.'.html', $this->export);
	}


	function sauve() {
		if ($this->name == __CLASS__) { 
			print "Une classe qui n'a pas donné son nom\n";
			return false;
		}
		$this->init_file();
		$this->export .= $this->array2li($this->functions);
		$this->finish_file();
		$this->save_file($this->name);

		if ($this->inverse) {
			$this->init_file();
			$this->export = $this->array2li($this->array_invert($this->functions));
			$this->save_file($this->name.".inverse");		
		}
	}

function array2li($array) {
	$retour = '';
	if (count($array) == 0) { 
		$retour .= "Aucune valeur trouvee";
	} else {
		$retour .= "<ul>";
		foreach($array as $name => $fonctions) {
		    if (count($fonctions) == 0) { continue; }
			$name = str_replace('/Users/macbook/Desktop/audit/','',$name);
			$retour .= "<li>$name<ul>";
			foreach($fonctions as $nom => $nombre) {
				$retour .= "<li>".highlight_string($nom, true)." : $nombre</li>";
			}
			$retour .= "</ul>";
		}
	}
	
	return $retour;
}

function array_invert($array) {
	$retour = array();	
	
	foreach($array as $key => $value) {
		foreach($value as $k => $v) {
			$retour[$k][] = $key;
		}
	}
	
	return $retour;
}
*/
}


?>