<?php

require_once dirname(__DIR__, 3) . '/include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/modules/wakka/include/config.inc.php';
if (isset($_GET['op'])) {
    $op = $_GET['op'];
}
switch ($op) {
    case 'save':
        save();
        break;
    default:
        view();
        break;
}
function save()
{
    global $xoopsDB, $_POST;

    if (isset($_POST)) {
        foreach ($_POST as $k => $v) {
            ${$k} = $v;
        }
    }

    $config = '<' . "?php\n\n";

    $config .= "\$moduleConfig['commentspem'] = array(";

    foreach (explode('|', $commentspem) as $v) {
        $config .= '"' . $v . '",';
    }

    $config = mb_substr($config, 0, -1) . ");\n\n";

    $config .= "\$moduleConfig['createpem'] = array(";

    foreach (explode('|', $createpem) as $v) {
        $config .= '"' . $v . '",';
    }

    $config = mb_substr($config, 0, -1) . ");\n\n";

    $config .= "\$moduleConfig['writepem'] = array(";

    foreach (explode('|', $writepem) as $v) {
        $config .= '"' . $v . '",';
    }

    $config = mb_substr($config, 0, -1) . ");\n\n?>";

    $file = fopen(XOOPS_ROOT_PATH . '/modules/wakka/include/config.inc.php', 'wb');

    fwrite($file, $config);

    fclose($file);

    redirect_header('index.php', 1, _TAKINGBACK);
}

function view()
{
    global $moduleConfig;

    xoops_cp_header();

    echo '<b>' . _MI_PERM . '</b>' . "<table class=outer cellpadding=4 cellspacing=1 width=80%><form action='index.php' method='post'>" . '<input type=hidden name=op value=save><input type=hidden name=createpem><input type=hidden name=writepem><input type=hidden name=commentspem>';

    echo "<tr><td class='head' valign=top>" . _MI_CREATEACL . '</td><td class=odd><table><tr><td>' . "<tr ><td><select name='create' size=7 multiple style='width:100px'>";

    $admins = $moduleConfig['createpem'];

    $groupHandler = xoops_getHandler('group');

    foreach ($admins as $admin) {
        if ('' != $admin) {
            $group = $groupHandler->get($admin);

            echo "<option value='" . $admin . "'>" . $group->getVar('name') . "</option>\n";
        }
    }

    echo '</select></td><td>'
         . "<input type=button value='"
         . _MI_REMOVE
         . "' onclick='move(this.form.group0,this.form.create)'><br><br>"
         . "<input type=button value='"
         . _MI_ADD
         . "' onclick='move(this.form.create,this.form.group0)'></td>"
         . "<td><select name='group0' size=7 multiple style='width:100px'>\n";

    $memberHandler = xoops_getHandler('member');

    $groups = $memberHandler->getGroups();

    foreach ($groups as $group) {
        $groupid = $group->getVar('groupid');

        if (!in_array($groupid, $admins, true)) {
            echo "<option value='" . $groupid . "'>" . $group->getVar('name') . "</option>\n";
        }
    }

    echo '</select></td></tr></table></td></tr>';

    echo "<tr><td class='head' valign=top>" . _MI_WRITEACL . '</td><td class=odd><table><tr><td>' . "<tr ><td><select name='write' size=7 multiple style='width:100px'>";

    $admins = $moduleConfig['writepem'];

    $groupHandler = xoops_getHandler('group');

    foreach ($admins as $admin) {
        if ('' != $admin) {
            $group = $groupHandler->get($admin);

            echo "<option value='" . $admin . "'>" . $group->getVar('name') . "</option>\n";
        }
    }

    echo '</select></td><td>'
         . "<input type=button value='"
         . _MI_REMOVE
         . "' onclick='move(this.form.group,this.form.write)'><br><br>"
         . "<input type=button value='"
         . _MI_ADD
         . "' onclick='move(this.form.write,this.form.group)'></td>"
         . "<td><select name='group' size=7 multiple style='width:100px'>\n";

    $memberHandler = xoops_getHandler('member');

    $groups = $memberHandler->getGroups();

    foreach ($groups as $group) {
        $groupid = $group->getVar('groupid');

        if (!in_array($groupid, $admins, true)) {
            echo "<option value='" . $groupid . "'>" . $group->getVar('name') . "</option>\n";
        }
    }

    echo '</select></td></tr></table></td></tr>';

    echo "<tr><td class='head' valign=top>" . _MI_COMMENTSACL . '</td><td class=odd><table><tr><td>' . "<tr ><td><select name='comments' size=7 multiple style='width:100px'>";

    $admins = $moduleConfig['commentspem'];

    $groupHandler = xoops_getHandler('group');

    foreach ($admins as $admin) {
        if ('' != $admin) {
            $group = $groupHandler->get($admin);

            echo "<option value='" . $admin . "'>" . $group->getVar('name') . "</option>\n";
        }
    }

    echo '</select></td><td>'
         . "<input type=button value='"
         . _MI_REMOVE
         . "' onclick='move(this.form.member,this.form.comments)'><br><br>"
         . "<input type=button value='"
         . _MI_ADD
         . "' onclick='move(this.form.comments,this.form.member)'></td>"
         . "<td><select name='member' size=7 multiple style='width:100px'>\n";

    $memberHandler = xoops_getHandler('member');

    $groups = $memberHandler->getGroups();

    foreach ($groups as $group) {
        $groupid = $group->getVar('groupid');

        if (!in_array($groupid, $admins, true)) {
            echo "<option value='" . $groupid . "'>" . $group->getVar('name') . "</option>\n";
        }
    }

    echo '</select></td></tr></table></td></tr>';

    echo javascript()
         . "<tr class='foot'><td colspan=2><input type='submit' value='"
         . _SUBMIT
         . "' onclick='commit(this.form.write,this.form.writepem);commit(this.form.create,this.form.createpem);commit(this.form.comments,this.form.commentspem);this.form.submit();'> <input type=button value='"
         . _CANCEL
         . "' onclick='javascript:history.go(-1);'></td></tr>"
         . '</form></table>';

    xoops_cp_footer();
}

function javascript()
{
    $string = "<script>\n"
              . 'function move(add,del){'
              . ' var key=new Array;'
              . ' var val=new Array;'
              . ' if (del.options.selectedIndex==-1)'
              . ' del.options.selectedIndex=del.options.length-1;'
              . " if(del.options.length && del.options[del.options.selectedIndex].value!=''){"
              . ' add.options.length=add.options.length+1;'
              . ' add.options[add.options.length-1].value=del.options[del.options.selectedIndex].value;'
              . ' add.options[add.options.length-1].text=del.options[del.options.selectedIndex].text;'
              . ' counter=0;'
              . ' for (var i=0;i<del.options.length;i++){'
              . ' if (!del.options[i].selected){'
              . ' key[counter] = del.options[i].text;'
              . ' val[counter] = del.options[i].value;'
              . ' counter++;'
              . ' }'
              . ' }'
              . ' del.options.length = del.options.length -1;'
              . ' for (i in key){'
              . ' del.options[i].text = key[i];'
              . ' del.options[i].value = val[i];'
              . ' }'
              . ' }'
              . '}'
              . "function commit(admins,admin){\n"
              . "var value='';\n"
              . "for (var i=0;i<admins.options.length;i++){\n"
              . ' if (i==admins.options.length-1)'
              . " admin.value=admin.value+admins.options[i].value;\n"
              . ' else'
              . " admin.value=admin.value+admins.options[i].value+'|';\n"
              . "}\n"
              . "}\n</script>\n";

    return $string;
}
