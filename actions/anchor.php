<?php

// Param name
if ($vars[0]) {
    $href = $vars[0];

    $text = '';

    if ($vars['text']) {
        if (false !== mb_strpos($vars['text'], '~')) {
            $vars['text'] = str_replace('~', $href, $vars['text']);
        }

        $text = htmlspecialchars($vars['text'], ENT_QUOTES | ENT_HTML5);
    }

    $title = '';

    if ($vars['title']) {
        $title = htmlspecialchars($vars['title'], ENT_QUOTES | ENT_HTML5);
    }

    $href = htmlspecialchars($href, ENT_QUOTES | ENT_HTML5);

    echo "<a name=\"$href\" href=\"#$href\" title=\"$title\">$text</a>\n";
}
