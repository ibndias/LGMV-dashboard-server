<?php
function getNextMinificationPlaceholder(){
global $minificationStore;
return '<-!!-' . sizeof($minificationStore) . '-!!->';
}
abstract class MinificationSequenceFinder
{
public $start_idx;
public $end_idx;
public $type;
abstract protected function findFirstValue($string);
public function isValid(){
return $this->start_idx !== false;
}
}
class StringSequenceFinder extends MinificationSequenceFinder
{
protected $start_delimiter;
protected $end_delimiter;
function __construct($start_delimiter, $end_delimiter){
$this->type = $start_delimiter;
$this->start_delimiter = $start_delimiter;
$this->end_delimiter = $end_delimiter;
}
public function findFirstValue($string){
$this->start_idx = strpos($string, $this->start_delimiter);
if ($this->isValid()){
$this->end_idx = strpos($string, $this->end_delimiter, $this->start_idx+1);
// sanity check for non well formed lines
$this->end_idx = ($this->end_idx === false ? strlen($string) : $this->end_idx + strlen($this->end_delimiter));
}
}
}
class QuoteSequenceFinder extends MinificationSequenceFinder
{
function __construct($type){
$this->type = $type;
}
public function findFirstValue($string){
$this->start_idx = strpos($string, $this->type);
if ($this->isValid()){
// look for first non escaped endquote
$this->end_idx = $this->start_idx+1;
while ($this->end_idx < strlen($string)){
// find number of escapes before endquote
if (preg_match('/(\\\\*)(' . preg_quote($this->type) . ')/', $string, $match, PREG_OFFSET_CAPTURE, $this->end_idx)){
$this->end_idx = $match[2][1] + 1;
// if odd number of escapes before endquote, endquote is escaped. Keep going
if (!isset($match[1][0]) || strlen($match[1][0]) % 2 == 0){
return;
}
}else{
// no match, not well formed
$this->end_idx = strlen($string);
return;
}
}
}
}
}
function getNextSpecialSequence($string, $sequences){
// $special_idx is an array of the nearest index for all special characters
$special_idx = array();
foreach ($sequences as $finder){
$finder->findFirstValue($string);
if ($finder->isValid()){
$special_idx[$finder->start_idx] = $finder;
}
}
// if none found, return
if (count($special_idx) == 0){return false;}
// get first occuring item
asort($special_idx);
return $special_idx[min(array_keys($special_idx))];
}
class JSRegexSequenceFinder extends MinificationSequenceFinder
{
function __construct(){
$this->type = 'regex';
}
/* check to make sure this isn't the start of a comment or
a division
*/
public function findPossibleStart($string, $idx = 0){
$start_idx = strpos($string, '/', $idx);
if ($start_idx === false){
return false;
}
if (substr($string, $start_idx, 2) === '//' || substr($string, $start_idx, 2) === '/*'){
// found comment, not pattern, don't bother continuing
return false;
}
$tmp = $start_idx - 1;
// get first nonspace previous char
while ($tmp > 0 && substr($string, $tmp, 1) == ' '){$tmp--;}
if ($tmp > 0){
$char = substr($string, $tmp, 1);
// if char or number than this is division, get further
if (is_numeric($char) || ctype_alpha($char) || $char == ')' || $char == ']'){
return $this->findPossibleStart($string, $start_idx + 1);
}
}
return $start_idx;
}
public function findFirstValue($string){
$this->start_idx = $this->findPossibleStart($string);
if ($this->start_idx === false){
return;
}
// position of first newline after pattern
$nl = strpos($string, "\n", $this->start_idx);
// look for first non escaped endquote
$end_idx = $this->start_idx+1;
while ($end_idx < strlen($string) // if there's still room to explore in the string
&& ($nl === false || $end_idx < $nl)) // and we're not at a newline yet
{
// find number of escapes before endquote
if (preg_match('/(\\\\*)(\/)/', $string, $match, PREG_OFFSET_CAPTURE, $end_idx)){
$end_idx = $match[2][1] + 1;
// if odd number of escapes before endquote, endquote is escaped. Keep going
if (!isset($match[1][0]) || strlen($match[1][0]) % 2 == 0){
if ($nl !== false && $end_idx > $nl){return false;}
$this->end_idx = $end_idx;
return;
}
// no match, not well formed
} else{
$this->start_idx = false;
return;
}
}
}
}
$lineCommentFinder = new StringSequenceFinder('//', "\n");
$singleQuoteSequenceFinder = new QuoteSequenceFinder('\'');
$doubleQuoteSequenceFinder = new QuoteSequenceFinder('"');
$blockCommentFinder = new StringSequenceFinder('/*', '*/');
function minifyJavascript($javascript){
global $minificationStore, $singleQuoteSequenceFinder, $doubleQuoteSequenceFinder, $blockCommentFinder, $lineCommentFinder;
$java_special_chars = array($blockCommentFinder, // JavaScript Block Comment
$lineCommentFinder, // JavaScript Line Comment
$singleQuoteSequenceFinder, // single quote escape, e.g. :before{ content: '-';}
$doubleQuoteSequenceFinder, // double quote
new JSRegexSequenceFinder() // JavaScript regex expression
);
// pull out everything that needs to be pulled out and saved
while ($sequence = getNextSpecialSequence($javascript, $java_special_chars)){
switch ($sequence->type){
case '/*':
case '//':// remove comments
$javascript = substr($javascript, 0, $sequence->start_idx) . substr($javascript, $sequence->end_idx);
break;
default: // quoted strings or regex that need to be preservered
$start_idx = $sequence->start_idx;
$end_idx = $sequence->end_idx;
$placeholder = getNextMinificationPlaceholder();
$minificationStore[$placeholder] =substr($javascript, $start_idx, $end_idx - $start_idx);
$javascript = substr($javascript, 0, $start_idx) . $placeholder . substr($javascript, $end_idx);
}
}
// special case where the + indicates treating variable as numeric, e.g. a = b + +c
$javascript = preg_replace('/([-\+])\s+\+([^\s;]*)/', '$1 (+$2)', $javascript);
// condense spaces
$javascript = preg_replace("/\s*\n\s*/", "\n", $javascript); // spaces around newlines
$javascript = preg_replace("/\h+/", " ", $javascript); // \h+ horizontal white space
// remove unnecessary horizontal spaces around non variables (alphanumerics, underscore, dollar sign)
$javascript = preg_replace("/\h([^A-Za-z0-9\_\$])/", '$1', $javascript);
$javascript = preg_replace("/([^A-Za-z0-9\_\$])\h/", '$1', $javascript);
// remove unnecessary spaces around brackets and parentheses
$javascript = preg_replace("/\s?([\(\[{])\s?/", '$1', $javascript);
$javascript = preg_replace("/\s([\)\]}])/", '$1', $javascript);
// remove unnecessary spaces around operators that don't need any spaces (specifically newlines)
$javascript = preg_replace("/\s?([\.=:\-+,])\s?/", '$1', $javascript);
// unnecessary characters
$javascript = preg_replace("/;\n/", ";", $javascript); // semicolon before newline
$javascript = preg_replace('/;}/', '}', $javascript); // semicolon before end bracket
// put back the preserved strings
foreach($minificationStore as $placeholder => $original){
$javascript = str_replace($placeholder, $original, $javascript);
}
return trim($javascript);
}

?> 