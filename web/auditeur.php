<!DOCTYPE html>
<html>
<head>
  <link href="css/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="js/jquery.min.js"></script>
  <script src="js/jquery-ui.min.js"></script>
  
  <script>
$(function(){
    $('#nav>li>ul').hide();
    $('#nav>li').click(function(){
        // create a reference to the active element (this)
        // so we don't have to keep creating a jQuery object
        $heading = $(this);
        // check to see if any sub menus are open
        if ($heading.siblings().find('ul:visible').size()!=0) {
            // close open sub menus
            $heading.siblings().find('ul:visible').slideUp(500, function(){
                // open current menu if it's closed
                $heading.find('ul:hidden').slideDown(500);
            });
        }
        else {
            // open current menu if it's closed
            $heading.find('ul:hidden').slideDown(500);
        }
    })
    $('#nav>li>ul>li').click(function(){
        alert('ok');
    });
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

$res = $DATABASE->query("SELECT DISTINCT module, file FROM <report>");
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
$lis = array();
if (count($rows) > 0) {
    foreach($rows as $row) {
        $lis[$row['module']][] = $row['file'];
    }
    
    foreach($lis as $id => $li) {
        $lis[$id] = "$id ".array2li($li);
    }
    $list = array2li($lis);
    $c = 1;
    $list = '<ul id="nav">'.substr($list, 4);
} else {
    $list = "None";
}

?>
<?php echo $list ?>
</body>
</html>