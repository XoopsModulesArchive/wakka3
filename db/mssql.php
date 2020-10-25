<?php

// Non-working MSSQL driver.
function quote($string)
{
    $s = $GLOBALS['xoopsDB']->escape($string);

    $s = str_replace('\\\\', '\\', $s);

    return str_replace("\\'", "''", $s);
}

//Все ДБАЛы, кроме майэскуэльного, должны парсить запросы на предмет LIMIT.
function query($query, $dblink = '')
{
    if (mb_strpos($query, 'limit')) {
        //1. get no

        preg_match("/(limit\s+([0-9])+[\s;]*$)/i", $query, $m);

        $no = 1 * $m[2];

        //2. delete limit

        $query = str_replace($m[1], '', $query);

        //3. insert top

        if (0 === mb_strpos($query, 'select')) {
            $query = str_replace('select', 'select top ' . $no, $query);
        }
    }

    $query = str_replace('now()', 'getdate()', $query);

    if (!$result = mssql_query($query, $dblink)) {
        ob_end_clean();

        die('Query failed: ' . $query . ' (' . mssql_get_last_message() . ')');
    }

    return $result;
}

function fetch_assoc($rs)
{
    return mssql_fetch_assoc($rs);
}

function free_result($rs)
{
    return mssql_free_result($rs);
}

function connect($host, $user, $passw, $db)
{
    $dblink = mssql_connect($host, $user, $passw);

    mssql_select_db($db, $dblink);

    return $dblink;
}
