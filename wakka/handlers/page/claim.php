<?php

// only claim ownership if this page has no owner, and if user is logged in.
if (($this->page && !$this->GetPageOwner() && $this->GetUser()) or $this->xoopsConfig['isadmin']) {
    $this->SetPageOwner($this->GetPageTag(), $this->GetUserName());

    // $this->SetMessage("You are now the owner of this page.");
}
$this->Redirect($this->href());
