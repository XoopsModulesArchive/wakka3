<?php

// ÍïæÒðàíñëèò
$NpjLettersFrom = 'àáâãäåçèêëìíîïðñòóôöû';
$NpjLettersTo = 'abvgdeziklmnoprstufcy';
$NpjBiLetters = [
    'é' => 'jj',
'¸' => 'jo',
'æ' => 'zh',
'õ' => 'kh',
'÷' => 'ch',
'ø' => 'sh',
'ù' => 'shh',
'ý' => 'je',
'þ' => 'ju',
'ÿ' => 'ja',
'ú' => '',
'ü' => '',
];
$NpjCaps = 'ÀÁÂÃÄÅ¨ÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÛÝÞß';
$NpjSmall = 'àáâãäå¸æçèéêëìíîïðñòóôõö÷øùüúûýþÿ';
$tag = $text;
//insert _ between words
$tag = preg_replace("/\s+/ms", '_', $tag);
$tag = mb_strtolower($tag);
$tag = strtr($tag, $NpjCaps, $NpjSmall);
$tag = strtr($tag, $NpjLettersFrom, $NpjLettersTo);
$tag = strtr($tag, $NpjBiLetters);
$tag = preg_replace('/[^a-z0-9_.]+/mi', '', $tag);
echo $tag;
