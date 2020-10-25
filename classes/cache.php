<?php

// $Id: cache.php,v 1.4 2004/07/26 05:31:30 kukutz Exp $
class Cache
{
    public $cache_ttl = 600;

    public $cache_dir = '_cache/';

    public $debug = 1;

    //Constructor

    public function __construct($cache_dir, $cache_ttl)
    {
        $this->cache_dir = $cache_dir;

        $this->cache_ttl = $cache_ttl;

        $this->timer = $this->GetMicroTime();
    }

    //Достать контент из кэша

    public function GetCached($page, $method, $query)
    {
        $filename = $this->ConstructID($page, $method, $query);

        if (!@file_exists($filename)) {
            return false;
        }

        if ((time() - @filemtime($filename)) > $this->cache_ttl) {
            return false;
        }

        $fp = fopen($filename, 'rb');

        $contents = fread($fp, filesize($filename));

        fclose($fp);

        return $contents;
    }

    public function ConstructID($page, $method, $query)
    {
        $page = mb_strtolower(str_replace('\\', '', str_replace("'", '', str_replace('_', '', $page))));

        $this->Log('ConstructID page=' . $page);

        $this->Log('ConstructID md5=' . md5($page));

        $filename = $this->cache_dir . md5($page) . '_' . $method . '_' . $query;

        return $filename;
    }

    //Достать timestamp контента из кэша

    public function GetCachedTime($page, $method, $query)
    {
        $filename = $this->ConstructID($page, $method, $query);

        if (!@file_exists($filename)) {
            return false;
        }

        if ((time() - @filemtime($filename)) > $this->cache_ttl) {
            return false;
        }

        return @filemtime($filename);
    }

    //Положить контент в кэш

    public function StoreToCache($data, $page = false, $method = false, $query = false)
    {
        if (!$page) {
            $page = $this->page;
        }

        if (!$method) {
            $method = $this->method;
        }

        if (!$query) {
            $query = $this->query;
        }

        $page = mb_strtolower(str_replace('\\', '', str_replace("'", '', str_replace('_', '', $page))));

        $filename = $this->ConstructID($page, $method, $query);

        $fp = fopen($filename, 'wb');

        fwrite($fp, $data);

        fclose($fp);

        if ($this->wacko) {
            $this->wacko->Query(
                'insert into ' . $this->wacko->config['table_prefix'] . 'cache set ' . "name ='" . quote(md5($page)) . "', " . "method='" . quote($method) . "', " . "query ='" . quote($query) . "'"
            );
        }

        @chmod($newname, octdec('0777'));

        return true;
    }

    //Сбросить кэш

    public function CacheInvalidate($page)
    {
        if ($this->wacko) {
            $page = mb_strtolower(str_replace('\\', '', str_replace("'", '', str_replace('_', '', $page))));

            $this->Log('CacheInvalidate page=' . $page);

            $this->Log('CacheInvalidate query=' . 'select * from ' . $this->wacko->config['table_prefix'] . "cache where name ='" . quote(md5($page)) . "'");

            $params = $this->wacko->LoadAll('select * from ' . $this->wacko->config['table_prefix'] . "cache where name ='" . quote(md5($page)) . "'");

            $this->Log('CacheInvalidate count params=' . count($params));

            foreach ($params as $param) {
                $filename = $this->ConstructID($page, $param['method'], $param['query']);

                $this->Log('CacheInvalidate delete=' . $filename);

                if (@file_exists($filename)) {
                    @unlink($filename);
                }
            }

            $this->wacko->Query('delete from ' . $this->wacko->config['table_prefix'] . "cache where name ='" . quote(md5($page)) . "'");

            $this->Log('CacheInvalidate end');

            return true;
        }

        return false;
    }

    public function Log($msg)
    {
        if ($this->debug > 1) {
            $fp = fopen($this->cache_dir . 'log', 'ab');

            fwrite($fp, $msg . "\n");

            fclose($fp);
        }
    }

    //Проверяем http-запрос. Возможно, выдаём из кэша.

    public function CheckHttpRequest($page, $method)
    {
        if (!$page) {
            return false;
        }

        foreach ($_GET as $k => $v) {
            if ('v' != $k && 'wakka' != $k) {
                $_query[$k] = $v;
            }
        }

        if ($_query) {
            ksort($_query);

            reset($_query);

            foreach ($_query as $k => $v) {
                $query .= urlencode($k) . '=' . urlencode($v) . '&';
            }
        }

        $this->Log('CheckHttpRequest query=' . $query);

        //проверяем кэш

        if ($mtime = $this->GetCachedTime($page, $method, $query)) {
            $this->Log('CheckHttpRequest incache mtime=' . $mtime);

            $gmt = gmdate('D, d M Y H:i:s \G\M\T', $mtime);

            $etag = $_SERVER['HTTP_IF_NONE_MATCH'];

            $lastm = $_SERVER['HTTP_IF_MODIFIED_SINCE'];

            if ($p = mb_strpos($lastm, ';')) {
                $lastm = mb_substr($lastm, 0, $p);
            }

            if ('GET' == $_SERVER['REQUEST_METHOD']) { //поддержать HEAD ???
                if (!$lastm && !$etag) {
                } elseif ($lastm && $gmt != $lastm) {
                } elseif ($etag && $gmt != trim($etag, '\"')) {
                } else {
                    header('HTTP/1.1 304 Not Modified');

                    die();
                }

                $cached = $this->GetCached($page, $method, $query);

                header('Last-Modified: ' . $gmt);

                header('ETag: "' . $gmt . '"');

                //header ("Content-Type: text/xml");

                //header ("Content-Length: ".strlen($cached));

                //header ("Cache-Control: max-age=0");

                //header ("Expires: ".gmdate('D, d M Y H:i:s \G\M\T', time()));

                echo($cached);

                // считаем, как долго работал скрипт

                if ($this->debug >= 1 && false === mb_strpos($method, '.xml')) {
                    $ddd = $this->GetMicroTime();

                    echo("<div style='margin:5px 20px; color:#999999'><small>cache time: " . (number_format(($ddd - $this->timer), 3)) . ' s<br>');

                    echo '</small></div>';
                }

                if (false === mb_strpos($method, '.xml')) {
                    echo '</body></html>';
                }

                die();
            }
        }

        //сюда мы попадаем, если в кэше нет свежей версии

        $this->page = $page;

        $this->method = $method;

        $this->query = $query;

        return true;
        //index.php должен в конце обработки запроса звать cache->storetocache???
    }

    public function Output()
    {
        clearstatcache();

        if (!($mtime = $this->GetCachedTime($this->page, $this->method, $this->query))) {
            $mtime = time();
        }

        {
            $gmt = gmdate('D, d M Y H:i:s \G\M\T', $mtime);
            $res = &$this->result;
            header('Last-Modified: ' . $gmt);
            header('ETag: "' . $gmt . '"');
            header('Content-Type: text/xml');
            //header ("Content-Length: ".strlen($res));
            //header ("Cache-Control: max-age=0");
            //header ("Expires: ".gmdate('D, d M Y H:i:s \G\M\T', time()));
            echo($res);
            die();
        }
    }

    public function GetMicroTime()
    {
        [$usec, $sec] = explode(' ', microtime());

        return ((float)$usec + (float)$sec);
    }
}
