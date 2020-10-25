<?php

if ($pages = $this->LoadAllPages()) {
    foreach ($pages as $page) {
        if (0 !== strpos($page['tag'], "Comment")) {
            $page['url'] = $page['tag'];

            $page['tag'] = $this->conver_pagename($page['tag']);

            if (is_file(XOOPS_ROOT_PATH . '/modules/wakka/language/' . $this->xoopsConfig['language'] . '/class.pindex.php')) {
                require_once 'language/' . $this->xoopsConfig['language'] . '/class.pindex.php';

                $pindex = new Pindex();

                $index = $pindex->make_index($page['tag']);
            } else {
                $index = $page['tag'][0];
            }

            $newpages[$index][] = $page;
        }
    }

    foreach (
        [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            '#',
        ] as $index
    ) {
        if (count($newpages[$index]) > 0) {
            print('<strong>' . $index . "</strong><br>\n");

            foreach ($newpages[$index] as $page) {
                print($this->Link($page['url'], '', $page['tag']) . "<br>\n");
            }

            print("<br>\n");
        }
    }
} else {
    print('<em>' . _MI_NOPAGE . '</em>');
}
