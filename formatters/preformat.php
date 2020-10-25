<?php

if (!class_exists('preformatter')) {
    class preformatter
    {
        public $object;

        public function __construct(&$object)
        {
            $this->object = &$object;

            $this->PREREGEXP = "/(\%\%.*?\%\%|\"\".*?\"\"|::(\S)?::" . ($this->object->userlang != $this->object->pagelang ? "|\[\[(\S+?)([ \t]+([^\n]+?))?\]\]|\(\((\S+?)([ \t]+([^\n]+?))?\)\)" : '') . ')/sm';
        }

        public function precallback($things)
        {
            $wacko = &$this->object;

            $thing = $things[1];

            if (preg_match("/^\%\%(.*)\%\%$/s", $thing, $matches)) {
                return '%%' . $matches[1] . '%%';
            } elseif (preg_match('/^""(.*)""$/s', $thing, $matches)) {
                return '""' . $matches[1] . '""';
            } elseif (':::::' == $thing) {
                return '((/' . $wacko->GetUserName() . ' ' . $wacko->GetUserName() . ')):';
            } elseif ('::::' == $thing) {
                return '((/' . $wacko->GetUserName() . ' ' . $wacko->GetUserName() . '))';
            } elseif ('::@::' == $thing) {
                return '((/' . $wacko->GetUserName() . ' ' . $wacko->GetUserName() . '))' . ' ' . date('/d.m.Y H:i/');
            } elseif ('::+::' == $thing) {
                return date('d.m.Y H:i');
            } elseif ((preg_match("/^(\[\[)(.+)(\]\])$/", $thing, $matches))
                      || (preg_match("/^(\(\()(.+)(\)\))$/", $thing, $matches))) {
                [, $b1, $cont, $b2] = $matches;

                if (preg_match("/\&\#\d+;/", $cont, $matches)) {
                    $thing = $b1 . @strtr($cont, $this->object->unicode_entities) . ' @@' . $this->object->userlang . $b2;
                }

                return $thing;
            }

            return $thing;
        }
    }
}
$parser = new preformatter($this);
$text = preg_replace_callback($parser->PREREGEXP, [&$parser, 'precallback'], $text);
print($text);
