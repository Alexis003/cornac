<!DOCTYPE html>
<html>
<head>
  <link href="css/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="js/jquery.min.js"></script>
  <script src="js/jquery-ui.min.js"></script>
  
  <script>
  $(document).ready(function() {
    $("#tabs").tabs();
  });
  </script>
</head>
<body style="font-size:62.5%;">
  
<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011 Alter Way Solutions (France)               |
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
// @todo : use the configuration file! 
include('include/config.php');
include('../libs/html.php');

$inventory = array("PHP extensions" => array('query' => 'SELECT DISTINCT element FROM <report> WHERE module="Php_Modules" ORDER BY element',
                                         'headers' => array('Extension'),
                                         'columns' => array('element')),
               "Constants" => array('query' => 'SELECT element, COUNT(*) as NB FROM <report> WHERE module="Constants_Definitions" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Constant','Number'),
                                    'columns' => array('element','NB')),
               "Classes" => array('query' => 'SELECT T1.class, T1.file AS file, IFNULL(T2.code, "") AS abstract
FROM <tokens> T1
LEFT JOIN <tokens> T2
    ON T2.file = T1.file AND
       T2.code != T1.class AND
       (T2.left = T1.left + 1 ) AND
       T2.type = "token_traite"
WHERE T1.type="_class" AND
      T1.class!= "" 
ORDER BY T1.class',
                                  'headers' => array('Classe','abstract','File'),
                                  'columns' => array('class','file','abstract')),
               "Interfaces" => array('query' => 'SELECT element, COUNT(*) as NB FROM <report> WHERE module="Classes_Interfaces" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Interface','Number'),
                                    'columns' => array('element','NB')),
               "Methods" => array('query' => 'SELECT T1.class, T1.scope AS method, T1.file AS file, 
if (SUM(if(T2.code="private",1,0))>0, "private","") AS private,
if (SUM(if(T2.code="protected",1,0))>0, "protected","") AS protected,
if ((SUM(if(T2.code="public",1,0))>0) OR 
(SUM(if(T2.code="protected",1,0)) + SUM(if(T2.code="private",1,0)) = 0), "public","") as public,
if (SUM(if(T2.code="abstract",1,0))>0, "abstract","") as abstract,
if (SUM(if(T2.code="final",1,0))>0, "final","") as final,
if (SUM(if(T2.code="static",1,0))>0, "static","") as static
FROM <tokens> T1
LEFT JOIN <tokens> T2
    ON T2.file = T1.file AND
       T2.type = "token_traite" AND
       (T2.left = T1.left + 1 OR 
        T2.left = T1.left + 3 OR 
        T2.left = T1.left + 5
        )
WHERE T1.type="_function" AND
      T1.class!= ""
GROUP BY T1.class, T1.scope, T1.file
ORDER BY T1.class, T1.scope
',
                                    'headers' => array('Class','Method','private','protected','public','static','final','abstract','File'),
                                    'columns' => array('class','method','private','protected','public','static','final','abstract','file')),
               "Properties" => array('query' => 'SELECT T1.class, T1.code AS property, T1.file AS file, 
if (SUM(if(T2.code="public",1,0))>0, "public","") as public,
if (SUM(if(T2.code="protected",1,0))>0, "protected","") as protected,
if (SUM(if(T2.code="private",1,0))>0, "private","") as private,
if (SUM(if(T2.code="static",1,0))>0, "static","") as static
FROM <tokens> T1
LEFT JOIN <tokens> T2
    ON T2.file = T1.file AND
       T2.code != T1.class AND
       (T2.left = T1.left + 1 OR
        T2.left = T1.left + 3 OR
        T2.left = T1.left + 5) AND
       T2.type = "token_traite"
WHERE T1.type="_var" AND
      T1.class!= ""
GROUP BY T1.class, T1.code
ORDER BY T1.class
',
                                    'headers' => array('Class','Property','private','protected','public','static','File'),
                                    'columns' => array('class','property','private','protected','public','static','file')),
               "Functions" => array('query' => 'SELECT element, file FROM <report> WHERE module="Functions_Definitions" ORDER BY element',
                                    'headers' => array('Functions','Number'),
                                    'columns' => array('element','file')),
               "Parameters" => array('query' => 'SELECT element, COUNT(*) as NB FROM <report> WHERE module="Variables_Gpc" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Variable','Number'),
                                    'columns' => array('element','NB')),
               "Sessions" => array('query' => 'SELECT element, COUNT(*) as NB FROM <report> WHERE module="Variables_Session" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Variable','Number'),
                                    'columns' => array('element','NB')),
               "Variables" => array('query' => 'SELECT element, COUNT(*) as NB FROM <report> WHERE module="Variables_Names" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Variable','Number'),
                                    'columns' => array('element','NB')),
               "Files   " => array('query' => 'SELECT DISTINCT file AS element FROM <report> GROUP BY file ORDER BY file DESC',
                                    'headers' => array('file'),
                                    'columns' => array('file')),
               "Globals"  => array(  'query' => 'SELECT element, COUNT(*) as NB FROM <report> WHERE module="Php_Globals" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Variable','Number'),
                                    'columns' => array('element','NB')),
);

$i = 0;
$tabs = array();
$divs = array();
foreach($inventory as $name => $config) {
    $i++;
    $res = $DATABASE->query($config['query']);
    $rows = $res->fetchAll(PDO::FETCH_ASSOC);
    $li = array();
    
    $tabs[] = "        <li><a href=\"#fragment-$i\"><span>$name</span></a></li>\n";
    
    if (count($rows) > 0) {
        foreach($rows as $row) {
            $li[] = $row['element'];
        }
        $list = array2li($li);
    } else {
        $list = "None";
    }
        $divs[] = '<div id="fragment-'.$i.'">
<h2>'.$name.'</h2>
'.$list.'
</div>
';
}

?>
<div id="tabs">
    <ul>
<?php echo join('', $tabs); ?>
    </ul>
<?php echo join('', $divs); ?>
</div>
</body>
</html>