<?php

class paragrafica
{
    // абзац/параграф это такая хрень: <t->text, text, fucking text<-t>

    public $wacko;

    public $t0 = [ // терминаторы вида <-t>$1<t->
                   "/(<br[^>]*>)(\s*<br[^>]*>)+/si",
                   '/(<hr[^>]*>)/si',
    ];

    public $t1 = [ // терминаторы вида <-t>$1
                   [ // rightinators
                     '!(<table)!si',
                     '!(<a[^>]*></a><h[1-9]>)!si',
                     '!(<(u|o)l)!si',
                     '!(<div)!si',
                     '!(<p)!si',
                     '!(<form)!si',
                     '!(<textarea)!si',
                     '!(<blockquote)!si',
                     //"/<!--notypo-->/si", // ??? obvious
                   ],
                   [ // wronginators
                     '!(</td>)!si',
                     '!(</li>)!si',
                   ],
    ];

    public $t2 = [ // терминаторы вида $1<t->
                   [ // rightinators
                     '!(</table>)!si',
                     '!(</h[1-9]>)!si',
                     '!(</(u|o)l>)!si',
                     '!(</div>)!si',
                     '!(</p>)!si',
                     '!(</form>)!si',
                     '!(</textarea>)!si',
                     '!(</blockquote>)!si',
                     //"/<!--\/notypo-->/si", // ??? obvious
                   ],
                   [ // wronginators
                     '!(<td[^>]*>)!si',
                     '!(<li>)!is',
                   ],
    ];

    public $mark1 = "\200"; // <-t>
    public $mark2 = "\201"; // <t->
    public $mark3 = "\199"; // (*) wronginator mark: в конструкциях вида <t->(*).....<-t> & vice versa -- параграфы не ставятся
    /*
    var $mark1 = "<-t>"; // <-t>
    var $mark2 = "<=t>"; // <t->
    */

    public $prefix1 = '<p class="auto" id="p';

    public $prefix2 = '">';

    public $postfix = '</p>';

    // var $prefix = '<+t>';

    // var $postfix = '<-t>';

    public function __construct(&$wacko)
    {
        $this->wacko = &$wacko;
    }

    public function correct($what)
    {
        // 1. insert terminators appropriately

        foreach ($this->t0 as $t) {
            $what = preg_replace($t, $this->mark1 . '$1' . $this->mark2, $what);
        }

        foreach ($this->t1[0] as $t) {
            $what = preg_replace($t, $this->mark1 . '$1', $what);
        }

        foreach ($this->t2[0] as $t) {
            $what = preg_replace($t, '$1' . $this->mark2, $what);
        }

        foreach ($this->t1[1] as $t) {
            $what = preg_replace($t, $this->mark3 . $this->mark1 . '$1', $what);
        }

        foreach ($this->t2[1] as $t) {
            $what = preg_replace($t, '$1' . $this->mark2 . $this->mark3, $what);
        }

        $what = $this->mark2 . $what . $this->mark1;

        // 2bis. swap <t-><br> -> <br><t->

        $what = preg_replace('!(' . $this->mark2 . ")((\s*<br[^>]*>)+)!si", '$2$1', $what);

        //noneedin:

        $what = preg_replace("!((<br[^>]*>\s*)+)(" . $this->mark1 . ')!s', '$3', $what);

        // 2. cleanup <t->\s<-t>

        do {
            $_w = $what;

            $what = preg_replace('!(' . $this->mark2 . ")((\s|(<br[^>]*>))*)(" . $this->mark1 . ')!si', '$2', $what);
        } while ($_w != $what);

        // 3. replace each <t->....<-t> to <p class="auto">....</p>

        $pcount = 0;

        $pieces = explode($this->mark2, $what);

        foreach ($pieces as $k => $v) {
            if ($k > 0) {
                $pos = mb_strpos($v, $this->mark1);

                $pos2 = mb_strpos($v, $this->mark3);

                if (false !== $pos) {
                    if (false === $pos2) {
                        $pcount++;

                        $pieces[$k] = '<a name="p' . $this->wacko->page['id'] . '-' . $pcount . '"></a>' . $this->prefix1 . $this->wacko->page['id'] . '-' . $pcount . $this->prefix2 . // '<poloskuns1-'.$this->wacko->page["id"].'-'.$pcount.'>'.
                                      mb_substr($v, 0, $pos) . // '<poloskuns2-'.$this->wacko->page["id"].'-'.$pcount.'>'.
                                      $this->postfix . mb_substr($v, $pos + 1);
                    }
                }
            }
        }

        $what = implode('', $pieces);

        // 4. remove unused <t-> & <-t>

        $what = str_replace($this->mark1, '', $what);

        $what = str_replace($this->mark2, '', $what);

        $what = str_replace($this->mark3, '', $what);

        // -. done with P

        // ==================================================================

        // Forming body_toc

        // * in wacko formatter we have done "#h1249_1"

        // * right here we have done "#p1249_1"

        // 1. get all ^^ of this

        $this->toc = [];

        $what = preg_replace_callback(
            '!' . '(<a name="(h[0-9]+-[0-9]+)"></a><h([0-9])>(.*?)</h\\3>)' . // 2=id, 3=depth, 4=name
            '|' . '(<a name="(p[0-9]+-[0-9]+)"></a>)' . // 6=id
            '|' . "\xA1\xA1include\s+[^=]+=([^\xA1 ]+)(\s+notoc=\"?[^0]\")?.*?\xA1\xA1" . // {{include xxxx="TAG" notoc="1"}}
            '!si',
            [&$this, 'add_toc_entry'],
            $what
        );

        return $what;
    }

    public function add_toc_entry($matches)
    {
        if ('' != $matches[7]) {
            if ('' == $matches[8]) {
                $this->toc[] = [$this->wacko->UnwrapLink(trim($matches[7], '"')), '(include)', 99999];
            }
        } elseif ('' != $matches[6]) {
            $this->toc[] = [$matches[6], '(p)', 77777];
        } else {
            $this->toc[] = [$matches[2], $matches[4], $matches[3]];
        }

        return $matches[0];
    }
}
