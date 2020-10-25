<?php
/*
Yes, most of the formatting used in this file is HORRIBLY BAD STYLE. However,
most of the action happens outside of this file, and I really wanted the code
to look as small as what it does. Basically. Oh, I just suck. :)
*/

// do not change this line, you fool. In fact, don't change anything! Ever!
define('WAKKA_VERSION', '0.1.2');
include '../../mainfile.php';
require_once 'class/class.wakka.php';
// workaround for the amazingly annoying magic quotes.
$wakkapage['PageIndex'] = _MI_PAGEINDEX;
$wakkapage['RecentChanges'] = _MI_RECENTCHANGES;
$wakkapage['RecentlyCommented'] = _MI_RECENTLYCOMMENTED;
$wakkapage['HomePage'] = _MI_HOMEPAGE;
$wakkapage['MyChanges'] = _MI_MYCHANGES;
$wakkapage['MyPages'] = _MI_MYPAGES;
$wakkapage['OrphanedPages'] = _MI_ORPHANEDPAGES;
$wakkapage['TextSearch'] = _MI_TEXTSEARCH;
$wakkapage['WantedPages'] = _MI_WANTEDPAGES;
// default configuration values
$wakkaConfig = [
    'mysql_host' => XOOPS_DB_HOST,
'mysql_database' => XOOPS_DB_NAME,
'mysql_user' => XOOPS_DB_USER,
'table_prefix' => XOOPS_DB_PREFIX . '_wiki',
'root_page' => 'HomePage',
'wakka_name' => _MI_NAME,
'base_url' => XOOPS_URL . '/modules/wakka/',
'rewrite_mode' => '0',
'action_path' => 'actions',
'handler_path' => 'handlers',
'header_action' => 'header',
'footer_action' => 'footer',
'navigation_links' => "<a href='"
                              . XOOPS_URL
                              . "/modules/wakka/PageIndex'>"
                              . _MI_PAGEINDEX
                              . "</a> :: <a href='"
                              . XOOPS_URL
                              . "/modules/wakka/RecentChanges'>"
                              . _MI_RECENTCHANGES
                              . "</a> :: <a href='"
                              . XOOPS_URL
                              . "/modules/wakka/RecentlyCommented'>"
                              . _MI_RECENTLYCOMMENTED
                              . "</a> :: <a href='"
                              . XOOPS_URL
                              . "/modules/wakka/WantedPages'>"
                              . _MI_WANTEDPAGES
                              . '</a>',
'referrers_purge_time' => '1',
'pages_purge_time' => '0',
'hide_comments' => '0',
'default_write_acl' => '*',
'default_read_acl' => '*',
'default_comment_acl' => '*',
'mysql_password' => '',
'meta_keywords' => '',
'meta_description' => '',
'wakka_version' => '0.1.2',
'page_array' => ['PageIndex', 'RecentChanges', 'RecentlyCommented', 'HomePage', 'MyChanges', 'MyPages', 'OrphanedPages', 'TextSearch', 'WantedPages'],
'page_name' => $wakkapage,
];
/*
// load config
if (!$configfile = GetEnv("WAKKA_CONFIG")) $configfile = "wakka.config.php";
if (file_exists($configfile)) include $configfile;
$wakkaConfigLocation = $configfile;
$wakkaConfig = array_merge($wakkaDefaultConfig, $wakkaConfig);*/
// check for locking
if (file_exists('locked')) {
    // read password from lockfile

    $lines = file('locked');

    $lockpw = trim($lines[0]);

    // is authentification given?

    if (isset($_SERVER['PHP_AUTH_USER'])) {
        if (!(('admin' == $_SERVER['PHP_AUTH_USER']) && ($_SERVER['PHP_AUTH_PW'] == $lockpw))) {
            $ask = 1;
        }
    } else {
        $ask = 1;
    }

    if ($ask) {
        header('WWW-Authenticate: Basic realm="' . $wakkaConfig['wakka_name'] . ' Install/Upgrade Interface"');

        header('HTTP/1.0 401 Unauthorized');

        print('This site is currently being upgraded. Please try again later.');

        exit;
    }
}
/*
// compare versions, start installer if necessary
if ($wakkaConfig["wakka_version"] != WAKKA_VERSION)
{
// start installer
if (!$installAction = trim($_REQUEST["installAction"])) $installAction = "default";
include "setup/header.php";
if (file_exists("setup/".$installAction.".php")) include "setup/".$installAction.".php"); else print("<em>Invalid action</em>";
include "setup/footer.php";
exit;
}
*/
// start session
//session_start();
$xoopsConfig['isadmin'] = $xoopsUser->isAdmin();
$xoopsConfig['uname'] = $xoopsUser->uname();
$xoopsConfig['grouplist'] = $allgroups;
$xoopsConfig['groups'] = &$xoopsUser->getGroups();
$xoopsConfig['css'] = $xoopsModuleConfig['css'];
// fetch wakka location
$wakka = $_REQUEST['wakka'];
// remove leading slash
$wakka = preg_replace("/^\//", '', $wakka);
// split into page/method
if (preg_match('#^(.+?)/(.*)$#', $wakka, $matches)) {
    [, $page, $method] = $matches;
} elseif (preg_match('#^(.*)$#', $wakka, $matches)) {
    [, $page] = $matches;
}
// create wakka object
$wakka = new Wakka($wakkaConfig, $xoopsConfig);
// go!
$wakka->Run($page, $method);
