<?php

/*
SAFEHTML Parser.
v1.1.0.
22 May 2004.
http://www.npj.ru/kukutz/safehtml/
Copyright (c) 2004, Roman Ivanov.
All rights reserved.
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
notice, this list of conditions and the following disclaimer in the
documentation and/or other materials provided with the distribution.
3. The name of the author may not be used to endorse or promote products
derived from this software without specific prior written permission.
THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

class safehtml
{
    public $xhtml = '';

    public $Counter;

    public $Stack = [];

    public $dcStack = [];

    public $Protopreg = [];

    public $csspreg = [];

    public $dcCounter;

    // single tags ("<tag>")

    public $Singles = ['br', 'area', 'hr', 'img', 'input', 'wbr'];

    // dangerous tags

    public $Deletes = ['base', 'basefont', 'head', 'html', 'body', 'applet', 'object', 'iframe', 'frame', 'frameset', 'script', 'layer', 'ilayer', 'embed', 'bgsound', 'link', 'meta', 'style', 'title', 'blink', 'plaintext'];

    // all content inside this tags will be also removed

    public $DeleteContent = ['script', 'style', 'title', 'xml'];

    // dangerous protocols

    public $BlackProtocols = ['javascript', 'vbscript', 'about', 'wysiwyg', 'data', 'view-source', 'ms-its', 'mhtml', 'shell', 'lynxexec', 'lynxcgi', 'hcp', 'ms-help', 'help', 'disk', 'vnd.ms.radio', 'opera', 'res', 'resource', 'chrome', 'mocha', 'livescript'];

    // pass only these protocols

    public $WhiteProtocols = ['http', 'https', 'ftp', 'telnet', 'news', 'nntp', 'gopher', 'mailto', 'file'];

    // white or black-listing of protocols?
    public $ProtocolFiltering = 'white'; //or "black"
    // attributes that can contains protocols
    public $ProtocolAttributes = ['src', 'href', 'action', 'lowsrc', 'dynsrc', 'background', 'codebase'];

    // dangerous CSS keywords

    public $CSS = ['absolute', 'fixed', 'expression', 'moz-binding', 'content', 'behavior', 'include-source'];

    // tags that can have no "closing tag"

    public $noClose = ['p', 'li'];

    // dangerous attributes

    public $Attributes = ['dynsrc'];

    // constructor

    public function __construct()
    {
        //making regular expressions based on Proto & CSS arrays

        foreach ($this->BlackProtocols as $proto) {
            $preg = "/[\s\x01-\x1F]*";

            for ($i = 0, $iMax = mb_strlen($proto); $i < $iMax; $i++) {
                $preg .= $proto[$i] . "[\s\x01-\x1F]*";
            }

            $preg .= ':/i';

            $this->Protopreg[] = $preg;
        }

        foreach ($this->CSS as $css) {
            $this->csspreg[] = '/' . $css . '/i';
        }
    }

    // Handles the writing of attributes - called from $this->openHandler()

    public function writeAttrs($attrs)
    {
        if (is_array($attrs)) {
            foreach ($attrs as $name => $value) {
                $name = mb_strtolower($name);

                if (0 === mb_strpos($name, 'on')) {
                    continue;
                }

                if (0 === mb_strpos($name, 'data')) {
                    continue;
                }

                if (in_array($name, $this->Attributes, true)) {
                    continue;
                }

                if (!preg_match('/^[a-z0-9]+$/i', $name)) {
                    continue;
                }

                if (true === $value || null === $value) {
                    $value = $name;
                }

                if ('style' == $name) {
                    $value = str_replace('\\', '', $value);

                    $value = str_replace('&amp;', '&', $value);

                    $value = str_replace('&', '&amp;', $value);

                    foreach ($this->csspreg as $css) {
                        if (preg_match($css, $value)) {
                            continue 2;
                        }
                    }

                    foreach ($this->Protopreg as $proto) {
                        if (preg_match($proto, $value)) {
                            continue 2;
                        }
                    }
                }

                $tempval = preg_replace('/&#(\d+);/me', "chr('\\1')", $value); //"'

                if (in_array($name, $this->ProtocolAttributes, true) && false !== mb_strpos($tempval, ':')) {
                    if ('black' == $this->ProtocolFiltering) {
                        foreach ($this->Protopreg as $proto) {
                            if (preg_match($proto, $tempval)) {
                                continue 2;
                            }
                        }
                    } else {
                        $_tempval = explode(':', $tempval);

                        $proto = $_tempval[0];

                        if (!in_array($proto, $this->WhiteProtocols, true)) {
                            continue;
                        }
                    }
                }

                if (false !== mb_strpos($value, '"')) {
                    $q = "'";
                } else {
                    $q = '"';
                }

                $this->xhtml .= ' ' . $name . '=' . $q . $value . $q;
            }
        }
    }

    // Opening tag handler

    public function openHandler(&$parser, $name, $attrs)
    {
        $name = mb_strtolower($name);

        if (in_array($name, $this->DeleteContent, true)) {
            $this->dcStack[] = $name;

            $this->dcCounter[$name]++;
        }

        if (0 != count($this->dcStack)) {
            return true;
        }

        if (in_array($name, $this->Deletes, true)) {
            return true;
        }

        if (!preg_match('/^[a-z0-9]+$/i', $name)) {
            if (preg_match("!(?:\@|://)!i", $name)) {
                $this->xhtml .= '&lt;' . $name . '&gt;';
            }

            return true;
        }

        if (in_array($name, $this->Singles, true)) {
            $this->xhtml .= '<' . $name;

            $this->writeAttrs($attrs);

            $this->xhtml .= '>';

            return true;
        }

        $this->xhtml .= '<' . $name;

        $this->writeAttrs($attrs);

        $this->xhtml .= '>';

        $this->Stack[] = $name;

        $this->Counter[$name]++;
    }

    // Closing tag handler

    public function closeHandler(&$parser, $name)
    {
        $name = mb_strtolower($name);

        if ($this->dcCounter[$name] > 0 && in_array($name, $this->DeleteContent, true)) {
            while ($name != ($tag = array_pop($this->dcStack))) {
                $this->dcCounter[$tag]--;
            }

            $this->dcCounter[$name]--;
        }

        if (0 != count($this->dcStack)) {
            return true;
        }

        if ($this->Counter[$name] > 0) {
            while ($name != ($tag = array_pop($this->Stack))) {
                if (!in_array($tag, $this->noClose, true)) {
                    $this->xhtml .= '</' . $tag . '>';
                }

                $this->Counter[$tag]--;
            }

            $this->xhtml .= '</' . $name . '>';

            $this->Counter[$name]--;
        }
    }

    // Character data handler

    public function dataHandler(&$parser, $data)
    {
        if (0 == count($this->dcStack)) {
            $this->xhtml .= $data;
        }
    }

    // Escape handler

    public function escapeHandler(&$parser, $data)
    {
    }

    // Return the XHTML document

    public function getXHTML()
    {
        while ($tag = array_pop($this->Stack)) {
            if (!in_array($tag, $this->noClose, true)) {
                $this->xhtml .= '</' . $tag . ">\n";
            }
        }

        return $this->xhtml;
    }

    public function clear()
    {
        $this->xhtml = '';
    }
}
