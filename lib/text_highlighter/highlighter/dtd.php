<?php
/**
 * Auto-generated class. DTD syntax highlighting
 *
 * @author  Andrey Demenev <demenev@on-line.jar.ru>
 */

/**
 * @ignore
 */

/**
 * Auto-generated class. DTD syntax highlighting
 *
 * @author  Andrey Demenev <demenev@on-line.jar.ru>
 */
class Text_Highlighter_DTD extends Text_Highlighter
{
    /**
     * PHP4 Compatible Constructor
     *
     * @param array $options
     */
    public function Text_Highlighter_DTD($options = [])
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
                'comment' => [
                    'name' => 'comment',
'case' => true,
'innerClass' => 'comment',
'delimClass' => 'comment',
'start' => '/\\<!--/',
'end' => '/--\\>/',
'remember' => false,
                ],
'redecl' => [
                    'name' => 'redecl',
'case' => true,
'innerClass' => 'code',
'delimClass' => 'brackets',
'start' => '/\\<\\!\\[/',
'end' => '/\\]\\]\\>/',
'remember' => false,
'lookfor' => [
                        'regions' => [
                            0 => 'comment',
1 => 'tag',
                        ],
'blocks' => [
                            0 => 'pcdata',
1 => 'entity',
2 => 'identifier',
                        ],
                    ],
                ],
'tag' => [
                    'name' => 'tag',
'case' => true,
'innerClass' => 'code',
'delimClass' => 'brackets',
'start' => '/\\</',
'end' => '/\\>/',
'remember' => false,
'lookfor' => [
                        'regions' => [
                            0 => 'comment',
1 => 'brackets',
2 => 'strsingle',
3 => 'strdouble',
                        ],
'blocks' => [
                            0 => 'tagname',
1 => 'reserved',
2 => 'pcdata',
3 => 'entity',
4 => 'identifier',
                        ],
                    ],
                ],
'brackets' => [
                    'name' => 'brackets',
'case' => true,
'innerClass' => 'code',
'delimClass' => 'brackets',
'start' => '/\\(/',
'end' => '/\\)/',
'remember' => false,
'lookfor' => [
                        'blocks' => [
                            0 => 'entity',
1 => 'identifier',
                        ],
                    ],
                ],
'strsingle' => [
                    'name' => 'strsingle',
'case' => true,
'innerClass' => 'string',
'delimClass' => 'quotes',
'start' => '/\'/',
'end' => '/\'/',
'remember' => false,
'lookfor' => [
                        'blocks' => [
                            0 => 'entity',
                        ],
                    ],
                ],
'strdouble' => [
                    'name' => 'strdouble',
'case' => true,
'innerClass' => 'string',
'delimClass' => 'quotes',
'start' => '/"/',
'end' => '/"/',
'remember' => false,
'lookfor' => [
                        'blocks' => [
                            0 => 'entity',
                        ],
                    ],
                ],
            ],
'keywords' => [],
'blocks' => [
                'tagname' => [
                    'name' => 'tagname',
'case' => true,
'innerClass' => 'var',
'match' => '/^!(ENTITY|ATTLIST|ELEMENT|NOTATION)\\b/',
'multiline' => false,
                ],
'reserved' => [
                    'name' => 'reserved',
'case' => true,
'innerClass' => 'reserved',
'match' => '/\\s(#(IMPLIED|REQUIRED|FIXED))|CDATA|ENTITY|NOTATION|NMTOKENS?|PUBLIC|SYSTEM\\b/',
'multiline' => false,
                ],
'pcdata' => [
                    'name' => 'pcdata',
'case' => true,
'innerClass' => 'reserved',
'match' => '/#PCDATA\\b/',
'multiline' => false,
                ],
'entity' => [
                    'name' => 'entity',
'case' => true,
'innerClass' => 'special',
'match' => '/(\\&|\\%)[\\w\\-\\.]+;/',
'multiline' => false,
                ],
'identifier' => [
                    'name' => 'identifier',
'case' => false,
'innerClass' => 'identifier',
'match' => '/[a-z][a-z\\d\\-\\,:]+/i',
'multiline' => false,
                ],
            ],
'toplevel' => [
                'blocks' => [
                    0 => 'entity',
                ],
'regions' => [
                    0 => 'comment',
1 => 'redecl',
2 => 'tag',
                ],
            ],
'case' => true,
'defClass' => 'code',
        ];

        parent::_init($options);
    }
}
