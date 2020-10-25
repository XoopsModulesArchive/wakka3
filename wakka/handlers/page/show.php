<div class="page">
    <?php
    if ($this->HasAccess('read')) {
        if (!$this->page) {
            print("This page doesn't exist yet. Maybe you want to <a href=\"" . $this->href('edit') . '">create</a> it?');
        } else {
            // comment header?

            if ($this->page['comment_on']) {
                print('<div class="commentinfo">This is a comment on ' . $this->Link($this->page['comment_on'], '', '', 0) . ', posted by ' . $this->Format($this->page['user']) . ' at ' . $this->page['time'] . '</div>');
            }

            if ('N' == $this->page['latest']) {
                print('<div class="revisioninfo">This is an old revision of <a href="' . $this->href() . '">' . $this->GetPageTag() . '</a> from ' . $this->page['time'] . '.</div>');
            }

            // display page

            print($this->Format($this->page['body'], 'wakka'));

            // if this is an old revision, display some buttons

            if ($this->HasAccess('write') && ('N' == $this->page['latest'])) {
                $latest = $this->LoadPage($this->tag); ?>
                <br>
                <?php echo $this->FormOpen('edit') ?>
                <input type="hidden" name="previous" value="<?php echo $latest['id'] ?>">
                <input type="hidden" name="body" value="<?php echo htmlspecialchars($this->page['body'], ENT_QUOTES | ENT_HTML5) ?>">
                <input type="submit" value="Re-edit this old revision">
                <?php echo $this->FormClose(); ?>
                <?php
            }
        }
    } else {
        print("<em>You aren't allowed to read this page.</em>");
    }
    ?>
</div>
<?php
if ($this->HasAccess('read') && 1 != $this->GetConfigValue('hide_comments')) {
        // load comments for this page

        $comments = $this->LoadComments($this->tag);

        // store comments display in session

        $tag = $this->GetPageTag();

        if (!isset($_SESSION['show_comments'][$tag])) {
            $_SESSION['show_comments'][$tag] = ($this->UserWantsComments() ? '1' : '0');
        }

        switch ($_REQUEST['show_comments']) {
        case '0':
            $_SESSION['show_comments'][$tag] = 0;
            break;
        case '1':
            $_SESSION['show_comments'][$tag] = 1;
            break;
    }

        // display comments!

        if ($this->page && $_SESSION['show_comments'][$tag]) {
            // display comments header
        ?>
        <a name="comments"></a>
        <div class="commentsheader">
            <?php echo _MI_COMMENT; ?> [<a href="<?php echo $this->href('', '', 'show_comments=0') ?>"><?php echo _MI_HIDECOMMENT; ?></a>]
        </div>
        <?php
        // display comments themselves
        if ($comments) {
            foreach ($comments as $comment) {
                print('<a name="' . $comment['tag'] . "\"></a>\n");

                print("<div class=\"comment\">\n");

                print($this->Format($comment['body']) . "\n");

                print("<div class=\"commentinfo\">\n-- " . $this->Format($comment['user']) . ' (' . $comment['time'] . ")\n</div>\n");

                print("</div>\n");
            }
        }

            // display comment form

            print("<div class=\"commentform\">\n");

            if ($this->HasAccess('comment')) {
                ?>
            <?php echo _MI_ADDCOMMENT; ?>:<br>
            <?php echo $this->FormOpen('addcomment'); ?>
            <textarea name="body" rows="6" style="width: 96%"></textarea><br>
            <input type="submit" value="<?php echo _MI_SUBMITCOMMENT; ?>" accesskey="s">
            <?php echo $this->FormClose(); ?>
            <?php
            }

            print("</div>\n");
        } else {
            ?>
        <div class="commentsheader">
            <?php
            switch (count($comments)) {
                case 0:
                    print(_MI_NOCOMMENT);
                    break;
                case 1:
                    print(_MI_ONECOMMENT);
                    break;
                default:
                    sprintf(_MI_MORECOMMENT, count($comments));
            } ?>
            [<a href="<?php echo $this->href('', '', 'show_comments=1#comments') ?>"><?php echo _MI_DISPLAYCOMMENT; ?></a>]
        </div>
        <?php
        }
    }
?>
