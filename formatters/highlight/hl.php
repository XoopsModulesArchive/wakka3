<?php

require_once 'lib/Text_Highlighter/Highlighter.php';
if ($options['_default']) {
    $hl = Text_Highlighter::factory(mb_strtoupper($options['_default']), ['numbers' => false]);

    echo '<!--no' . 'typo-->';

    echo '<div class="code">';

    echo '<pre>' . $hl->highlight($text) . '</pre>';

    echo '</div>';

    echo '<!--/no' . 'typo-->';
} else {
    echo $text;
}
