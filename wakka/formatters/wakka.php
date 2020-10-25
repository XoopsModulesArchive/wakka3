<?php

// This may look a bit strange, but all possible formatting tags have to be in a single regular expression for this to work correctly. Yup!
if (!function_exists('wakka2callback')) {
    function wakka2callback($things)
    {
        $thing = $things[1];

        static $oldIndentLevel = 0;

        static $indentClosers = [];

        static $br = 1;

        global $wakka;

        // convert HTML thingies

        if ('<' == $thing) {
            return '&lt;';
        } elseif ('>' == $thing) {
            return '&gt;';
        } // bold

        elseif ('**' == $thing) {
            static $bold = 0;

            return (++$bold % 2 ? '<strong>' : '</strong>');
        } // italic

        elseif ('//' == $thing) {
            static $italic = 0;

            return (++$italic % 2 ? '<em>' : '</em>');
        } // underlinue

        elseif ('__' == $thing) {
            static $underline = 0;

            return (++$underline % 2 ? '<u>' : '</u>');
        } // monospace

        elseif ('##' == $thing) {
            static $monospace = 0;

            return (++$monospace % 2 ? '<tt>' : '</tt>');
        } // notes

        elseif ("''" == $thing) {
            static $notes = 0;

            return (++$notes % 2 ? '<span class="notes">' : '</span>');
        } // urls

        elseif (preg_match("/^([a-z]+:\/\/\S+?)([^[:alnum:]^\/])?$/", $thing, $matches)) {
            $url = $matches[1];

            return "<a href=\"$url\">$url</a>" . $matches[2];
        } // headers

        elseif (preg_match('/^======(.*)======$/s', $thing, $matches)) {
            $br = 0;

            return '<h1>' . $matches[1] . '</h1>';
        } elseif (preg_match('/^=====(.*)=====$/s', $thing, $matches)) {
            $br = 0;

            return '<h2>' . $matches[1] . '</h2>';
        } elseif (preg_match('/^====(.*)====$/s', $thing, $matches)) {
            $br = 0;

            return '<h3>' . $matches[1] . '</h3>';
        } elseif (preg_match('/^===(.*)===$/s', $thing, $matches)) {
            $br = 0;

            return '<h4>' . $matches[1] . '</h4>';
        } elseif (preg_match('/^==(.*)==$/s', $thing, $matches)) {
            $br = 0;

            return '<h5>' . $matches[1] . '</h5>';
        } // separators

        elseif ('----' == $thing) {
            // TODO: This could probably be improved for situations where someone puts text on the same line as a separator.

            // Which is a stupid thing to do anyway! HAW HAW! Ahem.

            $br = 0;

            return '<hr noshade="noshade" size="1">';
        } // forced line breaks

        elseif ('---' == $thing) {
            return '<br>';
        } // escaped text

        elseif (preg_match('/^""(.*)""$/s', $thing, $matches)) {
            return $matches[1];
        } // code text

        elseif (preg_match("/^\%\%(.*)\%\%$/s", $thing, $matches)) {
            // check if a language has been specified

            $code = $matches[1];

            if (preg_match("/^\((.+?)\)(.*)$/s", $code, $matches)) {
                [, $language, $code] = $matches;
            }

            switch ($language) {
                case 'php':
                    $formatter = 'php';
                    break;
                default:
                    $formatter = 'code';
            }

            $output = '<div class="code">';

            $output .= $wakka->Format(trim($code), $formatter);

            $output .= '</div>';

            return $output;
        } // forced links

        elseif (preg_match("/^\[\[(\S*)(\s+(.+))?\]\]$/", $thing, $matches)) {
            [, $url, , $text] = $matches;

            if ($url) {
                if (!$text) {
                    $text = $url;
                }

                return $wakka->Link($url, '', $text);
            }

            return '';
        } // indented text

        elseif (preg_match("/\n(\t+)(-|([0-9,a-z,A-Z]+)\))?(\n|$)/s", $thing, $matches)) {
            // new line

            $result .= ($br ? "<br>\n" : "\n");

            // we definitely want no line break in this one.

            $br = 0;

            // find out which indent type we want

            $newIndentType = $matches[2];

            if (!$newIndentType) {
                $opener = '<div class="indent">';

                $closer = '</div>';

                $br = 1;
            } elseif ('-' == $newIndentType) {
                $opener = '<ul><li>';

                $closer = '</li></ul>';

                $li = 1;
            } else {
                $opener = '<ol type="' . $newIndentType . '"><li>';

                $closer = '</li></ol>';

                $li = 1;
            }

            // get new indent level

            $newIndentLevel = mb_strlen($matches[1]);

            if ($newIndentLevel > $oldIndentLevel) {
                for ($i = 0; $i < $newIndentLevel - $oldIndentLevel; $i++) {
                    $result .= $opener;

                    $indentClosers[] = $closer;
                }
            } elseif ($newIndentLevel < $oldIndentLevel) {
                for ($i = 0; $i < $oldIndentLevel - $newIndentLevel; $i++) {
                    $result .= array_pop($indentClosers);
                }
            }

            $oldIndentLevel = $newIndentLevel;

            if ($li && !preg_match('/' . str_replace(')', "\)", $opener) . '$/', $result)) {
                $result .= '</li><li>';
            }

            return $result;
        } // new lines

        elseif ("\n" == $thing) {
            // if we got here, there was no tab in the next line; this means that we can close all open indents.

            $c = count($indentClosers);

            for ($i = 0; $i < $c; $i++) {
                $result .= array_pop($indentClosers);

                $br = 0;
            }

            $oldIndentLevel = 0;

            $result .= ($br ? "<br>\n" : "\n");

            $br = 1;

            return $result;
        } // events

        elseif (preg_match("/^\{\{(.*?)\}\}$/s", $thing, $matches)) {
            if ($matches[1]) {
                return $wakka->Action($matches[1]);
            }

            return '{{}}';
        } // interwiki links!

        elseif (preg_match('/^[A-Z][A-Z,a-z]+[:]([A-Z,a-z,0-9]*)$/s', $thing)) {
            return $wakka->Link($thing);
        } // wakka links!

        elseif (preg_match('/^[A-Z][a-z]+[A-Z,0-9][A-Z,a-z,0-9]*$/s', $thing)) {
            return $wakka->Link($thing);
        }

        // if we reach this point, it must have been an accident.

        return $thing;
    }
}
$text = str_replace("\r", '', $text);
$text = trim($text) . "\n";
$text = preg_replace_callback(
    "/(\%\%.*?\%\%|\"\".*?\"\"|\[\[.*?\]\]|\b[a-z]+:\/\/\S+|\'\'|\*\*|\#\#|__|<|>|\/\/|======.*?======|=====.*?=====|====.*?====|===.*?===|==.*?==|----|---|\n\t+(-|[0-9,a-z,A-Z]+\))?|\{\{.*?\}\}|" . "\b[A-Z][A-Z,a-z]+[:]([A-Z,a-z,0-9]*)\b|" . "\b([A-Z][a-z]+[A-Z,0-9][A-Z,a-z,0-9]*)\b|" . "\n)/ms",
    'wakka2callback',
    $text
);
// we're cutting the last <br>
$text = preg_replace("/<br \>$/", '', trim($text));
print($text);
