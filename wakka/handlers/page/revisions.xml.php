<?php

header('Content-type: text/xml');
$xml = '<?xml version="1.0" encoding="' . _CHARSET . "\"?>\n";
$xml .= "<rss version=\"0.92\">\n";
$xml .= "<channel>\n";
$xml .= '<title>' . $this->GetConfigValue('wakka_name') . ' - ' . $this->tag . "</title>\n";
$xml .= '<link>' . $this->GetConfigValue('base_url') . $this->tag . "</link>\n";
$xml .= '<description>History/revisions of ' . $this->GetConfigValue('wakka_name') . '/' . $this->tag . "</description>\n";
$xml .= "<language>en-us</language>\n";
if ($this->HasAccess('read')) {
    // load revisions for this page

    if ($pages = $this->LoadRevisions($this->tag)) {
        $max = 20;

        $c = 0;

        foreach ($pages as $page) {
            $c++;

            if (($c <= $max) || !$max) {
                $xml .= "<item>\n";

                $xml .= '<title>' . $page['time'] . "</title>\n";

                $xml .= '<link>' . $this->href('show') . '?time=' . urlencode($page['time']) . "</link>\n";

                $xml .= '<description>edited by ' . $page['user'] . "</description>\n";

                $xml .= "</item>\n";
            }
        }

        $output .= '</table>' . $this->FormClose() . "\n";
    }
} else {
    $xml .= "<item>\n";

    $xml .= "<title>Error</title>\n";

    $xml .= '<link>' . $this->href('show') . "</link>\n";

    $xml .= "<description>You're not allowed to access this information.</description>\n";

    $xml .= "</item>\n";
}
$xml .= "</channel>\n";
$xml .= "</rss>\n";
print($xml);
