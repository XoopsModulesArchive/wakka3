<?php

/**
 * Java Syntax Highlighting
 * *****************************
 * Port to java highlighting (c) Mark Hissink Muller, 2004
 * *****************************
 * D'apres le code originale de FEREY Damien et Dark Skull Software
 * publie sur http://www.phpcs.com/article.aspx?Val=649
 * Modifie par Eric Feldstein (mise sous forme de classe et adapte a WikiNi)
 * *****************************
 * Peut facilement etre adapte pour d'autres langages (vb, c, c++...)
 * Il suffit de modifier le contenu des variables
 *
 * @version   1.0
 * @copyright FEREY Damien 23/06/2005
 * @copyright Dark Skull Software
 * http://www.darkskull.net
 **/
class JavaHighlighter
{
    public $code = ''; //the code to be hightlighed
    public $newcode = ''; //the generated code
    public $tok; // Le mot en train d'etre decoupe
    public $char; // Le caractere en cours
    public $i; // La position en cours dans le code
    public $codelength; // La longueur de la chaine de code
    /****************************************************************/

    /* Les variables qui definissent le comportement de l'analyseur */

    /****************************************************************/

    public $case_sensitive = true; // Langage sensible a la case ou pas
    public $tokdelimiters = " []()=+-/*:;,.\n\t\r "; // Les delimiteurs de mots
    /***************************************************/

    /* Les couleurs associees a chaque type de donnees */

    /***************************************************/

    public $colorkeyword = '#0000CC';

    public $colortext = '';

    public $colorstring = '#000000';

    public $colorcomment = '#006600';

    public $colorsymbol = '';

    public $colornumber = '#000080';

    public $colorpreproc = '#008000';

    /*************************************************/

    /* Les styles donnes pour chaque type de donnees */

    /*************************************************/

    public $stylekeyword = ['<b>', '</b>'];

    public $styletext = ['', ''];

    //var $stylestring = array("<span style=\"background-color:yellow\">", "</span>");

    public $stylestring = ['', ''];

    public $stylecomment = ['<i>', '</i>'];

    public $stylesymbol = ['', ''];

    public $stylenumber = ['', ''];

    public $stylepreproc = ['<i>', '</i>'];

    /*****************/

    /* Keywords */

    /*****************/

    public $keywords = [
        'abstract',
        'double',
        'double',
        'strictfp',
        'boolean',
        'else',
        'interface',
        'super',
        'break',
        'extends',
        'long',
        'switch',
        'byte',
        'final',
        'native',
        'synchronized',
        'case',
        'finally',
        'new',
        'this',
        'catch',
        'float',
        'package',
        'throw',
        'char',
        'for',
        'protected',
        'public',
        'published',
        'record',
        'packed',
        'case',
        'of',
        'const',
        'array',
        'private',
        'throws',
        'class',
        'goto',
        'protected',
        'transient',
        'const',
        'if',
        'public',
        'try',
        'constructor',
        'destructor',
        'library',
        'set',
        'inherited',
        'object',
        'overload',
        'continue',
        'implements',
        'return',
        'void',
        'default',
        'import',
        'short',
        'volatile',
        'do',
        'instanceof',
        'static',
        'while',
    ];

    /***********************************/

    /* Delimiters for comment */

    /***********************************/

    public $commentdelimiters = [
        ['//', "\n"],
        ['/*', '*/'],
        ['/**', '*/'],
    ];

    /********************************************/

    /* Delimiters for Strings */

    /********************************************/

    public $stringdelimiters = [
        ['"', '"'],
    ];

    /********************************************************/

    /* Delimiters for pre-processor-instructions */

    /********************************************************/

    public $preprocdelimiters = [
        ['(*$', '*)'],
        ['{$', '}'],
    ];

    /////////////////////////////////////////////////////////////////////////////////////////

    // Le code en lui-meme

    /////////////////////////////////////////////////////////////////////////////////////////

    /************************************************************************/

    /* Renvoie vrai si un caractere est visible et peut etre mis en couleur */

    /************************************************************************/

    public function visiblechar($char)
    {
        $inviblechars = " \t\n\r ";

        return (!is_int(mb_strpos($inviblechars, $char)));
    }

    /************************************************************/

    /* Formatte un mot d'une maniere speciale (couleur + style) */

    /************************************************************/

    public function formatspecialtok($tok, $color, $style)
    {
        if (empty($color)) {
            return sprintf("%s$tok%s", $style[0], $style[1]);
        }

        return sprintf("%s<font color=\"%s\">$tok</font>%s", $style[0], $color, $style[1]);
    }

    /*******************************************************************/

    /* Recherche un element dans un tableau sans se soucier de la case */

    /*******************************************************************/

    public function array_search_case($needle, $array)
    {
        if (!is_array($array)) {
            return false;
        }

        if (empty($array)) {
            return false;
        }

        foreach ($array as $index => $string) {
            if (0 == strcasecmp($needle, $string)) {
                return (int)$index;
            }
        }

        return false;
    }

    /*****************************************************/

    /* Analyse un mot et le renvoie de maniere formattee */

    /*****************************************************/

    public function analyseword($tok)
    {
        // Si c'est un nombre

        if (('$' == $tok[0]) || ('#' == $tok[0]) || ($tok == (string)(int)$tok)) {
            return $this->formatspecialtok($tok, $this->colornumber, $this->stylenumber);
        }

        // Si c'est vide, on renvoie une chaine vide

        if (empty($tok)) {
            return $tok;
        }

        // Si c'est un mot cle

        if ((($this->case_sensitive) && (is_int(array_search($tok, $this->keywords, false))))
            || ((!$this->case_sensitive) && (is_int($this->array_search_case($tok, $this->keywords))))) {
            return $this->formatspecialtok($tok, $this->colorkeyword, $this->stylekeyword);
        }

        // Sinon, on renvoie le mot sans formattage

        return $this->formatspecialtok($tok, $this->colortext, $this->styletext);
    }

    /***************************************************/

    /* On regarde si on ne tombe pas sur un delimiteur */

    /***************************************************/

    public function parsearray($array, $color = '#000080', $style = ['<i>', '</i>'])
    {
        // On effectue quelques verifications

        if (!is_array($array)) {
            return false;
        }

        if (!mb_strlen($this->code)) {
            return false;
        }

        if (!count($array)) {
            return false;
        }

        // On va essayer de comparer le caractere courrant avec le 1ø

        // caractere de chaque premier delimiteur

        foreach ($array as $delimiterarray) {
            $delimiter1 = $delimiterarray[0];

            // Si le 1ø char correspond

            if ($this->char == $delimiter1[0]) {
                $match = true;

                // On va tenter de comparer tous les autres caracteres

                // Pour verifier qu'on a bien le delimiteur complet

                for ($j = 1; ($j < mb_strlen($delimiter1)) && $match; $j++) {
                    $match = ($this->code[$this->i + $j] == $delimiter1[$j]);
                } // for

                // Si on l'a en entier

                if ($match) {
                    $delimiter2 = $delimiterarray[1];

                    // Alors on recherche le delimiteur de fin

                    $delimiterend = mb_strpos($this->code, $delimiter2, $this->i + mb_strlen($delimiter1));

                    // Si on ne trouve pas le delimiteur de fin, on prend tout le fichier

                    if (!is_int($delimiterend)) {
                        $delimiterend = mb_strlen($this->code);
                    }

                    // Maintenant qu'on a tout, on analyse le mot avant le delimiteur, s'il existe

                    if (!empty($this->tok)) {
                        $this->newcode .= $this->analyseword($this->tok);

                        $this->tok = '';
                    }

                    // Ensuite, on place le texte contenu entre les delimiteurs

                    $this->newcode .= $this->formatspecialtok(mb_substr($this->code, $this->i, $delimiterend - $this->i + mb_strlen($delimiter2)), $color, $style);

                    // On replace l'indice au bon endroit

                    $this->i = $delimiterend + mb_strlen($delimiter2);

                    // Enfin on recupere le caractere en cours

                    if ($this->i > $this->codelength) {
                        $this->char = null;
                    } else {
                        $this->char = $this->code[$this->i];
                    }

                    // On precise qu'on a trouve

                    return true;
                } //if
            } // if
        } // foreach
        return false;
    }

    /******************************/

    /* On traite les cas speciaux */

    /******************************/

    public function parsearrays()
    {
        $haschanged = true;

        // A chaque changement, on redemarre la boucle entiere

        while ($haschanged) {
            // On regarde si on ne tombe pas sur un delimiteur de commentaire

            $haschanged = $this->parsearray($this->preprocdelimiters, $this->colorpreproc, $this->stylepreproc);

            if (!$haschanged) {
                // On regarde si on ne tombe pas sur un delimiteur de commentaire

                $haschanged = $this->parsearray($this->commentdelimiters, $this->colorcomment, $this->stylecomment);

                if (!$haschanged) {
                    // Ou de chaine de caractere

                    $haschanged = $this->parsearray($this->stringdelimiters, $this->colorstring, $this->stylestring);
                } // if
            } // if
        } // while
    }

    // parsearrays

    public function dump($var, $name)
    {
        // echo "<pre>$name = \n";
        // print_r($var);
        // echo "</pre><br>";
    }

    public function trace($msg)
    {
        error_log((string)$msg);
    }

    /***************************/

    /*Analyse the complete code */

    /***************************/

    public function analysecode($text)
    {
        // Initialize variables

        $this->newcode = '';

        $this->tok = '';

        $this->char = null;

        $this->code = $text;

        $this->codelength = mb_strlen($this->code);

        $this->trace('debut analysecode');

        $this->dump($this->codelength, 'codelength');

        $this->dump($this->code, 'code');

        for ($this->i = 0; $this->i < $this->codelength; $this->i++) {
            $this->dump($this->i, 'i');

            $this->char = $this->code[$this->i];

            $this->dump($this->char, 'char');

            // On regarde si on tombe sur un cas special

            $this->parsearrays();

            // On regarde si on est arrive au bout de la chaine

            if (null === $this->char) {
                return $this->newcode;
            }

            // On a fini d'analyser les commentaires, on regarde si on a un mot complet

            if (is_int(mb_strpos($this->tokdelimiters, $this->char))) {
                // On tombe sur un delimiteur, on coupe le mot

                $this->newcode .= $this->analyseword($this->tok);

                // On formatte le delimiteur

                if ($this->visiblechar($this->char)) {
                    $this->newcode .= $this->formatspecialtok($this->char, $this->colorsymbol, $this->stylesymbol);
                } else {
                    $this->newcode .= $this->char;
                }

                // On remet a 0 le mot en cours

                $this->tok = '';
            } else {// On n'a pas de mot complet, on complete le mot
                $this->tok .= $this->char;
            }
        } // for

        // On regarde si on arrive au bout du code

        if (!empty($this->tok)) {
            $this->newcode .= $this->analyseword($this->tok);
        }

        return $this->newcode;
    }
}
