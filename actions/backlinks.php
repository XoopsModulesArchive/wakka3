<?php

if ($vars[0]) {
    $tag = $this->UnwrapLink($vars[0]);
} else {
    $tag = $this->getPageTag();
}
if ($pages = $this->LoadPagesLinkingTo($tag)) {
    print('<fieldset><legend>' . $this->GetResourceValue('ReferringPages') . ":</legend>\n");

    foreach ($pages as $page) {
        if ($this->config['hide_locked']) {
            $access = $this->HasAccess('read', $page['tag']);
        } else {
            $access = true;
        }

        if ($access) {
            echo($this->Link('/' . $page['tag'] . '#' . $this->NpjTranslit($tag), '', $page['tag']) . "<br>\n");
        }
    }

    echo "</fieldset>\n";
} else {
    echo $this->GetResourceValue('NoReferringPages');
}
