<?php

class Wakka
{
    public $dblink;

    public $page;

    public $tag;

    public $queryLog = [];

    public $interWiki = [];

    public $VERSION;

    public $myts;

    public $xoopsConfig;

    // constructor

    public function __construct($config, $xoopsConfig)
    {
        $this->config = $config;

        $this->db = XoopsDatabaseFactory::getDatabaseConnection();

        $this->VERSION = WAKKA_VERSION;

        $this->myts = MyTextSanitizer::getInstance();

        $this->xoopsConfig = $xoopsConfig;
    }

    public function LoadSingle($query)
    {
        if ($data = $this->LoadAll($query)) {
            return $data[0];
        }
    }

    public function LoadAll($query)
    {
        if ($r = $this->db->query($query)) {
            while (false !== ($row = $this->db->fetchArray($r))) {
                $data[] = $row;
            }

            $this->db->freeRecordSet($r);
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

            if (file_exists($fullfilename)) {
                if (is_array($vars)) {
                    extract($vars);
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

    public function GetWakkaName()
    {
        return $this->GetConfigValue('wakka_name');
    }

    public function GetWakkaVersion()
    {
        return $this->VERSION;
    }

    // PAGES

    public function LoadPage($tag, $time = '', $cache = 1)
    {
        // retrieve from cache

        if (!$time && $cache && ($cachedPage = $this->GetCachedPage($tag))) {
            $page = $cachedPage;
        }

        // load page

        if (!$page) {
            $page = $this->LoadSingle('select * from ' . $this->config['table_prefix'] . "pages where tag = '" . $this->escape_string($tag) . "' " . ($time ? "and time = '" . $this->escape_string($time) . "'" : "and latest = 'Y'") . ' limit 1');
        }

        // cache result

        if (!$time) {
            $this->CachePage($page);
        }

        return $page;
    }

    public function GetCachedPage($tag)
    {
        return $this->pageCache[$tag];
    }

    public function CachePage($page)
    {
        $this->pageCache[$page['tag']] = $page;
    }

    public function SetPage($page)
    {
        $this->page = $page;

        if ($this->page['tag']) {
            $this->tag = $this->page['tag'];
        }
    }

    public function LoadPageById($id)
    {
        return $this->LoadSingle('select * from ' . $this->config['table_prefix'] . "pages where id = '" . $this->escape_string($id) . "' limit 1");
    }

    public function LoadRevisions($page)
    {
        return $this->LoadAll('select * from ' . $this->config['table_prefix'] . "pages where tag = '" . $this->escape_string($page) . "' order by time desc");
    }

    public function LoadPagesLinkingTo($tag)
    {
        return $this->LoadAll('select from_tag as tag from ' . $this->config['table_prefix'] . "links where to_tag = '" . $this->escape_string($tag) . "' order by tag");
    }

    public function LoadRecentlyChanged()
    {
        if ($pages = $this->LoadAll('select * from ' . $this->config['table_prefix'] . "pages where latest = 'Y' and comment_on = '' order by time desc")) {
            foreach ($pages as $page) {
                $this->CachePage($page);
            }

            return $pages;
        }
    }

    public function LoadWantedPages()
    {
        return $this->LoadAll(
            'select distinct '
            . $this->config['table_prefix']
            . 'links.to_tag as tag,count('
            . $this->config['table_prefix']
            . 'links.from_tag) as count from '
            . $this->config['table_prefix']
            . 'links left join '
            . $this->config['table_prefix']
            . 'pages on '
            . $this->config['table_prefix']
            . 'links.to_tag = '
            . $this->config['table_prefix']
            . 'pages.tag where '
            . $this->config['table_prefix']
            . 'pages.tag is NULL group by tag order by count desc'
        );
    }

    public function LoadOrphanedPages()
    {
        return $this->LoadAll(
            'select distinct tag from '
            . $this->config['table_prefix']
            . 'pages left join '
            . $this->config['table_prefix']
            . 'links on '
            . $this->config['table_prefix']
            . 'pages.tag = '
            . $this->config['table_prefix']
            . 'links.to_tag where '
            . $this->config['table_prefix']
            . 'links.to_tag is NULL and '
            . $this->config['table_prefix']
            . "pages.comment_on = '' order by tag"
        );
    }

    public function LoadPageTitles()
    {
        return $this->LoadAll('select distinct tag from ' . $this->config['table_prefix'] . 'pages order by tag');
    }

    public function LoadAllPages()
    {
        return $this->LoadAll('select * from ' . $this->config['table_prefix'] . "pages where latest = 'Y' order by tag");
    }

    public function FullTextSearch($phrase)
    {
        return $this->LoadAll('select * from ' . $this->config['table_prefix'] . "pages where latest = 'Y' and match(tag, body) against('" . $this->escape_string($phrase) . "')");
    }

    public function SavePage($tag, $body, $comment_on = '')
    {
        // get current user

        $user = $this->GetUserName();

        //die($tag);

        // TODO: check write privilege

        if ($this->HasAccess('write', $tag)) {
            // is page new?

            if (!$oldPage = $this->LoadPage($tag)) {
                /* // create default write acl. store empty write ACL for comments.
                $this->SaveAcl($tag, "write", ($comment_on ? "" : $this->GetConfigValue("default_write_acl")));
                // create default read acl
                $this->SaveAcl($tag, "read", $this->GetConfigValue("default_read_acl"));
                // create default comment acl.
                $this->SaveAcl($tag, "comment", $this->GetConfigValue("default_comment_acl"));
                // current user is owner; if user is logged in! otherwise, no owner.*/

                if ($this->GetUser()) {
                    $owner = $user;
                }
            } else {
                // aha! page isn't new. keep owner!

                $owner = $oldPage['owner'];
            }

            // set all other revisions to old

            $this->db->query('update ' . $this->config['table_prefix'] . "pages set latest = 'N' where tag = '" . $this->escape_string($tag) . "'");

            // add new revision

            $this->db->query(
                'insert into '
                . $this->config['table_prefix']
                . 'pages set '
                . "tag = '"
                . $this->escape_string($tag)
                . "', "
                . ($comment_on ? "comment_on = '" . $this->escape_string($comment_on) . "', " : '')
                . 'time = now(), '
                . "owner = '"
                . $this->escape_string($owner)
                . "', "
                . "user = '"
                . $this->escape_string($user)
                . "', "
                . "latest = 'Y', "
                . "body = '"
                . $this->escape_string(trim($body))
                . "'"
            );
        }

        $this->WriteRecentChangesXML();
    }

    // COOKIES

    public function SetSessionCookie($name, $value)
    {
        setcookie($name, $value, 0, '/');

        $_COOKIE[$name] = $value;
    }

    public function SetPersistentCookie($name, $value)
    {
        setcookie($name, $value, time() + 90 * 24 * 60 * 60, '/');

        $_COOKIE[$name] = $value;
    }

    public function DeleteCookie($name)
    {
        setcookie($name, '', 1, '/');

        $_COOKIE[$name] = '';
    }

    public function GetCookie($name)
    {
        return $_COOKIE[$name];
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

    // returns just PageName[/method].

    public function MiniHref($method = '', $tag = '')
    {
        if (!$tag = trim($tag)) {
            $tag = $this->tag;
        }

        $tag = str_replace('%2F', '/', urlencode($tag));

        return $tag . ($method ? '/' . $method : '');
    }

    // returns the full url to a page/method.

    public function Href($method = '', $tag = '', $params = '')
    {
        $href = $this->config['base_url'] . $this->MiniHref($method, $tag);

        if ($params) {
            $href .= ($this->config['rewrite_mode'] ? '?' : '&amp;') . $params;
        }

        return $href;
    }

    public function Link($tag, $method = '', $text = '', $track = 1)
    {
        if (!$text) {
            $text = $this->conver_pagename($tag);
        }

        // is this an interwiki link?

        if (preg_match('/^([A-Z][A-Z,a-z]+)[:]([A-Z][a-z]+[A-Z,0-9][A-Z,a-z,0-9]*)$/', $tag, $matches)) {
            $tag = $this->GetInterWikiUrl($matches[1], $matches[2]);

            return "<a href=\"$tag\">$text</a>";
        } // is this a full link? ie, does it contain alpha-numeric characters?

        elseif (ord($tag[0]) > 123) {
            if ($_SESSION['linktracking'] && $track) {
                $this->TrackLinkTo($tag);
            }

            return ($this->LoadPage($tag) ? '<a href="' . $this->Href($method, $tag) . '">' . $text . '</a>' : '<span class="missingpage">' . $text . '</span><a href="' . $this->Href('edit', $tag) . '">?</a>');
        // return "<a href=\"$tag\">$text</a>";
        } elseif (preg_match('/[^[:alnum:]]/', $tag)) {
            // check for email addresses

            if (preg_match("/^.+\@.+$/", $tag)) {
                $tag = 'mailto:' . $tag;
            } // check for protocol-less URLs

            elseif (!preg_match('/:/', $tag)) {
                $tag = 'http://' . $tag;
            }

            return "<a href=\"$tag\">$text</a>";
        }  

        // it's a Wakka link!

        if ($_SESSION['linktracking'] && $track) {
            $this->TrackLinkTo($tag);
        }

        return ($this->LoadPage($tag) ? '<a href="' . $this->Href($method, $tag) . '">' . $text . '</a>' : '<span class="missingpage">' . $text . '</span><a href="' . $this->Href('edit', $tag) . '">?</a>');
    }

    // function PregPageLink($matches) { return $this->Link($matches[1]); }

    public function IsWikiName($text)
    {
        return preg_match('/^[A-Z][a-z]+[A-Z,0-9][A-Z,a-z,0-9]*$/', $text);
    }

    public function TrackLinkTo($tag)
    {
        $_SESSION['linktable'][] = $tag;
    }

    public function GetLinkTable()
    {
        return $_SESSION['linktable'];
    }

    public function ClearLinkTable()
    {
        $_SESSION['linktable'] = [];
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

        $this->db->query('delete from ' . $this->config['table_prefix'] . "links where from_tag = '" . $this->escape_string($this->GetPageTag()) . "'");

        if ($linktable = $this->GetLinkTable()) {
            $from_tag = $this->escape_string($this->GetPageTag());

            foreach ($linktable as $to_tag) {
                $lower_to_tag = mb_strtolower($to_tag);

                if (!$written[$lower_to_tag]) {
                    $this->db->query('insert into ' . $this->config['table_prefix'] . "links set from_tag = '" . $from_tag . "', to_tag = '" . $this->escape_string($to_tag) . "'");

                    $written[$lower_to_tag] = 1;
                }
            }
        }
    }

    public function Header()
    {
        return $this->Action($this->GetConfigValue('header_action'), 1);
    }

    public function Footer()
    {
        return $this->Action($this->GetConfigValue('footer_action'), 1);
    }

    // FORMS

    public function FormOpen($method = '', $tag = '', $formMethod = 'POST')
    {
        $result = '<form action="' . $this->Href($method, $tag) . '" method="' . $formMethod . "\">\n";

        if (!$this->config['rewrite_mode']) {
            $result .= '<input type="hidden" name="wakka" value="' . $this->MiniHref($method, $tag) . "\">\n";
        }

        return $result;
    }

    public function FormClose()
    {
        return "</form>\n";
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
        $this->interWiki[$name] = $url;
    }

    public function GetInterWikiUrl($name, $tag)
    {
        if ($url = $this->interWiki[$name]) {
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
            $this->db->query(
                'insert into ' . $this->config['table_prefix'] . 'referrers set ' . "page_tag = '" . $this->escape_string($tag) . "', " . "referrer = '" . $this->escape_string($referrer) . "', " . 'time = now()'
            );
        }
    }

    public function LoadReferrers($tag = '')
    {
        return $this->LoadAll('select referrer, count(referrer) as num from ' . $this->config['table_prefix'] . 'referrers ' . ($tag = trim($tag) ? "where page_tag = '" . $this->escape_string($tag) . "'" : '') . ' group by referrer order by num desc');
    }

    // PLUGINS

    public function Action($action, $forceLinkTracking = 0)
    {
        $action = trim($action);

        // stupid attributes check

        if (mb_stristr($action, '="')) {
            // extract $action and $vars_temp ("raw" attributes)

            preg_match('/^([A-Za-z0-9]*)(.*)$/', $action, $matches);

            [, $action, $vars_temp] = $matches;

            // match all attributes (key and value)

            preg_match_all('/([A-Za-z0-9]*)="(.*)"/U', $vars_temp, $matches);

            // prepare an array for extract() to work with (in $this->IncludeBuffered())

            if (is_array($matches)) {
                for ($a = 0, $aMax = count($matches); $a < $aMax; $a++) {
                    $vars[$matches[1][$a]] = $matches[2][$a];
                }
            }
        }

        if (!$forceLinkTracking) {
            $this->StopLinkTracking();
        }

        $result = $this->IncludeBuffered(mb_strtolower($action) . '.php', "<i>Unknown action \"$action\"</i>", $vars, $this->config['action_path']);

        $this->StartLinkTracking();

        return $result;
    }

    public function Method($method)
    {
        if (!$handler = $this->page['handler']) {
            $handler = 'page';
        }

        $methodLocation = $handler . '/' . $method . '.php';

        return $this->IncludeBuffered($methodLocation, "<i>Unknown method \"$methodLocation\"</i>", '', $this->config['handler_path']);
    }

    public function Format($text, $formatter = 'wakka')
    {
        return $this->IncludeBuffered('formatters/' . $formatter . '.php', "<i>Formatter \"$formatter\" not found</i>", compact('text'));
    }

    // USERS

    public function LoadUser($name, $password = 0)
    {
        return $this->LoadSingle('select * from ' . $this->config['table_prefix'] . "users where name = '" . $this->escape_string($name) . "' " . (0 === $password ? '' : "and password = '" . $this->escape_string($password) . "'") . ' limit 1');
    }

    public function LoadUsers()
    {
        return $this->LoadAll('SELECT * FROM ' . $this->db->prefix('users') . ' WHERE level > 0 ORDER BY uname');
    }

    public function GetUserName()
    {
        return $this->xoopsConfig['uname'];
    }

    public function UserName()
    { /* deprecated! */
        return $this->GetUserName();
    }

    public function GetUser()
    {
        return [$this->xoopsConfig['uname']];
    }

    public function SetUser($user)
    {
        $_SESSION['user'] = $user;

        $this->SetPersistentCookie('name', $user['name']);

        $this->SetPersistentCookie('password', $user['password']);
    }

    public function LogoutUser()
    {
        $_SESSION['user'] = '';

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

    // COMMENTS

    public function LoadComments($tag)
    {
        return $this->LoadAll('select * from ' . $this->config['table_prefix'] . "pages where comment_on = '" . $this->escape_string($tag) . "' and latest = 'Y' order by time");
    }

    public function LoadRecentComments()
    {
        return $this->LoadAll('select * from ' . $this->config['table_prefix'] . "pages where comment_on != '' and latest = 'Y' order by time desc");
    }

    public function LoadRecentlyCommented($limit = 50)
    {
        // NOTE: this is really stupid. Maybe my SQL-Fu is too weak, but apparently there is no easier way to simply select

        // all comment pages sorted by their first revision's (!) time. ugh!

        // load ids of the first revisions of latest comments. err, huh?

        if ($ids = $this->LoadAll('select min(id) as id from ' . $this->config['table_prefix'] . "pages where comment_on != '' group by tag order by id desc")) {
            // load complete comments

            foreach ($ids as $id) {
                $comment = $this->LoadSingle('select * from ' . $this->config['table_prefix'] . "pages where id = '" . $id['id'] . "' limit 1");

                if (!$comments[$comment['comment_on']] && $num < $limit) {
                    $comments[$comment['comment_on']] = $comment;

                    $num++;
                }
            }

            // now load pages

            if ($comments) {
                // now using these ids, load the actual pages

                foreach ($comments as $comment) {
                    $page = $this->LoadPage($comment['comment_on']);

                    $page['comment_user'] = $comment['user'];

                    $page['comment_time'] = $comment['time'];

                    $page['comment_tag'] = $comment['tag'];

                    $pages[] = $page;
                }
            }
        }

        // load tags of pages

        //return $this->LoadAll("select comment_on as tag, max(time) as time, tag as comment_tag, user from ".$this->config["table_prefix"]."pages where comment_on != '' group by comment_on order by time desc");

        return $pages;
    }

    // ACCESS CONTROL

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

    public function GetPageOwner($tag = '', $time = '')
    {
        if (!$tag = trim($tag)) {
            $tag = $this->GetPageTag();
        }

        if ($page = $this->LoadPage($tag, $time)) {
            return $page['owner'];
        }
    }

    public function SetPageOwner($tag, $user)
    {
        // check if user exists

        // if (!$this->LoadUser($user)) return;

        // updated latest revision with new owner

        $this->db->queryF('update ' . $this->config['table_prefix'] . "pages set owner = '" . $user . "' where tag = '" . $this->escape_string($tag) . "' and latest = 'Y' limit 1");
    }

    public function LoadAcl($tag, $privilege, $useDefaults = 1)
    {
        if ((!$acl = $this->LoadSingle('select * from ' . $this->config['table_prefix'] . "acls where page_tag = '" . $this->escape_string($tag) . "' and privilege = '" . $this->escape_string($privilege) . "' limit 1")) && $useDefaults) {
            $acl = ['page_tag' => $tag, 'privilege' => $privilege, 'list' => $this->GetConfigValue('default_' . $privilege . '_acl')];
        }

        return $acl;
    }

    public function SaveAcl($tag, $privilege, $list)
    {
        if ('' == $list) {
            $this->db->queryF('delete from ' . $this->config['table_prefix'] . "acls where page_tag = '" . $this->escape_string($tag) . "' and privilege = '" . $this->escape_string($privilege) . "'");
        } elseif ($this->LoadAcl($tag, $privilege, 0)) {
            $this->db->query('update ' . $this->config['table_prefix'] . "acls set list = '" . $list . "' where page_tag = '" . $this->escape_string($tag) . "' and privilege = '" . $this->escape_string($privilege) . "' limit 1");
        } else {
            $this->db->query('insert into ' . $this->config['table_prefix'] . "acls set list = '" . $list . "', page_tag = '" . $this->escape_string($tag) . "', privilege = '" . $this->escape_string($privilege) . "'");
        }
    }

    // returns true if $user (defaults to current user) has access to $privilege on $page_tag (defaults to current page)

    public function HasAccess($privilege, $tag = '', $user = '')
    {
        // see whether user is registered and logged in

        if ($user = $this->GetUser()) {
            $registered = true;
        }

        // set defaults

        if (!$tag = trim($tag)) {
            $tag = $this->GetPageTag();
        }

        if (!$user = $this->GetUserName()) {
        }

        // load acl

        $acl = $this->LoadAcl($tag, $privilege);

        // if current user is owner, return true. owner can do anything!

        if ($this->UserIsOwner($tag)) {
            return true;
        }

        // fine fine... now go through acl

        if ('' == $acl['list'] or $this->xoopsConfig['isadmin']) {
            return true;
        }

        $acls = explode('|', $acl['list']);

        foreach ($this->xoopsConfig['groups'] as $groupid) {
            if (in_array($groupid, $acls, true)) {
                return true;
            }
        }

        return false;
    }

    // XML

    public function WriteRecentChangesXML()
    {
        $xml = '<?xml version="1.0" encoding="' . _CHARSET . "\"?>\n";

        $xml .= "<rss version=\"0.92\">\n";

        $xml .= "<channel>\n";

        $xml .= '<title>' . $this->GetConfigValue('wakka_name') . " - RecentChanges</title>\n";

        $xml .= '<link>' . $this->GetConfigValue('base_url') . "</link>\n";

        $xml .= '<description>Recent changes to the ' . $this->GetConfigValue('wakka_name') . " WakkaWiki</description>\n";

        $xml .= "<language>en-us</language>\n";

        if ($pages = $this->LoadRecentlyChanged()) {
            foreach ($pages as $i => $page) {
                if ($i < 50) {
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
            $this->db->query('delete from ' . $this->config['table_prefix'] . "referrers where time < date_sub(now(), interval '" . $this->escape_string($days) . "' day)");
        }

        // purge old page revisions

        if ($days = $this->GetConfigValue('pages_purge_time')) {
            $this->db->query('delete from ' . $this->config['table_prefix'] . "pages where time < date_sub(now(), interval '" . $this->escape_string($days) . "' day) and latest = 'N'");
        }
    }

    // THE BIG EVIL NASTY ONE!

    public function Run($tag, $method = '')
    {
        $this->Maintenance(); // TODO: maybe only do this occasionally?

        $this->ReadInterWikiConfig();

        // do our stuff!

        if (!$this->method = trim($method)) {
            $this->method = 'show';
        }

        if (!$this->tag = trim($tag)) {
            $this->Redirect($this->Href('', $this->config['root_page']));
        }

        if ((!$this->GetUser() && $_COOKIE['name']) && ($user = $this->LoadUser($_COOKIE['name'], $_COOKIE['password']))) {
            $this->SetUser($user);
        }

        $this->SetPage($this->LoadPage($tag, $_REQUEST['time']));

        $this->LogReferrer();

        if (!preg_match("/\.xml$/", $this->method)) {
            print($this->Header() . $this->Method($this->method) . $this->Footer());
        } else {
            print($this->Method($this->method));
        }
    }

    public function escape_string($string)
    {
        $string = $this->myts->censorString($string);

        $string = $this->myts->addSlashes($string);

        return $string;
    }

    public function conver_pagename($pagename)
    {
        if (in_array($pagename, $this->config['page_array'], true)) {
            return $this->config['page_name'][$pagename];
        }

        return $pagename;
    }
}
