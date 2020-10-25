<?php

$vars[0] = $this->UnwrapLink($vars[0]);
if (!$this->HasAccess('read', $vars[0])) {
    echo $this->GetResourceValue('NoAccessToSourcePage');
} else {
    if (!$phrase_page = $this->LoadPage($vars[0], $_REQUEST['time'])) {
        echo '<em> ' . $this->GetResourceValue('SourcePageDoesntExist') . '(' . $vars[0] . ')</em>';
    } else {
        $strings = preg_replace("/\{\{[^\}]+\}\}/", '', $phrase_page['body']);

        $strings = $this->Format($strings);

        $splitexpr = '|<br>|';

        if (1 == $useemptystring) {
            $splitexpr = "|<br>[\n\r ]*<br>|";
        }

        $lines = preg_preg_split($splitexpr, $strings);

        $lines = array_values(array_filter($lines, 'trim'));

        mt_srand((float)microtime() * 1000000);

        print $lines[mt_rand(0, count($lines) - 1)];
    }
}
