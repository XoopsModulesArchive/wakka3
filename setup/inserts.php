<?php

function InsertPage($tag, $body, $lng)
{
    global $config, $dblink;

    if (0 == $GLOBALS['xoopsDB']->getRowsNum($GLOBALS['xoopsDB']->queryF('SELECT * FROM ' . $config['table_prefix'] . "pages where tag='" . $tag . "'", $dblink))) {
        $GLOBALS['xoopsDB']->queryF(
            'insert into ' . $config['table_prefix'] . "pages set tag = '" . $tag . "', " . "supertag='" . NpjTranslit($tag, $lng) . "', body = '" . $body . "', " . "user = 'WackoInstaller', owner = '" . $config['admin_name'] . "', " . "time = now(), latest = 'Y', lang='" . $lng . "'",
            $dblink
        );

        $GLOBALS['xoopsDB']->queryF(
            'insert into ' . $config['table_prefix'] . "acls set page_tag = '" . $tag . "', " . "supertag='" . NpjTranslit($tag, $lng) . "', privilege = 'read', list='*'",
            $dblink
        );

        $GLOBALS['xoopsDB']->queryF(
            'insert into ' . $config['table_prefix'] . "acls set page_tag = '" . $tag . "', " . "supertag='" . NpjTranslit($tag, $lng) . "', privilege = 'write', list='*'",
            $dblink
        );

        $GLOBALS['xoopsDB']->queryF(
            'insert into ' . $config['table_prefix'] . "acls set page_tag = '" . $tag . "', " . "supertag='" . NpjTranslit($tag, $lng) . "', privilege = 'comment', list='*'",
            $dblink
        );
    }
}

function NpjTranslit($tag, $lng)
{
    $language = SetLanguage($lng);

    $tag = str_replace('//', '/', $tag);

    $tag = str_replace('-', '', $tag);

    $tag = str_replace(' ', '', $tag);

    $tag = str_replace("'", '_', $tag);

    $tag = @strtr($tag, $language['NpjLettersFrom'], $language['NpjLettersTo']);

    $tag = @strtr($tag, $language['NpjBiLetters']);

    $tag = mb_strtolower($tag);

    return rtrim($tag, '/');
}

function SetLanguage($lng)
{
    global $language, $languages;

    if (!$languages[$lng]) {
        // echo ("$lng<br>");

        $resourcefile = 'lang/lang.' . $lng . '.php';

        if (@file_exists($resourcefile)) {
            include $resourcefile;
        }

        $languages[$lng] = $wackoLanguage;
    }

    $language = &$languages[$lng];

    setlocale(LC_CTYPE, $language['locale']);

    $language['locale'] = setlocale(LC_CTYPE, 0);

    return $language;
}

if ($config['multilanguage']) {
    $handle = opendir('setup/lang');

    while (false !== ($file = readdir($handle))) {
        if (1 == preg_match("/^inserts\.(.*?)\.php$/", $file, $match)) {
            $langlist[] = $match[1];
        }
    }

    closedir($handle);

    foreach ($langlist as $_lang) {
        require 'setup/lang/inserts.' . $_lang . '.php';
    }
} else {
    require 'setup/lang/inserts.' . $config['language'] . '.php';
}
