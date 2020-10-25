<?php

global $tree_pages_array;
if (!function_exists('create_cluster_tree')) {
    function create_cluster_tree(&$wacko, $supertag, $tag, $depth)
    {
        global $tree_pages_array;

        while (!(false === current($tree_pages_array))) {
            $page_supertag = key($tree_pages_array);

            $page_tag = current($tree_pages_array);

            //Сами себя не рисуем, об этом родитель должен позаботиться

            if ($supertag == $page_supertag) {
                next($tree_pages_array);

                continue;
            }

            if ('/' != $supertag && !(0 === mb_strpos($page_supertag, $supertag . '/'))) {
                //Кончились "Наши" листики.

                break;
            }

            //Считаем supertag подстраницы

            //Относительный

            if ('/' != $supertag) {
                $rel_supertag = mb_substr($page_supertag, mb_strlen($supertag) + 1);
            } else {
                $rel_supertag = mb_substr($page_supertag, 1);
            }

            if (false === !mb_strpos($rel_supertag, '/')) {
                $rel_supertag = mb_substr($rel_supertag, 0, mb_strpos($rel_supertag, '/'));
            }

            //И абсолютный

            if ('/' != $supertag) {
                $sub_supertag = $supertag . '/' . $rel_supertag;
            } else {
                $sub_supertag = '/' . $rel_supertag;
            }

            if ($depth > 0) {
                //Надо посчитать таг для этого супертага

                $sub_tag = '';

                $exists = 0;

                if ($tree_pages_array[$sub_supertag]) {
                    //Такая страница есть, берем ее таг.

                    $sub_tag = $tree_pages_array[$sub_supertag];

                    $exists = 1;
                } else {
                    //Такой страницы нет, есть ее подстраницы. Будем считать вероятный таг.

                    $sub_sub_tag = $page_tag;

                    //Отбираем столько слэшей, сколько есть в супертаге

                    $scount = mb_substr_count($sub_supertag, '/');

                    for ($i = 0; $i < $scount - 1; $i++) {
                        $sub_tag .= mb_substr($sub_sub_tag, 0, mb_strpos($sub_sub_tag, '/') + 1);

                        $sub_sub_tag = mb_substr($sub_sub_tag, mb_strpos($sub_sub_tag, '/') + 1);
                    }

                    //Отбрасываем все после следующего слеша.

                    $sub_tag .= mb_substr($sub_sub_tag, 0, mb_strpos($sub_sub_tag, '/'));
                }

                $sub_pages_tree[$sub_tag]['supertag'] = $sub_supertag;

                $sub_pages_tree[$sub_tag]['exists'] = $exists;
            }

            $sub_tree = create_cluster_tree($wacko, $sub_supertag, '', $depth - 1);

            if ($depth > 0) {
                $sub_pages_tree[$sub_tag]['subtree'] = $sub_tree;
            }
        }

        return $sub_pages_tree;
    }
}
if (!function_exists('test_page_existance')) {
    function test_page_existance($page_array)
    {
        if ($page_array['exists']) {
            return true;
        }

        $sub_tree = $page_array['subtree'];

        if (is_array($sub_tree)) {
            foreach ($sub_tree as $sub_tag => $sub_page_array) {
                if (test_page_existance($sub_page_array)) {
                    return true;
                }
            }
        }

        return false;
    }
}
if (!function_exists('print_cluster_tree')) {
    function print_cluster_tree(&$wacko, $tree, $style, $current_depth, $abc, $filter)
    {
        if (is_array($tree)) {
            ksort($tree, SORT_STRING);

            static $letter = '';

            static $need_letter = 0;

            static $newletter = '!';

            if ('ul' == $style) {
                print '<ul>';
            }

            if ('ol' == $style) {
                print '<ol>';
            }

            if ('indent' == $style) {
                print "<div class='indent'>";
            }

            foreach ($tree as $sub_tag => $sub_tag_array) {
                $sub_supertag = $sub_tag_array['supertag'];

                $sub_exists = $sub_tag_array['exists'];

                $linktext = $sub_tag;

                if ('br' != $style && (false === !mb_strpos($linktext, '/'))) {
                    //Выводим только последнее слово

                    $linktext = mb_substr($linktext, mb_strrpos($linktext, '/') + 1);
                }

                if ($abc && (1 == $current_depth)) {
                    $newletter = mb_strtoupper(mb_substr($linktext, 0, 1));

                    if (!preg_match('/[' . $wacko->language['ALPHA_P'] . ']/', $newletter)) {
                        $newletter = '#';
                    }

                    if ('' == $newletter) {
                        $newletter = $linktext[0];
                    }

                    if ($letter != $newletter) {
                        $need_letter = 1; //Напечатать при первом удобном случае
                    }
                }

                if ($sub_exists || ('br' != $style && ('all' == $filter || test_page_existance($sub_tag_array)))) {
                    if (1 == $need_letter) { //Удобный случай напечатать букву
                        if (('ul' == $style || 'ol' == $style) && $letter) {
                            print '<br>';
                        }

                        if ($letter) {
                            print '<br>';
                        }

                        $letter = $newletter;

                        print '<strong>' . $letter . '</strong><br>';

                        $need_letter = 0;
                    }

                    if ('ul' == $style || 'ol' == $style) {
                        print '<li>';
                    }

                    print($wacko->Link($sub_supertag, '', $linktext) . "\n");

                    if ('indent' == $style || 'br' == $style) {
                        print '<br>';
                    }
                }

                print_cluster_tree($wacko, $sub_tag_array['subtree'], $style, $current_depth + 1, $abc, $filter);
            }

            if ('ul' == $style) {
                print '</ul>';
            }

            if ('ol' == $style) {
                print '</ol>';
            }

            if ('indent' == $style) {
                print '</div>';
            }
        }
    }
}
$root = $vars[0];
if (!isset($root)) {
    $root = '/' . $this->page['tag'];
}
if ('/' == $root) {
    $root = '';
}
if ($root) {
    $root = $this->UnwrapLink($root);
}
if (!$depth) {
    $depth = 0;
}
if (!is_numeric($depth)) {
    $depth = 0;
}
if (0 == $depth) {
    $depth = 2147483647;
}//Что значит неограниченно
if (!$style) {
    $style = 'indent';
}
if (!in_array($style, ['br', 'ul', 'ol', 'indent'], true)) {
    $style = 'indent';
}
if ($root) {
    if (!$nomark) {
        $title = $this->GetResourceValue('Tree:ClusterTitle');

        $title = str_replace('%1', $this->Link('/' . $root, '', $root), $title);

        print('<fieldset><legend>' . $title . ":</legend>\n");
    }

    $query = "'" . quote($this->NpjTranslit($root)) . "/%'";
} else {
    if (!$nomark) {
        print('<fieldset><legend>' . $this->GetResourceValue('Tree:SiteTitle') . "</legend>\n");
    }

    $query = "'%'";
}
$pages = $this->LoadAll(
    'select ' . $this->pages_meta . ' from ' . $this->config['table_prefix'] . 'pages where supertag like ' . $query . ($owner ? " AND owner='" . quote($owner) . "'" : '') . " and comment_on = ''"
);
if ($pages) {
    //Кэшируем страницы и готовим список для кэширования acl

    foreach ($pages as $page) {
        $this->CachePage($page, 1);

        $supertag_list[] = $page['supertag'];
    }

    //Составляем строчку запроса для acl

    for ($i = 0, $iMax = count($supertag_list); $i < $iMax; $i++) {
        $supertag_str .= "'" . quote($supertag_list[$i]) . "', ";
    }

    $supertag_str = mb_substr($supertag_str, 0, -2);

    //Кэшируем права доступа

    if ($read_acls = $this->LoadAll('select * from ' . $this->config['table_prefix'] . 'acls where supertag in (' . $supertag_str . ") and privilege = 'read'")) {
        for ($i = 0, $iMax = count($read_acls); $i < $iMax; $i++) {
            $this->CacheACL($read_acls[$i]['supertag'], 'read', 1, $read_acls[$i]);
        }
    }

    //Собираем массив страниц

    $tree_pages_array = [];

    foreach ($pages as $page) {
        if (!$this->config['hide_locked'] || $this->HasAccess('read', $page['supertag'])) {
            $tree_pages_array['/' . $page['supertag']] = $page['tag'];
        }
    }

    //Сортируем в порядке супертагов

    ksort($tree_pages_array, SORT_STRING);

    $tree = create_cluster_tree($this, '/' . $this->NpjTranslit($root), $root, $depth);

    print_cluster_tree($this, $tree, $style, 1, $abc, $filter);
} else {
    $empty_string = $this->GetResourceValue('Tree:Empty');

    $empty_string = str_replace('%1', $this->Link('/' . $root, '', $root), $empty_string);

    print($empty_string . '<br>');
}
if (!$nomark) {
    echo "</fieldset>\n";
}
