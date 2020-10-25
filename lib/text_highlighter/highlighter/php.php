<?php
/**
 * Auto-generated class. PHP syntax highlighting
 *
 * @author  Andrey Demenev <demenev@on-line.jar.ru>
 */

/**
 * @ignore
 */

/**
 * Auto-generated class. PHP syntax highlighting
 *
 * @author  Andrey Demenev <demenev@on-line.jar.ru>
 */
class Text_Highlighter_PHP extends Text_Highlighter
{
    /**
     * PHP4 Compatible Constructor
     *
     * @param array $options
     */
    public function Text_Highlighter_PHP($options = [])
    {
        $this->__construct($options);
    }

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->_syntax = [
            'regions' => [
                'phpCode' => [
                    'name' => 'phpCode',
'case' => false,
'innerClass' => 'code',
'delimClass' => 'inlinetags',
'start' => '/\\<\\?(php|=)?/i',
'end' => '/\\?\\>/i',
'remember' => false,
'lookfor' => [
                        'regions' => [
                            0 => 'block',
1 => 'brackets',
2 => 'sqbrackets',
3 => 'mlcomment',
4 => 'strdouble',
5 => 'exec',
6 => 'heredoc',
7 => 'strsingle',
8 => 'comment',
                        ],
'blocks' => [
                            0 => 'identifier',
1 => 'typecast',
2 => 'var',
3 => 'integer',
4 => 'hexinteger',
5 => 'octinteger',
6 => 'float',
7 => 'exponent',
                        ],
                    ],
                ],
'block' => [
                    'name' => 'block',
'case' => false,
'innerClass' => 'code',
'delimClass' => 'brackets',
'start' => '/\\{/i',
'end' => '/\\}/i',
'remember' => false,
'lookfor' => [
                        'regions' => [
                            0 => 'block',
1 => 'brackets',
2 => 'sqbrackets',
3 => 'mlcomment',
4 => 'strdouble',
5 => 'exec',
6 => 'heredoc',
7 => 'strsingle',
8 => 'comment',
9 => 'codeescape',
                        ],
'blocks' => [
                            0 => 'identifier',
1 => 'typecast',
2 => 'var',
3 => 'integer',
4 => 'hexinteger',
5 => 'octinteger',
6 => 'float',
7 => 'exponent',
                        ],
                    ],
                ],
'brackets' => [
                    'name' => 'brackets',
'case' => false,
'innerClass' => 'code',
'delimClass' => 'brackets',
'start' => '/\\(/i',
'end' => '/\\)/i',
'remember' => false,
'lookfor' => [
                        'regions' => [
                            0 => 'block',
1 => 'brackets',
2 => 'sqbrackets',
3 => 'mlcomment',
4 => 'strdouble',
5 => 'exec',
6 => 'heredoc',
7 => 'strsingle',
8 => 'comment',
                        ],
'blocks' => [
                            0 => 'identifier',
1 => 'typecast',
2 => 'var',
3 => 'integer',
4 => 'hexinteger',
5 => 'octinteger',
6 => 'float',
7 => 'exponent',
                        ],
                    ],
                ],
'sqbrackets' => [
                    'name' => 'sqbrackets',
'case' => false,
'innerClass' => 'code',
'delimClass' => 'brackets',
'start' => '/\\[/i',
'end' => '/\\]/i',
'remember' => false,
'lookfor' => [
                        'regions' => [
                            0 => 'block',
1 => 'brackets',
2 => 'sqbrackets',
3 => 'mlcomment',
4 => 'strdouble',
5 => 'exec',
6 => 'heredoc',
7 => 'strsingle',
8 => 'comment',
                        ],
'blocks' => [
                            0 => 'identifier',
1 => 'typecast',
2 => 'var',
3 => 'integer',
4 => 'hexinteger',
5 => 'octinteger',
6 => 'float',
7 => 'exponent',
                        ],
                    ],
                ],
'mlcomment' => [
                    'name' => 'mlcomment',
'case' => false,
'innerClass' => 'comment',
'delimClass' => 'comment',
'start' => '/\\/\\*/i',
'end' => '/\\*\\//i',
'remember' => false,
'lookfor' => [
                        'blocks' => [
                            0 => 'phpdoc',
1 => 'url',
2 => 'email',
3 => 'note',
4 => 'cvstag',
                        ],
                    ],
                ],
'strdouble' => [
                    'name' => 'strdouble',
'case' => false,
'innerClass' => 'string',
'delimClass' => 'quotes',
'start' => '/"/i',
'end' => '/"/i',
'remember' => false,
'lookfor' => [
                        'blocks' => [
                            0 => 'descaped',
1 => 'curlyvar',
2 => 'var',
                        ],
                    ],
                ],
'exec' => [
                    'name' => 'exec',
'case' => false,
'innerClass' => 'string',
'delimClass' => 'quotes',
'start' => '/`/i',
'end' => '/`/i',
'remember' => false,
'lookfor' => [
                        'blocks' => [
                            0 => 'escaped',
1 => 'curlyvar',
2 => 'var',
                        ],
                    ],
                ],
'heredoc' => [
                    'name' => 'heredoc',
'case' => false,
'innerClass' => 'string',
'delimClass' => 'quotes',
'start' => '/\\<\\<\\<[\\x20\\x09]*(\\w+)$/mi',
'end' => '/^%1%;?$/mi',
'remember' => true,
'lookfor' => [
                        'blocks' => [
                            0 => 'descaped',
1 => 'curlyvar',
2 => 'var',
                        ],
                    ],
                ],
'strsingle' => [
                    'name' => 'strsingle',
'case' => false,
'innerClass' => 'string',
'delimClass' => 'quotes',
'start' => '/\'/i',
'end' => '/\'/i',
'remember' => false,
'lookfor' => [
                        'blocks' => [
                            0 => 'escaped',
                        ],
                    ],
                ],
'comment' => [
                    'name' => 'comment',
'case' => false,
'innerClass' => 'comment',
'delimClass' => 'comment',
'start' => '/(#|\\/\\/)/i',
'end' => '/$|(?=\\?\\>)/mi',
'remember' => false,
'lookfor' => [
                        'blocks' => [
                            0 => 'phpdoc',
1 => 'url',
2 => 'email',
3 => 'note',
4 => 'cvstag',
                        ],
                    ],
                ],
'codeescape' => [
                    'name' => 'codeescape',
'case' => false,
'innerClass' => 'default',
'delimClass' => 'inlinetags',
'start' => '/\\?\\>/i',
'end' => '/\\<\\?(php|=)?/i',
'remember' => false,
                ],
            ],
'keywords' => [
                'constants' => [
                    'name' => 'constants',
'innerClass' => 'reserved',
'case' => true,
'inherits' => 'identifier',
'match' => [
                        'DIRECTORY_SEPARATOR' => true,
'PATH_SEPARATOR' => true,
                    ],
                ],
'reserved' => [
                    'name' => 'reserved',
'innerClass' => 'reserved',
'case' => false,
'inherits' => 'identifier',
'match' => [
                        'echo' => true,
'foreach' => true,
'else' => true,
'if' => true,
'elseif' => true,
'for' => true,
'as' => true,
'while' => true,
'break' => true,
'continue' => true,
'class' => true,
'const' => true,
'declare' => true,
'switch' => true,
'case' => true,
'endfor' => true,
'endswitch' => true,
'endforeach' => true,
'endif' => true,
'array' => true,
'default' => true,
'do' => true,
'enddeclare' => true,
'eval' => true,
'exit' => true,
'die' => true,
'extends' => true,
'function' => true,
'global' => true,
'include' => true,
'include_once' => true,
'require' => true,
'require_once' => true,
'isset' => true,
'empty' => true,
'list' => true,
'new' => true,
'static' => true,
'unset' => true,
'var' => true,
'return' => true,
'try' => true,
'catch' => true,
'final' => true,
'throw' => true,
'public' => true,
'private' => true,
'protected' => true,
'abstract' => true,
'interface' => true,
'implements' => true,
'define' => true,
'__file__' => true,
'__line__' => true,
'__class__' => true,
'__method__' => true,
'__function__' => true,
'null' => true,
'true' => true,
'false' => true,
'and' => true,
'or' => true,
'xor' => true,
                    ],
                ],
            ],
'blocks' => [
                'escaped' => [
                    'name' => 'escaped',
'case' => false,
'innerClass' => 'special',
'match' => '/\\\\\\\\|\\\\"|\\\\\'|\\\\`/i',
'multiline' => false,
                ],
'descaped' => [
                    'name' => 'descaped',
'case' => false,
'innerClass' => 'special',
'match' => '/\\\\[\\\\"\'`tnr\\$\\{]/i',
'multiline' => false,
                ],
'identifier' => [
                    'name' => 'identifier',
'case' => false,
'innerClass' => 'identifier',
'match' => '/[a-z_]\\w*/i',
'multiline' => false,
                ],
'typecast' => [
                    'name' => 'typecast',
'case' => false,
'innerClass' => 'reserved',
'match' => '/\\((array|int|integer|string|bool|boolean|object|float|double)\\)/i',
'multiline' => false,
                ],
'curlyvar' => [
                    'name' => 'curlyvar',
'case' => false,
'innerClass' => 'var',
'match' => '/\\{\\$[a-z_].*\\}/i',
'multiline' => false,
                ],
'var' => [
                    'name' => 'var',
'case' => false,
'innerClass' => 'var',
'match' => '/\\$[a-z_]\\w*/i',
'multiline' => false,
                ],
'integer' => [
                    'name' => 'integer',
'case' => false,
'innerClass' => 'number',
'match' => '/\\d\\d*|\\b0\\b/i',
'multiline' => false,
                ],
'hexinteger' => [
                    'name' => 'hexinteger',
'case' => false,
'innerClass' => 'number',
'match' => '/0[xX][\\da-f]+/i',
'multiline' => false,
                ],
'octinteger' => [
                    'name' => 'octinteger',
'case' => false,
'innerClass' => 'number',
'match' => '/0[0-7]+/i',
'multiline' => false,
                ],
'float' => [
                    'name' => 'float',
'case' => false,
'innerClass' => 'number',
'match' => '/(\\d*\\.\\d+)|(\\d+\\.\\d*)/i',
'multiline' => false,
                ],
'exponent' => [
                    'name' => 'exponent',
'case' => false,
'innerClass' => 'number',
'match' => '/((\\d+|((\\d*\\.\\d+)|(\\d+\\.\\d*)))[eE][+-]?\\d+)/i',
'multiline' => false,
                ],
'phpdoc' => [
                    'name' => 'phpdoc',
'case' => false,
'innerClass' => 'inlinedoc',
'match' => '/\\s@\\w+\\s/i',
'multiline' => false,
                ],
'url' => [
                    'name' => 'url',
'case' => false,
'innerClass' => 'url',
'match' => '/((https?|ftp):\\/\\/[\\w\\?\\.\\-\\&=\\/]+([^\\w\\?\\.\\&=\\/]|$))|(^|[\\s,!?])www\\.\\w+\\.\\w+[\\w\\?\\.\\&=\\/]*([^\\w\\?\\.\\&=\\/]|$)/mi',
'multiline' => false,
                ],
'email' => [
                    'name' => 'email',
'case' => false,
'innerClass' => 'url',
'match' => '/\\w+[\\.\\w\\-]+@(\\w+[\\.\\w\\-])+/i',
'multiline' => false,
                ],
'note' => [
                    'name' => 'note',
'case' => false,
'innerClass' => 'inlinedoc',
'match' => '/\\bnote:/i',
'multiline' => false,
                ],
'cvstag' => [
                    'name' => 'cvstag',
'case' => false,
'innerClass' => 'inlinedoc',
'match' => '/\\$\\w+:.+\\$/i',
'multiline' => false,
                ],
            ],
'toplevel' => [
                'regions' => [
                    0 => 'phpCode',
                ],
            ],
'case' => false,
'defClass' => 'code',
        ];

        parent::_init($options);
    }
}
