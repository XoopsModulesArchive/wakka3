<?php

class post_wacko
{
    public $object;

    public function __construct(&$object)
    {
        $this->object = &$object;
    }

    public function postcallback($things)
    {
        $thing = $things[1];

        $wacko = &$this->object;

        // forced links ((link link == desc desc))

        if (preg_match("/^\xA2\xA2([^\n]+)==([^\n]*)\xAF\xAF$/", $thing, $matches)) {
            [, $url, $text] = $matches;

            if ($url) {
                $url = str_replace(' ', '', $url);

                $text = trim(preg_replace("/\xA4\xA4|__|\[\[|\(\(/", '', $text));

                if (mb_stristr($text, '@@')) {
                    $t = explode('@@', $text);

                    $text = $t[0];

                    $lang = $t[1];
                }

                return $wacko->Link($url, '', $text, 1, 1, $lang);
            }

            return '';
        } // actions

        elseif (preg_match("/^\xA1\xA1\s*([^\n]+?)\xA1\xA1$/s", $thing, $matches)) {
            if ($matches[1]) {
                // разборка на параметры.

                $sep = mb_strpos($matches[1], ' ');

                if (false === $sep) {
                    $action = $matches[1];

                    $params = [];
                } else {
                    $action = mb_substr($matches[1], 0, $sep);

                    $p = ' ' . mb_substr($matches[1], $sep) . ' ';

                    $paramcount = preg_match_all(
                        "/(([^\s=]+)(\=((\"(.*?)\")|([^\"\s]+)))?)\s/",
                        $p,
                        $matches,
                        PREG_SET_ORDER
                    );

                    $params = [];

                    $c = 0;

                    foreach ($matches as $m) {
                        $value = $m[3] ? ($m[5] ? $m[6] : $m[7]) : '1';

                        $params[$c] = $value;

                        $params[$m[2]] = $value;

                        $c++;
                    }
                }

                return $wacko->Action($action, $params);
            }

            return '{{}}';
        }

        // if we reach this point, it must have been an accident.

        return $thing;
    }
}
