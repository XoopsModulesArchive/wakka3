<?php

if ($pages = $this->LoadRecentlyCommented()) {
    foreach ($pages as $page) {
        // day header

        [$day, $time] = explode(' ', $page['comment_time']);

        if ($day != $curday) {
            if ($curday) {
                print("<br>\n");
            }

            print("<strong>$day:</strong><br>\n");

            $curday = $day;
        }

        // print entry

        print('&nbsp;&nbsp;&nbsp;(' . $page['comment_time'] . ') <a href="' . $this->href('', $page['tag'], 'show_comments=1') . '#' . $page['comment_tag'] . '">' . $page['tag'] . '</a> . . . . ' . _MI_LATESTCOMMENT . $this->Format($page['comment_user']) . "<br>\n");
    }
} else {
    print('<em>' . _MI_NOPAGE . '</em>');
}
