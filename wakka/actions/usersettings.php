<?php

if ('logout' == $_REQUEST['action']) {
    $this->LogoutUser();

    $this->SetMessage('You are now logged out.');

    $this->Redirect($this->href());
} elseif ($user = $this->GetUser()) {
    // is user trying to update?

    if ('update' == $_REQUEST['action']) {
        $this->Query(
            'update '
            . $this->config['table_prefix']
            . 'users set '
            . "email = '"
            . $GLOBALS['xoopsDB']->escape($_POST['email'])
            . "', "
            . "doubleclickedit = '"
            . $GLOBALS['xoopsDB']->escape($_POST['doubleclickedit'])
            . "', "
            . "show_comments = '"
            . $GLOBALS['xoopsDB']->escape($_POST['show_comments'])
            . "', "
            . "revisioncount = '"
            . $GLOBALS['xoopsDB']->escape($_POST['revisioncount'])
            . "', "
            . "changescount = '"
            . $GLOBALS['xoopsDB']->escape($_POST['changescount'])
            . "', "
            . "motto = '"
            . $GLOBALS['xoopsDB']->escape($_POST['motto'])
            . "' "
            . "where name = '"
            . $user['name']
            . "' limit 1"
        );

        $this->SetUser($this->LoadUser($user['name']));

        // forward

        $this->SetMessage('User settings stored!');

        $this->Redirect($this->href());
    }

    // user is logged in; display config form

    print($this->FormOpen()); ?>
    <input type="hidden" name="action" value="update">
    <table>
        <tr>
            <td align="right"></td>
            <td>Hello, <?php echo $this->Link($user['name']) ?>!</td>
        </tr>
        <tr>
            <td align="right">Your email address:</td>
            <td><input name="email" value="<?php echo htmlentities($user['email'], ENT_QUOTES | ENT_HTML5) ?>" size="40"></td>
        </tr>
        <tr>
            <td align="right">Doubleclick Editing:</td>
            <td><input type="hidden" name="doubleclickedit" value="N"><input type="checkbox" name="doubleclickedit" value="Y" <?php echo 'Y' == $user['doubleclickedit'] ? 'checked' : '' ?>></td>
        </tr>
        <tr>
            <td align="right">Show comments by default:</td>
            <td><input type="hidden" name="show_comments" value="N"><input type="checkbox" name="show_comments" value="Y" <?php echo 'Y' == $user['show_comments'] ? 'checked' : '' ?>></td>
        </tr>
        <tr>
            <td align="right">Recent changes limit:</td>
            <td><input name="changescount" value="<?php echo htmlentities($user['changescount'], ENT_QUOTES | ENT_HTML5) ?>" size="40"></td>
        </tr>
        <tr>
            <td align="right">Revision list limit:</td>
            <td><input name="revisioncount" value="<?php echo htmlentities($user['revisioncount'], ENT_QUOTES | ENT_HTML5) ?>" size="40"></td>
        </tr>
        <tr>
            <td align="right">Your motto:</td>
            <td><input name="motto" value="<?php echo htmlentities($user['motto'], ENT_QUOTES | ENT_HTML5) ?>" size="40"></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" value="Update Settings"> <input type="button" value="Logout" onClick="document.location='<?php echo $this->href('', '', 'action=logout'); ?>'"></td>
        </tr>
    </table>
    <br>
    See a list of pages you own (<a href="<?php echo $this->href('', 'MyPages'); ?>">MyPages</a>) and pages you've edited (<a href="<?php echo $this->href('', 'MyChanges'); ?>">MyChanges</a>).<br>
    <?php
    print($this->FormClose());
} else {
    // user is not logged in

    // is user trying to log in or register?

    if ('login' == $_REQUEST['action']) {
        // if user name already exists, check password

        if ($existingUser = $this->LoadUser($_POST['name'])) {
            // check password

            if ($existingUser['password'] == md5($_POST['password'])) {
                $this->SetUser($existingUser);

                $this->Redirect($this->href());
            } else {
                $error = 'Wrong password!';
            }
        } // otherwise, create new account

        else {
            $name = trim($_POST['name']);

            $email = trim($_POST['email']);

            $password = $_POST['password'];

            $confpassword = $_POST['confpassword'];

            // check if name is WikkiName style

            if (!$this->IsWikiName($name)) {
                $error = 'User name must be WikiName formatted!';
            } elseif (!$email) {
                $error = 'You must specify an email address.';
            } elseif (!preg_match("/^.+?\@.+?\..+$/", $email)) {
                $error = "That doesn't quite look like an email address.";
            } elseif ($confpassword != $password) {
                $error = "Passwords didn't match.";
            } elseif (preg_match('/ /', $password)) {
                $error = "Spaces aren't allowed in passwords.";
            } elseif (mb_strlen($password) < 5) {
                $error = 'Password too short.';
            } else {
                $this->Query(
                    'insert into ' . $this->config['table_prefix'] . 'users set ' . 'signuptime = now(), ' . "name = '" . $GLOBALS['xoopsDB']->escape($name) . "', " . "email = '" . $GLOBALS['xoopsDB']->escape($email) . "', " . "password = md5('" . $GLOBALS['xoopsDB']->escape($_POST['password']) . "')"
                );

                // log in

                $this->SetUser($this->LoadUser($name));

                // forward

                $this->Redirect($this->href());
            }
        }
    }

    print($this->FormOpen()); ?>
    <input type="hidden" name="action" value="login">
    <table>
        <tr>
            <td align="right"></td>
            <td><?php echo $this->Format("If you're already a registered user, log in here!"); ?></td>
        </tr>
        <?php
        if ($error) {
            print('<tr><td></td><td><div class="error">' . $this->Format($error) . "</div></td></tr>\n");
        } ?>
        <tr>
            <td align="right">Your WikiName:</td>
            <td><input name="name" size="40" value="<?php echo $name ?>"></td>
        </tr>
        <tr>
            <td align="right">Password (5+ chars):</td>
            <td><input type="password" name="password" size="40"></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" value="Login / Register" size="40"></td>
        </tr>
        <tr>
            <td align="right"></td>
            <td width="500"><?php echo $this->Format("Stuff you only need to fill in when you're logging in for the first time (and thus signing up as a new user on this site)."); ?></td>
        </tr>
        <tr>
            <td align="right">Confirm password:</td>
            <td><input type="password" name="confpassword" size="40"></td>
        </tr>
        <tr>
            <td align="right">Email address:</td>
            <td><input name="email" size="40" value="<?php echo $email ?>"></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" value="Login / Register" size="40"></td>
        </tr>
    </table>
    <?php
    print($this->FormClose());
}
?>
