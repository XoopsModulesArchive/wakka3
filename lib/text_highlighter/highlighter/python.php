<?php
/**
 * Auto-generated class. PYTHON syntax highlighting
 *
 * @author  Andrey Demenev <demenev@on-line.jar.ru>
 */

/**
 * @ignore
 */

/**
 * Auto-generated class. PYTHON syntax highlighting
 *
 * @author  Andrey Demenev <demenev@on-line.jar.ru>
 */
class Text_Highlighter_PYTHON extends Text_Highlighter
{
    /**
     * PHP4 Compatible Constructor
     *
     * @param array $options
     */
    public function Text_Highlighter_PYTHON($options = [])
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
                'strsingle3' => [
                    'name' => 'strsingle3',
'case' => false,
'innerClass' => 'string',
'delimClass' => 'quotes',
'start' => '/\'\'\'/i',
'end' => '/\'\'\'/i',
'remember' => false,
'lookfor' => [
                        'blocks' => [
                            0 => 'escaped',
                        ],
                    ],
                ],
'strdouble3' => [
                    'name' => 'strdouble3',
'case' => false,
'innerClass' => 'string',
'delimClass' => 'quotes',
'start' => '/"""/i',
'end' => '/"""/i',
'remember' => false,
'lookfor' => [
                        'blocks' => [
                            0 => 'escaped',
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
                            0 => 'escaped',
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
                            0 => 'strsingle3',
1 => 'strdouble3',
2 => 'strdouble',
3 => 'strsingle',
4 => 'brackets',
5 => 'sqbrackets',
                        ],
'blocks' => [
                            0 => 'possiblefunction',
1 => 'identifier',
2 => 'exponent',
3 => 'imaginary',
4 => 'float',
5 => 'integer',
6 => 'hexinteger',
7 => 'octinteger',
8 => 'comment',
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
                            0 => 'strsingle3',
1 => 'strdouble3',
2 => 'strdouble',
3 => 'strsingle',
4 => 'brackets',
5 => 'sqbrackets',
                        ],
'blocks' => [
                            0 => 'possiblefunction',
1 => 'identifier',
2 => 'exponent',
3 => 'imaginary',
4 => 'float',
5 => 'integer',
6 => 'hexinteger',
7 => 'octinteger',
8 => 'comment',
                        ],
                    ],
                ],
            ],
'keywords' => [
                'reserved' => [
                    'name' => 'reserved',
'innerClass' => 'reserved',
'case' => true,
'inherits' => 'identifier',
'match' => [
                        'and' => true,
'del' => true,
'for' => true,
'is' => true,
'raise' => true,
'assert' => true,
'elif' => true,
'from' => true,
'lambda' => true,
'return' => true,
'break' => true,
'else' => true,
'global' => true,
'not' => true,
'try' => true,
'class' => true,
'except' => true,
'if' => true,
'or' => true,
'while' => true,
'continue' => true,
'exec' => true,
'import' => true,
'pass' => true,
'yield' => true,
'def' => true,
'finally' => true,
'in' => true,
'print' => true,
'False' => true,
'True' => true,
'None' => true,
'NotImplemented' => true,
'Ellipsis' => true,
'Exception' => true,
'SystemExit' => true,
'StopIteration' => true,
'StandardError' => true,
'KeyboardInterrupt' => true,
'ImportError' => true,
'EnvironmentError' => true,
'IOError' => true,
'OSError' => true,
'WindowsError' => true,
'EOFError' => true,
'RuntimeError' => true,
'NotImplementedError' => true,
'NameError' => true,
'UnboundLocalError' => true,
'AttributeError' => true,
'SyntaxError' => true,
'IndentationError' => true,
'TabError' => true,
'TypeError' => true,
'AssertionError' => true,
'LookupError' => true,
'IndexError' => true,
'KeyError' => true,
'ArithmeticError' => true,
'OverflowError' => true,
'ZeroDivisionError' => true,
'FloatingPointError' => true,
'ValueError' => true,
'UnicodeError' => true,
'UnicodeEncodeError' => true,
'UnicodeDecodeError' => true,
'UnicodeTranslateError' => true,
'ReferenceError' => true,
'SystemError' => true,
'MemoryError' => true,
'Warning' => true,
'UserWarning' => true,
'DeprecationWarning' => true,
'PendingDeprecationWarning' => true,
'SyntaxWarning' => true,
'OverflowWarning' => true,
'RuntimeWarning' => true,
'FutureWarning' => true,
                    ],
                ],
'builtin' => [
                    'name' => 'builtin',
'innerClass' => 'builtin',
'case' => true,
'inherits' => 'possiblefunction',
'otherwise' => 'identifier',
'match' => [
                        '__import__' => true,
'abs' => true,
'apply' => true,
'basestring' => true,
'bool' => true,
'buffer' => true,
'callable' => true,
'chr' => true,
'classmethod' => true,
'cmp' => true,
'coerce' => true,
'compile' => true,
'complex' => true,
'delattr' => true,
'dict' => true,
'dir' => true,
'divmod' => true,
'enumerate' => true,
'eval' => true,
'execfile' => true,
'file' => true,
'filter' => true,
'float' => true,
'getattr' => true,
'globals' => true,
'hasattr' => true,
'hash' => true,
'help' => true,
'hex' => true,
'id' => true,
'input' => true,
'int' => true,
'intern' => true,
'isinstance' => true,
'issubclass' => true,
'iter' => true,
'len' => true,
'list' => true,
'locals' => true,
'long' => true,
'map' => true,
'max' => true,
'min' => true,
'object' => true,
'oct' => true,
'open' => true,
'ord' => true,
'pow' => true,
'property' => true,
'range' => true,
'raw_input' => true,
'reduce' => true,
'reload' => true,
'repr' => true,
'round' => true,
'setattr' => true,
'slice' => true,
'staticmethod' => true,
'sum' => true,
'super' => true,
'str' => true,
'tuple' => true,
'type' => true,
'unichr' => true,
'unicode' => true,
'vars' => true,
'xrange' => true,
'zip' => true,
                    ],
                ],
            ],
'blocks' => [
                'escaped' => [
                    'name' => 'escaped',
'case' => false,
'innerClass' => 'special',
'match' => '/\\\\./i',
'multiline' => false,
                ],
'possiblefunction' => [
                    'name' => 'possiblefunction',
'case' => false,
'innerClass' => 'identifier',
'match' => '/[a-z_]\\w*(?=\\s*\\()/i',
'multiline' => false,
                ],
'identifier' => [
                    'name' => 'identifier',
'case' => false,
'innerClass' => 'identifier',
'match' => '/[a-z_]\\w*/i',
'multiline' => false,
                ],
'exponent' => [
                    'name' => 'exponent',
'case' => false,
'innerClass' => 'number',
'match' => '/((\\d+|((\\d*\\.\\d+)|(\\d+\\.\\d*)))[eE][+-]?\\d+)/i',
'multiline' => false,
                ],
'imaginary' => [
                    'name' => 'imaginary',
'case' => false,
'innerClass' => 'number',
'match' => '/((\\d*\\.\\d+)|(\\d+\\.\\d*)|(\\d+))j/i',
'multiline' => false,
                ],
'float' => [
                    'name' => 'float',
'case' => false,
'innerClass' => 'number',
'match' => '/(\\d*\\.\\d+)|(\\d+\\.\\d*)/i',
'multiline' => false,
                ],
'integer' => [
                    'name' => 'integer',
'case' => false,
'innerClass' => 'number',
'match' => '/\\d+l?|\\b0l?\\b/i',
'multiline' => false,
                ],
'hexinteger' => [
                    'name' => 'hexinteger',
'case' => false,
'innerClass' => 'number',
'match' => '/0[xX][\\da-f]+l?/i',
'multiline' => false,
                ],
'octinteger' => [
                    'name' => 'octinteger',
'case' => false,
'innerClass' => 'number',
'match' => '/0[0-7]+l?/i',
'multiline' => false,
                ],
'comment' => [
                    'name' => 'comment',
'case' => false,
'innerClass' => 'comment',
'match' => '/#.+/i',
'multiline' => false,
                ],
            ],
'toplevel' => [
                'blocks' => [
                    0 => 'possiblefunction',
1 => 'identifier',
2 => 'exponent',
3 => 'imaginary',
4 => 'float',
5 => 'integer',
6 => 'hexinteger',
7 => 'octinteger',
8 => 'comment',
                ],
'regions' => [
                    0 => 'strsingle3',
1 => 'strdouble3',
2 => 'strdouble',
3 => 'strsingle',
4 => 'brackets',
5 => 'sqbrackets',
                ],
            ],
'case' => false,
'defClass' => 'code',
        ];

        parent::_init($options);
    }
}
