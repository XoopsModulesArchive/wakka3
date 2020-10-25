<?php

/*
+----------------------------------------------------------------------+
| PHP Version 4  |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2002 The PHP Group |
+----------------------------------------------------------------------+
| This source file is subject to version 2.02 of the PHP license, |
| that is bundled with this package in the file LICENSE, and is |
| available at through the world-wide-web at |
| http://www.php.net/license/3_0.txt.  |
| If you did not receive a copy of the PHP license and are unable to |
| obtain it through the world-wide-web, please send a note to |
| license@php.net so we can mail you a copy immediately. |
+----------------------------------------------------------------------+
| Authors: Alexander Zhukov <alex@veresk.ru> Original port from Python |
| Authors: Harry Fuecks <hfuecks@phppatterns.com> Port to PEAR + more |
| Authors: Many @ Sitepointforums Advanced PHP Forums |
+----------------------------------------------------------------------+
WARNING! This is modified XML_HTMLSax-2.1.2.
If you'll use unmodified HTMLSax, Safehtml will be NOT SAFE!
*/

class HTMLSax
{
    public function __construct()
    {
        $i = 1;
    }
}

define('WHITESPACE', " \t\r\n\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F\x10\x11\x12\x13\x14\x15\x15\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F");

/**
 * Base State Parser
 * @abstract
 */
class XML_HTMLSax_StateParser
{
    /**
     * Instance of user front end class to be passed to callbacks
     * @var XML_HTMLSax
     */

    public $htmlsax;

    /**
     * User defined object for handling elements
     * @var object
     */

    public $handler_object_element;

    /**
     * User defined open tag handler method
     * @var string
     */

    public $handler_method_opening;

    /**
     * User defined close tag handler method
     * @var string
     */

    public $handler_method_closing;

    /**
     * User defined object for handling data in elements
     * @var object
     */

    public $handler_object_data;

    /**
     * User defined data handler method
     * @var string
     */

    public $handler_method_data;

    /**
     * User defined object for handling processing instructions
     * @var object
     */

    public $handler_object_pi;

    /**
     * User defined processing instruction handler method
     * @var string
     */

    public $handler_method_pi;

    /**
     * User defined object for handling JSP/ASP tags
     * @var object
     */

    public $handler_object_jasp;

    /**
     * User defined JSP/ASP handler method
     * @var string
     */

    public $handler_method_jasp;

    /**
     * User defined object for handling XML escapes
     * @var object
     */

    public $handler_object_escape;

    /**
     * User defined XML escape handler method
     * @var string
     */

    public $handler_method_escape;

    /**
     * User defined handler object or NullHandler
     * @var object
     */

    public $handler_default;

    /**
     * Parser options determining parsing behavior
     * @var array
     */

    public $parser_options = [];

    /**
     * XML document being parsed
     * @var string
     */

    public $rawtext;

    /**
     * Position in XML document relative to start (0)
     * @var int
     */

    public $position;

    /**
     * Length of the XML document in characters
     * @var int
     */

    public $length;

    /**
     * Array of state objects
     * @var array
     */

    public $State = [];

    /**
     * Constructs XML_HTMLSax_StateParser setting up states
     *
     * @param mixed $htmlsax
     */
    public function __construct(&$htmlsax)
    {
        $this->htmlsax = &$htmlsax;

        $this->State[XML_HTMLSAX_STATE_START] = new XML_HTMLSax_StartingState();

        $this->State[XML_HTMLSAX_STATE_CLOSING_TAG] = new XML_HTMLSax_ClosingTagState();

        $this->State[XML_HTMLSAX_STATE_TAG] = new XML_HTMLSax_TagState();

        $this->State[XML_HTMLSAX_STATE_OPENING_TAG] = new XML_HTMLSax_OpeningTagState();

        $this->State[XML_HTMLSAX_STATE_PI] = new XML_HTMLSax_PiState();

        $this->State[XML_HTMLSAX_STATE_JASP] = new XML_HTMLSax_JaspState();

        $this->State[XML_HTMLSAX_STATE_ESCAPE] = new XML_HTMLSax_EscapeState();
    }

    /**
     * Moves the position back one character
     */
    public function unscanCharacter()
    {
        $this->position -= 1;
    }

    /**
     * Moves the position forward one character
     */
    public function ignoreCharacter()
    {
        $this->position += 1;
    }

    /**
     * Returns the next character from the XML document or void if at end
     * @return mixed
     */
    public function scanCharacter()
    {
        if ($this->position < $this->length) {
            return $this->rawtext[$this->position++];
        }
    }

    /**
     * Returns a string from the current position to the next occurance
     * of the supplied string
     * @param mixed $string
     * @return string
     */
    public function scanUntilString($string)
    {
        $start = $this->position;

        //echo $start.",$string,".$this->length."<br>";

        $this->position = mb_strpos($this->rawtext, $string, $start);

        if (false === $this->position) {
            $this->position = $this->length;
        }

        return mb_substr($this->rawtext, $start, $this->position - $start);
    }

    /**
     * Returns a string from the current position until the first instance of
     * one of the characters in the supplied string argument
     * @param mixed $string
     * @return string
     * @abstract
     */
    public function scanUntilCharacters($string)
    {
    }

    /**
     * Moves the position forward past any whitespace characters
     * @abstract
     */
    public function ignoreWhitespace()
    {
    }

    /**
     * Begins the parsing operation, setting up any decorators, depending on
     * parse options invoking _parse() to execute parsing
     * @param mixed $data
     */
    public function parse($data)
    {
        if (1 == $this->parser_options['XML_OPTION_TRIM_DATA_NODES']) {
            $decorator = new XML_HTMLSax_Trim(
                $this->handler_object_data,
                $this->handler_method_data
            );

            $this->handler_object_data = &$decorator;

            $this->handler_method_data = 'trimData';
        }

        if (1 == $this->parser_options['XML_OPTION_CASE_FOLDING']) {
            $open_decor = new XML_HTMLSax_CaseFolding(
                $this->handler_object_element,
                $this->handler_method_opening,
                $this->handler_method_closing
            );

            $this->handler_object_element = &$open_decor;

            $this->handler_method_opening = 'foldOpen';

            $this->handler_method_closing = 'foldClose';
        }

        if (1 == $this->parser_options['XML_OPTION_LINEFEED_BREAK']) {
            $decorator = new XML_HTMLSax_Linefeed(
                $this->handler_object_data,
                $this->handler_method_data
            );

            $this->handler_object_data = &$decorator;

            $this->handler_method_data = 'breakData';
        }

        if (1 == $this->parser_options['XML_OPTION_TAB_BREAK']) {
            $decorator = new XML_HTMLSax_Tab(
                $this->handler_object_data,
                $this->handler_method_data
            );

            $this->handler_object_data = &$decorator;

            $this->handler_method_data = 'breakData';
        }

        if (1 == $this->parser_options['XML_OPTION_ENTITIES_UNPARSED']) {
            $decorator = new XML_HTMLSax_Entities_Unparsed(
                $this->handler_object_data,
                $this->handler_method_data
            );

            $this->handler_object_data = &$decorator;

            $this->handler_method_data = 'breakData';
        }

        if (1 == $this->parser_options['XML_OPTION_ENTITIES_PARSED']) {
            $decorator = new XML_HTMLSax_Entities_Parsed(
                $this->handler_object_data,
                $this->handler_method_data
            );

            $this->handler_object_data = &$decorator;

            $this->handler_method_data = 'breakData';
        }

        $this->rawtext = $data;

        $this->length = mb_strlen($data);

        $this->position = 0;

        $this->_parse();
    }

    /**
     * Performs the parsing itself, delegating calls to a specific parser
     * state
     * @param mixed $state
     */
    public function _parse($state = XML_HTMLSAX_STATE_START)
    {
        do {
            $state = $this->State[$state]->parse($this);
        } while (XML_HTMLSAX_STATE_STOP != $state
                 && $this->position < $this->length);
    }
}

/**
 * Parser for PHP Versions below 4.3.0. Uses a slower parsing mechanism than
 * the equivalent PHP 4.3.0+ subclass of StateParser
 * @see     XML_HTMLSax_StateParser_Gtet430
 */
class XML_HTMLSax_StateParser_Lt430 extends XML_HTMLSax_StateParser
{
    /**
     * Constructs XML_HTMLSax_StateParser_Lt430 defining available
     * parser options
     *
     * @param mixed $htmlsax
     */
    public function __construct(&$htmlsax)
    {
        parent::__construct($htmlsax);

        $this->parser_options['XML_OPTION_TRIM_DATA_NODES'] = 0;

        $this->parser_options['XML_OPTION_CASE_FOLDING'] = 0;

        $this->parser_options['XML_OPTION_LINEFEED_BREAK'] = 0;

        $this->parser_options['XML_OPTION_TAB_BREAK'] = 0;

        $this->parser_options['XML_OPTION_ENTITIES_PARSED'] = 0;

        $this->parser_options['XML_OPTION_ENTITIES_UNPARSED'] = 0;

        $this->parser_options['XML_OPTION_FULL_ESCAPES'] = 0;
    }

    /**
     * Returns a string from the current position until the first instance of
     * one of the characters in the supplied string argument
     * @param mixed $string
     * @return string
     */
    public function scanUntilCharacters($string)
    {
        $startpos = $this->position;

        while ($this->position < $this->length && false === mb_strpos($string, $this->rawtext[$this->position])) {
            $this->position++;
        }

        return mb_substr($this->rawtext, $startpos, $this->position - $startpos);
    }

    /**
     * Moves the position forward past any whitespace characters
     */
    public function ignoreWhitespace()
    {
        while ($this->position < $this->length
               && false !== mb_strpos(WHITESPACE, $this->rawtext[$this->position])) {
            $this->position++;
        }
    }

    /**
     * Begins the parsing operation, setting up the unparsed XML entities
     * decorator if necessary then delegating further work to parent
     * @param mixed $data
     */
    public function parse($data)
    {
        parent::parse($data);
    }
}

/**
 * Parser for PHP Versions equal to or greater than 4.3.0. Uses a faster
 * parsing mechanism than the equivalent PHP < 4.3.0 subclass of StateParser
 * @see     XML_HTMLSax_StateParser_Lt430
 */
class XML_HTMLSax_StateParser_Gtet430 extends XML_HTMLSax_StateParser
{
    /**
     * Constructs XML_HTMLSax_StateParser_Gtet430 defining available
     * parser options
     *
     * @param mixed $htmlsax
     */
    public function __construct(&$htmlsax)
    {
        parent::__construct($htmlsax);

        $this->parser_options['XML_OPTION_TRIM_DATA_NODES'] = 0;

        $this->parser_options['XML_OPTION_CASE_FOLDING'] = 0;

        $this->parser_options['XML_OPTION_LINEFEED_BREAK'] = 0;

        $this->parser_options['XML_OPTION_TAB_BREAK'] = 0;

        $this->parser_options['XML_OPTION_ENTITIES_PARSED'] = 0;

        $this->parser_options['XML_OPTION_ENTITIES_UNPARSED'] = 0;

        $this->parser_options['XML_OPTION_FULL_ESCAPES'] = 0;
    }

    /**
     * Returns a string from the current position until the first instance of
     * one of the characters in the supplied string argument.
     * @param mixed $string
     * @return string
     */
    public function scanUntilCharacters($string)
    {
        $startpos = $this->position;

        $length = strcspn($this->rawtext, $string, $startpos);

        $this->position += $length;

        return mb_substr($this->rawtext, $startpos, $length);
    }

    /**
     * Moves the position forward past any whitespace characters
     */
    public function ignoreWhitespace()
    {
        $this->position += strspn($this->rawtext, WHITESPACE, $this->position);
    }

    /**
     * Begins the parsing operation, setting up the parsed and unparsed
     * XML entity decorators if necessary then delegating further work
     * to parent
     * @param mixed $data
     */
    public function parse($data)
    {
        parent::parse($data);
    }
}

/**
 * Default NullHandler for methods which were not set by user
 */
class XML_HTMLSax_NullHandler
{
    /**
     * Generic handler method which does nothing
     */
    public function DoNothing()
    {
    }
}

/**
 * User interface class. All user calls should only be made to this class
 */
class XML_HTMLSax
{//extends Pear {
    /**
     * Instance of concrete subclass of XML_HTMLSax_StateParser
     * @var XML_HTMLSax_StateParser
     */

    public $state_parser;

    /**
     * Constructs XML_HTMLSax selecting concrete StateParser subclass
     * depending on PHP version being used as well as setting the default
     * NullHandler for all callbacks<br>
     */
    public function __construct()
    {
        if (version_compare(phpversion(), '4.3', 'ge')) {
            $this->state_parser = new XML_HTMLSax_StateParser_Gtet430($this);
        } else {
            $this->state_parser = new XML_HTMLSax_StateParser_Lt430($this);
        }

        $nullhandler = new XML_HTMLSax_NullHandler();

        $this->set_object($nullhandler);

        $this->set_elementHandler('DoNothing', 'DoNothing');

        $this->set_dataHandler('DoNothing');

        $this->set_piHandler('DoNothing');

        $this->set_jaspHandler('DoNothing');

        $this->set_escapeHandler('DoNothing');
    }

    /**
     * Sets the user defined handler object. Returns a PEAR Error
     * if supplied argument is not an object.
     * @param mixed $object
     * @return mixed
     */
    public function set_object(&$object)
    {
        if (is_object($object)) {
            $this->state_parser->handler_default = &$object;

            return true;
        }

        return 'XML_HTMLSax::set_object requires ' . 'an object instance';
    }

    /**
     * Sets a parser option. Returns a PEAR Error if option is invalid<br>
     * @param mixed $name
     * @param mixed $value
     * @return bool
     */
    public function set_option($name, $value = 1)
    {
        if (array_key_exists($name, $this->state_parser->parser_options)) {
            $this->state_parser->parser_options[$name] = $value;

            return true;
        }

        return 'XML_HTMLSax::set_option(' . $name . ') illegal';
    }

    /**
     * Sets the data handler method which deals with the contents of XML
     * elements.<br>
     * The handler method must accept two arguments, the first being an
     * instance of XML_HTMLSax and the second being the contents of an
     * XML element e.g.
     * <pre>
     * function myDataHander(& $parser,$data){}
     * </pre>
     * @param mixed $data_method
     * @see    set_object
     */
    public function set_dataHandler($data_method)
    {
        $this->state_parser->handler_object_data = &$this->state_parser->handler_default;

        $this->state_parser->handler_method_data = $data_method;
    }

    /**
     * Sets the open and close tag handlers
     * <br>The open handler method must accept three arguments; the parser,
     * the tag name and an array of attributes e.g.
     * <pre>
     * function myOpenHander(& $parser,$tagname,$attrs=array()){}
     * </pre>
     * The close handler method must accept two arguments; the parser and
     * the tag name e.g.
     * <pre>
     * function myCloseHander(& $parser,$tagname){}
     * </pre>
     * @param mixed $opening_method
     * @param mixed $closing_method
     * @see    set_object
     */
    public function set_elementHandler($opening_method, $closing_method)
    {
        $this->state_parser->handler_object_element = &$this->state_parser->handler_default;

        $this->state_parser->handler_method_opening = $opening_method;

        $this->state_parser->handler_method_closing = $closing_method;
    }

    /**
     * Sets the processing instruction handler method e.g. for PHP open
     * and close tags<br>
     * The handler method must accept three arguments; the parser, the
     * PI target and data inside the PI
     * <pre>
     * function myPIHander(& $parser,$target, $data){}
     * </pre>
     * @param mixed $pi_method
     * @see    set_object
     */
    public function set_piHandler($pi_method)
    {
        $this->state_parser->handler_object_pi = &$this->state_parser->handler_default;

        $this->state_parser->handler_method_pi = $pi_method;
    }

    /**
     * Sets the XML escape handler method e.g. for comments and doctype
     * declarations<br>
     * The handler method must accept two arguments; the parser and the
     * contents of the escaped section
     * <pre>
     * function myEscapeHander(& $parser, $data){}
     * </pre>
     * @param mixed $escape_method
     * @see    set_object
     */
    public function set_escapeHandler($escape_method)
    {
        $this->state_parser->handler_object_escape = &$this->state_parser->handler_default;

        $this->state_parser->handler_method_escape = $escape_method;
    }

    /**
     * Sets the JSP/ASP markup handler<br>
     * The handler method must accept two arguments; the parser and
     * body of the JASP tag
     * <pre>
     * function myJaspHander(& $parser, $data){}
     * </pre>
     * @param mixed $jasp_method
     * @see    set_object
     */
    public function set_jaspHandler($jasp_method)
    {
        $this->state_parser->handler_object_jasp = &$this->state_parser->handler_default;

        $this->state_parser->handler_method_jasp = $jasp_method;
    }

    /**
     * Returns the current string position of the "cursor" inside the XML
     * document
     * <br>Intended for use from within a user defined handler called
     * via the $parser reference e.g.
     * <pre>
     * function myDataHandler(& $parser,$data) {
     * echo( 'Current position: '.$parser->get_current_position() );
     * }
     * </pre>
     * @return int
     * @see    get_length
     */
    public function get_current_position()
    {
        return $this->state_parser->position;
    }

    /**
     * Returns the string length of the XML document being parsed
     * @return int
     */
    public function get_length()
    {
        return $this->state_parser->length;
    }

    /**
     * Start parsing some XML
     * @param mixed $data
     */
    public function parse($data)
    {
        $this->state_parser->parse($data);
    }
}

/**
 * Decorators for dealing with parser options
 * @version $Id: HTMLSax.php,v 1.1 2004/07/31 10:26:18 kukutz Exp $
 * @see     XML_HTMLSax::set_option
 */

/**
 * Trims the contents of element data from whitespace at start and end
 */
class XML_HTMLSax_Trim
{
    /**
     * Original handler object
     * @var object
     */

    public $orig_obj;

    /**
     * Original handler method
     * @var string
     */

    public $orig_method;

    /**
     * Constructs XML_HTMLSax_Trim
     * @param mixed $orig_obj
     * @param mixed $orig_method
     */
    public function __construct(&$orig_obj, $orig_method)
    {
        $this->orig_obj = &$orig_obj;

        $this->orig_method = $orig_method;
    }

    /**
     * Trims the data
     * @param mixed $parser
     * @param mixed $data
     */
    public function trimData(&$parser, $data)
    {
        $data = trim($data);

        if ('' != $data) {
            $this->orig_obj->{$this->orig_method}($parser, $data);
        }
    }
}

/**
 * Coverts tag names to upper case
 */
class XML_HTMLSax_CaseFolding
{
    /**
     * Original handler object
     * @var object
     */

    public $orig_obj;

    /**
     * Original open handler method
     * @var string
     */

    public $orig_open_method;

    /**
     * Original close handler method
     * @var string
     */

    public $orig_close_method;

    /**
     * Constructs XML_HTMLSax_CaseFolding
     * @param mixed $orig_obj
     * @param mixed $orig_open_method
     * @param mixed $orig_close_method
     */
    public function __construct(&$orig_obj, $orig_open_method, $orig_close_method)
    {
        $this->orig_obj = &$orig_obj;

        $this->orig_open_method = $orig_open_method;

        $this->orig_close_method = $orig_close_method;
    }

    /**
     * Folds up open tag callbacks
     * @param mixed $parser
     * @param mixed $tag
     * @param mixed $attrs
     * @param mixed $empty
     */
    public function foldOpen(&$parser, $tag, $attrs = [], $empty = false)
    {
        $this->orig_obj->{$this->orig_open_method}($parser, mb_strtoupper($tag), $attrs, $empty);
    }

    /**
     * Folds up close tag callbacks
     * @param mixed $parser
     * @param mixed $tag
     * @param mixed $empty
     */
    public function foldClose(&$parser, $tag, $empty = false)
    {
        $this->orig_obj->{$this->orig_close_method}($parser, mb_strtoupper($tag), $empty);
    }
}

/**
 * Breaks up data by linefeed characters, resulting in additional
 * calls to the data handler
 */
class XML_HTMLSax_Linefeed
{
    /**
     * Original handler object
     * @var object
     */

    public $orig_obj;

    /**
     * Original handler method
     * @var string
     */

    public $orig_method;

    /**
     * Constructs XML_HTMLSax_LineFeed
     * @param mixed $orig_obj
     * @param mixed $orig_method
     */
    public function __construct(&$orig_obj, $orig_method)
    {
        $this->orig_obj = &$orig_obj;

        $this->orig_method = $orig_method;
    }

    /**
     * Breaks the data up by linefeeds
     * @param mixed $parser
     * @param mixed $data
     */
    public function breakData(&$parser, $data)
    {
        $data = explode("\n", $data);

        foreach ($data as $chunk) {
            $this->orig_obj->{$this->orig_method}($parser, $chunk);
        }
    }
}

/**
 * Breaks up data by tab characters, resulting in additional
 * calls to the data handler
 */
class XML_HTMLSax_Tab
{
    /**
     * Original handler object
     * @var object
     */

    public $orig_obj;

    /**
     * Original handler method
     * @var string
     */

    public $orig_method;

    /**
     * Constructs XML_HTMLSax_Tab
     * @param mixed $orig_obj
     * @param mixed $orig_method
     */
    public function __construct(&$orig_obj, $orig_method)
    {
        $this->orig_obj = &$orig_obj;

        $this->orig_method = $orig_method;
    }

    /**
     * Breaks the data up by linefeeds
     * @param mixed $parser
     * @param mixed $data
     */
    public function breakData(&$parser, $data)
    {
        $data = explode("\t", $data);

        foreach ($data as $chunk) {
            $this->orig_obj->{$this->orig_method}($this, $chunk);
        }
    }
}

/**
 * Breaks up data by XML entities and parses them with html_entity_decode(),
 * resulting in additional calls to the data handler<br>
 * Requires PHP 4.3.0+
 */
class XML_HTMLSax_Entities_Parsed
{
    /**
     * Original handler object
     * @var object
     */

    public $orig_obj;

    /**
     * Original handler method
     * @var string
     */

    public $orig_method;

    /**
     * Constructs XML_HTMLSax_Entities_Parsed
     * @param mixed $orig_obj
     * @param mixed $orig_method
     */
    public function __construct(&$orig_obj, $orig_method)
    {
        $this->orig_obj = &$orig_obj;

        $this->orig_method = $orig_method;
    }

    /**
     * Breaks the data up by XML entities
     * @param mixed $parser
     * @param mixed $data
     */
    public function breakData(&$parser, $data)
    {
        $data = preg_preg_split('/(&.+?;)/', $data, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        foreach ($data as $chunk) {
            $chunk = html_entity_decode($chunk, ENT_NOQUOTES);

            $this->orig_obj->{$this->orig_method}($this, $chunk);
        }
    }
}

/**
 * Compatibility with older PHP versions
 */
if (version_compare(phpversion(), '4.3', '<') && !function_exists('html_entity_decode')) {
    function html_entity_decode($str, $style = ENT_NOQUOTES)
    {
        return strtr(
            $str,
            array_flip(get_html_translation_table(HTML_ENTITIES, $style))
        );
    }
}

/**
 * Breaks up data by XML entities but leaves them unparsed,
 * resulting in additional calls to the data handler<br>
 */
class XML_HTMLSax_Entities_Unparsed
{
    /**
     * Original handler object
     * @var object
     */

    public $orig_obj;

    /**
     * Original handler method
     * @var string
     */

    public $orig_method;

    /**
     * Constructs XML_HTMLSax_Entities_Unparsed
     * @param mixed $orig_obj
     * @param mixed $orig_method
     */
    public function __construct(&$orig_obj, $orig_method)
    {
        $this->orig_obj = &$orig_obj;

        $this->orig_method = $orig_method;
    }

    /**
     * Breaks the data up by XML entities
     * @param mixed $parser
     * @param mixed $data
     */
    public function breakData(&$parser, $data)
    {
        $data = preg_preg_split('/(&.+?;)/', $data, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        foreach ($data as $chunk) {
            $this->orig_obj->{$this->orig_method}($this, $chunk);
        }
    }
}

/**
 * Main parser components
 * @version $Id: HTMLSax.php,v 1.1 2004/07/31 10:26:18 kukutz Exp $
 */
/**
 * Define parser states
 */
define('XML_HTMLSAX_STATE_STOP', 0);
define('XML_HTMLSAX_STATE_START', 1);
define('XML_HTMLSAX_STATE_TAG', 2);
define('XML_HTMLSAX_STATE_OPENING_TAG', 3);
define('XML_HTMLSAX_STATE_CLOSING_TAG', 4);
define('XML_HTMLSAX_STATE_ESCAPE', 6);
define('XML_HTMLSAX_STATE_JASP', 7);
define('XML_HTMLSAX_STATE_PI', 8);

/**
 * StartingState searches for the start of any XML tag
 */
class XML_HTMLSax_StartingState
{
    /**
     * @param mixed $context
     * @return int XML_HTMLSAX_STATE_TAG
     */
    public function parse($context)
    {
        $data = $context->scanUntilString('<');

        if ('' != $data) {
            $context->handler_object_data->{$context->handler_method_data}($context->htmlsax, $data);
        }

        $context->IgnoreCharacter();

        return XML_HTMLSAX_STATE_TAG;
    }
}

/**
 * Decides which state to move one from after StartingState
 */
class XML_HTMLSax_TagState
{
    /**
     * @param mixed $context
     * @return int the next state to move into
     */
    public function parse($context)
    {
        switch ($context->ScanCharacter()) {
            case '/':
                return XML_HTMLSAX_STATE_CLOSING_TAG;
                break;
            case '?':
                return XML_HTMLSAX_STATE_PI;
                break;
            case '%':
                return XML_HTMLSAX_STATE_JASP;
                break;
            case '!':
                return XML_HTMLSAX_STATE_ESCAPE;
                break;
            default:
                $context->unscanCharacter();

                return XML_HTMLSAX_STATE_OPENING_TAG;
        }
    }
}

/**
 * Dealing with closing XML tags
 */
class XML_HTMLSax_ClosingTagState
{
    /**
     * @param mixed $context
     * @return int XML_HTMLSAX_STATE_START
     */
    public function parse($context)
    {
        $tag = $context->scanUntilCharacters('>');

        if ('' != $tag) {
            $char = $context->scanCharacter();

            if ('/' == $char) {
                $char = $context->scanCharacter();

                if ('>' != $char) {
                    $context->unscanCharacter();
                }
            }

            $context->handler_object_element->{$context->handler_method_closing}($context->htmlsax, $tag, false);
        }

        return XML_HTMLSAX_STATE_START;
    }
}

/**
 * Dealing with opening XML tags
 */
class XML_HTMLSax_OpeningTagState
{
    /**
     * Handles attributes
     * @param mixed $context
     * @return array
     * @see    XML_HTMLSax_AttributeStartState
     */
    public function parseAttributes($context)
    {
        $Attributes = [];

        $context->ignoreWhitespace();

        $attributename = $context->scanUntilCharacters('=>' . WHITESPACE);

        while ('' != $attributename) {
            $attributevalue = null;

            $context->ignoreWhitespace();

            $char = $context->scanCharacter();

            if ('=' == $char) {
                $context->ignoreWhitespace();

                $char = $context->ScanCharacter();

                if ('"' == $char) {
                    $attributevalue = $context->scanUntilString('"');

                    $context->IgnoreCharacter();
                } elseif ("'" == $char) {
                    $attributevalue = $context->scanUntilString("'");

                    $context->IgnoreCharacter();
                } else {
                    $context->unscanCharacter();

                    $attributevalue = $context->scanUntilCharacters('>' . WHITESPACE);
                }
            } elseif (null !== $char) {
                $attributevalue = true;

                $context->unscanCharacter();
            }

            $Attributes[$attributename] = $attributevalue;

            $context->ignoreWhitespace();

            $attributename = $context->scanUntilCharacters('=>' . WHITESPACE);
        }

        return $Attributes;
    }

    /**
     * @param mixed $context
     * @return int XML_HTMLSAX_STATE_START
     */
    public function parse($context)
    {
        $tag = $context->scanUntilCharacters('>' . WHITESPACE);

        if ('' != $tag) {
            $this->attrs = [];

            $Attributes = $this->parseAttributes($context);

            $char = $context->scanCharacter();

            if ('/' == $char) {
                $char = $context->scanCharacter();

                if ('>' != $char) {
                    $context->unscanCharacter();
                }

                $context->handler_object_element->{$context->handler_method_opening}(
                    $context->htmlsax,
                    $tag,
                    $Attributes,
                    true
                );

                $context->handler_object_element->{$context->handler_method_closing}(
                    $context->htmlsax,
                    $tag,
                    true
                );
            } else {
                $context->handler_object_element->{$context->handler_method_opening}(
                    $context->htmlsax,
                    $tag,
                    $Attributes,
                    false
                );
            }
        }

        return XML_HTMLSAX_STATE_START;
    }
}

/**
 * Deals with XML escapes handling comments and CDATA correctly
 */
class XML_HTMLSax_EscapeState
{
    /**
     * @param mixed $context
     * @return int XML_HTMLSAX_STATE_START
     */
    public function parse($context)
    {
        if (0 == $context->parser_options['XML_OPTION_FULL_ESCAPES']) {
            $char = $context->ScanCharacter();

            if ('-' == $char) {
                $char = $context->ScanCharacter();

                if ('-' == $char) {
                    $text = $context->scanUntilString('-->');

                    $context->IgnoreCharacter();

                    $context->IgnoreCharacter();
                } else {
                    $context->unscanCharacter();

                    $text = $context->scanUntilString('>');
                }
            } elseif ('[' == $char) {
                /*$context->scanUntilString('CDATA[');
                for ( $i=0;$i<6;$i++ ) {
                $context->IgnoreCharacter();
                }*/

                $text = $context->scanUntilString(']>');

                //$context->IgnoreCharacter();

                $context->IgnoreCharacter();
            } else {
                $context->unscanCharacter();

                $text = $context->scanUntilString('>');
            }
        } else {
            $text = $context->scanUntilString('>');
        }

        $context->IgnoreCharacter();

        if ('' != $text) {
            $context->handler_object_escape->{$context->handler_method_escape}($context->htmlsax, $text);
        }

        return XML_HTMLSAX_STATE_START;
    }
}

/**
 * Deals with JASP/ASP markup
 */
class XML_HTMLSax_JaspState
{
    /**
     * @param mixed $context
     * @return int XML_HTMLSAX_STATE_START
     */
    public function parse($context)
    {
        $text = $context->scanUntilString('%>');

        if ('' != $text) {
            $context->handler_object_jasp->{$context->handler_method_jasp}($context->htmlsax, $text);
        }

        $context->IgnoreCharacter();

        $context->IgnoreCharacter();

        return XML_HTMLSAX_STATE_START;
    }
}

/**
 * Deals with XML processing instructions
 */
class XML_HTMLSax_PiState
{
    /**
     * @param mixed $context
     * @return int XML_HTMLSAX_STATE_START
     */
    public function parse($context)
    {
        $target = $context->scanUntilCharacters(WHITESPACE);

        $data = $context->scanUntilString('>');

        if ('' != $data) {
            $context->handler_object_pi->{$context->handler_method_pi}($context->htmlsax, $target, $data);
        }

        // $context->IgnoreCharacter();

        $context->IgnoreCharacter();

        return XML_HTMLSAX_STATE_START;
    }
}
