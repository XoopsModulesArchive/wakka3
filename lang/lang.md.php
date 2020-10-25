<?php

$wackoLanguage = [
    'name' => 'Moldavian',
'code' => 'md',
'charset' => 'windows-1251',
'UPPER_P' => "A-Z\xc0-\xdf\xa8",
'LOWER_P' => "a-z\xe0-\xff\xb8\/\'",
'ALPHA_P' => "A-Za-z\xc0-\xff\xa8\xb8\_\/\'",
'locale' => (version_compare(PHP_VERSION, '4.3.0', '<')) ? 'ru_RU.CP1251' : ['ru_RU.CP1251', 'ru_RU.KOI8-r', 'ru_RU', 'russian', 'ru_SU', 'ru'],
'NpjLettersFrom' => 'àáâãäåçèêëìíîïðñòóôõúüöûÀÁÂÃÄÅÇÈÊËÌÍÎÏÐÑÒÓÔÕÚÜÖÛ',
'NpjLettersTo' => 'abvgdeziklmnoprstufx__cyABVGDEZIKLMNOPRSTUFX__CY',
'NpjBiLetters' => [
        'é' => 'jj',
'¸' => 'jo',
'æ' => 'zh',
'÷' => 'ch',
'ø' => 'sh',
'ù' => 'shh',
'ý' => 'je',
'þ' => 'ju',
'ÿ' => 'ja',
'É' => 'Jj',
'¨' => 'Jo',
'Æ' => 'Zh',
'×' => 'Ch',
'Ø' => 'Sh',
'Ù' => 'Shh',
'Ý' => 'Je',
'Þ' => 'Ju',
'ß' => 'Ja',
    ],
'unicode_entities' => [
        'À' => '&#1040;',
'Á' => '&#1041;',
'Â' => '&#1042;',
'Ã' => '&#1043;',
'Ä' => '&#1044;',
'Å' => '&#1045;',
'Æ' => '&#1046;',
'Ç' => '&#1047;',
'È' => '&#1048;',
'É' => '&#1049;',
'Ê' => '&#1050;',
'Ë' => '&#1051;',
'Ì' => '&#1052;',
'Í' => '&#1053;',
'Î' => '&#1054;',
'Ï' => '&#1055;',
'Ð' => '&#1056;',
'Ñ' => '&#1057;',
'Ò' => '&#1058;',
'Ó' => '&#1059;',
'Ô' => '&#1060;',
'Õ' => '&#1061;',
'Ö' => '&#1062;',
'×' => '&#1063;',
'Ø' => '&#1064;',
'Ù' => '&#1065;',
'Ú' => '&#1066;',
'Û' => '&#1067;',
'Ü' => '&#1068;',
'Ý' => '&#1069;',
'Þ' => '&#1070;',
'ß' => '&#1071;',
'à' => '&#1072;',
'á' => '&#1073;',
'â' => '&#1074;',
'ã' => '&#1075;',
'ä' => '&#1076;',
'å' => '&#1077;',
'æ' => '&#1078;',
'ç' => '&#1079;',
'è' => '&#1080;',
'é' => '&#1081;',
'ê' => '&#1082;',
'ë' => '&#1083;',
'ì' => '&#1084;',
'í' => '&#1085;',
'î' => '&#1086;',
'ï' => '&#1087;',
'ð' => '&#1088;',
'ñ' => '&#1089;',
'ò' => '&#1090;',
'ó' => '&#1091;',
'ô' => '&#1092;',
'õ' => '&#1093;',
'ö' => '&#1094;',
'÷' => '&#1095;',
'ø' => '&#1096;',
'ù' => '&#1097;',
'ú' => '&#1098;',
'û' => '&#1099;',
'ü' => '&#1100;',
'ý' => '&#1101;',
'þ' => '&#1102;',
'ÿ' => '&#1103;',
'¸' => '&#1105;',
'¨' => '&#1025;',
    ],
];