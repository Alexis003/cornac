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
class export_html {
    
    function __construct($comments) {
        // @todo check for incoming array !
        $this->comments = $comments;
    }
    
    function save($dir) {
        $this->save_by_file($dir);
        $this->save_by_tag($dir);
    }

    function save_by_file($dir) {
        setlocale(LC_TIME, "fr_FR");
        $date = strftime("%A %d %B %Y %T" );
    
        $css = $this->getCSS();
        
        $html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="fr">
    <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
$css
    </head>
    <body>
    <p>Generated on : $date</p>
HTML;
    
        $files = $tags = array();
        foreach($this->comments as $id => $comment) {
            $files[$comment['file']][] = $comment;
            $tags = array_merge($tags, $comment['tags']);
        }
        $tags = array_count_values($tags);
    
        $html .= "    <p>Distinct files : ".count($files)."</p>\n";
        $html .= "    <p>Distinct comments : ".count($this->comments)."</p>\n";
        $html .= "    <p>Distinct tags : ".count($tags)."</p>\n";
    
    
        $html .= "<table id=\"box-table-a\">\n";
        foreach($files as $file => $commentaires) {
            $count = count($commentaires);
            $html .= "<tr id=\"box-table-a-section\">
            <td colspan=\"2\"><a name=\"".$this->make_anchor($file)."\"><b>".htmlentities($file)."</b></td>
            <td>$count</td>
            </tr>\n";
            foreach ($commentaires as $id => $commentaire) {
                $tags = "";
    
                foreach($commentaire['tags'] as $tag) {
                    $tags .= "<a href=\"tags.html#".$this->make_anchor($tag)."\">".htmlentities($tag)."</a>, ";
                }
                $tags = substr($tags, 0, -2);
                
                $odd = $id % 2 ? 'odd' : '';
                $html .= "<tr id=\"$odd\">
        <td>$id)</td>
        <td>".wordwrap(htmlspecialchars($commentaire['text'],ENT_COMPAT, 'UTF-8'), 150, '<br />', TRUE)."</td>
        <td>$tags</td>
        </tr>\n";
            }
        }
        $html .= "</table>";
        $html .= <<<HTML
        </body>
    </html>
HTML;
    
        file_put_contents($dir.'files.html', $html);
    }
    
    
    function save_by_tag($dir) {
        setlocale(LC_TIME, "fr_FR");
        $date = strftime("%A %d %B %Y %T");
    
        $css = $this->getCSS();
        
        $html = <<<HTML
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="fr">
        <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    $css
        </head>
        <body>
        <p>Generated on : $date</p>
HTML;
    
        $files = $tags = array();
        foreach($this->comments as $id => $comment) {
            $files[] = $comment['file'];
            foreach($comment['tags'] as $tag) {
                $tags[$tag][] = $comment;
            }
        }
        
        $files = array_count_values($files);
    
        $html .= "    <p>Distinct files : ".count($files)."</p>\n";
        $html .= "    <p>Distinct comments : ".count($this->comments)."</p>\n";
        $html .= "    <p>Distinct tags : ".count($tags)."</p>\n";
        $html .= "    <p><a name=\"_summary\" />";
        
        $min = 100; $max = 0;
        foreach($tags as $tag) {
            $min = min($min, count($tag));
            $max = max($max, count($tag));
        }
        
        foreach(array_keys($tags) as $tag) {
            $html .= "<a href=\"#".$this->make_anchor($tag)."\" style=\"font-size: ".number_format((count($tags[$tag]) - $min) / ($max - $min) * 22 + 8 , 0)."px;\">$tag</a> - ";
        } 
        $html .= "</p>\n";
        
    
        $html .= "<table id=\"box-table-a\">\n";
        foreach($tags as $tag => $commentaires) {
            $count = count($commentaires);
            $html .= "<tr id=\"box-table-a-section\">
                <td colspan=\"2\"><a name=\"".$this->make_anchor($tag)."\"><b>".htmlentities($tag,ENT_COMPAT, 'UTF-8')."</b> [<a href=\"#_summary\">^</a>]</td>
                <td>$count</td>
                </tr>\n";
            foreach ($commentaires as $id => $commentaire) {
                $odd = $id % 2 ? 'odd' : '';
                $html .= "<tr id=\"$odd\">
        <td>$id) </td>
        <td>".wordwrap(htmlspecialchars($commentaire['text'],ENT_COMPAT, 'UTF-8'), 150, '<br />', TRUE)."</td>
        <td><a href=\"files.html#".$this->make_anchor($commentaire['file'])."\">".htmlentities($commentaire['file'],ENT_COMPAT, 'UTF-8')."</a></td>
        </tr>\n";
            }
        }
        $html .= "</table>";
        $html .= <<<HTML
        </body>
    </html>
HTML;
        file_put_contents($dir.'tags.html', $html);
    }

    function getCSS() {
        return <<<CSS
    <style type="text/css">
    #box-table-a
    {
        font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
        font-size: 12px;
        margin: 45px;
        width: 880px;
        text-align: left;
        border-collapse: collapse;
    }
    #box-table-a th
    {
        font-size: 13px;
        font-weight: normal;
        padding: 8px;
        background: #b9c9fe;
        border-top: 4px solid #aabcfe;
        border-bottom: 1px solid #fff;
        color: #039;
    }
    #box-table-a td
    {
        padding: 8px;
        background: #e8edff; 
        border-bottom: 1px solid #fff;
        color: #000000;
        border-top: 1px solid transparent;
        
    }
    
    #odd td
    {
        background: #f9feff; 
    #	background: #e8edff; 
        
    }
    
    #box-table-a-section td
    {
        background: #cccccc; 
        
    }
    
    #box-table-a tr:hover td
    {
        background: #d0dafd;
        color: #339;
    }
    </style>
CSS;
    }
    
    
    private function make_anchor($name) {
        $name = preg_replace('/[^a-zA-Z]/', '_', $name);
        $name = preg_replace('/_+/', '_', $name);
        $name = trim($name, '_');
        return $name;
    }
}

?>