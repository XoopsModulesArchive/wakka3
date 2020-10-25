<?php

if (0 === $stat) {
    $limit = 1000;
} else {
    $limit = 100;
}
if (!$max || $limit < $max) {
    $max = $limit;
}
$last_users = $this->LoadAll('select name, signuptime from ' . $this->config['user_table'] . ' order by signuptime desc limit ' . (int)$max);
foreach ($last_users as $user) {
    if ('0' !== $stat) {
        $num = $this->LoadSingle('select count(*) as n from ' . $this->config['table_prefix'] . "pages where owner='" . $user['name'] . "'");
    }

    print('(<span class="dt">' . $user['signuptime'] . ')</span> ' . $this->Link('/' . $user['name'], '', $user['name']) . ('0' !== $stat ? ' . . . (' . $num['n'] . ')' : '') . "<br>\n");
}
