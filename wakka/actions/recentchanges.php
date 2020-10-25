<?php

if ($pages = $this->LoadRecentlyChanged()) {
    print('<a href="' . $this->href('', 'xml/recentchanges_' . preg_replace('/[^a-zA-Z0-9]/', '', mb_strtolower($this->GetConfigValue('wakka_name'))) . '.xml') . '"><img src="' . $this->href('', 'xml/xml.gif') . '" width="36" height="14" style="border : 0px;" alt="XML"></a><br><br>');

    if ($user = $this->GetUser()) {
        $max = $user['changescount'];
    } else {
        $max = 50;
    }

    foreach ($pages as $i => $page) {
        if (($i < $max) || !$max) {
            // day header

            [$day, $time] = explode(' ', $page['time']);

            if ($day != $curday) {
                if ($curday) {
                    print("<br>\n");
                }

                print("<b>$day:</b><br>\n");

                $curday = $day;
            }

            // print entry

            print('&nbsp;&nbsp;&nbsp;(' . $page['time'] . ') (' . $this->Link($page['tag'], 'revisions', _MI_HISTORY, 0) . ') ' . $this->Link($page['tag'], '', '', 0) . ' . . . . ' . $this->Format($page['user']) . "<br>\n");
        }
    }
}
