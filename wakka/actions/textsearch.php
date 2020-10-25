<?php
echo $this->FormOpen('', '', 'GET') ?>
    <table border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td><?php echo _SEARCH; ?>&nbsp;:</td>
            <td><input name="phrase" size="40" value="<?php echo htmlspecialchars($_REQUEST['phrase'], ENT_QUOTES | ENT_HTML5) ?>"> <input type="submit" value="<?php echo _SEARCH; ?>"></td>
        </tr>
    </table>
<?php echo $this->FormClose(); ?>
<?php
if ($phrase = $_REQUEST['phrase']) {
    print('<br>');

    if ($results = $this->FullTextSearch($phrase)) {
        print("<strong>Search results for \"$phrase\":</strong><br><br>\n");

        foreach ($results as $i => $page) {
            print(($i + 1) . '. ' . $this->Link($page['tag']) . "<br>\n");
        }
    } else {
        print("No results for \"$phrase\". :-(");
    }
}
?>
