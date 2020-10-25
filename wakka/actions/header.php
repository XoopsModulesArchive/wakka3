<?php

$message = $this->GetMessage();
$user = $this->GetUser();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title><?php echo $this->GetWakkaName() . ' : ' . $this->GetPageTag(); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo _CHARSET; ?>">
    <meta name="keywords" content="<?php echo $this->GetConfigValue('meta_keywords') ?>">
    <meta name="description" content="<?php echo $this->GetConfigValue('meta_description') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->GetConfigValue('base_url') ?>css/<?php echo $this->xoopsConfig['css']; ?>.css">
    <script language="JavaScript" type="text/javascript">
        function fKeyDown() {
            if (event.keyCode == 9) {
                event.returnValue = false;
                document.selection.createRange().text = String.fromCharCode(9);
            }
        }

        function move(add, del) {
            var key = new Array;
            var val = new Array;
            if (del.options.selectedIndex == -1)
                del.options.selectedIndex = del.options.length - 1;
            if (del.options.length && del.options[del.options.selectedIndex].value != '') {
                add.options.length = add.options.length + 1;
                add.options[add.options.length - 1].value = del.options[del.options.selectedIndex].value;
                add.options[add.options.length - 1].text = del.options[del.options.selectedIndex].text;
                counter = 0;
                for (var i = 0; i < del.options.length; i++) {
                    if (!del.options[i].selected) {
                        key[counter] = del.options[i].text;
                        val[counter] = del.options[i].value;
                        counter++;
                    }
                }
                del.options.length = del.options.length - 1;
                for (i in key) {
                    del.options[i].text = key[i];
                    del.options[i].value = val[i];
                }
            }
        }

        function commit(admins, admin) {
            var value = '';
            var flag = true;
            admin.value = '';
            for (var i = 0; i < admins.options.length; i++) {
                if (admins.options[i].value == "*")
                    flag = false;
            }
            if (flag) {
                for (var i = 0; i < admins.options.length; i++) {
                    if (i == admins.options.length - 1)
                        admin.value = admin.value + admins.options[i].value;
                    else
                        admin.value = admin.value + admins.options[i].value + '|';
                }
            }
        }
    </script>
</head>
<body
    <?php echo(!$user || (Y == $user['doubleclickedit'])) && ('show' == $this->GetMethod()) ? "ondblclick=\"document.location='" . $this->href('edit') . "';\" " : '' ?>
    <?php echo $message ? "onLoad=\"alert('" . $message . "');\" " : '' ?>
>
<div class="header">
    <h2><?php echo $this->config['wakka_name'] ?> : <a href="<?php echo $this->config['base_url'] ?>TextSearch?phrase=<?php echo urlencode($this->GetPageTag()); ?>"><?php echo $this->conver_pagename($this->GetPageTag()); ?></a></h2>
    <a href='<?php echo XOOPS_URL; ?>'><?php echo _MI_HOME; ?></a> :: <a href='<?php echo XOOPS_URL . '/modules/wakka/HomePage'; ?>'><?php echo _MI_HOMEPAGE; ?></a> ::
    </a><?php echo $this->config['navigation_links']; ?>
    <!-- <?php echo $this->config['navigation_links'] ? $this->Format($this->config['navigation_links']) . ' :: ' : '' ?>-->
    <?php echo _MI_USER . $this->Format($this->UserName()) ?>
</div>
