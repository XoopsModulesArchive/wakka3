<?php

class Pindex
{
    public $_INDEX = [
        'A' => 0xb0c4,
'B' => 0xb2c0,
'C' => 0xb4ed,
'D' => 0xb6e9,
'E' => 0xb7a1,
'F' => 0xb8c0,
'G' => 0xb9fd,
'H' => 0xbbf6,
'J' => 0xbfa5,
'K' => 0xc0ab,
'L' => 0xc2e7,
'M' => 0xc4c2,
'N' => 0xc5b5,
'O' => 0xc5bd,
'P' => 0xc6d9,
'Q' => 0xc8ba,
'R' => 0xc8f5,
'S' => 0xcbf9,
'T' => 0xcdd9,
'W' => 0xcef3,
'X' => 0xd1b8,
'Y' => 0xd4d0,
'Z' => 0xd7fA,
    ];

    public $_INDEXSTART = 0xb0a0;

    public $_INDEXEND = 0xd7fA;

    public function __construct()
    {
    }

    public function make_index($cn)
    {
        $value = mb_strtoupper($cn[0]);

        $pindex = ord($value);

        if ($pindex > 64 && $pindex < 91) {
            $index = $value;
        } elseif ($pindex > 175) {
            $pindex = hexdec(str_replace('%', '', urlencode(mb_substr($cn, 0, 2))));

            if ($pindex > $this->_INDEXSTART && $pindex < $this->_INDEXEND) {
                $limit = $this->_INDEXSTART;

                foreach ($this->_INDEX as $key => $value) {
                    if ($pindex > $limit && $pindex <= $value) {
                        $index = $key;
                    }

                    $limit = $value;
                }
            } else {
                $index = '#';
            }
        } else {
            $index = '#';
        }

        return $index;
    }
}
