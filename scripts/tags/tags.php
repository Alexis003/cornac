#!/usr/bin/env php
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

// @todo merge comments nexting each other

include('../../libs/getopts.php');

$options = array('help' => array('help' => 'display this help',
                                 'option' => '?',
                                 'compulsory' => false),
                 'ini' => array('help' => 'configuration set or file',
                                 'get_arg_value' => null,
                                 'option' => 'I',
                                 'compulsory' => false),
                 'dir' => array('help' => 'comma separated list of target dirs',
                                 'get_arg_value' => '.',
                                 'option' => 'D',
                                 'compulsory' => false),
                 'show_files' => array('help' => 'Display processed files',
                                 'get_arg_value' => 'tree',
                                 'option' => 'f',
                                 'compulsory' => false),
                 'output' => array('help' => 'Output file (leave empty for stdout)',
                                   'get_arg_value' => '',
                                   'option' => 'o',
                                   'compulsory' => false),
                 'format' => array('help' => 'Format (default print_r; use all for all, comma separated list)',
                                   'get_arg_value' => 'print_r',
                                   'option' => 'F',
                                   'compulsory' => false),
                 'limit' => array('help' => 'Limit number of processed files',
                                   'get_arg_value' => '1000000',
                                   'option' => 'i',
                                   'compulsory' => false),
                 );
include('../../libs/getopts.php');

$formats = array('html','csv','php','json','xml','print_r');

$INI['format'] = explode(',', $INI['format']);
if (in_array('all', $INI['format'])) {
    $INI['format'] = array('all');
} else if (!array_intersect($INI['format'], $formats)) { 
    $INI['format'] = array('print_r'); 
} else {
    $INI['format'] = array_intersect($INI['format'], $formats);
}

$dirs = explode(',', $INI['dir']);
foreach($dirs as $id => $dir) {
    if (substr($dir, -1) == '/') {
        $dirs[$id] = substr($dir, 0, -1);
    }
    if (!file_exists($dir)) { 
        print "'$dir' doesn't exist : ignoring\n"; 
        unset($dirs[$id]);
    } else {
        // @empty_else
    }
}

$OPTIONS = parse_ini_file("tags.ini");

$liste = array();
foreach($dirs as $dir) {
    $liste_one_dir = liste_directories_recursive($dir);
    $liste = array_merge($liste_one_dir, $liste);
}

$liste = array_slice($liste, 0, $INI['limit']);

// @question : qoui?
# @autre : non?

$comments = array();
foreach($liste as $file) {
    $php = file_get_contents($file);
    $tokens = token_get_all($php);
    $comments_this_file = 0;
    foreach($tokens as $token) {
        if (is_array($token) && $token[0] == T_COMMENT) {
            $comment = remove_delimiter($token[1]);

            // comment used as presentation delimiter (------, ////////, ========)
            if (preg_match_all('/^\+?(.)\1+$/is', $comment, $r)) {
                continue;
            }
            
            // Copyright : this is probably a FLOSS disclaimer. Forget it.
            if (preg_match_all('/Copyright/is', $comment, $r)) {
                continue;
            }
            $comments_this_file++;

            if (preg_match_all('/(@[a-zA-Z0-9_\-]+)/is', $comment, $r)) {
                $token['tags'] = $r[1];
                if (in_array('@_', $token['tags'])) {
                // comments are ignored
                    continue;
                }
            } else {
                $token['tags'] = array('@no_tag');
            }

            // @note : identifying code in comments
            // @todo make this code better : it is not easy to spot PHP code in comments, token_get_all is not sufficent
            if (strpos($comment, ';')) {
                $code_php = '<?php '.$comment.' ?>';
                $t = token_get_all($code_php);
                if (count($t) != 0) {
                    $token['tags'][] = '@php_code';
                }
            }

            // @note : identifying bad words in comments
            $regex = join('|', $OPTIONS['smell_words']);
            if (preg_match('/\b('.$regex.')\b/is', $comment, $r)) {
               $token['tags'][] = '@smell_words';
            }

            // @doc clean comment
            // @note remove all tokens from the comment
            $token['text'] = preg_replace('/(@[a-zA-Z0-9_\-]+)/is','', $comment);
            // @note remove white space and : from comment
            $token['text'] = trim($token['text'], ' :');
            $token['file'] = $file;
            unset($token[0]);
            $token['row'] = $token[2];
            unset($token[2]);
            $token['raw'] = $comment;
            unset($token[1]);

            $comments[] = $token;
        }
    }

    if ($comments_this_file == 0) {
        $comments[] = array('file' => $file,
                            'tags' => array('@no_comment_in_file'),
                            'text' => '',);
    }
}

chdir(dirname(__FILE__));

if (!in_array('all',$INI['format'])) {
    $displays = $INI['format'];
} else {
    $displays = $formats;
}

foreach($displays as $format) {
    print "rendering $format\n";
    $class= 'export_'.$format; 
    include($class.'.php');
    $format = new $class($comments);
    $format->save($INI['output']);
}

die();

function remove_delimiter($comment) {
    $comment = trim($comment);

    if ($comment[0] == '#') {
        $comment = substr($comment, 1);
    } elseif (substr($comment, 0, 2) == '//') {
        $comment = substr($comment, 2);
    } elseif (substr($comment, 0, 2) == '/*') {
        $comment = substr($comment, 2, -2);
    } else {
        print "Unknown comment : $comment\n";
    }
    $comment = trim($comment);
    return $comment;
}

$dirs = array_map('dirname', $liste);
$dirs = array_count_values($dirs);
if (SHOW_DIRS) { display($dirs); }

$files = array_map('basename', $liste);
$files = array_count_values($files);
if (SHOW_FILES) { display($files); }

$exts = array_map('cb_exts', $liste);
$exts = array_count_values($exts);
if (SHOW_EXTS) { display($exts); }

function liste_directories_recursive( $path = '.', $level = 0 ){
    global $OPTIONS, $INI;
    $ignore_dirs = array_merge(array( 'cgi-bin', '.', '..' ), $OPTIONS['ignore_dirs']);

    $dh = opendir( $path );
    if (!is_resource($dh)) { return array(); }
    $retour = array();
    while( false !== ( $file = readdir( $dh ) ) ) {
        if( $file[0] == '.'                ){ continue; }

        if( is_dir( "$path/$file" ) ){
            if( in_array( $file, $ignore_dirs )){ continue; }
            $r = Liste_directories_recursive( "$path/$file", ($level+1) );
            $retour = array_merge($retour, $r);
        } else {
            $details = pathinfo($file);
            if (!isset($details['extension'])){
                $details['extension'] = '';
            }
            if (in_array($details['extension'], $OPTIONS['ignore_ext'])) { continue; }

            $retour[] = "$path/$file";
        }
        if ($INI['limit'] > 0 && count($retour) >= $INI['limit']) {
            return $retour;
        }
    }

    closedir( $dh );
    return $retour;
}

function cb_exts($filename) {
    $filename = basename($filename);
    $pos = strrpos($filename, '.');
    return substr($filename, $pos);
}

?>