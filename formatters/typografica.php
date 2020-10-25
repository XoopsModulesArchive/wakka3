<?php

if ('' == $text) {
    return;
}
$this->UseClass('typografica', 'formatters/classes/');
$typo = new typografica($this);
print $typo->correct($text, false);
