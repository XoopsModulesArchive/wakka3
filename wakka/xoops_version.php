<?php

$modversion['name'] = _MI_NAME;
$modversion['version'] = 0.1;
$modversion['description'] = _MI_DESC;
$modversion['credits'] = '';
$modversion['author'] = 'Dahoo Chen ( http://www.wakkawiki.com/WakkaWiki )';
$modversion['help'] = '';
$modversion['license'] = 'GPL see LICENSE';
$modversion['official'] = 0;
$modversion['image'] = 'xml/logo.png';
$modversion['dirname'] = 'wakka';
// Sql file (must contain sql generated by phpMyAdmin or phpPgAdmin)
// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
//$modversion['sqlfile']['postgresql'] = "sql/pgsql.sql";
// Tables created by sql file (without prefix!)
$modversion['tables'][0] = 'wikiacls';
$modversion['tables'][1] = 'wikilinks';
$modversion['tables'][2] = 'wikipages';
$modversion['tables'][3] = 'wikireferrers';
// Admin things
$modversion['hasAdmin'] = 1;
//$modversion['adminindex'] = "admin/admin.php";
//$modversion['adminmenu'] = "admin/menu.php";
// Menu
$modversion['hasMain'] = 1;
$modversion['config'][1]['name'] = 'css';
$modversion['config'][1]['title'] = '_MI_CSS';
$modversion['config'][1]['description'] = '';
$modversion['config'][1]['formtype'] = 'select';
$modversion['config'][1]['valuetype'] = 'text';
$modversion['config'][1]['default'] = 'wakka';
$modversion['config'][1]['options'] = ['wakka' => 'wakka', 'green' => 'green', 'pukiwiki' => 'pukiwiki'];
