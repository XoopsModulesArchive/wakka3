<?php
// actions/mychanges.php
// written by Carlo Zottmann
// http://wakkawikki.com/CarloZottmann
if ($user = $this->GetUser()) {
    $my_edits_count = 0;

    if (1 == $_REQUEST['bydate']) {
        print('<strong>' . _MI_ORDERTITLE . ' (<a href="' . $this->href('', $tag) . '">' . _MI_ORDERINDEX . "</a>).</strong><br><br>\n");

        if ($pages = $this->LoadAll('SELECT tag, time FROM ' . $this->config['table_prefix'] . "pages WHERE user = '" . $GLOBALS['xoopsDB']->escape($this->UserName()) . "' AND tag NOT LIKE 'Comment%' ORDER BY time ASC, tag ASC")) {
            foreach ($pages as $page) {
                $edited_pages[$page['tag']] = $page['time'];
            }

            $edited_pages = array_reverse($edited_pages);

            foreach ($edited_pages as $page['tag'] => $page['time']) {
                // day header

                [$day, $time] = explode(' ', $page['time']);

                if ($day != $curday) {
                    if ($curday) {
                        print("<br>\n");
                    }

                    print("<strong>$day:</strong><br>\n");

                    $curday = $day;
                }

                // print entry

                print("&nbsp;&nbsp;&nbsp;($time) (" . $this->Link($page['tag'], 'revisions', _MI_HISTORY, 0) . ') ' . $this->Link($page['tag'], '', '', 0) . "<br>\n");

                $my_edits_count++;
            }

            if (0 == $my_edits_count) {
                print("<em>You didn't edit any pages yet.</em>");
            }
        } else {
            print('<em>' . _MI_NOPAGE . '</em>');
        }
    } else {
        print('<strong>' . _MI_ORDERTITLE . ' (<a href="' . $this->href('', $tag) . '?bydate=1">' . _MI_ORDERDATE . "</a>).</strong><br><br>\n");

        if ($pages = $this->LoadAll('SELECT tag, time FROM ' . $this->config['table_prefix'] . "pages WHERE user = '" . $GLOBALS['xoopsDB']->escape($this->UserName()) . "' AND tag NOT LIKE 'Comment%' ORDER BY tag ASC, time DESC")) {
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
                        print('&nbsp;&nbsp;&nbsp;(' . $page['time'] . ') (' . $this->Link($page['url'], 'revisions', _MI_HISTORY, 0) . ') ' . $this->Link($page['url'], '', $page['tag']) . "<br>\n");
                    }

                    print("<br>\n");
                }
            }
        } else {
            print('<em>' . _MI_NOPAGE . '</em>');
        }
    }
} else {
    print("<em>You're not logged in, thus the list of pages you've edited couldn't be retrieved.</em>");
}
