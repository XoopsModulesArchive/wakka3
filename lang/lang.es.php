<?php

$wackoLanguage = [
    'name' => 'espanol',
'code' => 'es',
'charset' => 'iso8859-1',
'utfdecode' => [],
'UPPER_P' => "A-Z\xc0-\xd6\xd8-\xdd",
'LOWER_P' => "a-z\xdf-\xf6\xf8-\xfd\xff\/'",
'ALPHA_P' => "A-Za-z\xc0-\xd6\xd8-\xdd\xdf-\xf6\xf8-\xfd\xff\_\-\/'",
'locale' => (version_compare(PHP_VERSION, '4.3.0', '<')) ? 'es_ES' : ['es', 'es_ES', 'espanol'],
'NpjLettersFrom' => 'àáâãåçèéêëìíîïòóôõùúûýÀÁÂÃÅÇÈÉÊËÌÍÎÏÒÓÔÕÙÚÛÝñÑ',
'NpjLettersTo' => 'aaaaaceeeeiiiioooouuuyAAAAACEEEEIIIIOOOOUUUYnN',
'NpjBiLetters' => [
        'ä' => 'ae',
'ö' => 'oe',
'ü' => 'ue',
'æ' => 'ae',
'Ä' => 'Ae',
'Ö' => 'Oe',
'Ü' => 'Ue',
'Æ' => 'Ae',
'ÿ' => 'yu',
'ß' => 'ss',
    ],
'unicode_entities' => [
        'À' => '&#192;',
'Á' => '&#193;',
'Â' => '&#194;',
'Ã' => '&#195;',
'Ä' => '&#196;',
'Å' => '&#197;',
'Æ' => '&#198;',
'Ç' => '&#199;',
'È' => '&#200;',
'É' => '&#201;',
'Ê' => '&#202;',
'Ë' => '&#203;',
'Ì' => '&#204;',
'Í' => '&#205;',
'Î' => '&#206;',
'Ï' => '&#207;',
'Ð' => '&#208;',
'Ñ' => '&#209;',
'Ò' => '&#210;',
'Ó' => '&#211;',
'Ô' => '&#212;',
'Õ' => '&#213;',
'Ö' => '&#214;',
'Ù' => '&#217;',
'Ú' => '&#218;',
'Û' => '&#219;',
'Ü' => '&#220;',
'Ý' => '&#221;',
'Þ' => '&#222;',
'ß' => '&#223;',
'à' => '&#224;',
'á' => '&#225;',
'â' => '&#226;',
'ã' => '&#227;',
'ä' => '&#228;',
'å' => '&#229;',
'æ' => '&#230;',
'ç' => '&#231;',
'è' => '&#232;',
'é' => '&#233;',
'ê' => '&#234;',
'ë' => '&#235;',
'ì' => '&#236;',
'í' => '&#237;',
'î' => '&#238;',
'ï' => '&#239;',
'ð' => '&#240;',
'ñ' => '&#241;',
'ò' => '&#242;',
'ó' => '&#243;',
'ô' => '&#244;',
'õ' => '&#245;',
'ö' => '&#246;',
'ù' => '&#249;',
'ú' => '&#250;',
'û' => '&#251;',
'ü' => '&#252;',
'ý' => '&#253;',
'þ' => '&#254;',
'ÿ' => '&#255;',
    ],
];
