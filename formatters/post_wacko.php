<?php

//??? много простору для оптимизации :-)
$this->UseClass('post_wacko', 'formatters/classes/');
$parser = new post_wacko($this);
$text = preg_replace_callback(
    "/(\ў\ў(\S+?)([^\n]*?)==([^\n]*?)\Ї\Ї|\Ў\Ў[^\n]+?\Ў\Ў)/sm",
    [&$parser, 'postcallback'],
    $text
);
if ($options['stripnotypo']) {
    $text = str_replace('<!--notypo-->', '', $text);

    $text = str_replace('<!--/notypo-->', '', $text);
}
print($text);
