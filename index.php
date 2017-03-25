<?php

require "class.GitlabTeamsMessage.php";
require "config.php";

if(!defined('SCRIPTURL') && isset($_SERVER['SERVER_NAME'])) {
    define('SCRIPTURL',(isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']);
}
if(!defined('DEBUG')) {
    define('DEBUG',false);
}

if (php_sapi_name() == "cli") {
    if(isset($argv[1])) {
        $jsonfile = $argv[1];
        $json = readJsonFile($jsonfile);
    } else {
        die("I need some input: Syntax $argv[0] JSONFILE [CHANNELURL]\n");
    }
} else {
    if(isset($_GET['jsonfile']) && isset($_GET['hash']) && strlen($_GET['hash']) > 10) {
        $jsonfile = $_GET['jsonfile'];
        $json = readJsonFile($jsonfile,$_GET['hash']);
    } elseif($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_GET['url'])) {
            $url = $_GET['url'];
        } else {
            $url = preg_replace('/^.*(webhook\/.*)$/',APIBASEURL.'/$1',$_SERVER['REQUEST_URI']);
        }
        $json = file_get_contents('php://input');
    } else {
        errorAndExit("Microsoft Teams Gateway - no input");
    }
}

if (!($input = json_decode($json, true))) {
    errorAndExit("Can't parse JSON input");
}

if(!isset($jsonfile) && DEBUG && defined('JSONDIR')) {
    $input['webhook_url'] = $url;
    $jsonfile = $input['object_kind'].'.'.time().".json";
    if(!file_put_contents(JSONDIR.'/'.$jsonfile,json_encode($input))) {
        $jsonfile=null; # Can't write - can't reuse the file
    }
}

if(isset($input['webhook_url']) && !isset($url)) {
    $url = $input['webhook_url'];
    unset($input['webhook_url']);
}

$msg = new SimpleThings\GitlabTeamsMessage($json);
if(DEBUG) {
    if(defined('SCRIPTURL')) {
        $hash = md5_file(JSONDIR."/$jsonfile");
        $msg->addAction('Try again',SCRIPTURL."?jsonfile=$jsonfile&hash=$hash");
    }
    if(defined('JSONURL')) {
        $msg->addAction('View json',JSONURL.'/'.$jsonfile);
    }
}
$msg->send($url);
exit;

function readJsonFile($jsonfile,$md5 = false) {
    if(!preg_match('/^[a-z0-9\._]+$/',$jsonfile)) {
        die("Bad filename - I don't like this\n");
    }
    
    $jsonfile = JSONDIR."/$jsonfile";
    if(!is_readable($jsonfile)) {
        die("Can't open inputfile $jsonfile\n");
    }
    
    if($md5 && md5_file($jsonfile) !== $md5) {
        die("Bad hash - I don't like this\n");
    }

    return file_get_contents($jsonfile);
}

function errorAndExit($msg) {
    if (php_sapi_name() == "cli") {
        die("$msg\n");
    } else {
        header("HTTP/1.1 500 $msg");
        echo $msg;
        exit;
    }
}

