<div class="page">
    <?php
    if ($global = $_REQUEST['global']) {
        $title = 'Sites linking to this Wakka (<a href="' . $this->href('referrers_sites', '', 'global=1') . '">see list of domains</a>):';

        $referrers = $this->LoadReferrers();
    } else {
        $title = _MI_PAGELINK
                     . $this->Link($this->GetPageTag())
                     . ($this->GetConfigValue('referrers_purge_time') ? ' (' . _MI_LAST . ' ' . (1 == $this->GetConfigValue('referrers_purge_time') ? '24 ' . _MI_HOURS : $this->GetConfigValue('referrers_purge_time') . ' days') . ')' : '')
                     . ' (<a href="'
                     . $this->href('referrers_sites')
                     . '">'
                     . _MI_LISTDOMAIN
                     . '</a>):';

        $referrers = $this->LoadReferrers($this->GetPageTag());
    }
    print("<strong>$title</strong><br><br>\n");
    if ($referrers) {
        {
            print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
            foreach ($referrers as $referrer) {
                print('<tr>');

                print('<td width="30" align="right" valign="top" style="padding-right: 10px">' . $referrer['num'] . '</td>');

                print('<td valign="top"><a href="' . $referrer['referrer'] . '">' . $referrer['referrer'] . '</a></td>');

                print("</tr>\n");
            }
            print("</table>\n");
        }
    } else {
        print('<em>' . _MI_NOPAGE . "</em><br>\n");
    }
    if ($global) {
        print('<br>[<a href="' . $this->href('referrers_sites') . '">View referring sites for ' . $this->GetPageTag() . ' only</a> | <a href="' . $this->href('referrers') . '">View referrers for ' . $this->GetPageTag() . ' only</a>]');
    } else {
        print('<br>[<a href="' . $this->href('referrers_sites', '', 'global=1') . '">View global referring sites</a> | <a href="' . $this->href('referrers', '', 'global=1') . '">View global referrers</a>]');
    }
    ?>
</div>
