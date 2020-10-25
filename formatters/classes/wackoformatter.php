<?php

/*
WackoFormatter
--------
Version 2.0.3.
WackoWiki mod.
http://wackowiki.com/projects/wackoformatter
--------
*/

class WackoFormatter
{
    public $object;

    public $oldIndentLevel = 0;

    public $indentClosers = [];

    public $tdoldIndentLevel = 0;

    public $tdindentClosers = [];

    public $br = 1;

    public $intable = 0;

    public $intablebr = 0;

    public $cols = 0;

    public function __construct(&$object)
    {
        $this->object = &$object;

        $this->LONGREGEXP = "/(\xa5\xa5.*?\xa5\xa5|"
                               . (1 == $object->GetConfigValue('allow_rawhtml') ? "\<\#.*?\#\>|" : '')
                               . "\(\?(\S+?)([ \t]+([^\n]+?))?\?\)|"
                               . (1 == $object->GetConfigValue('disable_bracketslinks') ? '' : "\[\[(\S+?)([ \t]+([^\n]+?))?\]\]|\(\((\S+?)([ \t]+([^\n]+?))?\)\)|"
                                                                                               . "\/\/([^\n]*?"
                                                                                               . '('
                                                                                               . "\[\[(\S+?)([ \t]+([^\n]+?))?\]\]|\(\((\S+?)([ \t]+([^\n]+?))?\)\)"
                                                                                               . ')'
                                                                                               . "[^\n]*?)+\/\/|")
                               . "\^\^\S*?\^\^|vv\S*?vv|"
                               . "\n[ \t]*>+[^\n]*|"
                               . "<\[.*?\]>|"
                               . "\+\+[^\n]*?\+\+|"
                               . "\b[[:alpha:]]+:\/\/\S+|mailto\:[[:alnum:]\-\_\.]+\@[[:alnum:]\-\_\.]+|\?\?\S\?\?|\?\?(\S.*?\S)\?\?|"
                               . '\\\\\\\\['
                               . $object->language['ALPHANUM_P']
                               . "\-\_\\\!\.]+|"
                               . "\*\*[^\n]*?\*\*|\#\#[^\n]*?\#\#|\'\'.*?\'\'|\!\!\S\!\!|\!\!(\S.*?\S)\!\!|__[^\n]*?__|"
                               . "\xA4\xA4\S\xA4\xA4|\xA3\xA3\S\xA3\xA3|\xA4\xA4(\S.*?\S)\xA4\xA4|\xA3\xA3(\S.*?\S)\xA3\xA3|"
                               . "\#\|\||\#\||\|\|\#|\|\#|\|\|.*?\|\||"
                               . "<|>|\/\/[^\n]*?\/\/|"
                               . "\n[ \t]*=======.*?={2,6}|\n[ \t]*======.*?={2,6}|\n[ \t]*=====.*?={2,6}|\n[ \t]*====.*?={2,6}|\n[ \t]*===.*?={2,6}|\n[ \t]*==.*?={2,6}|"
                               . "[-]{4,}|---\n?\s*|--\S--|--(\S.*?[^- \t\n\r])--|"
                               . "\n(\t+|([ ]{2})+)(-|\*|[0-9,a-z,A-Z]{1,2}[\.\)](\#[0-9]{1,3})?)?|"
                               . "\b[[:alnum:]]+[:]["
                               . $object->language['ALPHANUM_P']
                               . "\!\.]["
                               . $object->language['ALPHANUM_P']
                               . "\-\_\.\+\&\=]+|"
                               . "~([^ \t\n]+)|"
                               . (1 == $object->GetConfigValue('disable_tikilinks') ? '' : "\b(" . $object->language['UPPER'] . $object->language['LOWER'] . $object->language['ALPHANUM'] . "*\." . $object->language['ALPHA'] . $object->language['ALPHANUM'] . "+)\b|")
                               . (1 == $this->object->GetConfigValue('disable_wikilinks') ? '' : "(~?)(?<=[^\."
                                                                                                 . $object->language['ALPHANUM_P']
                                                                                                 . "]|^)(((\.\.|!)?\/)?"
                                                                                                 . $object->language['UPPER']
                                                                                                 . $object->language['LOWER']
                                                                                                 . '+'
                                                                                                 . $object->language['UPPERNUM']
                                                                                                 . $object->language['ALPHANUM']
                                                                                                 . "*)\b|")
                               . (1 == $this->object->GetConfigValue('disable_npjlinks') ? '' : '(~?)'
                                                                                                . $object->language['ALPHANUM']
                                                                                                . "+\@"
                                                                                                . $object->language['ALPHA']
                                                                                                . '*(?!'
                                                                                                . $object->language['ALPHANUM']
                                                                                                . "*\."
                                                                                                . $object->language['ALPHANUM']
                                                                                                . "+)(\:"
                                                                                                . $object->language['ALPHANUM']
                                                                                                . '*)?|'
                                                                                                . $object->language['ALPHANUM']
                                                                                                . "+\:\:"
                                                                                                . $object->language['ALPHANUM']
                                                                                                . '+|')
                               . "\n)/sm";

        $this->NOTLONGREGEXP = '/(' . (1 == $this->object->GetConfigValue('disable_formatters') ? '' : "\%\%.*?\%\%|") . "~([^ \t\n]+)|" . '"".*?""|' . "\{\{[^\n]*?\}\}|" . "\xa5\xa5.*?\xa5\xa5" . ')/sm';

        $this->MOREREGEXP = '/(>>.*?<<|' . "~([^ \t\n]+)|" . "\xa5\xa5.*?\xa5\xa5" . ')/sm';
    }

    public function IndentClose()
    {
        if ($this->intable) {
            $Closers = &$this->tdindentClosers;
        } else {
            $Closers = &$this->indentClosers;
        }

        $c = count($Closers);

        for ($i = 0; $i < $c; $i++) {
            $result .= array_pop($Closers);
        }

        if ($this->intable) {
            $this->tdoldIndentLevel = 0;
        } else {
            $this->oldIndentLevel = 0;
        }

        return $result;
    }

    public function WackoPreprocess($things)
    {
        $thing = $things[1];

        $wacko = &$this->object;

        $callback = [&$this, 'wackoPreprocess'];

        if ('~' == $thing[0]) {
            if ('~' == $thing[1]) {
                return '~~' . $this->WackoPreprocess([0, mb_substr($thing, 2)]);
            }
        }

        // escaped text

        if (preg_match("/^\xa5\xa5(.*)\xa5\xa5$/s", $thing, $matches)) {
            return $matches[1];
        } // escaped text

        elseif (preg_match('/^""(.*)""$/s', $thing, $matches)) {
            return "\xa5\xa5<!--notypo-->" . str_replace("\n", '<br>', htmlspecialchars($matches[1], ENT_QUOTES | ENT_HTML5)) . "<!--/notypo-->\xa5\xa5";
        } // code text

        elseif (preg_match("/^\%\%(.*)\%\%$/s", $thing, $matches)) {
            // check if a formatter has been specified

            $code = $matches[1];

            if (preg_match("/^\(([^\n]+?)\)(.*)$/s", $code, $matches)) {
                $code = $matches[2];

                if ($matches[1]) {
                    // разборка на параметры делать имхо.

                    $sep = mb_strpos($matches[1], ' ');

                    if (false === $sep) {
                        $formatter = $matches[1];

                        $params = [];
                    } else {
                        $formatter = mb_substr($matches[1], 0, $sep);

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

                            if (0 == $c) {
                                $params['_default'] = $m[2];
                            }

                            $c++;
                        }
                    }
                }
            }

            $formatter = mb_strtolower($formatter);

            if ("\xF1" == $formatter) {
                $formatter = 'c';
            }

            if ('c' == $formatter) {
                $formatter = 'comments';
            }

            if ('' == $formatter) {
                $formatter = 'code';
            }

            $output .= $wacko->Format(trim($code), 'highlight/' . $formatter, $params);

            return "\xa5\xa5" . $output . "\xa5\xa5";
        } // actions
        elseif (preg_match("/^\{\{(.*?)\}\}$/s", $thing, $matches)) { // used in paragrafica, too
            return "\xa5\xa5<!--notypo-->\xA1\xA1" . $matches[1] . "\xA1\xA1<!--/notypo-->\xa5\xa5";
        }

        // if we reach this point, it must have been an accident.

        return $thing;
    }

    public function WackoMiddleprocess($things)
    {
        $thing = $things[1];

        $wacko = &$this->object;

        $callback = [&$this, 'WackoCallback'];

        if ('~' == $thing[0]) {
            if ('~' == $thing[1]) {
                return '~~' . $this->WackoMiddleprocess([0, mb_substr($thing, 2)]);
            }
        }

        // escaped text

        if (preg_match("/^\xa5\xa5(.*)\xa5\xa5$/s", $thing, $matches)) {
            return $matches[1];
        } // centered text

        elseif (preg_match('/^>>(.*)<<$/s', $thing, $matches)) {
            return "\xa5\xa5<div class=\"center\">" . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . "</div>\xa5\xa5";
        }

        return $thing;
    }

    public function WackoCallback($things)
    {
        $thing = $things[1];

        $wacko = &$this->object;

        $callback = [&$this, 'WackoCallback'];

        // convert HTML thingies

        if ('<' == $thing) {
            return '&lt;';
        } elseif ('>' == $thing) {
            return '&gt;';
        } // escaped text

        elseif (preg_match("/^\xa5\xa5(.*)\xa5\xa5$/s", $thing, $matches)) {
            return $matches[1];
        } // escaped html

        elseif (preg_match("/^\<\#(.*)\#\>$/s", $thing, $matches)) {
            return '<!--notypo-->' . $wacko->Format($matches[1], 'safehtml') . '<!--/notypo-->';
        } //table begin

        elseif ('#||' == $thing) {
            $this->br = 0;

            $this->cols = 0;

            $this->intablebr = true;

            return '<table class="dtable" border="0">';
        } //table end

        elseif ('||#' == $thing) {
            $this->br = 0;

            $this->intablebr = false;

            return '</table>';
        } elseif ('#|' == $thing) {
            $this->br = 0;

            $this->cols = 0;

            $this->intablebr = true;

            return '<table class="usertable" border="1">';
        } //table end

        elseif ('|#' == $thing) {
            $this->br = 0;

            $this->intablebr = false;

            return '</table>';
        } elseif (preg_match("/^\|\|(.*?)\|\|$/s", $thing, $matches)) {
            $this->br = 1;

            $this->intable = true;

            $this->intablebr = false;

            $output = '<tr class="userrow">';

            $cells = preg_split("\|", $matches[1]);

            $count = count($cells);

            $count--;

            for ($i = 0; $i < $count; $i++) {
                $this->tdoldIndentLevel = 0;

                $this->tdindentClosers = [];

                if ("\n" == $cells[$i][0]) {
                    $cells[$i] = mb_substr($cells[$i], 1);
                }

                $output .= str_replace("\177", '', str_replace("\177" . "<br>\n", '', '<td class="usercell">' . preg_replace_callback($this->LONGREGEXP, $callback, "\177\n" . $cells[$i])));

                $output .= $this->IndentClose();

                $output .= '</td>';
            }

            if ((0 != $this->cols) and ($count < $this->cols)) {
                $this->tdoldIndentLevel = 0;

                $this->tdindentClosers = [];

                if ("\n" == $cells[$i][0]) {
                    $cells[$count] = mb_substr($cells[$count], 1);
                }

                $output .= str_replace("\177", '', str_replace("\177" . "<br>\n", '', '<td class="usercell" colspan=' . ($this->cols - $count + 1) . '>' . preg_replace_callback($this->LONGREGEXP, $callback, "\177\n" . $cells[$count])));

                $output .= $this->IndentClose();

                $output .= '</td>';
            } else {
                $this->tdoldIndentLevel = 0;

                $this->tdindentClosers = [];

                if ("\n" == $cells[$i][0]) {
                    $cells[$count] = mb_substr($cells[$count], 1);
                }

                $output .= str_replace("\177", '', str_replace("\177" . "<br>\n", '', '<td class="usercell">' . preg_replace_callback($this->LONGREGEXP, $callback, "\177\n" . $cells[$count])));

                $output .= $this->IndentClose();

                $output .= '</td>';
            }

            $output .= '</tr>';

            if (0 == $this->cols) {
                $this->cols = $count;
            }

            $this->intablebr = true;

            $this->intable = false;

            return $output;
        } // Deleted

        elseif (preg_match("/^\xA4\xA4((\S.*?\S)|(\S))\xA4\xA4$/s", $thing, $matches)) {
            $this->br = 0;

            return '<span class="del">' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</span>';
        } // Inserted

        elseif (preg_match("/^\xA3\xA3((\S.*?\S)|(\S))\xA3\xA3$/s", $thing, $matches)) {
            $this->br = 0;

            return '<span class="add">' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</span>';
        } // bold

        elseif (preg_match("/^\*\*(.*?)\*\*$/", $thing, $matches)) {
            return '<strong>' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</strong>';
        } // italic

        elseif (preg_match("/^\/\/(.*?)\/\/$/", $thing, $matches)) {
            return '<em>' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</em>';
        } // underlinue

        elseif (preg_match('/^__(.*?)__$/', $thing, $matches)) {
            return '<u>' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</u>';
        } // monospace

        elseif (preg_match("/^\#\#(.*?)\#\#$/", $thing, $matches)) {
            return '<tt>' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</tt>';
        } // small

        elseif (preg_match("/^\+\+(.*?)\+\+$/", $thing, $matches)) {
            return '<small>' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</small>';
        } // cite

        elseif (preg_match("/^\'\'(.*?)\'\'$/s", $thing, $matches)
                || preg_match("/^\!\!((\S.*?\S)|(\S))\!\!$/s", $thing, $matches)) {
            $this->br = 1;

            return '<span class="cite">' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</span>';
        } elseif (preg_match("/^\?\?((\S.*?\S)|(\S))\?\?$/s", $thing, $matches)) {
            $this->br = 1;

            return '<span class="mark">' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</span>';
        } // urls

        elseif (preg_match("/^([[:alpha:]]+:\/\/\S+?|mailto\:[[:alnum:]\-\_\.]+\@[[:alnum:]\-\.\_]+?)([^[:alnum:]^\/\-\_\=]?)$/", $thing, $matches)) {
            $url = mb_strtolower($matches[1]);

            if ('.jpg' == mb_substr($url, -4) || '.gif' == mb_substr($url, -4) || '.png' == mb_substr($url, -4) || '.jpe' == mb_substr($url, -4)
                || '.jpeg' == mb_substr($url, -5)) {
                return '<img src="' . $matches[1] . '">' . $matches[2];
            }

            return $wacko->PreLink($matches[1]) . $matches[2];
        } // lan path
        elseif (preg_match('/^\\\\\\\\([' . $wacko->language['ALPHANUM_P'] . "\\\!\.\-\_]+)$/", $thing, $matches)) {//[[:alnum:]\\\!\.\_\-]+\\
            return '<a href="file://///' . str_replace('\\', '/', $matches[1]) . '">\\\\' . $matches[1] . '</a>';
        } // citated

        elseif (preg_match("/^\n[ \t]*(>+)(.*)$/s", $thing, $matches)) {
            return '<div class="email' . mb_strlen($matches[1]) . ' email-' . (mb_strlen($matches[1]) % 2 ? 'odd' : 'even') . '">' . htmlspecialchars($matches[1], ENT_QUOTES | ENT_HTML5) . preg_replace_callback($this->LONGREGEXP, $callback, $matches[2]) . '</div>';
        } // blockquote

        elseif (preg_match("/^<\[(.*)\]>$/s", $thing, $matches)) {
            //$this->br = 0;

            return '<blockquote>' . preg_replace_callback($this->LONGREGEXP, $callback, trim($matches[1], " \n\r\t")) . '</blockquote>';
        } // super

        elseif (preg_match("/^\^\^(.*)\^\^$/", $thing, $matches)) {
            return '<sup>' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</sup>';
        } // sub

        elseif (preg_match('/^vv(.*)vv$/', $thing, $matches)) {
            return '<sub>' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</sub>';
        } // headers

        elseif (preg_match("/\n[ \t]*=======(.*?)={2,6}$/", $thing, $matches)) {
            $result = $this->IndentClose();

            $this->br = 0;

            $wacko->headerCount++;

            return $result . '<a name="h' . $wacko->page['id'] . '-' . $wacko->headerCount . '"></a><h6>' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</h6>';
        } elseif (preg_match("/\n[ \t]*======(.*?)={2,6}$/", $thing, $matches)) {
            $result = $this->IndentClose();

            $this->br = 0;

            $wacko->headerCount++;

            return $result . '<a name="h' . $wacko->page['id'] . '-' . $wacko->headerCount . '"></a><h5>' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</h5>';
        } elseif (preg_match("/\n[ \t]*=====(.*?)={2,6}$/", $thing, $matches)) {
            $result = $this->IndentClose();

            $this->br = 0;

            $wacko->headerCount++;

            return $result . '<a name="h' . $wacko->page['id'] . '-' . $wacko->headerCount . '"></a><h4>' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</h4>';
        } elseif (preg_match("/\n[ \t]*====(.*?)={2,6}$/", $thing, $matches)) {
            $result = $this->IndentClose();

            $this->br = 0;

            $wacko->headerCount++;

            return $result . '<a name="h' . $wacko->page['id'] . '-' . $wacko->headerCount . '"></a><h3>' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</h3>';
        } elseif (preg_match("/\n[ \t]*===(.*?)={2,6}$/", $thing, $matches)) {
            $result = $this->IndentClose();

            $this->br = 0;

            $wacko->headerCount++;

            return $result . '<a name="h' . $wacko->page['id'] . '-' . $wacko->headerCount . '"></a><h2>' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</h2>';
        } elseif (preg_match("/\n[ \t]*==(.*?)={2,6}$/", $thing, $matches)) {
            $result = $this->IndentClose();

            $this->br = 0;

            $wacko->headerCount++;

            return $result . '<a name="h' . $wacko->page['id'] . '-' . $wacko->headerCount . '"></a><h1>' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</h1>';
        } // separators

        elseif (preg_match('/^[-]{4,}$/', $thing)) {
            $this->br = 0;

            return '<hr noshade="noshade" size="1">';
        } // forced line breaks

        elseif (preg_match("/^---\n?\s*$/", $thing, $matches)) {
            return "<br>\n";
        } // strike
        elseif (preg_match("/^--((\S.*?\S)|(\S))--$/s", $thing, $matches)) { //NB: wrong
            return '<s>' . preg_replace_callback($this->LONGREGEXP, $callback, $matches[1]) . '</s>';
        } // definitions

        elseif ((preg_match("/^\(\?(.+)(==|\|)(.*)\?\)$/", $thing, $matches))
                || (preg_match("/^\(\?(\S+)(\s+(.+))?\?\)$/", $thing, $matches))) {
            [, $def, , $text] = $matches;

            if ($def) {
                if ('' == $text) {
                    $text = $def;
                }

                $text = preg_replace("/\xA4\xA4|__|\[\[|\(\(/", '', $text);

                return '<dfn title="' . htmlspecialchars($text, ENT_QUOTES | ENT_HTML5) . '">' . $def . '</dfn>';
            }

            return '';
        } // forced links & footnotes

        elseif ((preg_match("/^\[\[(.+)(==|\|)(.*)\]\]$/", $thing, $matches))
                || (preg_match("/^\(\((.+)(==|\|)(.*)\)\)$/", $thing, $matches)) || (preg_match("/^\[\[(\S+)(\s+(.+))?\]\]$/", $thing, $matches))
                || (preg_match("/^\(\((\S+)(\s+(.+))?\)\)$/", $thing, $matches))) {
            [, $url, , $text] = $matches;

            if ($url) {
                if ('*' == $url[0]) {
                    $sup = 1;

                    if (preg_match("/^\*+$/", $url)) {
                        $aname = 'ftn' . mb_strlen($url);

                        if (!$text) {
                            $text = $url;
                        }
                    } elseif (preg_match("/^\*\d+$/", $url)) {
                        $aname = 'ftnd' . mb_substr($url, 1);
                    } else {
                        $aname = htmlspecialchars(mb_substr($url, 1), ENT_QUOTES | ENT_HTML5);

                        $sup = 0;
                    }

                    if (!$text) {
                        $text = mb_substr($url, 1);
                    }

                    return ($sup ? '<sup>' : '') . '<a href="#o' . $aname . '" name="' . $aname . '">' . $text . '</a>' . ($sup ? '</sup>' : '');
                } elseif ('#' == $url[0]) {
                    $anchor = mb_substr($url, 1);

                    $sup = 1;

                    if (preg_match("/^\*+$/", $anchor)) {
                        $ahref = 'ftn' . mb_strlen($anchor);
                    } elseif (preg_match("/^\d+$/", $anchor)) {
                        $ahref = 'ftnd' . $anchor;
                    } else {
                        $ahref = htmlspecialchars($anchor, ENT_QUOTES | ENT_HTML5);

                        $sup = 0;
                    }

                    if (!$text) {
                        $text = mb_substr($url, 1);
                    }

                    return ($sup ? '<sup>' : '') . '<a href="#' . $ahref . '" name="o' . $ahref . '">' . $text . '</a>' . ($sup ? '</sup>' : '');
                }  

                if ($url != ($url = (preg_replace("/\xA4\xA4|\xA3\xA3|\[\[|\(\(/", '', $url)))) {
                    $result = '</span>';
                }

                if ('(' == $url[0]) {
                    $url = mb_substr($url, 1);

                    $result .= '(';
                }

                if ('[' == $url[0]) {
                    $url = mb_substr($url, 1);

                    $result .= '[';
                }

                if (!$text) {
                    $text = $url;
                }

                $url = str_replace(' ', '', $url);

                $text = preg_replace("/\xA4\xA4|\xA3\xA3|\[\[|\(\(/", '', $text);

                return $result . $wacko->PreLink($url, $text);
            }

            return '';
        } // indented text

        elseif (preg_match("/(\n)(\t+|(?:[ ]{2})+)(-|\*|([0-9,a-z,A-Z]{1,2})[\.\)](\#[0-9]{1,3})?)?(\n|$)/s", $thing, $matches)) {
            // new line

            $result .= ($this->br ? "<br>\n" : "\n");

            //intable or not?

            if ($this->intable) {
                $Closers = &$this->tdindentClosers;

                $oldlevel = &$this->tdoldIndentLevel;

                $oldtype = &$this->tdoldIndentType;
            } else {
                $Closers = &$this->indentClosers;

                $oldlevel = &$this->oldIndentLevel;

                $oldtype = &$this->oldIndentType;
            }

            // we definitely want no line break in this one.

            $this->br = 0;

            //#18 syntax support

            if ($matches[5]) {
                $start = mb_substr($matches[5], 1);
            } else {
                $start = '';
            }

            // find out which indent type we want

            $newIndentType = $matches[3][0];

            if (!$newIndentType) {
                $opener = '<div class="indent">';

                $closer = '</div>';

                $this->br = 1;

                $newtype = 'i';
            } elseif ('-' == $newIndentType || '*' == $newIndentType) {
                $opener = '<ul><li>';

                $closer = '</li></ul>';

                $li = 1;

                $newtype = '*';
            } else {
                $opener = '<ol type="' . $newIndentType . '"><li' . ($start ? ' value="' . $start . '"' : '') . '>';

                $closer = '</li></ol>';

                $li = 1;

                $newtype = '1';
            }

            // get new indent level

            if (' ' == $matches[2][0]) {
                $newIndentLevel = (int)(mb_strlen($matches[2]) / 2);
            } else {
                $newIndentLevel = mb_strlen($matches[2]);
            }

            if ($newIndentLevel > $oldlevel) {
                for ($i = 0; $i < $newIndentLevel - $oldlevel; $i++) {
                    $result .= $opener;

                    $Closers[] = $closer;
                }
            } elseif ($newIndentLevel < $oldlevel) {
                for ($i = 0; $i < $oldlevel - $newIndentLevel; $i++) {
                    $result .= array_pop($Closers);
                }
            } elseif ($newIndentLevel == $oldlevel && $oldtype != $newtype) {
                $result .= array_pop($Closers);

                $result .= $opener;

                $Closers[] = $closer;
            }

            $oldlevel = $newIndentLevel;

            $oldtype = $newtype;

            if ($li && !preg_match('/' . str_replace(')', "\)", $opener) . '$/', $result)) {
                $result .= '</li><li' . ($start ? ' value="' . $start . '"' : '') . '>';
            }

            return $result;
        } // new lines

        elseif ("\n" == $thing && !$this->intablebr) {
            // if we got here, there was no tab in the next line; this means that we can close all open indents.

            $result = $this->IndentClose();

            if ($result) {
                $this->br = 0;
            }

            $result .= $this->br ? "<br>\n" : "\n";

            $this->br = 1;

            return $result;
        } // interwiki links

        elseif (preg_match('/^([[:alnum:]]+[:][' . $wacko->language['ALPHANUM_P'] . "\!\.][" . $wacko->language['ALPHANUM_P'] . "\-\_\.\+\&\=]+?)([^[:alnum:]^\/\-\_\=]?)$/s", $thing, $matches)) {
            return $wacko->PreLink($matches[1]) . $matches[2];
        } // tikiwiki links

        elseif ((!$wacko->_formatter_noautolinks) && 1 != $wacko->GetConfigValue('disable_tikilinks')
                && (preg_match('/^(' . $wacko->language['UPPER'] . $wacko->language['LOWER'] . $wacko->language['ALPHANUM'] . "*\." . $wacko->language['ALPHA'] . $wacko->language['ALPHANUM'] . '+)$/s', $thing, $matches))) {
            return $wacko->PreLink($thing);
        } // npj links

        elseif ((!$wacko->_formatter_noautolinks)
                && (preg_match('/^(~?)(' . $wacko->language['ALPHANUM'] . "+\@" . $wacko->language['ALPHA'] . "*(\:" . $wacko->language['ALPHANUM'] . '*)?|' . $wacko->language['ALPHANUM'] . "+\:\:" . $wacko->language['ALPHANUM'] . '+)$/s', $thing, $matches))) {
            if ('~' == $matches[1]) {
                return $matches[2];
            }

            return $wacko->PreLink($thing);
        } // wacko links!

        elseif ((!$wacko->_formatter_noautolinks)
                && (preg_match("/^(((\.\.)|!)?\/?|~)?(" . $wacko->language['UPPER'] . $wacko->language['LOWER'] . '+' . $wacko->language['UPPERNUM'] . $wacko->language['ALPHANUM'] . '*)$/s', $thing, $matches))) {
            if ('~' == $matches[1]) {
                return $matches[4];
            }

            return $wacko->PreLink($thing);
        }

        if (('~' == $thing[0]) && ('~' != $thing[1])) {
            $thing = ltrim($thing, '~');
        }

        if (('~' == $thing[0]) && ('~' == $thing[1])) {
            return '~' . preg_replace_callback($this->LONGREGEXP, $callback, mb_substr($thing, 2));
        }

        // if we reach this point, it must have been an accident.

        return htmlspecialchars($thing, ENT_QUOTES | ENT_HTML5);
    }
}
