<?php

//Вы таки не поверите, но это... ДБАЛ!
function quote($string)
{
    return $GLOBALS['xoopsDB']->escape($string);
}

//Все ДБАЛы, кроме майэскуэльного, должны парсить запросы на предмет LIMIT.
function query($query, $dblink = '')
{
    if (!$result = $GLOBALS['xoopsDB']->queryF($query, $dblink)) {
        ob_end_clean();

        die('Query failed: ' . $query . ' (' . $GLOBALS['xoopsDB']->error() . ')');
    }

    return $result;
}

function fetch_assoc($rs)
{
    return $GLOBALS['xoopsDB']->fetchArray($rs);
}

function free_result($rs)
{
    return $GLOBALS['xoopsDB']->freeRecordSet($rs);
}

function connect($host, $user, $passw, $db)
{
    if (!extension_loaded('mysql')) {
        dl('mysql.so');
    }

    $dblink = mysql_connect($host, $user, $passw);

    mysqli_select_db($GLOBALS['xoopsDB']->conn, $db, $dblink);

    return $dblink;
}
