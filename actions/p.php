<?php

/*
{{P style="BEFORE|after|left|right" // не реализованы табличные "left/right"
// стили ищи в /wakka.php
name="absolute|toc-relative|DOCUMENT-RELATIVE" // не реализовано пока "toc-relative"
}}
*/
$page = '';
$ppage = '';
$context = $this->tag;
$_page = $this->page;
$link = '';
if (!$name) {
    $name = 'document-relative';
}
if (!$style) {
    $style = 'before';
}
// отображени¤ здесь нет, только предобработка
{
    if ($this->post_wacko_toc) {
        $toc = &$this->post_wacko_toc;
    } else {
        $toc = $this->BuildToc($context, $start_depth, $end_depth, $numerate, $link);
    }
    { // ---------------------- p numeration ------------------------
        // вы¤сн¤ем, какие номера где сто¤т
        $toc_len = count($toc);
        $numbers = [];
        $depth = 0;
        $pnum = 0;
        for ($i = 0; $i < $toc_len; $i++) {
            if ($toc[$i][2] > 66666) { // нормировали глубину погружени¤
                $pnum++;

                if ('document-relative' == $name) {
                    $num = $pnum;
                } else {
                    $num = str_replace(
                        '-',
                        '&#0150;&sect;',
                        str_replace('p', 'є', $toc[$i][0])
                    );
                }

                // правим содержимое TOC @66

                $toc[$i][66] = $num;
            }
        }
        // неплохо б в кэш записать подобновлЄнную версию
        $this->tocs[$context] = &$toc;
        // теперь надо поставить флажок о том, что неплохо бы искурочить в пост-ваке
        // исходник странички, добавив туда цыфирки
        $this->post_wacko_toc = &$toc;
        $this->post_wacko_action['p'] = $style;
        $this->post_wacko_maxp = $pnum;
    } // --------------------------------------------------------------
}
