<?php

if (!$options['align']) {
    $options['align'] = 'right';
}
if (!$options['width']) {
    $options['width'] = '250';
}
echo '<div class="action" style="float:' . $options['align'] . '; width:' . $options['width'] . 'px">';
echo '<div class="action-content">';
echo $text;
echo '</div></div>';
