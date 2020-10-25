<?php

if ($pages = $this->LoadAllPages()) {
    foreach ($pages as $page) {
        if (0 !== strpos($page['tag'], "Comment")) {
            if ($this->config['hide_locked']) {
                $access = $this->HasAccess('read', $page['tag']);
            } else {
                $access = true;
            }

            if ($access) {
                $firstChar = mb_strtoupper($page['tag'][0]);

                if (!preg_match('/' . $this->language['ALPHA'] . '/', $firstChar)) {
                    $firstChar = '#';
                }

                if ($firstChar != $curChar) {
                    if ($curChar) {
                        print("<br>\n");
                    }

                    print("<strong>$firstChar</strong><br>\n");

                    $curChar = $firstChar;
                }

                print($this->Link('/' . $page['tag'], '', $page['tag']) . "<br>\n");
            }
        }
    }
} else {
    echo $this->GetResourceValue('NoPagesFound');
}
