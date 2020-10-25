<?php

$this->UseClass('WackoFormatter', 'formatters/classes/');
$text = str_replace("\r", '', $text);
$text = "\177\n" . $text . "\n";
$parser = new WackoFormatter($this);
$this->headerCount = 0;
$text = preg_replace_callback($parser->NOTLONGREGEXP, [&$parser, 'WackoPreprocess'], $text);
$texts = explode("\xa5\xa5", $text);
$wtext = $texts[0];
for ($i = 2, $iMax = count($texts); $i < $iMax; $i += 2) {
    $wtext .= "\xa6" . $texts[$i];
}
$wtext = preg_replace_callback($parser->MOREREGEXP, [&$parser, 'WackoMiddleprocess'], $wtext);
$wtext = preg_replace_callback($parser->LONGREGEXP, [&$parser, 'WackoCallback'], $wtext);
$wtexts = explode("\xa6", $wtext);
$text = '';
for ($i = 0, $iMax = count($wtexts); $i < $iMax; $i++) {
    $text .= $wtexts[$i] . $texts[2 * $i + 1];
}
//$text = implode("", $texts);
$text = str_replace("\177" . "<br>\n", '', $text);
$text = str_replace("\177" . '', '', $text);
// we're cutting the last <br>
$text = preg_replace("/<br \>$/", '', $text); //trim($text));
print($text);
