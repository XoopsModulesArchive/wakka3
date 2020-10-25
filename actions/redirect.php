<?php

$page = $this->UnwrapLink($vars[0]);
if ($page) {
    if ($this->LoadPage($page, '', LOAD_CACHE, LOAD_META)) {
        $user = $this->GetUser();

        if ('Y' == $user['options']['dont_redirect']) {
            print('<br><br><br><center>' . $this->GetResourceValue('PageMoved') . ' ' . $this->Link('/' . $page) . '</center><br><br><br>');
        } else {
            $this->Redirect($this->href('', $page));
        }
    } else {
        print('<i>' . $this->GetResourceValue('WrongPage4Redirect') . '</i>');
    }
}
