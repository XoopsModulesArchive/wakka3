<?php
/**
 * Auto-generated class. XML syntax highlighting
 *
 * @author  Andrey Demenev <demenev@on-line.jar.ru>
 */

/**
 * @ignore
 */

/**
 * Auto-generated class. XML syntax highlighting
 *
 * @author  Andrey Demenev <demenev@on-line.jar.ru>
 */
class Text_Highlighter_XML extends Text_Highlighter
{
    /**
     * PHP4 Compatible Constructor
     *
     * @param array $options
     */
    public function Text_Highlighter_XML($options = [])
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
                'cdata' => [
                    'name' => 'cdata',
'case' => false,
'innerClass' => 'comment',
'delimClass' => 'comment',
'start' => '/\\<\\!\\[CDATA\\[/i',
'end' => '/\\]\\]\\>/i',
'remember' => false,
                ],
'comment' => [
                    'name' => 'comment',
'case' => false,
'innerClass' => 'comment',
'delimClass' => 'comment',
'start' => '/\\<!--/i',
'end' => '/--\\>/i',
'remember' => false,
                ],
'tag' => [
                    'name' => 'tag',
'case' => false,
'innerClass' => 'code',
'delimClass' => 'brackets',
'start' => '/\\<[\\?\\/]?/i',
'end' => '/[\\/\\?]?\\>/i',
'remember' => false,
'lookfor' => [
                        'regions' => [
                            0 => 'param',
                        ],
'blocks' => [
                            0 => 'tagname',
1 => 'paramname',
                        ],
                    ],
                ],
'param' => [
                    'name' => 'param',
'case' => false,
'innerClass' => 'string',
'delimClass' => 'quotes',
'start' => '/"/i',
'end' => '/"/i',
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
'case' => false,
'innerClass' => 'reserved',
'match' => '/^[\\w\\-\\:]+/i',
'multiline' => false,
                ],
'paramname' => [
                    'name' => 'paramname',
'case' => false,
'innerClass' => 'var',
'match' => '/[\\w\\-\\:]+/i',
'multiline' => false,
                ],
'entity' => [
                    'name' => 'entity',
'case' => false,
'innerClass' => 'special',
'match' => '/(&|%)[\\w\\-\\.]+;/i',
'multiline' => false,
                ],
            ],
'toplevel' => [
                'blocks' => [
                    0 => 'entity',
                ],
'regions' => [
                    0 => 'cdata',
1 => 'comment',
2 => 'tag',
                ],
            ],
'case' => false,
'defClass' => 'code',
        ];

        parent::_init($options);
    }
}
