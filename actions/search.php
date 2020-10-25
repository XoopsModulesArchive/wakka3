<?php

if ((1 == $topic) || (1 == $title)) {
    $mode = 'topic';
} else {
    $mode = 'full';
}
if ('on' == $_REQUEST['topic']) {
    $mode = 'topic';
}
//if (!$delim) $delim="---";
if (!in_array($style, ['br', 'ul', 'ol', 'comma'], true)) {
    $style = 'ol';
}
$i = 0;
if ('pages' != $filter) {
    $filter = 'all';
}
if ('' != $vars[0]) {
    $phrase = $vars[0];
} else {
    $phrase = '';

    $form = 1;
}
if ($form) {
    echo $this->FormOpen('', '', 'GET') ?>
    <table border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td><?php echo $this->GetResourceValue('SearchFor'); ?>:&nbsp;</td>
            <td><input name="phrase" size="40" value="<?php echo htmlspecialchars($_REQUEST['phrase'], ENT_QUOTES | ENT_HTML5) ?>">
                <input type="submit" value="<?php echo $this->GetResourceValue('SearchButtonText'); ?>"></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><input type="checkbox" name="topic" <?php if ('topic' == $mode) {
        echo 'CHECKED';
    } ?> id="checkboxSearch">
                <label for="checkboxSearch"><?php echo $this->GetResourceValue('TopicSearchText'); ?></label>
        </tr>
    </table>
    <?php
    echo $this->FormClose();
}
if ('' == $phrase) {
    $phrase = $_REQUEST['phrase'];
}
if ($phrase) {
    if ($form) {
        print '<br>';
    }

    if (mb_strlen($phrase) > 2) {
        if ('topic' == $mode) {
            $results = $this->TagSearch($phrase);
        } else {
            $results = $this->FullTextSearch($phrase, ('all' == $filter ? 0 : 1));
        }

        $phrase = htmlspecialchars($phrase, ENT_QUOTES | ENT_HTML5);

        if ($results) {
            if (!$nomark) {
                print('<fieldset><legend><strong>' . $this->GetResourceValue(('topic' == $mode ? 'Topic' : '') . 'SearchResults') . " \"$phrase\":</strong></legend>");
            }

            if ('ul' == $style) {
                print "<ul class=\"SearchResults\">\n";
            }

            if ('ol' == $style) {
                print "<ol class=\"SearchResults\">\n";
            }

            foreach ($results as $page) {
                if (!$this->config['hide_locked'] || $this->HasAccess('read', $page['tag'])) {
                    if ('ul' == $style || 'ol' == $style) {
                        print '<li>';
                    }

                    if ('comma' == $style && $i > 0) {
                        print ",\n";
                    }

                    print($this->Link('/' . $page['tag'], '', $page['tag']));

                    if ('br' == $style) {
                        print "<br>\n";
                    }

                    if ('ul' == $style || 'ol' == $style) {
                        print "</li>\n";
                    }

                    $i++;
                }
            }

            if ('ul' == $style) {
                print '</ul>';
            }

            if ('ol' == $style) {
                print '</ol>';
            }

            if (!$nomark) {
                print('</fieldset>');
            }
        } elseif (!$nomark) {
            echo $this->GetResourceValue('NoResultsFor') . "\"$phrase\".";
        }
    } elseif (!$nomark) {
        echo $this->GetResourceValue('NoResultsFor') . "\"$phrase\".";
    }
}
?>
