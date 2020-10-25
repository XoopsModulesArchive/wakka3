<?php

$num = mb_substr_count($text, "\n") + 2;
if ('print' != $this->method && $num >= 15) {
    $num = 15;
}
print('<!--no' . 'typo--><textarea class="code" rows="' . $num . '" readonly="readonly">' . $text . '</text' . 'area><!--/no' . 'typo-->');
