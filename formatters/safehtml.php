<?php

$this->UseClass('SafeHtml', 'formatters/classes/');
require_once 'lib/HTMLSax/HTMLSax.php';
// Save all "<" symbols
$text = preg_replace("/<(?=[^a-zA-Z\/\!\?\%])/", '&lt;', $text);
// Instantiate the handler
$handler = new safehtml();
// Instantiate the parser
$parser = new XML_HTMLSax();
// Register the handler with the parser
$parser->set_object($handler);
// Set the handlers
$parser->set_elementHandler('openHandler', 'closeHandler');
$parser->set_dataHandler('dataHandler');
$parser->set_escapeHandler('escapeHandler');
$parser->parse($text);
echo($handler->getXHTML());
