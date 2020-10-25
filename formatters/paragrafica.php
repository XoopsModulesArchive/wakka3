<?php
// <a name="p1249-1"></a><p...>
// <a name="h1249-1"></a><hX..>
// занимаемся параграфированием.
$this->UseClass('paragrafica', 'formatters/classes/');
// we got pure HTML on input.
$para = new paragrafica($this);
$result = $para->correct($text);
$this->SetTocArray($para->toc);
echo $result;
