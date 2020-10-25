<?php
/**
 * Auto-generated class. DIFF syntax highlighting
 *
 * @author  Andrey Demenev <demenev@on-line.jar.ru>
 */

/**
 * @ignore
 */

/**
 * Auto-generated class. DIFF syntax highlighting
 *
 * @author  Andrey Demenev <demenev@on-line.jar.ru>
 */
class Text_Highlighter_DIFF extends Text_Highlighter
{
    /**
     * PHP4 Compatible Constructor
     *
     * @param array $options
     */
    public function Text_Highlighter_DIFF($options = [])
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
                'normNew' => [
                    'name' => 'normNew',
'case' => true,
'innerClass' => 'var',
'delimClass' => 'code',
'start' => '/^a\\d+\\s\\d+$/m',
'end' => '/(?=^[ad]\\d+\\s\\d+)/m',
'remember' => false,
                ],
'edNew' => [
                    'name' => 'edNew',
'case' => true,
'innerClass' => 'string',
'delimClass' => 'code',
'start' => '/^(\\d+)(,\\d+)?(a)$/m',
'end' => '/^(\\.)$/m',
'remember' => false,
                ],
'edChg' => [
                    'name' => 'edChg',
'case' => true,
'innerClass' => 'inlinedoc',
'delimClass' => 'code',
'start' => '/^(\\d+)(,\\d+)?(c)$/m',
'end' => '/^(\\.)$/m',
'remember' => false,
                ],
'fedNew' => [
                    'name' => 'fedNew',
'case' => true,
'innerClass' => 'string',
'delimClass' => 'code',
'start' => '/^a(\\d+)(\\s\\d+)?$/m',
'end' => '/^(\\.)$/m',
'remember' => false,
                ],
'fedChg' => [
                    'name' => 'fedChg',
'case' => true,
'innerClass' => 'inlinedoc',
'delimClass' => 'code',
'start' => '/^c(\\d+)(\\s\\d+)?$/m',
'end' => '/^(\\.)$/m',
'remember' => false,
                ],
            ],
'keywords' => [],
'blocks' => [
                'noNewLine' => [
                    'name' => 'noNewLine',
'case' => true,
'innerClass' => 'special',
'match' => '/^\\\\\\sNo\\snewline.+$/m',
'multiline' => false,
                ],
'diffSeparator' => [
                    'name' => 'diffSeparator',
'case' => true,
'innerClass' => 'code',
'match' => '/^\\-\\-\\-$/m',
'multiline' => false,
                ],
'diffCmdLine' => [
                    'name' => 'diffCmdLine',
'case' => true,
'innerClass' => 'var',
'match' => '/^(diff\\s+\\-|Only\\s+|Index).*$/m',
'multiline' => false,
                ],
'diffFiles' => [
                    'name' => 'diffFiles',
'case' => true,
'innerClass' => 'reserved',
'match' => '/^(\\-\\-\\-|\\+\\+\\+)\\s.+$/m',
'multiline' => false,
                ],
'contextOrg' => [
                    'name' => 'contextOrg',
'case' => true,
'innerClass' => 'quotes',
'match' => '/^\\*.*$/m',
'multiline' => false,
                ],
'contextNew' => [
                    'name' => 'contextNew',
'case' => true,
'innerClass' => 'string',
'match' => '/^\\+.*$/m',
'multiline' => false,
                ],
'contextChg' => [
                    'name' => 'contextChg',
'case' => true,
'innerClass' => 'inlinedoc',
'match' => '/^!.*$/m',
'multiline' => false,
                ],
'defOrg' => [
                    'name' => 'defOrg',
'case' => true,
'innerClass' => 'quotes',
'match' => '/^\\<\\s.*$/m',
'multiline' => false,
                ],
'defNew' => [
                    'name' => 'defNew',
'case' => true,
'innerClass' => 'string',
'match' => '/^\\>\\s.*$/m',
'multiline' => false,
                ],
'defChg' => [
                    'name' => 'defChg',
'case' => true,
'innerClass' => 'code',
'match' => '/^\\d+(\\,\\d+)?[acd]\\d+(,\\d+)?$/m',
'multiline' => false,
                ],
'uniOrg' => [
                    'name' => 'uniOrg',
'case' => true,
'innerClass' => 'quotes',
'match' => '/^\\-.*$/m',
'multiline' => false,
                ],
'uniNew' => [
                    'name' => 'uniNew',
'case' => true,
'innerClass' => 'string',
'match' => '/^\\+.*$/m',
'multiline' => false,
                ],
'uniChg' => [
                    'name' => 'uniChg',
'case' => true,
'innerClass' => 'code',
'match' => '/^@@.+@@$/m',
'multiline' => false,
                ],
'normOrg' => [
                    'name' => 'normOrg',
'case' => true,
'innerClass' => 'code',
'match' => '/^d\\d+\\s\\d+$/m',
'multiline' => false,
                ],
'edDel' => [
                    'name' => 'edDel',
'case' => true,
'innerClass' => 'code',
'match' => '/^(\\d+)(,\\d+)?(d)$/m',
'multiline' => false,
                ],
'fedDel' => [
                    'name' => 'fedDel',
'case' => true,
'innerClass' => 'code',
'match' => '/^d(\\d+)(\\s\\d+)?$/m',
'multiline' => false,
                ],
            ],
'toplevel' => [
                'blocks' => [
                    0 => 'noNewLine',
1 => 'diffSeparator',
2 => 'diffCmdLine',
3 => 'diffFiles',
4 => 'contextOrg',
5 => 'contextNew',
6 => 'contextChg',
7 => 'defOrg',
8 => 'defNew',
9 => 'defChg',
10 => 'uniOrg',
11 => 'uniNew',
12 => 'uniChg',
13 => 'normOrg',
14 => 'edDel',
15 => 'fedDel',
                ],
'regions' => [
                    0 => 'normNew',
1 => 'edNew',
2 => 'edChg',
3 => 'fedNew',
4 => 'fedChg',
                ],
            ],
'case' => true,
'defClass' => 'default',
        ];

        parent::_init($options);
    }
}
