<?php
// $Id: wacko.php,v 1.5 2004/07/31 10:26:12 kukutz Exp $
// constants
define('TRAN_DONTCHANGE', '0');
define('TRAN_LOWERCASE', '1');
define('TRAN_LOAD', '0');
define('TRAN_DONTLOAD', '1');
define('LOAD_NOCACHE', '0');
define('LOAD_CACHE', '1');
define('LOAD_ALL', '0');
define('LOAD_META', '1');
define('BM_AUTO', '0');
define('BM_USER', '1');
define('BM_DEFAULT', '2');

class Wacko
{
    public $dblink;

    public $page;

    public $tag;

    public $queryLog = [];

    public $interWiki = [];

    public $VERSION;

    public $WVERSION; //Wacko version

    public $context = [''];

    public $current_context = 0;

    public $pages_meta = 'id, tag, time, owner, user, latest, handler, comment_on, super_comment_on, supertag, lang, keywords, description';

    public $first_inclusion = []; // for backlinks

    // if you change this two symbols, settings for all users will be lost.

    public $optionSplitter = "\n";

    public $valueSplitter = '=';

    public $format_safe = true; //for htmlspecialchars() in PreLink
    public $unicode_entities = []; //common unicode array
    public $timer;

    public $toc_context = [];

    public $search_engines = ['bot', 'rambler', 'yandex', 'crawl', 'search', 'archiver', 'slurp', 'aport', 'crawler', 'google', 'inktomi', 'spider'];

    // constructor

    public function __construct($config)
    {
        $this->timer = $this->GetMicroTime();

        $this->config = $config;

        $this->dblink = connect($this->config['mysql_host'], $this->config['mysql_user'], $this->config['mysql_password'], $this->config['mysql_database']);

        $this->VERSION = WAKKA_VERSION;

        $this->WVERSION = WACKO_VERSION;
    }

    // DATABASE

    public function Query($query, $debug = 0)
    {
        if ($debug) {
            echo "((QUERY: $query))";
        }

        if ($this->GetConfigValue('debug') >= 2) {
            $start = $this->GetMicroTime();
        }

        $result = query($query, $this->dblink);

        if ($this->GetConfigValue('debug') >= 2) {
            $time = $this->GetMicroTime() - $start;

            $this->queryLog[] = [
                'query' => $query,
'time' => $time,
            ];
        }

        return $result;
    }

    public function LoadSingle($query)
    {
        if ($data = $this->LoadAll($query)) {
            return $data[0];
        }
    }

    public function LoadAll($query)
    {
        if ($r = $this->Query($query)) {
            while (false !== ($row = fetch_assoc($r))) {
                $data[] = $row;
            }

            free_result($r);
        }

        return $data;
    }

    // MISC

    public function GetMicroTime()
    {
        [$usec, $sec] = explode(' ', microtime());

        return ((float)$usec + (float)$sec);
    }

    public function IncludeBuffered($filename, $notfoundText = '', $vars = '', $path = '')
    {
        if ($path) {
            $dirs = explode(':', $path);
        } else {
            $dirs = [''];
        }

        foreach ($dirs as $dir) {
            if ($dir) {
                $dir .= '/';
            }

            $fullfilename = $dir . $filename;

            if (@file_exists($fullfilename)) {
                if (is_array($vars)) {
                    extract($vars, EXTR_SKIP);
                }

                ob_start();

                include $fullfilename;

                $output = ob_get_contents();

                ob_end_clean();

                return $output;
            }
        }

        if ($notfoundText) {
            return $notfoundText;
        }

        return false;
    }

    // VARIABLES

    public function GetPageTag()
    {
        return $this->tag;
    }

    public function GetPageSuperTag()
    {
        return $this->supertag;
    }

    public function GetPageTime()
    {
        return $this->page['time'];
    }

    public function GetMethod()
    {
        return $this->method;
    }

    public function GetConfigValue($name)
    {
        return $this->config[$name];
    }

    public function SetResource($lang)
    {
        $this->resource = &$this->resources[$lang];
    }

    public function SetLanguage($lang)
    {
        // echo "<b>SetLanguage:</b> ".$lang."<br>";

        $this->LoadResource($lang);

        $this->language = &$this->languages[$lang];

        setlocale(LC_CTYPE, $this->language['locale']);

        $this->language['locale'] = setlocale(LC_CTYPE, 0);

        $this->language['UPPER'] = '[' . $this->language['UPPER_P'] . ']';

        $this->language['UPPERNUM'] = '[0-9' . $this->language['UPPER_P'] . ']';

        $this->language['LOWER'] = '[' . $this->language['LOWER_P'] . ']';

        $this->language['ALPHA'] = '[' . $this->language['ALPHA_P'] . ']';

        $this->language['ALPHANUM'] = '[0-9' . $this->language['ALPHA_P'] . ']';

        $this->language['ALPHANUM_P'] = '0-9' . $this->language['ALPHA_P'];
    }

    public function LoadResource($lang)
    {
        if (!$this->resources[$lang]) {
            $resourcefile = 'lang/wakka.' . $lang . '.php';

            if (@file_exists($resourcefile)) {
                include $resourcefile;
            }

            // wakka.all

            $resourcefile = 'lang/wakka.all.php';

            if (!$this->resources['all']) {
                if (@file_exists($resourcefile)) {
                    include $resourcefile;
                }

                $this->resources['all'] = &$wackoAllResource;
            }

            $wackoResource = array_merge($wakkaResource, $this->resources['all']);

            // theme

            $resourcefile = 'themes/' . $this->config['theme'] . '/lang/wakka.' . $lang . '.php';

            if (@file_exists($resourcefile)) {
                include $resourcefile;
            }

            $wackoResource = array_merge($wackoResource, $themeResource);

            // wakka.all theme

            $resourcefile = 'themes/' . $this->config['theme'] . '/lang/wakka.all.php';

            if (@file_exists($resourcefile)) {
                include $resourcefile;
            }

            $wackoResource = array_merge($wackoResource, $themeResource);

            $this->resources[$lang] = $wackoResource;

            $this->LoadLang($lang);
        }
    }

    public function LoadLang($lang)
    {
        if (!$this->languages[$lang]) {
            $resourcefile = 'lang/lang.' . $lang . '.php';

            if (@file_exists($resourcefile)) {
                include $resourcefile;
            }

            $this->languages[$lang] = $wackoLanguage;

            $ue = @array_flip($wackoLanguage['unicode_entities']);

            $this->unicode_entities = array_merge($this->unicode_entities, $ue);

            unset($this->unicode_entities[0]);
        }
    }

    public function LoadAllLanguages()
    {
        if (!$this->GetConfigValue('multilanguage')) {
            return;
        }

        $langs = $this->AvailableLanguages();

        foreach ($langs as $lang) {
            $this->LoadLang($lang);
        }
    }

    public function AvailableLanguages()
    {
        $handle = opendir('lang');

        while (false !== ($file = readdir($handle))) {
            if ('.' != $file && '..' != $file && 'wakka.all.php' != $file && !is_dir('lang/' . $file) && 1 == preg_match("/^wakka\.(.*?)\.php$/", $file, $match)) {
                $langlist[] = $match[1];
            }
        }

        closedir($handle);

        return $langlist;
    }

    public function GetResourceValue($name, $lang = '', $dounicode = true)
    {
        if (!$this->GetConfigValue('multilanguage')) {
            return $this->resource[$name];
        }

        //echo "<b>GetResourceValue:</b> $lang + $name + $this->userlang + $this->pagelang<br>";

        if (!$lang && $this->userlang != $this->pagelang) {
            $lang = $this->userlang;
        }

        if ('' != $lang) {
            $this->LoadResource($lang);

            return (is_array($this->resources[$lang][$name])) ? $this->resources[$lang][$name] : ($dounicode ? $this->DoUnicodeEntities($this->resources[$lang][$name], $lang) : $this->resources[$lang][$name]);
        }

        return $this->resource[$name];
    }

    public function FormatResourceValue($name, $lang = '')
    {
        $string = $this->GetResourceValue($name, $lang, false);

        $this->format_safe = false;

        $string = $this->Format($string);

        $this->format_safe = true;

        return $string;
    }

    public function DetermineLang()
    {  //!!!! неверно!
        if ('edit' == $this->GetMethod() && 1 == $_GET['add']) {
            if ($_REQUEST['lang']) {
                $lang = $_REQUEST['lang'];
            } else {
                $lang = $this->userlang;
            }
        } else {
            $lang = $this->pagelang;
        }

        return $lang;
    }

    public function SetPageLang($lang)
    {
        if (!$lang) {
            return false;
        }

        $this->pagelang = $lang;

        $this->SetLanguage($lang);

        return true;
    }

    public function GetCharset()
    {
        $lang = $this->DetermineLang();

        $this->LoadResource($lang);

        return $this->languages[$lang]['charset'];
    }

    public function DoUnicodeEntities($string, $lang)
    {
        if (!$this->GetConfigValue('multilanguage')) {
            return $string;
        }

        $_lang = $this->DetermineLang();

        if ($lang == $_lang) {
            return $string;
        }

        // die("<h2>".$lang."<>".$_lang."</h2>");

        $this->LoadResource($lang);

        if (is_array($this->languages[$lang]['unicode_entities'])) {
            return @strtr($string, $this->languages[$lang]['unicode_entities']);
        }

        return $string;
    }

    public function tryUtfDecode($string)
    {
        $t1 = $this->utf8ToUnicodeEntities($string);

        $t2 = @strtr($t1, $this->unicode_entities);

        //echo "<pre><h1>".$string."|".$t1."|".$t2."</h1></pre>";

        if (!preg_match("/\&\#[0-9]+\;/", $t2)) {
            $string = $t2;
        }

        return $string;
    }

    public function utf8ToUnicodeEntities($source)
    {
        // array used to figure what number to decrement from character order value

        // according to number of characters used to map unicode to ascii by utf-8

        $decrement[4] = 240;

        $decrement[3] = 224;

        $decrement[2] = 192;

        $decrement[1] = 0;

        // the number of bits to shift each charNum by

        $shift[1][0] = 0;

        $shift[2][0] = 6;

        $shift[2][1] = 0;

        $shift[3][0] = 12;

        $shift[3][1] = 6;

        $shift[3][2] = 0;

        $shift[4][0] = 18;

        $shift[4][1] = 12;

        $shift[4][2] = 6;

        $shift[4][3] = 0;

        $pos = 0;

        $len = mb_strlen($source);

        $encodedString = '';

        while ($pos < $len) {
            $asciiPos = ord(mb_substr($source, $pos, 1));

            if (($asciiPos >= 240) && ($asciiPos <= 255)) {// 4 chars representing one unicode character
                $thisLetter = mb_substr($source, $pos, 4);

                $pos += 4;
            } elseif (($asciiPos >= 224) && ($asciiPos <= 239)) {// 3 chars representing one unicode character
                $thisLetter = mb_substr($source, $pos, 3);

                $pos += 3;
            } elseif (($asciiPos >= 192) && ($asciiPos <= 223)) {// 2 chars representing one unicode character
                $thisLetter = mb_substr($source, $pos, 2);

                $pos += 2;
            } else {// 1 char (lower ascii)
                $thisLetter = mb_substr($source, $pos, 1);

                $pos += 1;
            }

            // process the string representing the letter to a unicode entity

            $thisLen = mb_strlen($thisLetter);

            if ($thisLen > 1) {
                $thisPos = 0;

                $decimalCode = 0;

                while ($thisPos < $thisLen) {
                    $thisCharOrd = ord(mb_substr($thisLetter, $thisPos, 1));

                    if (0 == $thisPos) {
                        $charNum = (int)($thisCharOrd - $decrement[$thisLen]);

                        $decimalCode += ($charNum << $shift[$thisLen][$thisPos]);
                    } else {
                        $charNum = (int)($thisCharOrd - 128);

                        $decimalCode += ($charNum << $shift[$thisLen][$thisPos]);
                    }

                    $thisPos++;
                }

                $encodedLetter = '&#' . $decimalCode . ';';
            } else {
                $encodedLetter = $thisLetter;
            }

            $encodedString .= $encodedLetter;
        }

        return $encodedString;
    }

    public function GetWakkaName()
    {
        return $this->GetConfigValue('wakka_name');
    }

    public function GetWakkaVersion()
    {
        return $this->VERSION;
    }

    public function GetWackoVersion()
    {
        return $this->WVERSION;
    }

    // PAGES

    // Ќпжќдносторонний“ранслит

    public $NpjMacros = [
        'вики' => 'wiki',
'вака' => 'wacko',
'швака' => 'shwacko',
'веб' => 'web',
'ланс' => 'lance',
'кукуц' => 'kukutz',
'мендокуси' => 'mendokusee',
'¤ремко' => 'iaremko',
'николай' => 'nikolai',
'алексей' => 'aleksey',
'анатолий' => 'anatoly',
    ];

    public function NpjTranslit($tag, $strtolow = TRAN_LOWERCASE, $donotload = TRAN_LOAD)
    {
        if (!$this->GetConfigValue('multilanguage')) {
            $donotload = 1;
        }

        if (!$donotload) {
            if ($page = $this->LoadPage($tag, '', LOAD_CACHE, LOAD_META)) {
                $_lang = $this->language['code'];

                if ($page['lang']) {
                    $lang = $page['lang'];
                } else {
                    $lang = $this->GetConfigValue('language');
                }

                $this->SetLanguage($lang);
            }
        }

        $tag = str_replace('//', '/', $tag);

        $tag = str_replace('-', '', $tag);

        $tag = str_replace(' ', '', $tag);

        $tag = str_replace("'", '_', $tag);

        $_tag = mb_strtolower($tag);

        if ($strtolow) {
            $tag = @strtr($_tag, $this->NpjMacros);
        } else {
            foreach ($this->NpjMacros as $macro => $value) {
                while (false !== ($pos = mb_strpos($_tag, $macro))) {
                    $_tag = substr_replace($_tag, $value, $pos, mb_strlen($macro));

                    $tag = substr_replace($tag, ucfirst($value), $pos, mb_strlen($macro));
                }
            }
        }

        $tag = @strtr($tag, $this->language['NpjLettersFrom'], $this->language['NpjLettersTo']);

        $tag = @strtr($tag, $this->language['NpjBiLetters']);

        if ($strtolow) {
            $tag = mb_strtolower($tag);
        }

        if ($_lang) {
            $this->SetLanguage($_lang);
        }

        return rtrim($tag, '/');
    }

    public function Translit($tag, $direction = 1)
    { //deprecated
        return $tag;
    }

    public function LoadPage($tag, $time = '', $cache = LOAD_CACHE, $metadataonly = LOAD_ALL)
    {
        $supertag = $this->NpjTranslit($tag, TRAN_LOWERCASE, TRAN_DONTLOAD);

        if (1 == $this->GetCachedWantedPage($supertag)) {
            return '';
        }

        $page = $this->OldLoadPage($supertag, $time, $cache, true, $metadataonly); // 1. search for supertag
        if (!$page) {  // 2. if not found, search for tag
            // {
            $page = $this->OldLoadPage($tag, $time, $cache, false, $metadataonly);
        }

        /* if ($page)  // 3. if found, update supertag
{
$this->Query( "update ".$this->config["table_prefix"]."pages ".
"set supertag='".$supertag."' where tag = '".$tag."';" );
}
}
*/

        if (!$page) {
            $this->CacheWantedPage($supertag);
        }

        return $page;
    }

    public function OldLoadPage($tag, $time = '', $cache = 1, $supertagged = false, $metadataonly = 0)
    {
        if (!$supertagged) {
            $supertag = $this->NpjTranslit($tag, TRAN_LOWERCASE, TRAN_DONTLOAD);
        } else {
            $supertag = $tag;
        }

        // retrieve from cache

        if (!$time && $cache && ($cachedPage = $this->GetCachedPage($supertag, $metadataonly))) {
            $page = $cachedPage;
        }

        // load page

        if ($metadataonly) {
            $what = $this->pages_meta;
        } else {
            $what = '*';
        }

        if (!$page) {
            if ($supertagged) {
                $page = $this->LoadSingle('select ' . $what . ' from ' . $this->config['table_prefix'] . "pages where supertag='" . quote($tag) . "' and latest = 'Y' limit 1");

                if ($time && $time != $page['time']) {
                    $this->CachePage($page, $metadataonly);

                    $page = $this->LoadSingle('select ' . $what . ' from ' . $this->config['table_prefix'] . "revisions where supertag='" . quote($tag) . "' and time = '" . quote($time) . "' limit 1");
                }
            } else {
                $page = $this->LoadSingle('select ' . $what . ' from ' . $this->config['table_prefix'] . "pages where tag='" . quote($tag) . "' and latest = 'Y' limit 1");

                if ($time && $time != $page['time']) {
                    $this->CachePage($page, $metadataonly);

                    $page = $this->LoadSingle('select ' . $what . ' from ' . $this->config['table_prefix'] . "revisions where tag='" . quote($tag) . "' and time = '" . quote($time) . "' limit 1");
                }
            }
        }// cache result

        if (!$time && !$cachedPage) {
            $this->CachePage($page, $metadataonly);
        }

        return $page;
    }

    public function GetCachedPage($tag, $metadataonly = 0)
    {
        if (0 == $this->pageCache[$tag]['mdonly'] || $metadataonly == $this->pageCache[$tag]['mdonly']) {
            return $this->pageCache[$tag];
        }

        return false;
    }

    public function CachePage($page, $metadataonly = 0)
    {
        $page['supertag'] = $this->NpjTranslit($page['supertag'], TRAN_LOWERCASE, TRAN_DONTLOAD);

        $this->pageCache[$page['supertag']] = $page;

        $this->pageCache[$page['supertag']]['mdonly'] = $metadataonly;
    }

    public function CacheWantedPage($tag, $check = 0)
    {
        if (0 == $check) {
            $this->wantedCache[$this->language['code']][$tag] = 1;
        } elseif ('' == $this->OldLoadPage($tag, '', 1, false, 1)) {
            $this->wantedCache[$this->language['code']][$tag] = 1;
        }
    }

    public function ClearCacheWantedPage($tag)
    {
        $this->wantedCache[$this->language['code']][$tag] = 0;
    }

    public function GetCachedWantedPage($tag)
    {
        return $this->wantedCache[$this->language['code']][$tag];
    }

    public function GetCachedACL($tag, $privilege, $useDefaults)
    {
        return $this->aclCache[$tag . '#' . $privilege . '#' . $useDefaults];
    }

    public function CacheACL($tag, $privilege, $useDefaults, $acl)
    {
        $this->aclCache[$tag . '#' . $privilege . '#' . $useDefaults] = $acl;
    }

    public function CacheLinks()
    {
        if ($links = $this->LoadAll('select * from ' . $this->config['table_prefix'] . "links where from_tag='" . quote($this->GetPageTag()) . "'")) {
            $cl = count($links);

            for ($i = 0; $i < $cl; $i++) {
                $pages[$i] = $links[$i]['to_tag'];
            }
        }

        $user = $this->GetUser();

        $pages[$cl] = $user['name'];

        $bookm = $this->GetDefaultBookmarks($user['lang'], 'site') . "\n" . ($user['bookmarks'] ?: $this->GetDefaultBookmarks($user['lang']));

        $bookmarks = explode("\n", $bookm);

        for ($i = 0, $iMax = count($bookmarks); $i <= $iMax; $i++) {
            $pages[$cl + $i] = preg_replace("/^(.*?)\s.*$/", '\\1', preg_replace("/[\[\]\(\)]/", '', $bookmarks[$i]));
        }

        $pages[] = $this->GetPageTag();

        for ($i = 0, $iMax = count($pages); $i < $iMax; $i++) {
            $spages[$i] = $this->NpjTranslit($pages[$i], TRAN_LOWERCASE, TRAN_DONTLOAD);

            $spages_str .= "'" . quote($spages[$i]) . "', ";

            $pages_str .= "'" . quote($pages[$i]) . "', ";
        }

        $spages_str = mb_substr($spages_str, 0, -2);

        $pages_str = mb_substr($pages_str, 0, -2);

        if ($links = $this->LoadAll('select ' . $this->pages_meta . ' from ' . $this->config['table_prefix'] . 'pages where supertag in (' . $spages_str . ')')) {
            for ($i = 0, $iMax = count($links); $i < $iMax; $i++) {
                $this->CachePage($links[$i], 1);

                $exists[] = $links[$i]['supertag'];
            }
        }

        $notexists = @array_values(@array_diff($spages, $exists));

        for ($i = 0, $iMax = count($notexists); $i < $iMax; $i++) {
            $this->CacheWantedPage($pages[array_search($notexists[$i], $spages, true)], 1);

            $this->CacheACL($notexists[$i], 'read', 1, $acl);
        }

        // unset($exists);

        if ($read_acls = $this->LoadAll('select * from ' . $this->config['table_prefix'] . 'acls where BINARY page_tag in (' . $pages_str . ") and privilege = 'read'")) {
            for ($i = 0, $iMax = count($read_acls); $i < $iMax; $i++) {
                $this->CacheACL($read_acls[$i]['supertag'], 'read', 1, $read_acls[$i]);

                // $exists[] = $read_acls[$i]["tag"];
            }
        }

        /*
$notexists = @array_values(@array_diff($pages, $exists));
for ($i=0; $i<count($notexists); $i++)
{
$acl = array("supertag" => $notexists[$i], "page_tag" => $notexists[$i], "privilege" => "read", "list" => "*", "time" => date("YmdHis"));
$this->CacheACL($notexists[$i], "read", 1, $acl);
}
*/

        $ddd = $this->GetMicroTime();

        $this->queryLog[] = [
            'query' => '<b>end caching links</b>',
'time' => $this->GetResourceValue('MeasuredTime') . ': ' . (number_format(($ddd - $this->timer), 3)) . ' s',
        ];
    }

    public function SetPage($page)
    {
        $this->page = $page;

        if ($this->page['tag']) {
            $this->tag = $this->page['tag'];
        }

        if ($page['lang']) {
            $this->pagelang = $page['lang'];
        } elseif ($_REQUEST['add'] && $_REQUEST['lang']) {
            $this->pagelang = $_REQUEST['lang'];
        } elseif ($_REQUEST['add']) {
            $this->pagelang = $this->userlang;
        } else {
            $this->pagelang = $this->GetConfigValue('language');
        }
    }

    public function LoadPageById($id)
    {
        if ('-1' != $id) {
            return $this->LoadSingle('select * from ' . $this->config['table_prefix'] . "revisions where id = '" . quote($id) . "' limit 1");
        }

        return $this->LoadSingle('select * from ' . $this->config['table_prefix'] . "pages where tag='" . quote($this->GetPageTag()) . "' and latest='Y' limit 1");
    }

    public function LoadRevisions($page)
    {
        $rev = $this->LoadAll('select ' . $this->pages_meta . ' from ' . $this->config['table_prefix'] . "revisions where tag='" . quote($page) . "' order by time desc");

        if (is_array($rev)) {
            array_unshift($rev, $this->LoadSingle('select ' . $this->pages_meta . ' from ' . $this->config['table_prefix'] . "pages where tag='" . quote($page) . "' order by time desc limit 1"));
        } else {
            $rev[] = $this->LoadSingle('select ' . $this->pages_meta . ' from ' . $this->config['table_prefix'] . "pages where tag='" . quote($page) . "' order by time desc limit 1");
        }

        return $rev;
    }

    public function LoadPagesLinkingTo($tag, $for = '')
    {
        return $this->LoadAll(
            'select from_tag as tag from ' . $this->config['table_prefix'] . 'links where ' . ($for ? "from_tag like '" . quote($for) . "/%' and " : '') . "((to_supertag='' AND to_tag='" . quote($tag) . "') OR to_supertag='" . quote($this->NpjTranslit($tag)) . "')" . ' order by tag'
        );
    }

    public function LoadRecentlyChanged($limit = 70, $for = '', $from = '')
    {
        $limit = (int)$limit;

        if ($pages = $this->LoadAll(
            'select '
            . $this->pages_meta
            . ' from '
            . $this->config['table_prefix']
            . 'pages '
            . "where latest = 'Y' and comment_on = '' "
            . ($from ? "and time<='" . $from . " 23:59:59'" : '')
            . ($for ? "and supertag like '" . quote($this->NpjTranslit($for)) . "/%' " : '')
            . 'order by time desc limit '
            . $limit
        )) {
            foreach ($pages as $page) {
                $this->CachePage($page, 1);
            }

            if ($read_acls = $this->LoadAll(
                'select a.* '
                . 'from '
                . $this->config['table_prefix']
                . 'acls a, '
                . $this->config['table_prefix']
                . 'pages p '
                . "where p.latest = 'Y' "
                . "and p.comment_on = '' "
                . 'and a.supertag = p.supertag '
                . ($for ? "and p.supertag like '" . quote($this->NpjTranslit($for)) . "/%' " : '')
                . "and privilege = 'read' "
                . 'order by time desc limit '
                . $limit
            )) {
                for ($i = 0, $iMax = count($read_acls); $i < $iMax; $i++) {
                    $this->CacheACL($read_acls[$i]['supertag'], 'read', 1, $read_acls[$i]);
                }
            }

            return $pages;
        }
    }

    public function LoadWantedPages($for = '')
    {
        $pref = $this->config['table_prefix'];

        $sql = 'select distinct '
                . $pref
                . 'links.to_tag as tag,count('
                . $pref
                . 'links.from_tag) as count '
                . 'from '
                . $pref
                . 'links left join '
                . $pref
                . 'pages on '
                . '(('
                . $pref
                . 'links.to_tag = '
                . $pref
                . 'pages.tag AND '
                . $pref
                . "links.to_supertag='') "
                . ' OR '
                . $pref
                . 'links.to_supertag='
                . $pref
                . 'pages.supertag) '
                . 'where '
                . ($for ? $pref . "links.to_tag like '" . quote($for) . "/%' and " : '')
                . $pref
                . 'pages.tag is NULL group by tag order by count desc, tag asc';

        return $this->LoadAll($sql);
    }

    public function LoadOrphanedPages($for = '')
    {
        $pref = $this->config['table_prefix'];

        $sql = 'select distinct tag from '
                . $pref
                . 'pages left join '
                . $pref
                . 'links on '
                . // $pref."pages.tag = ".$pref."links.to_tag where ".
                '(('
                . $pref
                . 'links.to_tag = '
                . $pref
                . 'pages.tag AND '
                . $pref
                . "links.to_supertag='') "
                . ' OR '
                . $pref
                . 'links.to_supertag='
                . $pref
                . 'pages.supertag) where '
                . ($for ? $pref . "pages.tag like '" . quote($for) . "/%' and " : '')
                . $pref
                . 'links.to_tag is NULL and '
                . $pref
                . "pages.comment_on = '' "
                . 'order by tag';

        return $this->LoadAll($sql);
    }

    public function LoadPageTitles()
    {
        return $this->LoadAll('select distinct tag from ' . $this->config['table_prefix'] . 'pages order by tag');
    }

    public function LoadAllPages()
    {
        return $this->LoadAll('select ' . $this->pages_meta . ' from ' . $this->config['table_prefix'] . "pages where latest = 'Y' order by BINARY tag");
    }

    public function FullTextSearch($phrase, $filter)
    {
        return $this->LoadAll(
            'select ' . $this->pages_meta . ' from ' . $this->config['table_prefix'] . "pages where latest = 'Y' and (( match(body) against('" . quote($phrase) . "') or lower(tag) like lower('%" . quote($phrase) . "%')) " . ($filter ? "and comment_on=''" : '') . ' )'
        );
    }

    public function TagSearch($phrase)
    {
        return $this->LoadAll('select ' . $this->pages_meta . ' from ' . $this->config['table_prefix'] . "pages where latest = 'Y' and lower(tag) like binary lower('%" . quote($phrase) . "%') order by supertag");
    }

    public function SendMail($email, $subject, $message)
    {
        $headers = 'From: "' . $this->GetConfigValue('wakka_name') . '"<' . $this->GetConfigValue('admin_email') . ">\r\n";

        $headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n"; //mailer
        $headers .= "X-Priority: 3\r\n"; //1 UrgentMessage, 3 Normal
        $headers .= 'Content-Type: text/html; charset=' . $this->GetCharset() . "\r\n";

        $subject = '=?' . $this->GetCharset() . '?B?' . base64_encode($subject) . '?=';

        @mail($email, $subject, $message, $headers);
    }

    public function SavePage($tag, $body, $comment_on = '')
    {
        // get current user

        $user = $this->GetUserName();

        // die ($this->userlang."|".$this->pagelang."|".$_REQUEST["lang"]."|".$_POST["tag"]."|".$this->tag."|".$this->supertag);

        if ($_POST['tag']) {
            $this->tag = $tag = $_POST['tag'];

            $this->supertag = $this->NpjTranslit($tag);
        }

        if ($comment_on) {
            $this->cache->CacheInvalidate($comment_on);
        } else {
            $this->cache->CacheInvalidate($this->tag);

            $this->cache->CacheInvalidate($this->supertag);
        }

        if ($this->HasAccess('write', $tag) || ($comment_on && $this->HasAccess('comment', $comment_on))) {
            $body = $this->Format($body, 'preformat');

            // is page new?

            if (!$oldPage = $this->LoadPage($tag)) {
                if ($_REQUEST['lang']) {
                    $lang = $_REQUEST['lang'];
                } else {
                    $lang = $this->userlang;
                }

                if (!$lang) {
                    $lang = $this->GetConfigValue['language'];
                }

                $this->SetLanguage($lang);

                $body_r = $this->Format($body, 'wacko');

                if ($this->GetConfigValue('paragrafica') && !$comment_on) {
                    $body_r = $this->Format($body_r, 'paragrafica');

                    $body_toc = $this->body_toc;
                }

                // create default write acl. store empty write ACL for comments.

                // get default acl for root.

                if (mb_strstr($this->context[$this->current_context], '/')) {
                    $root = preg_replace('/^(.*)\\/([^\\/]+)$/', '$1', $this->context[$this->current_context]);

                    $write_acl = $this->LoadAcl($root, 'write');

                    while (1 == $write_acl['default']) {
                        $_root = $root;

                        $root = preg_replace('/^(.*)\\/([^\\/]+)$/', '$1', $root);

                        if ($root == $_root) {
                            break;
                        }

                        $write_acl = $this->LoadAcl($root, 'write');
                    }

                    $write_acl = $write_acl['list'];

                    $read_acl = $this->LoadAcl($root, 'read');

                    $read_acl = $read_acl['list'];

                    $comment_acl = $this->LoadAcl($root, 'comment');

                    $comment_acl = $comment_acl['list'];
                } else {
                    $write_acl = $this->GetConfigValue('default_write_acl');

                    $read_acl = $this->GetConfigValue('default_read_acl');

                    $comment_acl = $this->GetConfigValue('default_comment_acl');
                }

                // current user is owner; if user is logged in! otherwise, no owner.

                if ($this->GetUser()) {
                    $owner = $user;
                }

                $this->Query(
                    'insert into '
                    . $this->config['table_prefix']
                    . 'pages set '
                    . ($comment_on ? "comment_on = '" . quote($comment_on) . "', " : '')
                    . ($comment_on ? "super_comment_on = '" . quote($this->NpjTranslit($comment_on)) . "', " : '')
                    . 'time = now(), '
                    . "owner = '"
                    . quote($owner)
                    . "', "
                    . "user = '"
                    . quote($user)
                    . "', "
                    . "latest = 'Y', "
                    . "supertag = '"
                    . $this->NpjTranslit($tag)
                    . "', "
                    . "body = '"
                    . quote($body)
                    . "', "
                    . "body_r = '"
                    . quote($body_r)
                    . "', "
                    . "body_toc = '"
                    . quote($body_toc)
                    . "', "
                    . "lang = '"
                    . quote($lang)
                    . "', "
                    . "tag = '"
                    . quote($tag)
                    . "'"
                );

                $this->SaveAcl($tag, 'write', ($comment_on ? '' : $write_acl));

                $this->SaveAcl($tag, 'read', $read_acl);

                $this->SaveAcl($tag, 'comment', ($comment_on ? '' : $comment_acl));

                if ($this->GetUser() && !$this->GetConfigValue('disable_autosubscribe')) {
                    $this->SetWatch($this->GetUserName(), $this->GetPageTag());
                }

                if ($comment_on) {
                    $username = $this->GetUserName();

                    $Watchers = $this->LoadAll('select * from ' . $this->config['table_prefix'] . "pagewatches where tag = '" . quote($comment_on) . "'");

                    foreach ($Watchers as $Watcher) {
                        if ($Watcher['user'] != $username) {
                            $_user = $this->GetUser();

                            $Watcher['name'] = $Watcher['user'];

                            $this->SetUser($Watcher, 0);

                            if ($this->HasAccess('read', $comment_on, $Watcher['user'])) {
                                $User = $this->LoadSingle('select email, lang, more, email_confirm from ' . $this->config['user_table'] . " where name = '" . quote($Watcher['user']) . "'");

                                $User['options'] = $this->DecomposeOptions($User['more']);

                                if ('' == $User['email_confirm'] && 'N' != $User['options']['send_watchmail']) {
                                    $lang = $User['lang'];

                                    $subject = $this->GetResourceValue('Comment for watched page', $lang) . "'" . $comment_on . "'";

                                    $message = $this->GetResourceValue('MailHello', $lang) . $Watcher['user'] . '.<br> <br> ';

                                    $message .= $username . $this->GetResourceValue('Someone commented', $lang) . '<br> * <a href="' . $this->Href('', $comment_on, '') . '">' . $this->Href('', $comment_on, '') . '</a><br>';

                                    $message .= '<hr>' . $this->Format($body_r, 'post_wacko') . '<hr>';

                                    $message .= '<br>' . $this->GetResourceValue('MailGoodbye', $lang) . ' ' . $this->GetConfigValue('wakka_name');

                                    $this->SendMail($User['email'], $subject, $message);
                                }
                            }

                            $this->SetUser($_user, 0);
                        }
                    }
                }
            } else {
                $this->SetLanguage($this->pagelang);

                $body_r = $this->Format($body, 'wacko');

                if ($this->GetConfigValue('paragrafica')) {
                    $body_r = $this->Format($body_r, 'paragrafica');

                    $body_toc = $this->body_toc;
                }

                // aha! page isn't new. keep owner!

                $owner = $oldPage['owner'];

                if ($oldPage['body'] != $body) {
                    // move revision

                    $this->Query(
                        'insert into '
                        . $this->config['table_prefix']
                        . 'revisions (tag, time, body, owner, user, latest, handler, comment_on, supertag, keywords, description) '
                        . "select tag, time, body, owner, user, 'N', handler, comment_on, supertag, keywords, description from "
                        . $this->config['table_prefix']
                        . "pages WHERE tag = '"
                        . quote($tag)
                        . "' and latest='Y' LIMIT 1"
                    );

                    // add new revision

                    $this->Query(
                        'update '
                        . $this->config['table_prefix']
                        . 'pages set '
                        . ($comment_on ? "comment_on = '" . quote($comment_on) . "', " : '')
                        . ($comment_on ? "super_comment_on = '" . quote($this->NpjTranslit($comment_on)) . "', " : '')
                        . 'time = now(), '
                        . "owner = '"
                        . quote($owner)
                        . "', "
                        . "user = '"
                        . quote($user)
                        . "', "
                        . "supertag = '"
                        . $this->NpjTranslit($tag)
                        . "', "
                        . "body = '"
                        . quote($body)
                        . "', "
                        . "body_toc = '"
                        . quote($body_toc)
                        . "', "
                        . "body_r = '"
                        . quote($body_r)
                        . "' "
                        . "where tag = '"
                        . quote($tag)
                        . "' and latest='Y' LIMIT 1"
                    );
                }

                $username = $this->GetUserName();

                $Watchers = $this->LoadAll('select * from ' . $this->config['table_prefix'] . "pagewatches where tag = '" . quote($tag) . "'");

                if ($Watchers) {
                    foreach ($Watchers as $Watcher) {
                        if ($Watcher['user'] != $username) {
                            $_user = $this->GetUser();

                            $Watcher['name'] = $Watcher['user'];

                            $this->SetUser($Watcher, 0);

                            $lang = $Watcher['lang'];

                            if ($this->HasAccess('read', $tag, $Watcher['user'])) {
                                $User = $this->LoadSingle('select email, lang, more, email_confirm from ' . $this->config['user_table'] . " where name = '" . quote($Watcher['user']) . "'");

                                $User['options'] = $this->DecomposeOptions($User['more']);

                                if ('' == $User['email_confirm'] && 'N' != $User['options']['send_watchmail']) {
                                    $lang = $User['lang'];

                                    $subject = $this->GetResourceValue('A watched Page changed!', $lang) . "'" . $tag . "'";

                                    $message = "<style>.additions {color: #008800;}\n.deletions {color: #880000;}</style>";

                                    $message .= $this->GetResourceValue('MailHello', $lang) . $Watcher['user'] . '.<br> <br> ';

                                    $message .= $username . $this->GetResourceValue('Someone changed this page:', $lang) . '<br> '; //* <a href=\"".$this->Href("",$tag,"")."\">".$this->Href("",$tag,"")."</a><br>";

                                    $_REQUEST['fastdiff'] = 1;

                                    $_REQUEST['a'] = -1;

                                    $page = $this->LoadSingle('select ' . $this->pages_meta . ' from ' . $this->config['table_prefix'] . "revisions where tag='" . quote($tag) . "' order by time desc");

                                    $_REQUEST['b'] = $page['id'];

                                    $message .= '<hr>' . $this->IncludeBuffered('handlers/page/diff.php', 'oops') . '<hr>';

                                    $message .= '<br>' . $this->GetResourceValue('MailGoodbye', $lang) . ' ' . $this->GetConfigValue('wakka_name');

                                    $this->SendMail($User['email'], $subject, $message);
                                }
                            }

                            $this->SetUser($_user, 0);
                        }
                    }
                }

                $this->SetLanguage($this->userlang);
            }
        }

        $this->WriteRecentChangesXML();

        return $body_r;
    }

    public function SaveMeta($tag, $metadata)
    {
        if ($this->UserIsOwner($tag)) {
            // update

            $this->Query(
                'update ' . $this->config['table_prefix'] . 'pages set ' . "lang = '" . quote($metadata['lang']) . "', " . "keywords = '" . quote($metadata['keywords']) . "', " . "description = '" . quote($metadata['description']) . "' " . "where tag = '" . quote($tag) . "' and latest='Y' LIMIT 1"
            );
        }

        return true;
    }

    // COOKIES

    public function SetSessionCookie($name, $value)
    {
        setcookie($this->config['cookie_prefix'] . $name, $value, 0, '/');

        $_COOKIE[$this->config['cookie_prefix'] . $name] = $value;
    }

    public function SetPersistentCookie($name, $value, $remember = 1)
    {
        setcookie($this->config['cookie_prefix'] . $name, $value, time() + ($remember ? 90 * 24 * 60 * 60 : 60 * 60), '/');

        $_COOKIE[$this->config['cookie_prefix'] . $name] = $value;
    }

    public function DeleteCookie($name)
    {
        setcookie($this->config['cookie_prefix'] . $name, '', 1, '/');

        $_COOKIE[$this->config['cookie_prefix'] . $name] = '';
    }

    public function GetCookie($name)
    {
        return $_COOKIE[$this->config['cookie_prefix'] . $name];
    }

    // HTTP/REQUEST/LINK RELATED

    public function SetMessage($message)
    {
        $_SESSION['message'] = $message;
    }

    public function GetMessage()
    {
        $message = $_SESSION['message'];

        $_SESSION['message'] = '';

        return $message;
    }

    public function Redirect($url)
    {
        header("Location: $url");

        exit;
    }

    public function UnwrapLink($tag)
    {
        if ('/' == $tag) {
            return '';
        }

        if ('!' == $tag) {
            return $this->tag;
        }

        $newtag = $tag;

        if (mb_strstr($this->context[$this->current_context], '/')) {
            $root = preg_replace('/^(.*)\\/([^\\/]+)$/', '$1', $this->context[$this->current_context]);
        } else {
            $root = '';
        }

        if (preg_match("/^\.\/(.*)$/", $tag, $matches)) {
            $root = '';
        } elseif (preg_match("/^\/(.*)$/", $tag, $matches)) {
            $root = '';

            $newtag = $matches[1];
        } elseif (preg_match("/^\!\/(.*)$/", $tag, $matches)) {
            $root = $this->context[$this->current_context];

            $newtag = $matches[1];
        } elseif (preg_match("/^\.\.\/(.*)$/", $tag, $matches)) {
            $newtag = $matches[1];

            if (mb_strstr($root, '/')) {
                $root = preg_replace('/^(.*)\\/([^\\/]+)$/', '$1', $root);
            } else {
                $root = '';
            }
        }

        if ('' != $root) {
            $newtag = '/' . $newtag;
        }

        $tag = $root . $newtag;

        $tag = str_replace('//', '/', $tag);

        return $tag;
    }

    // returns just PageName[/method].

    public function MiniHref($method = '', $tag = '', $addpage = '')
    {
        if (!$tag = trim($tag)) {
            $tag = $this->tag;
        }

        if (!$addpage) {
            $tag = $this->SlimUrl($tag);
        }

        $tag = $this->Translit($tag, 0);

        return $tag . ($method ? '/' . $method : '');
    }

    // returns the full url to a page/method.

    public function Href($method = '', $tag = '', $params = '', $addpage = 0)
    {
        $href = $this->config['base_url'] . $this->MiniHref($method, $tag, $addpage);

        if ($addpage) {
            $params = 'add=1' . ($params ? '&amp;' . $params : '');
        }

        if ($params) {
            $href .= ($this->config['rewrite_mode'] ? '?' : '&amp;') . $params;
        }

        return $href;
    }

    public function ComposeLinkToPage($tag, $method = '', $text = '', $track = 1)
    {
        if (!$text) {
            $text = $this->AddSpaces($tag);
        }

        //$text = htmlentities($text);

        if ($_SESSION['linktracking'] && $track) {
            $this->TrackLinkTo($tag);
        }

        return '<a href="' . $this->Href($method, $tag) . '">' . $text . '</a>';
    }

    public function PreLink($tag, $text = '', $track = 1)
    {
        // if (!$text) $text = $this->AddSpaces($tag);
        if (preg_match("/^[\!\." . $this->language['ALPHANUM_P'] . ']+$/', $tag)) {// it's a Wiki link!
            if ($_SESSION['linktracking'] && $track) {
                $this->TrackLinkTo($this->UnwrapLink($tag));
            }
        }

        return "\xA2\xA2" . $tag . ' ==' . ($this->format_safe ? str_replace('>', '&gt;', str_replace('<', '&lt;', $text)) : $text) . "\xAF\xAF";
    }

    // <?

    public function Link($tag, $method = '', $text = '', $track = 1, $safe = 0, $linklang = '')
    {
        if (!$safe) {
            $text = htmlspecialchars($text, ENT_NOQUOTES);
        }

        if ($linklang) {
            $this->SetLanguage($linklang);
        }

        $imlink = false;

        if (preg_match("/^[\.\-" . $this->language['ALPHANUM_P'] . "]+\.(gif|jpg|jpe|jpeg|png)$/", $text)) {
            $imlink = $this->GetConfigValue('root_url') . '/images/' . $text;
        } elseif (preg_match("/^(http|https|ftp):\/\/([^\\s\"<>]+)\.(gif|jpg|jpe|jpeg|png)$/", preg_replace("/<\/?nobr>/", '', $text))) {
            $imlink = $text = preg_replace("/<\/?nobr>/", '', $text);
        }

        $url = '';

        if (preg_match("/^(mailto[:])?[^\\s\"<>&\:]+\@[^\\s\"<>&\:]+\.[^\\s\"<>&\:]+$/", $tag, $matches)) {// this is a valid Email
            $url = ('mailto:' == $matches[1] ? $tag : 'mailto:' . $tag);

            $title = $this->GetResourceValue('MailLink');

            $icon = $this->GetResourceValue('mailicon');

            $tpl = 'email';
        } elseif (preg_match('/^#/', $tag)) {// html-anchor
            $url = $tag;

            $tpl = 'anchor';
        } elseif (preg_match("/^[\.\-" . $this->language['ALPHANUM_P'] . "]+\.(gif|jpg|jpe|jpeg|png)$/", $tag)) {// image
            return '<img src="' . $this->GetConfigValue('root_url') . '/images/' . $tag . '" ' . ($text ? 'alt="' . $text . '" title="' . $text . '"' : '') . '>';
        } elseif (preg_match("/^(http|https|ftp|file):\/\/([^\\s\"<>]+)\.(gif|jpg|jpe|jpeg|png)$/", $tag)) {// external image
            return '<img src="' . str_replace('&', '&amp;', str_replace('&amp;', '&', $tag)) . '" ' . ($text ? 'alt="' . $text . '" title="' . $text . '"' : '') . '>';
        } elseif (preg_match("/^(http|https|ftp|file):\/\/([^\\s\"<>]+)\.(rpm|gz|tgz|zip|rar|exe|doc|xls|ppt|tgz|pdf)$/", $tag)) {// this is a file link
            $url = str_replace('&', '&amp;', str_replace('&amp;', '&', $tag));

            $title = $this->GetResourceValue('FileLink');

            $icon = $this->GetResourceValue('fileicon');

            $tpl = 'file';
        } elseif (preg_match("/^(http|https|ftp|file|nntp|telnet):\/\/([^\\s\"<>]+)$/", $tag)) {// this is a valid external URL
            $url = str_replace('&', '&amp;', str_replace('&amp;', '&', $tag));

            if (!mb_stristr($tag, $this->config['base_url'])) {
                $title = $this->GetResourceValue('OuterLink2');

                $icon = $this->GetResourceValue('outericon');
            }

            $tpl = 'outerlink';
        } elseif (preg_match("/^(_?)file:([^\\s\"<>\(\)]+)$/", $tag, $matches)) {// this is a file:
            $noimg = $matches[1];

            $thing = $matches[2];

            $arr = explode('/', $thing);

            //echo($thing."<br>");
            if (1 == count($arr)) { // file:shit.zip
                // echo ($thing."<br>");
                //try to find in global storage and return if success
                $desc = $this->CheckFileExists($thing);

                // print_r($desc);

                if (is_array($desc)) {
                    $title = $desc['description'] . ' (' . ceil($desc['filesize'] / 1024) . '&nbsp;' . $this->GetResourceValue('UploadKB') . ')';

                    if ($desc['picture_w'] && !$noimg) {
                        if (!$text) {
                            $text = $title;
                        }

                        return '<img src="' . $this->GetConfigValue('root_url') . $this->config['upload_path'] . '/' . $thing . '" ' . ($text ? 'alt="' . $text . '" title="' . $text . '"' : '') . " width='" . $desc['picture_w'] . "' height='" . $desc['picture_h'] . "'>";
                    }

                    $url = $this->GetConfigValue('root_url') . $this->config['upload_path'] . '/' . $thing;

                    $icon = $this->GetResourceValue('fileicon');

                    $imlink = false;

                    $tpl = 'localfile';
                }
            }

            if (2 == count($arr) && '' == $arr[0]) { // file:/shit.zip
                //try to find in global storage and return if success

                $desc = $this->CheckFileExists($arr[1]);

                if (is_array($desc)) {
                    $title = $desc['description'] . ' (' . ceil($desc['filesize'] / 1024) . '&nbsp;' . $this->GetResourceValue('UploadKB') . ')';

                    if ($desc['picture_w'] && !$noimg) {
                        if (!$text) {
                            $text = $title;
                        }

                        return '<img src="' . $this->GetConfigValue('root_url') . $this->config['upload_path'] . '/' . $thing . '" ' . ($text ? 'alt="' . $text . '" title="' . $text . '"' : '') . " width='" . $desc['picture_w'] . "' height='" . $desc['picture_h'] . "'>";
                    }

                    $url = $this->GetConfigValue('root_url') . $this->config['upload_path'] . $thing;

                    $imlink = false;

                    $icon = $this->GetResourceValue('fileicon');

                    $tpl = 'localfile';
                } else { //404
                    $tpl = 'wlocalfile';

                    $title = '404: /' . $this->config['upload_path'] . $thing;

                    $url = '404';
                }
            }

            if (!$url) {
                $file = $arr[count($arr) - 1];

                unset($arr[count($arr) - 1]);

                $_pagetag = implode('/', $arr);

                if ('' == $_pagetag) {
                    $_pagetag = '!/';
                }

                //unwrap tag (check !/, ../ cases)

                $pagetag = $this->UnwrapLink($_pagetag);

                //try to find in local $tag storage

                $desc = $this->CheckFileExists($file, $pagetag);

                if (is_array($desc)) {
                    //check 403 here!

                    if ($this->IsAdmin() || ($desc['id'] && ($this->GetPageOwner($this->tag) == $this->GetUserName())) || ($this->HasAccess('read', $pagetag)) || ($desc['user'] == $this->GetUserName())) {
                        $title = $desc['description'] . ' (' . ceil($desc['filesize'] / 1024) . '&nbsp;' . $this->GetResourceValue('UploadKB') . ')';

                        if ($desc['picture_w'] && !$noimg) {
                            if (!$text) {
                                $text = $title;
                            }

                            return '<img src="'
                                   . $this->config['base_url']
                                   . trim($pagetag, '/')
                                   . '/files'
                                   . ($this->config['rewrite_mode'] ? '?' : '&amp;')
                                   . 'get='
                                   . $file
                                   . '" '
                                   . ($text ? 'alt="' . $text . '" title="' . $text . '"' : '')
                                   . " width='"
                                   . $desc['picture_w']
                                   . "' height='"
                                   . $desc['picture_h']
                                   . "'>";
                        }

                        $url = $this->config['base_url'] . trim($pagetag, '/') . '/files' . ($this->config['rewrite_mode'] ? '?' : '&amp;') . 'get=' . $file;

                        $imlink = false;

                        $icon = $this->GetResourceValue('fileicon');

                        $tpl = 'localfile';
                    } else { //403
                        $url = $this->config['base_url'] . trim($pagetag, '/') . '/files' . ($this->config['rewrite_mode'] ? '?' : '&amp;') . 'get=' . $file;

                        $imlink = false;

                        $icon = $this->GetResourceValue('lockicon');

                        $tpl = 'localfile';

                        $class = 'denied';
                    }
                } else { //404
                    $tpl = 'wlocalfile';

                    $title = '404: /' . trim($pagetag, '/') . '/files' . ($this->config['rewrite_mode'] ? '?' : '&amp;') . 'get=' . $file;

                    $url = '404';
                }
            }

            //forgot 'bout 403
        } elseif (1 != $this->GetConfigValue('disable_tikilinks') && preg_match('/^(' . $this->language['UPPER'] . $this->language['LOWER'] . $this->language['ALPHANUM'] . "*)\.(" . $this->language['ALPHA'] . $this->language['ALPHANUM'] . '+)$/s', $tag, $matches)) {// it`s a Tiki link!
            if (!$text) {
                $text = $this->AddSpaces($tag);
            }

            $tag = '/' . $matches[1] . '/' . $matches[2];

            return $this->Link($tag, $method, $text, $track, 1);
        } elseif (preg_match('/^([[:alnum:]]+)[:]([' . $this->language['ALPHANUM_P'] . "\-\_\.\+\&\=]*)$/", $tag, $matches)) {// interwiki
            $parts = explode('/', $matches[2]);

            for ($i = 0, $iMax = count($parts); $i < $iMax; $i++) {
                $parts[$i] = urlencode($parts[$i]);
            }

            $url = $this->GetInterWikiUrl($matches[1], implode('/', $parts));

            $icon = $this->GetResourceValue('iwicon');

            $tpl = 'interwiki';

            if ($linklang) {
                $text = $this->DoUnicodeEntities($text, $linklang);
            }
        } elseif (preg_match("/^([\!\." . $this->language['ALPHANUM_P'] . "]+)(\#[" . $this->language['ALPHANUM_P'] . "\_\-]+)?$/", $tag, $matches)) {// it's a Wiki link!
            $tag = $otag = $matches[1];

            $untag = $unwtag = $this->UnwrapLink($tag);

            $regexHandlers = '/^(.*?)\/(' . $this->GetConfigValue('standartHandlers') . ')\/(.*)$/i';

            $ptag = $this->NpjTranslit($unwtag);

            if (preg_match($regexHandlers, '/' . $ptag . '/', $match)) {
                $handler = $match[2];

                $ptag = $match[1];

                $unwtag = '/' . $unwtag . '/';

                $co = mb_substr_count($_ptag, '/') - mb_substr_count($ptag, '/');

                for ($i = 0; $i < $co; $i++) {
                    $unwtag = mb_substr($unwtag, 0, mb_strrpos($unwtag, '/'));
                }

                if ($handler) {
                    $opar = '/' . $untag . '/';

                    for ($i = 0; $i < mb_substr_count($data, '/') + 2; $i++) {
                        $opar = mb_substr($opar, mb_strpos($opar, '/') + 1);
                    }

                    $params = explode('/', $opar); //содержит хорошие парамсы
                }
            }

            $unwtag = trim($unwtag, '/');

            if ($handler) {
                $method = $handler;
            }

            //if ($tag=="!/edit") echo "{".$tag."|".$untag."|".$unwtag."|".$handler."}";

            $thispage = $this->LoadPage($unwtag, '', LOAD_CACHE, LOAD_META);

            if (!$thispage && $linklang) {
                $this->SetLanguage($linklang);

                $lang = $linklang;

                $thispage = $this->LoadPage($unwtag, '', LOAD_CACHE, LOAD_META);
            }

            if ($thispage) {
                $_lang = $this->language['code'];

                if ($thispage['lang']) {
                    $lang = $thispage['lang'];
                } else {
                    $lang = $this->GetConfigValue('language');
                }

                $this->SetLanguage($lang);

                $supertag = $this->NpjTranslit($tag);

            // echo "<h1>".$_lang."|".$lang."|".$supertag."</h1>";
            } else {
                $supertag = $this->NpjTranslit($tag, TRAN_LOWERCASE, TRAN_DONTLOAD);
            }

            $aname = '';

            if ('!/' == mb_substr($tag, 0, 2)) {
                $icon = $this->GetResourceValue('childicon');

                $page0 = mb_substr($tag, 2);

                $page = $this->AddSpaces($page0);

                $tpl = 'childpage';
            } elseif ('../' == mb_substr($tag, 0, 3)) {
                $icon = $this->GetResourceValue('parenticon');

                $page0 = mb_substr($tag, 3);

                $page = $this->AddSpaces($page0);

                $tpl = 'parentpage';
            } elseif ('/' == mb_substr($tag, 0, 1)) {
                $icon = $this->GetResourceValue('rooticon');

                $page0 = mb_substr($tag, 1);

                $page = $this->AddSpaces($page0);

                $tpl = 'rootpage';
            } else {
                $icon = $this->GetResourceValue('equalicon');

                $page0 = $tag;

                $page = $this->AddSpaces($page0);

                $tpl = 'equalpage';
            }

            if ($imlink) {
                $text = "<img src=\"$imlink\" border=\"0\" title=\"$text\">";
            }

            if ($text) {
                $tpl = 'descrpage';

                $icon = '';
            }

            $pagepath = mb_substr($untag, 0, mb_strlen($untag) - mb_strlen($page0));

            $anchor = $matches[2];

            $tag = $unwtag;

            if ($_SESSION['linktracking'] && $track) {
                $this->TrackLinkTo($tag);
            }

            if (!$this->first_inclusion[$supertag]) {
                $aname = 'name="' . $supertag . '"';
            }

            $this->first_inclusion[$supertag] = 1;

            if ($thispage) {
                $pagelink = $this->Href($method, $thispage['tag']) . $this->AddDatetime($tag) . ($anchor ?: '');

                if ($this->config['hide_locked']) {
                    $access = $this->HasAccess('read', $tag);
                } else {
                    $access = true;

                    '*' == $this->_acl['list'];
                }

                if (!$access) {
                    $class = 'denied';

                    $accicon = $this->GetResourceValue('lockicon');
                } elseif ('*' == $this->_acl['list']) {
                    $class = '';

                    $accicon = '';
                } else {
                    $class = 'customsec';

                    $accicon = $this->GetResourceValue('keyicon');
                }

                // language

                // echo "<< ".$lang.":".$_lang.":".$otag."|$linklang >>";

                // if ($lang!=$this->pagelang)

                // {

                if ($text == trim($otag, '/') || $linklang) {
                    $text = $this->DoUnicodeEntities($text, $lang);
                }

                // echo "< ".$text.":".$otag." >";

                $page = $this->DoUnicodeEntities($page, $lang);

                // }

                if (isset($_lang)) {
                    $this->SetLanguage($_lang);
                }
            } else {
                $tpl = ('print' == $this->method || 'msword' == $this->method ? 'p' : '') . 'w' . $tpl;

                $pagelink = $this->Href('edit', $tag, $lang ? 'lang=' . $lang : '', 1);

                $accicon = $this->GetResourceValue('wantedicon');

                $title = $this->GetResourceValue('CreatePage');

                if ($linklang) {
                    $text = $this->DoUnicodeEntities($text, $linklang);

                    $page = $this->DoUnicodeEntities($page, $linklang);
                }
            }

            $icon = str_replace('{theme}', $this->GetConfigValue('theme_url'), $icon);

            $accicon = str_replace('{theme}', $this->GetConfigValue('theme_url'), $accicon);

            $res = $this->GetResourceValue('tpl.' . $tpl);

            $text = trim($text);

            if ($res) {
                //todo: pagepath

                $aname = str_replace('/', '.', $aname);

                $res = str_replace('{aname}', $aname, $res);

                $res = str_replace('{icon}', $icon, $res);

                $res = str_replace('{accicon}', $accicon, $res);

                $res = str_replace('{class}', $class, $res);

                $res = str_replace('{title}', $title, $res);

                $res = str_replace('{pagelink}', $pagelink, $res);

                $res = str_replace('{pagepath}', $pagepath, $res);

                $res = str_replace('{page}', $page, $res);

                $res = str_replace('{text}', $text, $res);

                // if ($linklang) {echo("{aname}". $aname);echo("{icon}". $icon);echo("{accicon}".$accicon);echo("{class}". $class);echo("{title}". $title);echo("{pagelink}". $pagelink);echo("{pagepath}". $pagepath);echo("{page}". $page);echo("{text}". $text);}

                if (!$text) {
                    $text = htmlspecialchars($tag, ENT_NOQUOTES);
                }

                if ($this->GetConfigValue('youarehere_text')) {
                    if ($this->NpjTranslit($tag) == $this->NpjTranslit($this->context[$this->current_context])) {
                        $res = str_replace('####', $text, $this->GetConfigValue('youarehere_text'));
                    }
                }

                return $res;
            }

            die("ERROR: no tpl '$tpl'!");
        }

        if (!$text) {
            $text = htmlspecialchars($tag, ENT_NOQUOTES);
        }

        if ($url) {
            if ($imlink) {
                $text = "<img src=\"$imlink\" border=\"0\" title=\"$text\">";
            }

            $icon = str_replace('{theme}', $this->GetConfigValue('theme_url'), $icon);

            $res = $this->GetResourceValue('tpl.' . $tpl);

            if ($res) {
                if (!$class) {
                    $class = 'outerlink';
                }

                $res = str_replace('{icon}', $icon, $res);

                $res = str_replace('{class}', $class, $res);

                $res = str_replace('{title}', $title, $res);

                $res = str_replace('{url}', $url, $res);

                $res = str_replace('{text}', $text, $res);

                return $res;
            }
        }

        //echo ("<br>".$tag."<br>");

        //die("^([[:alnum:]]+)[:]([".$this->language["ALPHANUM_P"]."\-\_\.\+\&\=]*)$");

        return $text;
    }

    public function AddDatetime($tag)
    {
        if ($user = $this->GetUser()) {
            $show = $user['showdatetime'];
        }

        if (!$show) {
            $show = $this->GetConfigValue('show_datetime');
        }

        if ('N' != $show) {
            $_page = $this->LoadPage($tag, '', LOAD_CACHE, LOAD_META);

            return ($this->config['rewrite_mode'] ? '?' : '&amp;') . 'v=' . base_convert($this->crc16(preg_replace("/[ :\-]/", '', $_page['time'])), 10, 36);
        }

        return '';
    }

    public function crc16($string)
    {
        $crc = 0xFFFF;

        for ($x = 0, $xMax = mb_strlen($string); $x < $xMax; $x++) {
            $crc ^= ord($string[$x]);

            for ($y = 0; $y < 8; $y++) {
                if (0x0001 == ($crc & 0x0001)) {
                    $crc = (($crc >> 1) ^ 0xA001);
                } else {
                    $crc >>= 1;
                }
            }
        }

        return $crc;
    }

    public function AddSpaces($text)
    {
        if ($user = $this->GetUser()) {
            $show = $user['show_spaces'];
        }

        if (!$show) {
            $show = $this->GetConfigValue('show_spaces');
        }

        if ('N' != $show) {
            $text = preg_replace('/(' . $this->language['ALPHANUM'] . ')(' . $this->language['UPPERNUM'] . ')/', '\\1&nbsp;\\2', $text);

            $text = preg_replace('/(' . $this->language['UPPERNUM'] . ')(' . $this->language['UPPERNUM'] . ')/', '\\1&nbsp;\\2', $text);

            $text = preg_replace('/(' . $this->language['ALPHANUM'] . ")\//", '\\1&nbsp;/', $text);

            $text = preg_replace('/(' . $this->language['UPPER'] . ')&nbsp;(?=' . $this->language['UPPER'] . '&nbsp;' . $this->language['UPPERNUM'] . ')/', '\\1', $text);

            $text = preg_replace('/(' . $this->language['UPPER'] . ')&nbsp;(?=' . $this->language['UPPER'] . "&nbsp;\/)/", '\\1', $text);

            $text = preg_replace("/\/(" . $this->language['ALPHANUM'] . ')/', '/&nbsp;\\1', $text);

            $text = preg_replace('/(' . $this->language['UPPERNUM'] . ')&nbsp;(' . $this->language['UPPERNUM'] . ")($|\b)/", '\\1\\2', $text);

            $text = preg_replace('/([0-9])(' . $this->language['ALPHA'] . ')/', '\\1&nbsp;\\2', $text);

            $text = preg_replace('/(' . $this->language['ALPHA'] . ')([0-9])/', '\\1&nbsp;\\2', $text);

            $text = preg_replace('/([0-9])&nbsp;(?=[0-9])/', '\\1', $text);
        }

        if (0 === mb_strpos($text, '/')) {
            $text = $this->GetResourceValue('RootLinkIcon') . mb_substr($text, 1);
        }

        if (0 === mb_strpos($text, '!/')) {
            $text = $this->GetResourceValue('SubLinkIcon') . mb_substr($text, 2);
        }

        if (0 === mb_strpos($text, '../')) {
            $text = $this->GetResourceValue('UpLinkIcon') . mb_substr($text, 3);
        }

        return $text;
    }

    public function SlimUrl($text)
    {
        $text = $this->NpjTranslit($text, TRAN_DONTCHANGE);

        $text = str_replace('_', "'", $text);

        if (1 == $this->config['urls_underscores']) {
            $text = preg_replace('/(' . $this->language['ALPHANUM'] . ')(' . $this->language['UPPERNUM'] . ')/', '\\1∂\\2', $text);

            $text = preg_replace('/(' . $this->language['UPPERNUM'] . ')(' . $this->language['UPPERNUM'] . ')/', '\\1∂\\2', $text);

            $text = preg_replace('/(' . $this->language['UPPER'] . ')∂(?=' . $this->language['UPPER'] . '∂' . $this->language['UPPERNUM'] . ')/', '\\1', $text);

            $text = preg_replace('/(' . $this->language['UPPER'] . ')∂(?=' . $this->language['UPPER'] . "∂\/)/", '\\1', $text);

            $text = preg_replace('/(' . $this->language['UPPERNUM'] . ')∂(' . $this->language['UPPERNUM'] . ")($|\b)/", '\\1\\2', $text);

            $text = preg_replace("/\/∂(" . $this->language['UPPERNUM'] . ')/', '/\\1', $text);

            $text = str_replace('∂', '_', $text);
        }

        return $text;
    }

    public function IsWikiName($text)
    {
        return preg_match('/^' . $this->language['UPPER'] . $this->language['LOWER'] . '+' . $this->language['UPPERNUM'] . $this->language['ALPHANUM'] . '*$/', $text);
    }

    public function TrackLinkTo($tag)
    {
        $this->linktable[] = $tag;
    }

    public function GetLinkTable()
    {
        return $this->linktable;
    }

    public function ClearLinkTable()
    {
        $this->linktable = [];
    }

    public function StartLinkTracking()
    {
        $_SESSION['linktracking'] = 1;
    }

    public function StopLinkTracking()
    {
        $_SESSION['linktracking'] = 0;
    }

    public function WriteLinkTable()
    {
        // delete old link table

        $this->Query('delete from ' . $this->config['table_prefix'] . "links where from_tag = '" . quote($this->GetPageTag()) . "'");

        if ($linktable = $this->GetLinkTable()) {
            $from_tag = quote($this->GetPageTag());

            foreach ($linktable as $to_tag) {
                $lower_to_tag = mb_strtolower($to_tag);

                if (!$written[$lower_to_tag]) {
                    $query .= "('" . $from_tag . "', '" . quote($to_tag) . "', '" . quote($this->NpjTranslit($to_tag)) . "'),";

                    $written[$lower_to_tag] = 1;
                }
            }

            $this->Query('insert into ' . $this->config['table_prefix'] . 'links (from_tag, to_tag, to_supertag) VALUES ' . rtrim($query, ','));
        }
    }

    public function Header($mod = '')
    {
        // $this->StopLinkTracking();

        $result = $this->IncludeBuffered('header' . $mod . '.php', 'Theme is corrupt: ' . $this->GetConfigValue('theme'), '', 'themes/' . $this->GetConfigValue('theme') . '/appearance');

        // $this->StartLinkTracking();

        return $result;
    }

    public function Footer($mod = '')
    {
        // $this->StopLinkTracking();

        $result = $this->IncludeBuffered('footer' . $mod . '.php', 'Theme is corrupt: ' . $this->GetConfigValue('theme'), '', 'themes/' . $this->GetConfigValue('theme') . '/appearance');

        // $this->StartLinkTracking();

        return $result;
    }

    public function UseClass($class_name, $class_dir = '', $file_name = '')
    {
        if (!class_exists($class_name)) {
            if ('' == $file_name) {
                $file_name = $class_name;
            }

            if ('' == $class_dir) {
                $class_dir = $this->classes_dir;
            }

            $class_file = $class_dir . $file_name . '.php';

            if (!@is_readable($class_file)) {
                die('Cannot load class ' . $class_name . ' from ' . $class_file . ' (' . $class_dir . ')');
            }  

            require_once $class_file;
        }
    }

    // tabbed theme output routine

    public function EchoTab($link, $hint, $text, $selected = false, $bonus = '')
    {
        $xsize = $selected ? 7 : 8;

        $ysize = $selected ? 25 : 30;

        if ('' == $text) {
            return;
        } // no tab;

        if ($selected) {
            $text = "<a href=\"$link\" title=\"$hint\">" . $text . '</a>';
        }

        if (!$selected) {
            echo "<div class='TabSelected$bonus' style='background-image:url(" . $this->GetConfigValue('theme_url') . "icons/tabbg.gif);' >";
        } else {
            echo "<div class='Tab$bonus' style='background-image:url(" . $this->GetConfigValue('theme_url') . 'icons/tabbg' . ('2a' == $bonus ? 'del' : '1') . ".gif);'>";
        }

        $bonus2 = '2a' == $bonus ? 'del' : '';

        echo '<table cellspacing="0" cellpadding="0" border="0" ><tr>';

        echo "<td><img src='" . $this->GetConfigValue('theme_url') . "icons/tabr$selected" . $bonus2 . ".gif' width='$xsize' align='top' hspace='0' vspace='0' height='$ysize' alt='' border='0'></td>";

        if (!$selected) {
            echo '<td>';
        } else {
            echo "<td valign='top'>";
        }

        echo "<div class='TabText'>" . $text . '</div>';

        echo '</td>';

        echo "<td><img src='" . $this->GetConfigValue('theme_url') . "icons/tabl$selected" . $bonus2 . ".gif' width='$xsize' align='top' hspace='0' vspace='0' height='$ysize' alt='' border='0'></td>";

        echo '</tr></table>';

        echo '</div>';
    }

    // FORMS

    public function FormOpen($method = '', $tag = '', $formMethod = 'post', $formname = '', $formMore = '')
    {
        $result = '<form action="' . $this->Href($method, $tag, '', $_REQUEST['add']) . '" ' . $formMore . ' method="' . $formMethod . '" ' . ($formname ? 'name="' . $formname . '" ' : '') . ">\n";

        if (!$this->config['rewrite_mode']) {
            $result .= '<input type="hidden" name="wakka" value="' . $this->MiniHref($method, $tag, $_REQUEST['add']) . "\">\n";
        }

        return $result;
    }

    public function FormClose()
    {
        return "</form>\n";
    }

    public function NoCache()
    {
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
        header('Cache-Control: post-check=0, pre-check=0', false);

        header('Pragma: no-cache'); // HTTP/1.0
    }

    // INTERWIKI STUFF

    public function ReadInterWikiConfig()
    {
        if ($lines = file('interwiki.conf')) {
            foreach ($lines as $line) {
                if ($line = trim($line)) {
                    [$wikiName, $wikiUrl] = explode(' ', trim($line));

                    $this->AddInterWiki($wikiName, $wikiUrl);
                }
            }
        }
    }

    public function AddInterWiki($name, $url)
    {
        $this->interWiki[mb_strtolower($name)] = $url;
    }

    public function GetInterWikiUrl($name, $tag)
    {
        if ($url = $this->interWiki[mb_strtolower($name)]) {
            if (mb_strpos($url, '%s')) {
                return str_replace('%s', $tag, $url);
            }

            return $url . $tag;
        }
    }

    // REFERRERS

    public function LogReferrer($tag = '', $referrer = '')
    {
        // fill values

        if (!$tag = trim($tag)) {
            $tag = $this->GetPageTag();
        }

        if (!$referrer = trim($referrer)) {
            $referrer = $_SERVER['HTTP_REFERER'];
        }

        // check if it's coming from another site

        if ($referrer && !preg_match('/^' . preg_quote($this->GetConfigValue('base_url'), '/') . '/', $referrer)) {
            $this->Query(
                'insert into ' . $this->config['table_prefix'] . 'referrers set ' . "page_tag = '" . quote($tag) . "', " . "referrer = '" . quote($referrer) . "', " . 'time = now()'
            );
        }
    }

    public function LoadReferrers($tag = '')
    {
        return $this->LoadAll('select referrer, count(referrer) as num from ' . $this->config['table_prefix'] . 'referrers ' . ($tag = trim($tag) ? "where page_tag = '" . quote($tag) . "'" : '') . ' group by referrer order by num desc');
    }

    // PLUGINS

    public function Action($action, $params, $forceLinkTracking = 0)
    {
        $action = trim($action);

        if (!$forceLinkTracking) {
            $this->StopLinkTracking();
        }

        $result = $this->IncludeBuffered(mb_strtolower($action) . '.php', "<i>Unknown action \"$action\"</i>", $params, $this->config['action_path']);

        $this->StartLinkTracking();

        $this->NoCache();

        return $result;
    }

    public function Method($method)
    {
        if ('show' == $method) {
            $this->CacheLinks();
        }

        if (!$handler = $this->page['handler']) {
            $handler = 'page';
        }

        $methodLocation = $handler . '/' . $method . '.php';

        return $this->IncludeBuffered($methodLocation, "<i>Unknown method \"$methodLocation\"</i>", '', $this->config['handler_path']);
    }

    public function Format($text, $formatter = 'wakka', $options = '')
    {
        $text = $this->IncludeBuffered(
            'formatters/' . $formatter . '.php',
            "<i>Formatter \"$formatter\" not found</i>",
            compact('text', 'options')
        );

        if ('wacko' == $formatter && $this->GetConfigValue('default_typografica')) {
            $text = $this->IncludeBuffered('formatters/typografica.php', "<i>Formatter \"$formatter\" not found</i>", compact('text'));
        }

        return $text;
    }

    // USERS

    public function LoadUser($name, $password = 0)
    {
        $user = $this->LoadSingle(
            'select * from ' . $this->config['user_table'] . " where name = '" . quote($name) . "' " . (0 === $password ? '' : "and password = '" . quote($password) . "'") . ' limit 1'
        );

        if ($user) {
            $user['options'] = $this->DecomposeOptions($user['more']);
        }

        return $user;
    }

    public function LoadUsers()
    {
        return $this->LoadAll('select * from ' . $this->config['user_table'] . ' order by binary name');
    }

    public function GetUserName()
    {
        if ($user = $this->GetUser()) {
            $name = $user['name'];
        } elseif ($this->_userhost) {
            $name = $this->_userhost;
        } else {
            if ('WIN' == mb_strtoupper(mb_substr(PHP_OS, 0, 3))) {
                $name = $_SERVER['REMOTE_ADDR'];
            } elseif (!$name = $this->_gethostbyaddr($_SERVER['REMOTE_ADDR'])) {
                $name = $_SERVER['REMOTE_ADDR'];
            }

            $this->_userhost = $name;
        }

        return $name;
    }

    public function _gethostbyaddr($ip)
    {
        if ($this->GetConfigValue('allow_gethostbyaddr')) {
            return gethostbyaddr($ip);
        }

        return false;
    }

    public function GetUser()
    {
        return $_SESSION[$this->config['cookie_prefix'] . 'user'];
    }

    public function SetUser($user, $setcookie = 1)
    {
        $_SESSION[$this->config['cookie_prefix'] . 'user'] = $user;

        if ($setcookie) {
            $this->SetPersistentCookie('name', $user['name'], 1);
        }
    }

    public function LogUserIn($user)
    {
        $this->SetPersistentCookie('name', $user['name'], 1);

        $this->SetPersistentCookie('password', $user['password']);
    }

    public function LogoutUser()
    {
        $_SESSION[$this->config['cookie_prefix'] . 'user'] = '';

        $this->DeleteCookie('name');

        $this->DeleteCookie('password');
    }

    public function UserWantsComments()
    {
        if (!$user = $this->GetUser()) {
            return false;
        }

        return ('Y' == $user['show_comments']);
    }

    public function UserWantsFiles()
    {
        if (!$user = $this->GetUser()) {
            return false;
        }

        return ('Y' == $user['options']['show_files']);
    }

    public function DecomposeOptions($more)
    {
        $b = [];

        $opts = explode($this->optionSplitter, $more);

        foreach ($opts as $o) {
            $params = explode($this->valueSplitter, trim($o));

            $b[$params[0]] = $params[1];
        }

        return $b;
    }

    public function ComposeOptions($options)
    {
        $opts = [];

        foreach ($options as $k => $v) {
            $opts[] = $k . $this->valueSplitter . $v;
        }

        return implode($this->optionSplitter, $opts);
    }

    // COMMENTS

    public function LoadComments($tag)
    {
        return $this->LoadAll('select * from ' . $this->config['table_prefix'] . "pages where comment_on = '" . quote($tag) . "' and latest = 'Y' order by time");
    }

    // ACCESS CONTROL

    public function IsAdmin()
    {
        if (is_array($this->config['aliases'])) {
            $al = $this->config['aliases'];

            $adm = explode("\n", $al['Admins']);

            if (in_array($this->GetUserName(), $adm, true)) {
                return true;
            }
        }

        return false;
    }

    // returns true if logged in user is owner of current page, or page specified in $tag

    public function UserIsOwner($tag = '')
    {
        // check if user is logged in

        if (!$this->GetUser()) {
            return false;
        }

        // set default tag

        if (!$tag = trim($tag)) {
            $tag = $this->GetPageTag();
        }

        // check if user is owner

        if ($this->GetPageOwner($tag) == $this->GetUserName()) {
            return true;
        }
    }

    public function GetPageOwnerFromComment()
    {
        if ($this->page['comment_on']) {
            return $this->GetPageOwner($this->page['comment_on']);
        }

        return false;
    }

    public function GetPageOwner($tag = '', $time = '')
    {
        if (!$tag = trim($tag)) {
            $tag = $this->GetPageTag();
        }

        if ($page = $this->LoadPage($tag, $time, LOAD_CACHE, LOAD_META)) {
            return $page['owner'];
        }
    }

    public function SetPageOwner($tag, $user)
    {
        // check if user exists

        if (!$this->LoadUser($user)) {
            return;
        }

        // updated latest revision with new owner

        $this->Query('update ' . $this->config['table_prefix'] . "pages set owner = '" . quote($user) . "' where tag = '" . quote($tag) . "' and latest = 'Y' limit 1");
    }

    public function LoadAcl($tag, $privilege, $useDefaults = 1)
    {
        $supertag = $this->NpjTranslit($tag);

        if ($cachedACL = $this->GetCachedACL($supertag, $privilege, $useDefaults)) {
            $acl = $cachedACL;
        }

        if (!$acl) {
            if ($cachedACL = $this->GetCachedACL($tag, $privilege, $useDefaults)) {
                $acl = $cachedACL;
            }

            if (!$acl) {
                $acl = $this->LoadSingle(
                    'select * from ' . $this->config['table_prefix'] . 'acls where ' . "supertag = '" . quote($supertag) . "' " . "and privilege = '" . quote($privilege) . "' limit 1"
                );

                if (!$acl) {
                    $acl = $this->LoadSingle(
                        'select * from ' . $this->config['table_prefix'] . 'acls where ' . "page_tag = '" . quote($tag) . "' " . "and privilege = '" . quote($privilege) . "' limit 1"
                    );

                    /* if ($acl)
{
$this->Query( "update ".$this->config["table_prefix"]."acls ".
"set supertag='".$supertag."' where page_tag = '".$tag."';" );
$acl["supertag"]=$supertag;
}
*/
                }

                if (!$acl && $useDefaults) {
                    $acl = [
                        'supertag' => $supertag,
'page_tag' => $tag,
'privilege' => $privilege,
'list' => $this->GetConfigValue('default_' . $privilege . '_acl'),
'time' => date('YmdHis'),
'default' => '1',
                    ];
                }

                $this->CacheACL($supertag, $privilege, $useDefaults, $acl);
            }
        }

        return $acl;
    }

    public function SaveAcl($tag, $privilege, $list)
    {
        $supertag = $this->NpjTranslit($tag);

        if ($this->LoadAcl($tag, $privilege, 0)) {
            $this->Query(
                'update ' . $this->config['table_prefix'] . "acls set list = '" . quote(trim(str_replace("\r", '', $list))) . "' where supertag = '" . quote($supertag) . "' and privilege = '" . quote($privilege) . "' "
            );
        } else {
            $this->Query(
                'insert into ' . $this->config['table_prefix'] . "acls set list = '" . quote(trim(str_replace("\r", '', $list))) . "', " . "supertag = '" . quote($supertag) . "', " . "page_tag = '" . quote($tag) . "', " . "privilege = '" . quote($privilege) . "'"
            );
        }
    }

    public function RemoveAcls($tag)
    {
        return $this->Query('delete from ' . $this->config['table_prefix'] . "acls where page_tag = '" . quote($tag) . "' ");
    }

    public function RemovePage($tag)
    {
        return $this->Query('delete from ' . $this->config['table_prefix'] . "revisions where tag = '" . quote($tag) . "' ") && $this->Query('delete from ' . $this->config['table_prefix'] . "pages where tag = '" . quote($tag) . "' ");
    }

    public function RemoveComments($tag)
    {
        return $this->Query('delete from ' . $this->config['table_prefix'] . "pages where comment_on = '" . quote($tag) . "' ");
    }

    public function RemoveWatches($tag)
    {
        return $this->Query('delete from ' . $this->config['table_prefix'] . "pagewatches where tag = '" . quote($tag) . "' ");
    }

    public function RemoveLinks($tag)
    {
        return $this->Query('delete from ' . $this->config['table_prefix'] . "links where from_tag = '" . quote($tag) . "' ");
    }

    public function RemoveReferrers($tag)
    {
        return $this->Query('delete from ' . $this->config['table_prefix'] . "referrers where page_tag = '" . quote($tag) . "' ");
    }

    // WATCHES

    public function IsWatched($user, $tag)
    {
        return $this->LoadSingle('select * from ' . $this->config['table_prefix'] . "pagewatches where user = '" . quote($user) . "' and tag = '" . quote($tag) . "'");
    }

    public function SetWatch($user, $tag)
    {
        return $this->Query('insert into ' . $this->config['table_prefix'] . "pagewatches (user,tag) values ( '" . quote($user) . "', '" . quote($tag) . "')");
        // TIMESTAMP type is filled automatically by MySQL
    }

    public function ClearWatch($user, $tag)
    {
        return $this->Query('delete from ' . $this->config['table_prefix'] . "pagewatches where user = '" . quote($user) . "' and tag = '" . quote($tag) . "'");
    }

    //aliases stuff

    public function ReplaceAliases($acl)
    {
        if (!is_array($this->config['aliases'])) {
            return $acl;
        }

        foreach ($this->config['aliases'] as $key => $val) {
            $aliases[mb_strtolower($key)] = $val;
        }

        do {
            $list = [];

            $replaced = 0;

            $lines = explode("\n", $acl);

            foreach ($lines as $line) {
                $linel = $line;

                // check for inversion character "!"

                if (preg_match("/^\!(.*)$/", $line, $matches)) {
                    $negate = 1;

                    $linel = $matches[1];
                } else {
                    $negate = 0;
                }

                $linel = mb_strtolower(trim($linel));

                if ($aliases[$linel]) {
                    foreach (explode("\n", $aliases[$linel]) as $item) {
                        $item = trim($item);

                        $list[] = ($negate) ? '!' . $item : $item;
                    }

                    $replaced++;
                } else {
                    $list[] = $line;
                }
            }

            $acl = implode("\n", $list);
        } while ($replaced > 0);

        return $acl;
    }

    // returns true if $user (defaults to current user) has access to $privilege on $page_tag (defaults to current page)

    public function HasAccess($privilege, $tag = '', $user = '')
    {
        // see whether user is registered and logged in

        if ('guest@wacko' != $user) {
            if ($user = $this->GetUser()) {
                $registered = true;
            }

            $user = mb_strtolower($this->GetUserName());

            if (!$registered) {
                $user = 'guest@wacko';
            }
        }

        if (!$tag = trim($tag)) {
            $tag = $this->GetPageTag();
        }

        // load acl

        $acl = $this->LoadAcl($tag, $privilege);

        $this->_acl = $acl;

        // if current user is owner, return true. owner can do anything!

        if ('guest@wacko' != $user) {
            if ($this->UserIsOwner($tag)) {
                return true;
            }
        }

        return $this->CheckACL($user, $acl['list'], true);
    }

    public function CheckACL($user, $acl_list, $copy_to_this_acl = false, $debug = 0)
    {
        $user = mb_strtolower($user);

        // replace groups

        $acl = str_replace(' ', '', mb_strtolower($this->ReplaceAliases($acl_list)));

        if ($copy_to_this_acl) {
            $this->_acl['list'] = $acl;
        }

        $acls = "\n" . $acl . "\n";

        if ('guest@wacko' == $user || '' == $user) {
            if (false === ($pos = mb_strpos($acls, '*'))) {
                return false;
            }

            if ('!' != $acls[$pos - 1]) {
                return true;
            }

            return false;
        }

        $upos = mb_strpos($acls, "\n" . $user . "\n");

        $aupos = mb_strpos($acls, "\n!" . $user . "\n");

        $spos = mb_strpos($acls, '*');

        $bpos = mb_strpos($acls, '$');

        $bpos2 = mb_strpos($acls, 'І'); //deprecate it!!

        if (false !== $aupos) {
            return false;
        }

        if (false !== $upos) {
            return true;
        }

        if (false !== $spos) {
            if ('!' == $acls[$spos - 1]) {
                return false;
            }
        }

        if (false !== $bpos) {
            if ('!' == $acls[$bpos - 1]) {
                return false;
            }
        }

        if (false !== $bpos2) {
            if ('!' == $acls[$bpos2 - 1]) {
                return false;
            }
        }

        if (false !== $spos) {
            return true;
        }

        if (false !== $bpos) {
            if ('guest@wacko' == $user || '' == $user) {
                return false;
            }

            return true;
        }

        if (false !== $bpos2) {
            if ('guest@wacko' == $user || '' == $user) {
                return false;
            }

            return true;
        }

        return false;
    }

    // XML

    public function WriteRecentChangesXML()
    {
        $xml = "<?xml version=\"1.0\" encoding=\"windows-1251\"?>\n";

        $xml .= "<rss version=\"0.92\">\n";

        $xml .= "<channel>\n";

        $xml .= '<title>' . $this->GetConfigValue('wakka_name') . " - RecentChanges</title>\n";

        $xml .= '<link>' . $this->GetConfigValue('root_url') . "</link>\n";

        $xml .= '<description>Recent changes to the ' . $this->GetConfigValue('wakka_name') . " WackoWiki</description>\n";

        $xml .= "<language>en-us</language>\n";

        if ($pages = $this->LoadRecentlyChanged()) {
            foreach ($pages as $i => $page) {
                if ($this->config['hide_locked']) {
                    $access = $this->HasAccess('read', $page['tag'], 'guest@wacko');
                }

                if ($access && ($count < 30)) {
                    $count++;

                    $xml .= "<item>\n";

                    $xml .= '<title>' . $page['tag'] . "</title>\n";

                    $xml .= '<link>' . $this->Href('show', $page['tag'], 'time=' . urlencode($page['time'])) . "</link>\n";

                    $xml .= '<description>' . $page['time'] . ' by ' . $page['user'] . "</description>\n";

                    $xml .= "</item>\n";
                }
            }
        }

        $xml .= "</channel>\n";

        $xml .= "</rss>\n";

        $filename = 'xml/recentchanges_' . preg_replace('/[^a-zA-Z0-9]/', '', mb_strtolower($this->GetConfigValue('wakka_name'))) . '.xml';

        $fp = @fopen($filename, 'wb');

        if ($fp) {
            fwrite($fp, $xml);

            fclose($fp);
        }
    }

    // MAINTENANCE

    public function Maintenance()
    {
        // purge referrers

        if ($days = $this->GetConfigValue('referrers_purge_time')) {
            $this->Query('delete from ' . $this->config['table_prefix'] . "referrers where time < date_sub(now(), interval '" . quote($days) . "' day)");
        }

        // purge old page revisions

        if ($days = $this->GetConfigValue('pages_purge_time')) {
            $this->Query('delete from ' . $this->config['table_prefix'] . "revisions where time < date_sub(now(), interval '" . quote($days) . "' day) and latest = 'N'");
        }
    }

    public function GetDefaultBookmarks($lang, $what = 'default')
    {
        if (!$lang) {
            $lang = $this->config['language'];
        }

        if (is_array($this->config[$what . '_bookmarks']) && isset($this->config[$what . '_bookmarks'][$lang])) {
            return $this->config[$what . '_bookmarks'][$lang];
        } elseif (isset($this->config[$what . '_bookmarks']) && !is_array($this->config[$what . '_bookmarks']) && ($this->config['language'] == $lang)) {
            return $this->config[$what . '_bookmarks'];
        }

        return $this->GetResourceValue($what . '_bookmarks', $lang, false);
    }

    public function SetBookmarks($set = BM_AUTO)
    {
        $user = $this->GetUser();

        if ($set || !($bookmarks = $this->GetBookmarks())) {
            $bookmarks = $user['bookmarks'] ?: $this->GetDefaultBookmarks($user['lang']);

            if (BM_DEFAULT == $set) {
                $bookmarks = $this->GetDefaultBookmarks($user['lang']);
            }

            $dummy = $this->Format($bookmarks, 'wacko');

            $this->ClearLinkTable();

            $this->StartLinkTracking();

            $dummy = $this->Format($dummy, 'post_wacko');

            $this->StopLinkTracking();

            $bmlinks = $this->GetLinkTable();

            $bookmarks = explode("\n", $bookmarks);

            for ($i = 0, $iMax = count($bmlinks); $i < $iMax; $i++) {
                $bmlinks[$i] = $this->NpjTranslit($bmlinks[$i]);
            }

            $_SESSION['bookmarks'] = $bookmarks;

            $_SESSION['bookmarklinks'] = $bmlinks;

            $_SESSION['bookmarksfmt'] = $this->Format(implode(' | ', $bookmarks), 'wacko');
        }

        if ($_REQUEST['addbookmark'] && $user) {
            $bookmark = '((' . $this->GetPageTag() . ($user['lang'] != $this->pagelang ? ' @@' . $this->pagelang : '') . '))';

            if (!in_array($bookmark, $bookmarks, true)) {
                $bookmarks[] = $bookmark;

                $this->Query(
                    'update ' . $this->config['user_table'] . ' set ' . "bookmarks = '" . quote(implode("\n", $bookmarks)) . "' " . "where name = '" . $user['name'] . "' limit 1"
                );

                $this->SetUser($this->LoadUser($user['name']));
            }

            $_SESSION['bookmarks'] = $bookmarks;

            //$_SESSION["bookmarksfmt"] = $this->Format($this->Format(implode(" | ", $bookmarks), "wacko"), "post_wacko");

            $_SESSION['bookmarksfmt'] = $this->Format(implode(' | ', $bookmarks), 'wacko');
        }

        if ($_REQUEST['removebookmark'] && $user) {
            foreach ($bookmarks as $bm) {
                $dummy = $this->Format($bm, 'wacko');

                $this->ClearLinkTable();

                $this->StartLinkTracking();

                $dummy = $this->Format($dummy, 'post_wacko');

                $this->StopLinkTracking();

                $bml = $this->GetLinkTable();

                if ($this->GetPageSuperTag() != $this->NpjTranslit($bml[0])) {
                    $newbm[] = $bm;
                }
            }

            $bookmarks = $newbm;

            $this->Query(
                'update ' . $this->config['user_table'] . ' set ' . "bookmarks = '" . quote(implode("\n", $bookmarks)) . "' " . "where name = '" . $user['name'] . "' limit 1"
            );

            $this->SetUser($this->LoadUser($user['name']));

            $_SESSION['bookmarks'] = $bookmarks;

            // $_SESSION["bookmarksfmt"] = $this->Format($this->Format(implode(" | ", $bookmarks), "wacko"), "post_wacko");

            $_SESSION['bookmarksfmt'] = $this->Format(implode(' | ', $bookmarks), 'wacko');
        }
    }

    public function GetBookmarks()
    {
        return $_SESSION['bookmarks'];
    }

    public function GetBookmarksFormatted()
    {
        return $_SESSION['bookmarksfmt'];
    }

    public function GetBookmarkLinks()
    {
        return $_SESSION['bookmarklinks'];
    }

    // THE BIG EVIL NASTY ONE!

    public function Run($tag, $method = '')
    {
        if (!($this->GetMicroTime() % 3)) {
            $this->Maintenance();
        }

        $this->ReadInterWikiConfig();

        if (!$this->GetConfigValue('multilanguage')) {
            $this->SetLanguage($this->GetConfigValue('language'));
        }

        foreach ($this->search_engines as $engine) {
            if (mb_stristr($_SERVER['HTTP_USER_AGENT'], $engine)) {
                $this->config['default_showdatetime'] = 0;

                $this->config['show_datetime'] = 'N';
            }
        }

        // do our stuff!

        if (!$this->method = trim($method)) {
            $this->method = 'show';
        }

        if (!$this->tag = trim($tag)) {
            $this->Redirect($this->Href('', $this->config['root_page']));
        }

        if ((!$this->GetUser() && $_COOKIE[$this->config['cookie_prefix'] . 'name']) && ($user = $this->LoadUser($_COOKIE[$this->config['cookie_prefix'] . 'name'], $_COOKIE[$this->config['cookie_prefix'] . 'password']))) {
            $this->SetUser($user);
        }

        $user = $this->GetUser();

        $this->userlang = ($user['lang'] ?: $this->GetConfigValue('language'));

        if (!($this->userlang)) {
            $this->userlang = 'en';
        }

        if ($user['options']['theme']) {
            $this->config['theme'] = $user['options']['theme'];

            $this->config['theme_url'] = $this->config['root_url'] . 'themes/' . $this->config['theme'] . '/';
        }

        $this->LoadAllLanguages();

        $this->LoadResource($this->userlang);

        $this->SetResource($this->userlang);

        $this->SetLanguage($this->userlang);

        $wacko = &$this;

        if (!preg_match('/^[' . $this->language['ALPHANUM_P'] . "\!]+$/", $tag)) {
            $tag = $this->tryUtfDecode($tag);
        }

        // if (!$_REQUEST["add"]=="1" || $this->method=="watch" )

        $tag = str_replace("'", '_', str_replace('\\', '', str_replace('_', '', $tag)));

        $tag = preg_replace('/[^' . $this->language['ALPHANUM_P'] . "\_\-]/", '', $tag);

        $this->tag = $this->Translit($tag, 1);

        $this->supertag = $this->NpjTranslit($tag);

        $page = $this->LoadPage($this->tag, $_REQUEST['time']);

        if ($this->GetConfigValue('outlook_workaround') && !$page) {
            $page = $this->LoadPage($this->supertag . "'", $_REQUEST['time']);
        }

        $this->SetPage($page);

        $this->LogReferrer();

        $this->SetBookmarks();

        if (!$this->GetUser()) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', strtotime($this->page['time']) + 120) . ' GMT');
        }

        if (preg_match("/(\.xml)$/", $this->method)) {
            print($this->Method($this->method));
        } elseif (preg_match('/print$/', $this->method)) {
            print($this->Header('print') . $this->Method($this->method) . $this->Footer('print'));
        } elseif (preg_match('/msword$/', $this->method)) {
            print($this->Header('msword') . $this->Method($this->method) . $this->Footer('print'));
        } else {
            $this->current_context++;

            $this->context[$this->current_context] = $this->tag;

            $data = $this->Method($this->method);

            $this->current_context--;

            print($this->Header() . $data . $this->Footer());
        }

        return $this->tag;
    }

    public function AvailableThemes()
    {
        $handle = opendir('themes');

        while (false !== ($file = readdir($handle))) {
            if ('.' != $file && 'CVS' != $file && '..' != $file && is_dir('themes/' . $file)) {
                $themelist[] = $file;
            }
        }

        closedir($handle);

        if ($allow = $this->GetConfigValue('allow_themes')) {
            $ath = explode(',', $allow);

            if (is_array($ath) && $ath[0]) {
                $themelist = array_intersect($ath, $themelist);
            }
        }

        return $themelist;
    }

    // TOC manipulations

    public function SetTocArray($toc)
    {
        $this->body_toc = '';

        foreach ($toc as $k => $v) {
            $toc[$k] = implode('<poloskuns,col>', $v);
        }

        $this->body_toc = implode('<poloskuns,row>', $toc);
    }

    public function BuildToc($tag, $from, $to, $num, $link = -1)
    {
        if (isset($this->tocs[$tag])) {
            return $this->tocs[$tag];
        }

        $page = $this->LoadPage($tag);

        if (-1 === $link) {
            $_link = ($this->page['tag'] != $page['tag']) ? $this->Href('', $page['tag']) : '';
        } else {
            $_link = $link;
        }

        $toc = explode('<poloskuns,row>', $page['body_toc']);

        foreach ($toc as $k => $v) {
            $toc[$k] = explode('<poloskuns,col>', $v);
        }

        $_toc = [];

        foreach ($toc as $k => $v) {
            if (99999 == $v[2]) {
                if (!in_array($v[0], $this->toc_context, true)) {
                    if (!($v[0] == $this->tag)) {
                        $this->toc_context[] = $v[0];

                        $_toc = array_merge($_toc, $this->BuildToc($v[0], $from, $to, $num, $link));

                        array_pop($this->toc_context);
                    }
                }
            } elseif (77777 == $v[2]) {
                $toc[$k][3] = $_link;

                $_toc[] = &$toc[$k];
            } elseif (($v[2] >= $from) && ($v[2] <= $to)) {
                $toc[$k][3] = $_link;

                $_toc[] = &$toc[$k];

                $toc[$k][1] = $this->Format($toc[$k][1], 'post_wacko');
            }
        }

        $this->tocs[$tag] = $_toc;

        return $_toc;
    }

    public function NumerateToc($what) // numerating toc using prepared "$this->post_wacko_toc"
    {
        if (!is_array($this->post_wacko_action)) {
            return $what;
        }

        // #1. hash toc

        $hash = [];

        foreach ($this->post_wacko_toc as $v) {
            $hash[$v[0]] = $v;
        }

        $this->post_wacko_toc_hash = &$hash;

        if ($this->post_wacko_action['toc']) {
            // #2. find all <a></a><hX> & guide them in subroutine

            // notice that complex regexp is copied & duplicated in formatters/paragrafica (subject to refactor)

            $what = preg_replace_callback(
                '!(<a name="(h[0-9]+-[0-9]+)"></a><h([0-9])>(.*?)</h\\3>)!i',
                [&$this, 'NumerateTocCallbackToc'],
                $what
            );
        }

        if ($this->post_wacko_action['p']) {
            // #2. find all <a></a><p...> & guide them in subroutine

            // notice that complex regexp is copied & duplicated in formatters/paragrafica (subject to refactor)

            $what = preg_replace_callback(
                '!(<a name="(p[0-9]+-[0-9]+)"></a><p([^>]+)>(.+?)</p>)!is',
                [&$this, 'NumerateTocCallbackP'],
                $what
            );
        }

        return $what;
    }

    public function NumerateTocCallbackToc($matches)
    {
        return '<a name="' . $matches[2] . '"></a><h' . $matches[3] . '>' . ($this->post_wacko_toc_hash[$matches[2]][1] ?: $matches[4]) . '</h' . $matches[3] . '>';
    }

    public $paragrafica_styles = [
        'before' => ['_before' => '', '_after' => '', 'before' => "<span class='pmark'>[##]</span><br>", 'after' => ''],
'after' => ['_before' => '', '_after' => '', 'before' => '', 'after' => " <span class='pmark'>[##]</span>"],
'right' => ['_before' => "<div class='pright'><div class='p-'>&nbsp;<span class='pmark'>[##]</span></div><div class='pbody-'>", '_after' => '</div></div>', 'before' => '', 'after' => ''],
'left' => ['_before' => "<div class='pleft'><div class='p-'><span class='pmark'>[##]</span>&nbsp;</div><div class='pbody-'>", '_after' => '</div></div>', 'before' => '', 'after' => ''],
    ];

    public $paragrafica_patches = [
        'before' => ['before'],
'after' => ['after'],
'right' => ['_before'],
'left' => ['_before'],
    ];

    public function NumerateTocCallbackP($matches)
    {
        $before = '';

        $after = '';

        if (!($style = $this->paragrafica_styles[$this->post_wacko_action['p']])) {
            $this->post_wacko_action['p'] = 'before';

            $style = $this->paragrafica_styles['before'];
        }

        $len = mb_strlen('' . $this->post_wacko_maxp);

        $link = '<a href="#' . $matches[2] . '">' . str_pad($this->post_wacko_toc_hash[$matches[2]][66], $len, '0', STR_PAD_LEFT) . '</a>';

        foreach ($this->paragrafica_patches[$this->post_wacko_action['p']] as $v) {
            $style[$v] = str_replace('##', $link, $style[$v]);
        }

        return $style['_before'] . '<a name="' . $matches[2] . '"></a><p' . $matches[3] . '>' . $style['before'] . $matches[4] . $style['after'] . '</p>' . $style['_after'];
    }

    // BREADCRUMBS -- additional navigation added with WackoClusters

    public function GetPagePath()
    {
        $steps = explode('/', $this->tag);

        $result = '';

        $links = [];

        $_links = [];

        for ($i = 0; $i < count($steps) - 1; $i++) {
            if (0 == $i) {
                $prev = '';
            } else {
                $prev = $links[$i - 1] . '/';
            }

            $links[] = $prev . $steps[$i];
        }

        for ($i = 0; $i < count($steps) - 1; $i++) {
            $result .= $this->Link($links[$i], '', $steps[$i]) . '/';
        }

        $result .= $steps[count($steps) - 1];

        return $result;
    }

    public function RenamePage($tag, $NewTag, $NewSuperTag = '')
    {
        if ('' == $NewSuperTag) {
            $NewSuperTag = $this->NpjTranslit($NewTag);
        }

        return $this->Query('update ' . $this->config['table_prefix'] . "revisions set tag = '" . quote($NewTag) . "', supertag = '" . quote($NewSuperTag) . "' where tag = '" . quote($tag) . "' ")
               && $this->Query(
                   'update ' . $this->config['table_prefix'] . "pages set tag = '" . quote($NewTag) . "', supertag = '" . quote($NewSuperTag) . "' where tag = '" . quote($tag) . "' "
               );
    }

    public function RenameAcls($tag, $NewTag, $NewSuperTag = '')
    {
        if ('' == $NewSuperTag) {
            $NewSuperTag = $this->NpjTranslit($NewTag);
        }

        return $this->Query('update ' . $this->config['table_prefix'] . "acls set page_tag = '" . quote($NewTag) . "', supertag = '" . quote($NewSuperTag) . "' where page_tag = '" . quote($tag) . "' ");
    }

    public function RenameComments($tag, $NewTag, $NewSuperTag = '')
    {
        return $this->Query('update ' . $this->config['table_prefix'] . "pages set comment_on = '" . quote($NewTag) . "' where comment_on = '" . quote($tag) . "' ");
    }

    public function RenameWatches($tag, $NewTag, $NewSuperTag = '')
    {
        return $this->Query('update ' . $this->config['table_prefix'] . "pagewatches set tag = '" . quote($NewTag) . "' where tag = '" . quote($tag) . "' ");
    }

    public function RenameLinks($tag)
    {
        return $this->Query('update ' . $this->config['table_prefix'] . "links set from_tag = '" . quote($NewTag) . "' where from_tag = '" . quote($tag) . "' ");
    }

    public function CheckFileExists($filename, $unwrapped_tag = '')
    {
        if ('' == $unwrapped_tag) {
            $page_id = 0;
        } else {
            $page = $this->LoadPage($unwrapped_tag, '', LOAD_CACHE, LOAD_META);

            $page_id = $page['id'];

            if (!$page_id) {
                return false;
            }
        }

        if (!($file = $this->filesCache[$page_id][$filename])) {
            $what = $this->LoadAll(
                'select id, filename, filesize, description, picture_w, picture_h, file_ext from ' . $this->config['table_prefix'] . 'upload where ' . "page_id = '" . quote($page_id) . "' and filename='" . quote($filename) . "'"
            );

            if (0 == count($what)) {
                return false;
            }

            $file = $what[0];

            $this->filesCache[$page_id][$filename] = $file;
        }

        return $file;
    }

    public function GetKeywords()
    {
        if ($this->page['keywords']) {
            return $this->page['keywords'];
        }

        return $this->GetConfigValue('meta_keywords');
    }

    public function GetDescription()
    {
        if ($this->page['description']) {
            return $this->page['description'];
        }

        return $this->GetConfigValue('meta_description');
    }
}
