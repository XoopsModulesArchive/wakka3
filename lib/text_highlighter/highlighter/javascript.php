<?php
/**
 * Auto-generated class. JAVASCRIPT syntax highlighting
 *
 * @author  Andrey Demenev <demenev@on-line.jar.ru>
 */

/**
 * @ignore
 */

/**
 * Auto-generated class. JAVASCRIPT syntax highlighting
 *
 * @author  Andrey Demenev <demenev@on-line.jar.ru>
 */
class Text_Highlighter_JAVASCRIPT extends Text_Highlighter
{
    /**
     * PHP4 Compatible Constructor
     *
     * @param array $options
     */
    public function Text_Highlighter_JAVASCRIPT($options = [])
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
5 => 'strsingle',
6 => 'comment',
7 => 'regexp',
                        ],
'blocks' => [
                            0 => 'identifier',
1 => 'number',
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
5 => 'strsingle',
6 => 'comment',
7 => 'regexp',
                        ],
'blocks' => [
                            0 => 'identifier',
1 => 'number',
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
5 => 'strsingle',
6 => 'comment',
7 => 'regexp',
                        ],
'blocks' => [
                            0 => 'identifier',
1 => 'number',
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
                            0 => 'url',
1 => 'email',
2 => 'note',
3 => 'cvstag',
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
'start' => '/\\/\\//i',
'end' => '/$/mi',
'remember' => false,
'lookfor' => [
                        'blocks' => [
                            0 => 'url',
1 => 'email',
2 => 'note',
3 => 'cvstag',
                        ],
                    ],
                ],
'regexp' => [
                    'name' => 'regexp',
'case' => true,
'innerClass' => 'string',
'delimClass' => 'quotes',
'start' => '/\\//',
'end' => '/\\/g?i?/',
'remember' => false,
'lookfor' => [
                        'blocks' => [
                            0 => 'reescaped',
                        ],
                    ],
                ],
            ],
'keywords' => [
                'builtin' => [
                    'name' => 'builtin',
'innerClass' => 'builtin',
'case' => true,
'inherits' => 'identifier',
'match' => [
                        'String' => true,
'Array' => true,
'RegExp' => true,
'Function' => true,
'Math' => true,
'Number' => true,
'Date' => true,
'Image' => true,
'window' => true,
'document' => true,
'navigator' => true,
'onAbort' => true,
'onBlur' => true,
'onChange' => true,
'onClick' => true,
'onDblClick' => true,
'onDragDrop' => true,
'onError' => true,
'onFocus' => true,
'onKeyDown' => true,
'onKeyPress' => true,
'onKeyUp' => true,
'onLoad' => true,
'onMouseDown' => true,
'onMouseOver' => true,
'onMouseOut' => true,
'onMouseMove' => true,
'onMouseUp' => true,
'onMove' => true,
'onReset' => true,
'onResize' => true,
'onSelect' => true,
'onSubmit' => true,
'onUnload' => true,
                    ],
                ],
'reserved' => [
                    'name' => 'reserved',
'innerClass' => 'reserved',
'case' => true,
'inherits' => 'identifier',
'match' => [
                        'break' => true,
'continue' => true,
'do' => true,
'while' => true,
'export' => true,
'for' => true,
'in' => true,
'if' => true,
'else' => true,
'import' => true,
'return' => true,
'label' => true,
'switch' => true,
'case' => true,
'var' => true,
'with' => true,
'delete' => true,
'new' => true,
'this' => true,
'typeof' => true,
'void' => true,
'abstract' => true,
'boolean' => true,
'byte' => true,
'catch' => true,
'char' => true,
'class' => true,
'const' => true,
'debugger' => true,
'default' => true,
'double' => true,
'enum' => true,
'extends' => true,
'false' => true,
'final' => true,
'finally' => true,
'float' => true,
'function' => true,
'implements' => true,
'goto' => true,
'instanceof' => true,
'int' => true,
'interface' => true,
'long' => true,
'native' => true,
'null' => true,
'package' => true,
'private' => true,
'protected' => true,
'public' => true,
'short' => true,
'static' => true,
'super' => true,
'synchronized' => true,
'throw' => true,
'throws' => true,
'transient' => true,
'true' => true,
'try' => true,
'volatile' => true,
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
'match' => '/\\\\\\\\|\\\\"|\\\\\'|\\\\`|\\\\t|\\\\n|\\\\r/i',
'multiline' => false,
                ],
'reescaped' => [
                    'name' => 'reescaped',
'case' => false,
'innerClass' => 'special',
'match' => '/\\\\\\//i',
'multiline' => false,
                ],
'identifier' => [
                    'name' => 'identifier',
'case' => false,
'innerClass' => 'identifier',
'match' => '/[a-z_]\\w*/i',
'multiline' => false,
                ],
'number' => [
                    'name' => 'number',
'case' => false,
'innerClass' => 'number',
'match' => '/\\d*\\.?\\d+/i',
'multiline' => false,
                ],
'url' => [
                    'name' => 'url',
'case' => false,
'innerClass' => 'url',
'match' => '/((https?|ftp):\\/\\/[\\w\\?\\.\\-\\&=\\/]+([^\\w\\?\\.\\&=\\/]|$))|(^|[\\s,!?])www\\.\\w+\\.\\w+[\\w\\?\\.\\&=\\/]*([^\\w\\?\\.\\&=\\/]|$)/i',
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
'match' => '/\\b(note|fixme):/i',
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
                'blocks' => [
                    0 => 'identifier',
1 => 'number',
                ],
'regions' => [
                    0 => 'block',
1 => 'brackets',
2 => 'sqbrackets',
3 => 'mlcomment',
4 => 'strdouble',
5 => 'strsingle',
6 => 'comment',
7 => 'regexp',
                ],
            ],
'case' => false,
'defClass' => 'code',
        ];

        parent::_init($options);
    }
}
