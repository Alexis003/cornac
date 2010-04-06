<?php

$html = file_get_contents('http://localhost/~alterway/auditeur/index.php');
preg_match_all('/<a href="index.php\?module=(.*?)">/', $html, $r);

$html = preg_replace('/<a href="index.php\?module=(.*?)">/', '<a href="\1.html">', $html);
$html = preg_replace('/index.php/', 'index.html', $html);

file_put_contents('scrape/index.html', $html);


$modules = $r[1];

foreach($modules as $module) {
    $html = file_get_contents('http://localhost/~alterway/auditeur/index.php?module='.$module);

    preg_match_all('/<a href="index.php\?module='.$module.'&type=(.*?)">/', $html, $r);

    $html = preg_replace('/<a href="index.php\?module='.$module.'&type=(.*?)">/', '<a href="'.$module.'.\1.html">', $html);
    $html = preg_replace('/\.dot\.html/', '.dot.dot', $html);
    $html = preg_replace('/index.php/', 'index.html', $html);
    file_put_contents("scrape/$module.html", $html);
    
    $types = $r[1];
    
    foreach($types as $type) {
        $html = file_get_contents('http://localhost/~alterway/auditeur/index.php?module='.$module.'&type='.$type);

        $html = preg_replace('/<a href="index.php\?module='.$module.'&type=(.*?)">/', '<a href="'.$module.'.\1.html">', $html);
        $html = preg_replace('/\.dot\.html/', '.dot.dot', $html);
        $html = preg_replace('/index.php/', 'index.html', $html);

        if ($type == 'dot') { 
            $ext = 'dot';
        } else {
            $ext = 'html';
        }
        file_put_contents("scrape/$module.$type.$ext", $html);
    }
    @$i++;
    if ($i == 4) {
//        die();
    }

}

?>