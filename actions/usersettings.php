<!--notypo-->
<?php
if ($_REQUEST['confirm']) {
    if ($this->LoadSingle(
        'select * from ' . $this->config['user_table'] . " where email_confirm = '" . quote($_REQUEST['confirm']) . "'"
    )) {
        $this->Query(
            'UPDATE ' . $this->config['user_table'] . " SET email_confirm = '' WHERE email_confirm = '" . quote($_REQUEST['confirm']) . "'"
        );

        echo '<br><br><center>' . $this->GetResourceValue('EmailConfirmed') . '</center><br><br>';
    } else {
        echo '<br><br><center>' . $this->GetResourceValue('EmailNotConfirmed') . '</center><br><br>';
    }
} elseif ('logout' == $_REQUEST['action']) {
    $this->LogoutUser();

    $this->SetMessage($this->GetResourceValue('LoggedOut'));

    $this->Redirect($this->href());
} elseif ($user = $this->GetUser()) {
    $this->SetPageLang($this->userlang);

    // is user trying to update?

    if ('update' == $_REQUEST['action']) {
        $bookmarks = str_replace("\r", '', $_POST['bookmarks']);

        $more = $this->ComposeOptions(
            [
                'theme' => $_POST['theme'],
'dont_redirect' => $_POST['dont_redirect'],
'send_watchmail' => $_POST['send_watchmail'],
'show_files' => $_POST['show_files'],
            ]
        );

        if ($user['email'] != $_POST['email']) {
            $confirm = md5(mt_rand() . $_POST['email'] . mt_rand());

            $subject = $this->GetResourceValue('Mail.Confirm');

            $message = $this->GetResourceValue('MailHello') . $user['name'] . '.<br> <br> ';

            $message .= str_replace(
                '%1',
                $this->GetConfigValue('wakka_name'),
                str_replace(
                                '%2',
                                $user['name'],
                                str_replace(
                                    '%3',
                                    $this->Href() . ($this->config['rewrite_mode'] ? '?' : '&amp;') . 'confirm=' . $confirm,
                                    $this->GetResourceValue('Mail.Verify')
                                )
                            )
            ) . '<br> ';

            $message .= '<br>' . $this->GetResourceValue('MailGoodbye') . ' ' . $this->GetConfigValue('wakka_name');

            $this->SendMail($_POST['email'], $subject, $message);
        }

        $this->Query(
            'update '
            . $this->config['user_table']
            . ' set '
            . "email = '"
            . quote($_POST['email'])
            . "', "
            . ($confirm ? "email_confirm = '" . quote($confirm) . "', " : '')
            . "doubleclickedit = '"
            . quote($_POST['doubleclickedit'])
            . "', "
            . "showdatetime = '"
            . quote(
                $_POST['showdatetimeinlinks']
            )
            . "', "
            . "show_comments = '"
            . quote($_POST['show_comments'])
            . "', "
            . "revisioncount = '"
            . quote($_POST['revisioncount'])
            . "', "
            . "changescount = '"
            . quote($_POST['changescount'])
            . "', "
            . "motto = '"
            . quote($_POST['motto'])
            . "', "
            . "bookmarks = '"
            . quote($bookmarks)
            . "', "
            . "show_spaces = '"
            . quote($_POST['show_spaces'])
            . "', "
            . "typografica = '"
            . quote($_POST['typografica'])
            . "', "
            . "lang = '"
            . quote($_POST['lang'])
            . "', "
            . "more = '"
            . quote($more)
            . "' "
            . "where name = '"
            . $user['name']
            . "' limit 1"
        );

        $this->SetUser($this->LoadUser($user['name']));

        $this->SetBookmarks(BM_USER);

        // forward

        $this->SetMessage($this->GetResourceValue('SettingsStored', $_POST['lang']));

        $this->Redirect($this->href());
    }

    // user is logged in; display config form

    print($this->FormOpen()); ?>
    <input type="hidden" name="action" value="update">
    <table width="90%">
        <tr>
            <td colspan="2" align="center"><?php echo $this->GetResourceValue('Hello') . ', ' . $this->ComposeLinkToPage($user['name']) ?>!<br><br></td>
        </tr>
        <tr>
            <td colspan="2" align="center"><a href="<?php echo $this->href('', 'Password') ?>"><?php echo $this->GetResourceValue('YouWantChangePassword'); ?></a></td>
        </tr>
        <tr>
            <td align="right"><?php echo $this->GetResourceValue('YourEmail'); ?>:</td>
            <td><input name="email" value="<?php echo htmlentities($user['email'], ENT_QUOTES | ENT_HTML5) ?>" size="40"></td>
        </tr>
        <tr>
            <td align="right"><?php echo $this->GetResourceValue('DoubleclickEditing'); ?>:</td>
            <td><input type="hidden" name="doubleclickedit" value="N"><input type="checkbox" name="doubleclickedit" value="Y" <?php echo 'Y' == $user['doubleclickedit'] ? 'checked' : '' ?>></td>
        </tr>
        <tr>
            <td align="right"><?php echo $this->GetResourceValue('ShowDateTimeInLinks'); ?>:</td>
            <td><input type="hidden" name="showdatetimeinlinks" value="N"><input type="checkbox" name="showdatetimeinlinks" value="Y" <?php echo 'Y' == $user['showdatetime'] ? 'checked' : '' ?>></td>
        </tr>
        <tr>
            <td align="right"><?php echo $this->GetResourceValue('ShowComments?'); ?>:</td>
            <td><input type="hidden" name="show_comments" value="N"><input type="checkbox" name="show_comments" value="Y" <?php echo 'Y' == $user['show_comments'] ? 'checked' : '' ?>></td>
        </tr>
        <tr>
            <td align="right"><?php echo $this->GetResourceValue('ShowFiles?'); ?>:</td>
            <td><input type="hidden" name="show_files" value="N"><input type="checkbox" name="show_files" value="Y" <?php echo 'Y' == $user['options']['show_files'] ? 'checked' : '' ?>></td>
        </tr>
        <tr>
            <td align="right"><?php echo $this->GetResourceValue('ShowSpaces'); ?>:</td>
            <td><input type="hidden" name="show_spaces" value="N"><input type="checkbox" name="show_spaces" value="Y" <?php echo 'Y' == $user['show_spaces'] ? 'checked' : '' ?>></td>
        </tr>
        <tr>
            <td align="right"><?php echo $this->GetResourceValue('DontRedirect'); ?>:</td>
            <td><input type="hidden" name="dont_redirect" value="N"><input type="checkbox" name="dont_redirect" value="Y" <?php echo 'Y' == $user['options']['dont_redirect'] ? 'checked' : '' ?>></td>
        </tr>
        <tr>
            <td align="right"><?php echo $this->GetResourceValue('SendWatchMail'); ?>:</td>
            <td><input type="hidden" name="send_watchmail" value="N"><input type="checkbox" name="send_watchmail" value="Y" <?php echo 'Y' == $user['options']['send_watchmail'] ? 'checked' : '' ?>></td>
        </tr>
        <!--tr>
<td align="right"><?php echo $this->GetResourceValue('Typografica'); ?>:</td>
<td><input type="hidden" name="typografica" value="N"><input type="checkbox" name="typografica" value="Y" <?php echo 'Y' == $user['typografica'] ? 'checked' : '' ?>></td>
</tr-->
        <tr>
            <td align="right"><?php echo $this->GetResourceValue('YourLanguage'); ?>:</td>
            <td>
                <select name="lang">
                    <option value=""></option>
                    <?php
                    $langs = $this->AvailableLanguages();

    for ($i = 0, $iMax = count($langs); $i < $iMax; $i++) {
        echo '<option value="' . $langs[$i] . '" ' . ($user['lang'] == $langs[$i] ? 'selected="selected"' : '') . '>' . $langs[$i] . '</option>';
    } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td align="right"><?php echo $this->GetResourceValue('ChooseTheme'); ?>:</td>
            <td>
                <select name="theme">
                    <option value=""></option>
                    <?php
                    $themes = $this->AvailableThemes();

    for ($i = 0, $iMax = count($themes); $i < $iMax; $i++) {
        echo '<option value="' . $themes[$i] . '" ' . ($user['options']['theme'] == $themes[$i] ? 'selected="selected"' : '') . '>' . $themes[$i] . '</option>';
    } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td align="right"><?php echo $this->GetResourceValue('RecentChangesLimit'); ?>:</td>
            <td><input name="changescount" value="<?php echo htmlentities($user['changescount'], ENT_QUOTES | ENT_HTML5) ?>" size="40"></td>
        </tr>
        <tr>
            <td align="right"><?php echo $this->GetResourceValue('RevisionListLimit'); ?>:</td>
            <td><input name="revisioncount" value="<?php echo htmlentities($user['revisioncount'], ENT_QUOTES | ENT_HTML5) ?>" size="40"></td>
        </tr>
        <tr>
            <td align="right"><?php echo $this->GetResourceValue('YourMotto'); ?>:</td>
            <td><input name="motto" value="<?php echo htmlspecialchars($user['motto'], ENT_QUOTES | ENT_HTML5) ?>" size="40"></td>
        </tr>
        <tr>
            <td align="right" valign="top"><?php echo $this->GetResourceValue('YourBookmarks'); ?>:</td>
            <td><textarea name="bookmarks" cols="40" rows="10"><?php echo htmlspecialchars($user['bookmarks'], ENT_QUOTES | ENT_HTML5) ?></textarea></td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <input class="OkBtn" onmouseover='this.className="OkBtn_";'
                       onmouseout='this.className="OkBtn";'
                       type="submit" align="top" value="<?php echo $this->GetResourceValue('UpdateSettingsButton'); ?>">
                <img src="<?php echo $this->GetConfigValue('root_url'); ?>images/z.gif" width="10" height="1" alt="" border="0">
                <input class="CancelBtn" onmouseover='this.className="CancelBtn_";'
                       onmouseout='this.className="CancelBtn";'
                       type="button" align="top" value="<?php echo $this->GetResourceValue('LogoutButton'); ?>" onclick="document.location='<?php echo $this->href('', '', 'action=logout'); ?>'">
        </tr>
    </table>
    <br>
    <?php
    // echo $this->FormatResourceValue("SeeListOfPages")."<br>";
    print($this->FormClose());
} else {
    // user is not logged in

    echo $this->Action('login', []);
}
?>
<!--/notypo-->
