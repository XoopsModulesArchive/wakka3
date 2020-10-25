<?php
echo $this->FormOpen('', 'TextSearch', 'get'); ?>
<div class="footer">
    <?php
    echo $this->HasAccess('write') ? '<a href="' . $this->href('edit') . '" title="Click to edit this page.">' . _MI_EDIT . "</a> ::\n" : '';
    echo $this->GetPageTime() ? '<a href="' . $this->href('revisions') . '" title="Click to view recent page revisions.">' . $this->GetPageTime() . '</a> <a href="' . $this->href('revisions.xml') . '" title="Click to view recent page revisions in XML format."><img src="' . $this->href(
        '',
        'xml/xml.gif'
    ) . "\" width=\"36\" height=\"14\" align=\"middle\" style=\"border : 0px;\" alt=\"XML\"></a> ::\n" : '';
    // if this page exists
    if ($this->page) {
        // if owner is current user

        if ($this->UserIsOwner()) {
            print(_MI_ISOWNER . ' :: <a href="' . $this->href('acls') . '">' . _MI_EDITACLS . '</a> ::');
        } else {
            $owner = $this->GetPageOwner();

            if ($this->xoopsConfig['isadmin'] && $owner) {
                print(_MI_OWNER . ': ' . $this->Format($owner) . ': (<a href="' . $this->href('claim') . '">' . _MI_OWNERSHIP . '</a>)');
            } elseif ($owner) {
                print(_MI_OWNER . ': ' . $this->Format($owner));
            } else {
                print(_MI_NOBODY . ($this->GetUser() ? ' (<a href="' . $this->href('claim') . '">' . _MI_OWNERSHIP . '</a>)' : ''));
            }

            print(' :: ');
        }
    }
    ?>
    <a href="<?php echo $this->href('referrers') ?>" title="Click to view a list of URLs referring to this page."><?php echo _MI_REFERRERS; ?></a> ::
    <?php echo _MI_SEARCH; ?>: <input name="phrase" size="15" style="border: none; border-bottom: 1px solid #CCCCAA; padding: 0px; margin: 0px;">
</div>
<?php echo $this->FormClose(); ?>
<div class="copyright">
    <?php echo $this->Link('http://validator.w3.org/check/referer', '', 'Valid XHTML 1.0 Transitional') ?> :: <?php echo $this->Link('http://jigsaw.w3.org/css-validator/check/referer', '', 'Valid CSS') ?> :: Turbinado pelo <?php echo $this->Link(
        'WakkaWiki:WakkaWiki',
        '',
        'Wakka ' . $this->GetWakkaVersion()
    ) ?>
</div>
<?php
if ($this->GetConfigValue('debug')) {
        print("<span style=\"font-size: 11px; color: #888888\"><strong>Query log:</strong><br>\n");

        foreach ($this->queryLog as $query) {
            print($query['query'] . ' (' . $query['time'] . ")<br>\n");
        }

        print('</span>');
    }
?>
</body>
</html>
