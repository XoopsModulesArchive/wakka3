<?php
//
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 The PHP Group  |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license, |
// | that is bundled with this package in the file LICENSE, and is |
// | available at through the world-wide-web at |
// | http://www.php.net/license/3_0.txt.  |
// | If you did not receive a copy of the PHP license and are unable to |
// | obtain it through the world-wide-web, please send a note to |
// | license@php.net so we can mail you a copy immediately. |
// +----------------------------------------------------------------------+
// | Author: Andrey Demenev <demenev@on-line.jar.ru> |
// +----------------------------------------------------------------------+
// $Id: Highlighter.php,v 1.2 2004/06/22 13:15:47 kukutz Exp $
/**
 * Text highlighter base class
 */
/**#@+
 * Constant for use with $options['tag']
 * @see Text_Highlighter::_init()
 */
/**
 * use CODE as top-level tag
 */
define('HL_TAG_CODE', 'code');
/**
 * use PRE as top-level tag
 */
define('HL_TAG_PRE', 'pre');
/**#@-*/
/**#@+
 * Constant for use with $options['numbers']
 * @see Text_Highlighter::_init()
 */
/**
 * use numbered list
 */
define('HL_NUMBERS_LI', 1);
/**
 * Use 2-column table with line numbers in left column and code in right column.
 * Forces $options['tag'] = HL_TAG_PRE
 */
define('HL_NUMBERS_TABLE', 2);
/**#@-*/
/**
 * just a big number, bigger than any string's length
 */
define('HL_BIG_NUM', 1000000000);

/**
 * Text highlighter base class
 *
 * This class implements all functions necessary for highlighting,
 * but it does not contain highlighting rules. Actual highlighting is
 * done using a descendent of this class.
 *
 * One is not supposed to manually create descendent classes.
 * Instead, describe highlighting rules in XML format and
 * use {@link Text_Highlighter_Generator} to create descendent class.
 * Alternatively, an instance of a descendent class can be created
 * directly.
 *
 * Use {@link Text_Highlighter::factory()} to create an
 * object for particular language highlighter
 *
 * Usage example
 * <code>
 * require_once __DIR__ . '/Text/Highlighter.php';
 * $hlSQL =& Text_Highlighter::factory('SQL',array('numbers'=>true));
 * echo $hlSQL->highlight('SELECT * FROM table a WHERE id = 12');
 * </code>
 *
 * @author  Andrey Demenev <demenev@on-line.jar.ru>
 */
class Text_Highlighter
{
    /**
     * Syntax highlighting rules.
     * Auto-generated classes set this var
     *
     * @see    _init
     * @var array
     */

    public $_syntax;

    /**#@+
     * @access private
     * @see    _init
     */

    /**
     * CSS class of last outputted text chunk
     *
     * @var string
     */

    public $_lastClass;

    /**
     * HTML tag surrounding the code
     *
     * @var string
     */

    public $_tag = HL_TAG_PRE;

    /**
     * Line numbering style
     *
     * @var int
     */

    public $_numbers = 0;

    /**
     * Tab size
     *
     * @var int
     */

    public $_tabsize = 4;

    /**#@-*/

    /**
     * Highlighted text
     *
     * @var string
     */

    public $_output = '';

    /**
     * Create a new Highlighter object for specified language
     *
     * @param string $lang    language, for example "SQL"
     * @param array  $options Highlighting options. This array is passed
     *                        to new object's constructor, and the constructor, in turn,
     *                        passes it to {@link Text_Highlighter::_init()}
     *
     * @return mixed a newly created Highlighter object, or a PEAR error object on
     * error
     *
     * @static
     * @see    Text_Highlighter::_init()
     */
    public function factory($lang, $options = [])
    {
        $lang = mb_strtoupper($lang);

        @require_once __DIR__ . '/Highlighter/' . $lang . '.php';

        $classname = 'Text_Highlighter_' . $lang;

        if (!class_exists($classname)) {
            return 'Highlighter for ' . $lang . ' not found';
        }

        $obj = new $classname($options);

        return $obj;
    }

    /**
     * Initilizes highlighting options
     *
     *
     * @param array $options Output options.
     *                       Assocative array with following elements (each being optional):
     *
     * - 'tag' - {@link HL_TAG_PRE} or {@link HL_TAG_CODE}
     * - 'numbers' - Whether to add line numbering
     * - 'tabsize' - Tab size
     * - 'style' - CSS style of top-level tag
     *
     * Passing any of these elements overwrites the default value of
     * corresponding variable.
     *
     * Descendents of Text_Highlighter call this method from the constructor,
     * passing $options they get as parameter.
     */
    public function _init($options = [])
    {
        $this->_lastClass = 'default';

        if (isset($options['tag'])) {
            $this->_tag = $options['tag'];
        }

        if (isset($options['numbers'])) {
            $this->_numbers = (int)$options['numbers'];

            if (HL_NUMBERS_LI != $this->_numbers && HL_NUMBERS_TABLE != $this->_numbers) {
                $this->_numbers = 0;
            }
        }

        if (isset($options['tabsize'])) {
            $this->_tabsize = $options['tabsize'];
        }

        if (HL_NUMBERS_TABLE == $this->_numbers) {
            $this->_tag = HL_TAG_PRE;
        }
    }

    /**
     * Highlights code
     *
     * @param string $str Code to highlight
     * @return string Highlighted text
     */
    public function highlight($str)
    {
        $this->_output = '';

        // normalize whitespace and tabs

        $str = str_replace("\r\n", "\n", $str);

        $str = str_replace("\t", str_repeat(' ', $this->_tabsize), $str);

        // current position in string

        $pos = 0;

        // nested regions stack

        $stack = [];

        // current region

        $current = null;

        // what to seek first

        $blocksToSeek = $this->_syntax['toplevel']['blocks'] ?? null;

        $regionsToSeek = $this->_syntax['toplevel']['regions'] ?? null;

        $defClass = $this->_syntax['defClass'];

        $matches = null;

        while (true) {
            // init loop vars

            $matchpos = HL_BIG_NUM;

            $matchlen = 0;

            $what = -1;

            $thematch = null;

            $theregion = null;

            // get rid of the chars already processed

            $substr = mb_substr($str, $pos);

            if (false === $substr) {
                break;
            }

            // trick for speeding up blocks lookups

            $firstline = $substr;

            // look for blocks, either top-level or

            // allowed within current region

            if ($matchpos && $blocksToSeek) {
                foreach ($blocksToSeek as $region) {
                    $region = $this->_syntax['blocks'][$region];

                    if ($current) {
                        $defClass = $current['innerClass'];
                    }

                    if (preg_match($region['match'], $firstline, $matches, PREG_OFFSET_CAPTURE)
                        && $matchpos > $matches[0][1]) {
                        $matchlen = mb_strlen($matches[0][0]);

                        $matchpos = $matches[0][1];

                        $thematch = $matches[0][0];

                        $thematches = $matches;

                        $what = 1;

                        $theregion = $region;

                        if (!$region['multiline']) {
                            // if found a block, and it is not multi-line

                            // then remove all after that line from the subject string

                            $newlinePos = mb_strpos($firstline, "\n", $matchpos);

                            if ($newlinePos) {
                                $firstline = mb_substr($firstline, 0, $newlinePos);
                            }
                        }
                    }

                    if (!$matchpos) {
                        break;
                    }
                }
            }

            // look for start of region, either top-level or

            // allowed within current region

            if ($matchpos && $regionsToSeek) {
                foreach ($regionsToSeek as $region) {
                    $region = $this->_syntax['regions'][$region];

                    if ($current) {
                        $defClass = $current['innerClass'];
                    }

                    if (preg_match($region['start'], $substr, $matches, PREG_OFFSET_CAPTURE)
                        && $matchpos > $matches[0][1]) {
                        $matchlen = mb_strlen($matches[0][0]);

                        $matchpos = $matches[0][1];

                        $thematch = $matches[0][0];

                        $what = 0;

                        if ($region['remember']) {
                            foreach ($matches as $i => $amatch) {
                                $quoted = preg_quote($amatch[0]);

                                $region['end'] = str_replace('%' . $i . '%', $quoted, $region['end']);
                            }
                        }

                        $theregion = $region;
                    }

                    if (!$matchpos) {
                        break;
                    }
                }
            }

            // look for end of region

            if ($matchpos
                && $current && preg_match($current['end'], $substr, $matches, PREG_OFFSET_CAPTURE)
                && $matchpos > $matches[0][1]) {
                $matchlen = mb_strlen($matches[0][0]);

                $matchpos = $matches[0][1];

                $thematch = $matches[0][0];

                $what = 2;

                $theregion = $region;
            }

            switch ($what) {
                // found start of region
                case 0:
                    if ($matchpos) {
                        $this->_chunk(mb_substr($substr, 0, $matchpos), $defClass);
                    }
                    $this->_chunk($thematch, $theregion['delimClass']);
                    if ($current) {
                        $stack[] = $current;
                    }
                    $current = $theregion;
                    $blocksToSeek = $current['lookfor']['blocks'] ?? null;
                    $regionsToSeek = $current['lookfor']['regions'] ?? null;
                    $pos += $matchpos + $matchlen;
                    $defClass = $current['innerClass'];
                    break;
                // found a block
                case 1:
                    if ($matchpos) {
                        $this->_chunk(mb_substr($substr, 0, $matchpos), $defClass);
                    }
                    if (isset($theregion['partClass'])) {
                        $partpos = $matchpos;

                        $nparts = count($thematches);

                        for ($i = 1; $i < $nparts; $i++) {
                            if (isset($theregion['partClass'][$i])) {
                                $this->_chunk(mb_substr($substr, $partpos, $thematches[$i][1] - $partpos), $class);

                                $this->_chunk($thematches[$i][0], $theregion['partClass'][$i]);
                            }

                            $partpos = $thematches[$i][1] + mb_strlen($thematches[$i][0]);
                        }

                        if ($partpos < $matchpos + $matchlen) {
                            $this->_chunk(mb_substr($substr, $partpos, $matchlen - $partpos + $matchpos), $class);
                        }
                    } else {
                        while (true) {
                            $newregion = null;

                            $class = $theregion['innerClass'] ?? $defClass;

                            foreach ((array)$this->_syntax['keywords'] as $kwgroup) {
                                if ($kwgroup['inherits'] == $theregion['name']) {
                                    $csmatch = $kwgroup['case'] ? $thematch : mb_strtolower($thematch);

                                    if (isset($kwgroup['match'][$csmatch])) {
                                        $class = $kwgroup['innerClass'];

                                        $newregion = null;

                                        break;
                                    }

                                    if (isset($kwgroup['otherwise'])) {
                                        $newregion = $this->_syntax['blocks'][$kwgroup['otherwise']];
                                    }
                                }
                            }

                            if ($newregion) {
                                $theregion = $newregion;

                                continue;
                            }

                            break;
                        }

                        $this->_chunk($thematch, $class);
                    }
                    $pos += $matchpos + $matchlen;
                    break;
                // found end of region
                case 2:
                    if ($matchpos) {
                        $this->_chunk(mb_substr($substr, 0, $matchpos), $current['innerClass']);
                    }
                    $pos += $matchpos + $matchlen;
                    $this->_chunk($thematch, $current['delimClass']);
                    $current = array_pop($stack);
                    if ($current) {
                        $blocksToSeek = $current['lookfor']['blocks'] ?? null;

                        $regionsToSeek = $current['lookfor']['regions'] ?? null;
                    } else {
                        $blocksToSeek = $this->_syntax['toplevel']['blocks'] ?? null;

                        $regionsToSeek = $this->_syntax['toplevel']['regions'] ?? null;

                        $defClass = $this->_syntax['defClass'];
                    }
                    break;
                default:
                    $this->_chunk($substr, $defClass);
                    $pos = HL_BIG_NUM;
            }
        }

        if ($pos < mb_strlen($substr)) {
            $this->_chunk($substr, $defClass);
        }

        $this->_finish();

        if (HL_TAG_PRE != $this->_tag) {
            $this->_output = nl2br($this->_output);

            $this->_output = str_replace(' ', ' &nbsp;', $this->_output);
        }

        if (HL_NUMBERS_LI == $this->_numbers) {
            /* additional whitespace for browsers that do not display
            empty list items correctly */

            $this->_output = preg_replace('~^|\n~', "\n<li>&nbsp;", $this->_output);

            $this->_output = '<ol class="hl-main"><' . $this->_tag . '>' . $this->_output . '</' . $this->_tag . '></ol>';

            // clean up a little ...

            $this->_output = preg_replace('~\<span\sclass="[^"]+"\>\s*\</span\>~U', '', $this->_output);
        } else {
            $this->_output = '<' . $this->_tag . ' class="hl-main">' . $this->_output . '</' . $this->_tag . '>';
        }

        if (HL_NUMBERS_TABLE == $this->_numbers) {
            $numbers = '';

            $nlines = mb_substr_count($this->_output, "\n") + 1;

            for ($i = 1; $i <= $nlines; $i++) {
                $numbers .= $i . "\n";
            }

            $this->_output = '<table class="hl-table" width="100%"><tr>' . '<td class="hl-gutter" align="right" valign="top" style="width:4ex;">' . '<pre>' . $numbers . '</pre></td><td class="hl-main" valign="top">' . $this->_output . '</td></tr></table>';
        }

        return $this->_output;
    }

    /**
     * Adds next chunk to output
     *
     * @param string $text  Text to output
     * @param string $class CSS class
     */
    public function _chunk($text, $class)
    {
        $iswhitespace = ctype_space($text);

        $text = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5);

        if ($class != $this->_lastClass && !$iswhitespace) {
            $tag = '';

            if ($this->_output) {
                $tag .= '</span>';
            }

            $tag .= '<span class="hl-' . $class . '">';

            $this->_output .= $tag;
        } else {
            $class = $this->_lastClass;
        }

        // make coloring tags not cross the list item tags

        if (HL_NUMBERS_LI == $this->_numbers) {
            $tag = "</span>\n<span class=\"hl-" . $class . '">';

            $text = str_replace("\n", $tag, $text);
        }

        $this->_output .= $text;

        if (!$iswhitespace) {
            $this->_lastClass = $class;
        }
    }

    /**
     * Closes tags
     */
    public function _finish()
    {
        if ($this->_output) {
            $this->_output .= '</span>';
        }
    }
}
