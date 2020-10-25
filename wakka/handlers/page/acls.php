<div class="page">
    <?php
    if ($this->UserIsOwner()) {
        if ($_POST) {
            // store lists

            $this->SaveAcl($this->GetPageTag(), 'read', $_POST['read_acl']);

            $this->SaveAcl($this->GetPageTag(), 'write', $_POST['write_acl']);

            $this->SaveAcl($this->GetPageTag(), 'comment', $_POST['comments_acl']);

            if ('' != $_POST['newowner']) {
                $this->SetPageOwner($this->GetPageTag(), $_POST['newowner']);
            }

            $message = _MI_MODMESSAGE;

            // redirect back to page

            $this->SetMessage($message . '!');

            $this->Redirect($this->Href());
        } else {
            // load acls

            $memberHandler = xoops_getHandler('member');

            $groupobjs = $memberHandler->getGroups();

            foreach ($groupobjs as $group) {
                $groups[$group->getVar('groupid')] = $group->getVar('name');
            }

            $ACL1 = $this->LoadAcl($this->GetPageTag(), 'read');

            $readacls = explode('|', $ACL1['list']);

            foreach ($readacls as $v) {
                if ('' != $v && '*' != $v) {
                    $readACL .= "<option value='" . $v . "'>" . $groups[$v] . "</option>\n";
                }
            }

            $ACL2 = $this->LoadAcl($this->GetPageTag(), 'write');

            $writeacls = explode('|', $ACL2['list']);

            foreach ($writeacls as $v) {
                if ('' != $v && '*' != $v) {
                    $writeACL .= "<option value='" . $v . "'>" . $groups[$v] . "</option>\n";
                }
            }

            $ACL3 = $this->LoadAcl($this->GetPageTag(), 'comment');

            $commentsacls = explode('|', $ACL3['list']);

            foreach ($commentsacls as $v) {
                if ('' != $v && '*' != $v) {
                    $commentsACL .= "<option value='" . $v . "'>" . $groups[$v] . "</option>\n";
                }
            }

            // show form ?>
            <h3><?php echo _MI_ACLSLIST . ' ' . $this->Link($this->GetPageTag()) ?></h3>
            <br>
            <?php echo $this->FormOpen('acls') ?>
            <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="3"><strong><?php echo _MI_READACL; ?>:</strong><br></td>
                </tr>
                <tr>
                    <td valign="top" style="padding-right: 20px">
                        <input type=hidden name='read_acl'>
                        <select name='readacl' size=7 multiple style='width:100px'>
                            <?php echo $readACL; ?>
                        </select>
                    </td>
                    <td>
                        <input type=button value='<?php echo _MI_REMOVE; ?>' onclick='move(this.form.group1,this.form.readacl)'><br><br>
                        <input type=button value='<?php echo _MI_ADD; ?>' onclick='move(this.form.readacl,this.form.group1)'>
                    </td>
                    <td>
                        <select name='group1' size=7 multiple style='width:100px'>
                            <?php
                            foreach ($groups as $groupid => $name) {
                                if ((!is_array($readacls) || !in_array($groupid, $readacls, true)) && XOOPS_GROUP_USERS != $groupid && XOOPS_GROUP_ANONYMOUS != $groupid) {
                                    echo "<option value='" . $groupid . "'>" . $name . "</option>\n";
                                }
                            } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"><strong><?php echo _MI_WRITEACL; ?>:</strong><br></td>
                </tr>
                <tr>
                    <td valign="top" style="padding-right: 20px">
                        <input type=hidden name='write_acl'>
                        <select name='writeacl' size=7 multiple style='width:100px'>
                            <?php echo $writeACL; ?>
                        </select>
                    </td>
                    <td>
                        <input type=button value='<?php echo _MI_REMOVE; ?>' onclick='move(this.form.group2,this.form.writeacl)'><br><br>
                        <input type=button value='<?php echo _MI_ADD; ?>' onclick='move(this.form.writeacl,this.form.group2)'>
                    </td>
                    <td>
                        <select name='group2' size=7 multiple style='width:100px'>
                            <?php
                            foreach ($groups as $groupid => $name) {
                                if ((!is_array($writeacls) || !in_array($groupid, $writeacls, true)) && XOOPS_GROUP_USERS != $groupid && XOOPS_GROUP_ANONYMOUS != $groupid) {
                                    echo "<option value='" . $groupid . "'>" . $name . "</option>\n";
                                }
                            } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"><strong><?php echo _MI_COMMENTSACL; ?>:</strong><br></td>
                </tr>
                <tr>
                    <td valign="top" style="padding-right: 20px">
                        <input type=hidden name='comments_acl'>
                        <select name='commentsacl' size=7 multiple style='width:100px'>
                            <?php echo $commentsACL; ?>
                        </select>
                    </td>
                    <td>
                        <input type=button value='<?php echo _MI_REMOVE; ?>' onclick='move(this.form.group3,this.form.commentsacl)'><br><br>
                        <input type=button value='<?php echo _MI_ADD; ?>' onclick='move(this.form.commentsacl,this.form.group3)'>
                    </td>
                    <td>
                        <select name='group3' size=7 multiple style='width:100px'>
                            <?php
                            foreach ($groups as $groupid => $name) {
                                if ((!is_array($commentsacls) || !in_array($groupid, $commentsacls, true)) && XOOPS_GROUP_USERS != $groupid && XOOPS_GROUP_ANONYMOUS != $groupid) {
                                    echo "<option value='" . $groupid . "'>" . $name . "</option>\n";
                                }
                            } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <HR>
                    </td>
                <tr>
                <tr>
                    <td colspan="1">
                        <strong><?php echo _MI_SETOWNER; ?>:</strong>
                    </td>
                    <td colspan="2">
                        <select name="newowner">
                            <option value=""><?php echo _MI_NOCHANGE; ?></option>
                            <?php
                            if ($users = $this->LoadUsers()) {
                                foreach ($users as $user) {
                                    print('<option value="' . $user['name'] . '">' . $user['name'] . "</option>\n");
                                }
                            } ?>
                        </select>
                    <td>
                </tr>
                <tr>
                    <td colspan="3">
                        <br>
                        <input type="button" value="<?php echo _MI_STOREACLS; ?>" style="width: 120px" accesskey="s" onclick="commit(this.form.readacl,this.form.read_acl);commit(this.form.writeacl,this.form.write_acl);commit(this.form.commentsacl,this.form.comments_acl);this.form.submit();">
                        <input type="button" value="<?php echo _CANCEL; ?>" onClick="history.back();" style="width: 120px">
                    </td>
                </tr>
            </table>
            <?php
            print($this->FormClose());
        }
    } else {
        print("<em>You're not the owner of this page.</em>");
    }
    ?>
</div>
