<?php
/**
 * Script for sanitizing HTML input to only allow what is in the whitelist.
 * Tested against majority of the hacks listed at http://ha.ckers.org/xss.html
 *
 * @author Cameron Zemek <grom@zeminvaders.net>
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
 
/**
 * Callback handler for HtmlParser
 */
interface HtmlParserHandler {
    /**
     * Callback for open tag
     *
     * @param $tagName string Tag name
     * @param $attributes array Attributes as an associative array of name => value
     */
    public function openTag($tagName, $attributes);
 
    /**
     * Callback for close tag
     *
     * @param $tagName string Tag name
     */
    public function closeTag($tagName);
 
    /**
     * Callback for comment tags
     *
     * @param $comment string Comment
     */
    public function comment($comment);
 
    /**
     * Callback for text
     *
     * @param $text string Escaped text
     */
    public function text($text);
}
 
/**
 * Handles HTML special characters (ie. < & > ") thereby making it easier
 * for the filter to remove XSS attacks.
 */
class HtmlParser {
    /**
     * Parse HTML snippet
     *
     * @param $html string HTML snippet
     * @param $handler HtmlParserHandler Callback handler
     */
    public function parse($html, HtmlParserHandler $handler) {
        $this->input = $html;
        $this->pos = 0;
        $this->len = strlen($html);
        $this->handler = $handler;
 
        $text = '';
        while ($this->pos < $this->len) {
            $char = $this->look();
            if ($char === '<') {
                if ($this->lookMatches('<!')) {
                    // Handle HTML comment
                    $this->commentBlock();
                } else {
                    // Process tag
                    $this->fireText($text);
                    $text = '';
                    $this->tag();
                }
            } else {
                $text .= $this->char();
            }
        }
        if ($text !== '') {
            $this->fireText($text);
        }
    }
 
    protected function fireOpenTag($tagName, $attributes) {
        $this->handler->openTag($tagName, $attributes);
    }
 
    protected function fireCloseTag($tagName) {
        $this->handler->closeTag($tagName);
    }
 
    protected function fireComment($comment) {
        $this->handler->comment($comment);
    }
 
    protected function fireText($text) {
        if (strlen($text) == '') {
            return;
        }
        $this->handler->text($text);
    }
 
    /**
     * Parse HTML comment block
     */
    private function commentBlock() {
        $comment = '';
        $this->matches('<!');
        while ($this->look() !== '>' && $this->pos < $this->len) {
            if ($this->lookMatches('--')) {
                $comment .= $this->comment();
            } else {
                $this->matchAny(); // Ignore characters outside comment
            }
        }
        $this->match('>');
        $this->fireComment($comment);
    }
 
    /**
     * Parse HTML comment
     */
    private function comment() {
        $comment = '';
        $this->matches('--');
        while (!$this->lookMatches('--') && $this->pos < $this->len) {
            // Convert characters to stop comment hacks <!------><script>alert('xss')</script>-->
            $comment .= htmlspecialchars($this->matchAny());
        }
        $this->matches('--');
        return $comment;
    }
 
    /**
     * Parse HTML tag
     */
    private function tag() {
        $output = $this->match('<');
 
        // Check if close tag
        $closeTag = false;
        if ($this->look() === '/') {
            $closeTag = true;
            $output .= $this->match('/');
        }
 
        $tagName = strtolower($this->matchWord());
        if ($tagName === 'h' && $this->look() >= '1' && $this->look() <= '6') {
            $tagName .= $this->matchAny();
        }
        $output .= $tagName;
 
        // If not valid tag, escape output
        if (strlen($tagName) === 0) {
            $this->fireText(htmlspecialchars($output));
            return;
        }
 
        if ($closeTag) {
            if ($this->look() !== '>') {
                $this->fireText(htmlspecialchars($output));
                return;
            }
            $this->match('>');
            $this->fireCloseTag($tagName);
            return;
        }
 
        $ws = $this->matchWhitespace();
        if ($ws === '' && !($this->look() === '/' || $this->look() === '>')) {
            $this->fireText(htmlspecialchars($output));
            return;
        }
        $attributes = array();
        while (!$this->lookMatches('/>') && $this->look() !== '>' && $this->pos < $this->len) {
            $attribute = $this->attribute();
 
            // Invalid attribute, finish tag
            if (strlen($attribute) === 0) {
                break;
            }
 
            list($attributeName, $attributeValue) = $attribute;
            $attributes[$attributeName] = $attributeValue;
            $this->matchWhitespace();
        }
        if ($this->look() === '/') {
            $closeTag = true;
            $this->match('/');
            $this->match('>');
        }
        if ($this->look() === '>') {
            $this->match('>');
        }
        $this->fireOpenTag($tagName, $attributes);
        if ($closeTag) {
            $this->fireCloseTag($tagName);
        }
    }
 
    /**
     * Parse attribute
     */
    private function attribute() {
        $attributeName = strtolower($this->matchHyphenWords());
        if (strlen($attributeName) === 0) {
            return '';
        }
        $this->matchWhitespace();
        if ($this->look() === '=') {
            $this->match('=');
            $this->matchWhitespace();
            $attributeValue = $this->attributeValue();
        } else {
            $attributeValue = null;
        }
        return array($attributeName, $attributeValue);
    }
 
    /**
     * Parse attribute value
     */
    private function attributeValue() {
        if ($this->look() === '"' || $this->look() === "'") {
            $quoteChar = $this->look();
            $this->match($quoteChar);
            $attributeValue = $this->matchUntil($quoteChar);
            $this->match($quoteChar);
        } else {
            $attributeValue = '';
            while (!ctype_space($this->look()) && $this->look() != '>' && $this->pos < $this->len) {
                $attributeValue .= $this->char();
            }
        }
        if (strlen($attributeValue) === 0) {
            return null;
        }
        return $attributeValue;
    }
 
    /**
     * Match character, handling special characters and character entities
     */
    private function char() {
        if ($this->look() === '&') {
            return $this->entity();
        } else {
            return htmlspecialchars($this->matchAny());
        }
    }
 
    /**
     * Parse HTML entity
     */
    private function entity() {
        if ($this->lookMatches('&#x')) {
            return $this->entityHex();
        } elseif ($this->lookMatches('&#')) {
            return $this->entityNumber();
        } else {
            return $this->entityName();
        }
    }
 
    /**
     * Parse HTML entity name
     */
    private function entityName() {
        $entity = $this->match('&') . $this->matchWord();
        if (strlen($entity) === 1) {
            // Invalid entity, escape &
            return htmlspecialchars($entity);
        }
        if ($this->look() === ';') {
            $entity .= $this->match(';');
        } else {
            $entity .= ';';
        }
        return $entity;
    }
 
    /**
     * Parse HTML entity in number format. Eg. &#169;
     */
    private function entityNumber() {
        $entity = $this->matches('&#');
        $entity .= $this->matchNumber();
        $len = strlen($entity);
        if ($len <= 2 || $len > 6) {
            // Invalid entity, escape &
            return htmlspecialchars($entity);
        }
        if ($this->look() === ';') {
            $entity .= $this->match(';');
        } else {
            $entity .= ';';
        }
        return $entity;
    }
 
    /**
     * Parse HTML entity in hex format. Eg. &#x6A;
     */
    private function entityHex() {
        $entity = $this->matches('&#x', true);
        $entity .= $this->matchHexNumber();
        $len = strlen($entity);
        if ($len <= 3 || $len > 7) {
            // Invalid entity, escape &
            return htmlspecialchars($entity);
        }
        if ($this->look() === ';') {
            $entity .= $this->match(';');
        } else {
            $entity .= ';';
        }
        return $entity;
    }
 
    private function look() {
        return $this->input[ $this->pos ];
    }
 
    private function lookMatches($str, $ignoreCase = true) {
        $input = substr($this->input, $this->pos, strlen($str));
        if ($ignoreCase) {
            $input = strtolower($input);
        }
        return $str === $input;
    }
 
    private function matchAny() {
        return $this->input[ $this->pos++ ];
    }
 
    private function match($char, $ignoreCase = false) {
        $input = $this->look();
        if ($ignoreCase) {
            $input = strtolower($input);
        }
        if ($input !== $char) {
            throw new Exception('Invalid match');
        }
        return $this->input[ $this->pos++ ];
    }
 
    private function matches($str, $ignoreCase = false) {
        $input = substr($this->input, $this->pos, strlen($str));
        if ($ignoreCase) {
            $input = strtolower($input);
        }
        if ($str !== $input) {
            throw new Exception('Invalid match');
        }
        $this->pos += strlen($str);
        return $str;
    }
 
    private function matchUntil($char) {
        $match = '';
        while ($this->look() !== $char && $this->pos < $this->len) {
            $match .= $this->char();
        }
        return $match;
    }
 
    private function matchHyphenWords() {
        $words = $this->matchWord();
        while ($this->look() === '-' && $this->pos < $this->len) {
            // Is there another character after the hypen?
            if ($this->pos + 1 >= $this->len) {
                break;
            }
            // Is next character after hypen part of a word?
            if (!ctype_alpha($this->input [ $this->pos + 1 ])) {
                break;
            }
            // There is another word, so match the hyphen and word
            $words .= $this->match('-') . $this->matchWord();
        }
        return $words;
    }
 
    private function matchWord() {
        $word = '';
        while (ctype_alpha($this->input[ $this->pos ]) && $this->pos < $this->len) {
            $word .= $this->input[ $this->pos++ ];
        }
        return $word;
    }
 
    private function matchNumber() {
        $num = '';
        while (ctype_digit($this->input[ $this->pos ]) && $this->pos < $this->len) {
            $num .= $this->input[ $this->pos++ ];
        }
        return $num;
    }
 
    private function matchHexNumber() {
        $num = '';
        while (ctype_xdigit($this->input[ $this->pos ]) && $this->pos < $this->len) {
            $num .= $this->input[ $this->pos++ ];
        }
        return $num;
    }
 
    private function matchWhitespace() {
        $ws = '';
        while (ctype_space($this->input[ $this->pos ]) && $this->pos < $this->len) {
            $ws .= $this->input[ $this->pos++ ];
        }
        return $ws;
    }
 
    private $pos; // Position in input
    private $len; // Length of input
    private $input;
    private $handler;
}
 
class HtmlFilter implements HtmlParserHandler {
    /**
     * Allowed tags
     */
    private $allowedTags = array('a', 'b', 'i', 'u', 'strong', 'em',
        'sub', 'sup', 'br', 'ul', 'ol', 'li', 'table', 'colgroup', 'col',
        'thead', 'tbody', 'tr', 'td', 'img', 'p', 'code', 'blockquote', 'pre');
 
    /**
     * Attributes that are allowed
     */
    private $allowedAttributes = array(
        'a' => array('href'),
        'col' => array('width'),
        'td' => array('rowspan', 'colspan', 'bgcolor', 'align'),
        'img' => array('src', 'width', 'height', 'alt'),
		'code' => array('class'),
		'pre' => array('class')
    );
 
    /**
     * Tag stack is used to balance tags
     */
    private $tagStack = array();
 
    /**
     * Tags which should always be self-closing (eg. "<img />")
     */
    private $selfCloseTags = array('img', 'br', 'col');
 
    /**
     * Attributes which contain URLs
     */
    private $urlAttributes = array('src', 'href');
 
    /**
     * Protocols which are allowed
     */
    private $allowedProtocols = array('http');
 
    /**
     * Are URL paths allowed, that is no protocol scheme is specified. Eg. /images/photo.jpg
     */
    private $urlPathAllowed = true;
 
    /**
     * Should comments be removed?
     */
    private $stripComments = true;
 
    private $output = ''; // Safe HTML
 
    public function openTag($tagName, $attributes) {
        // Ignore tags that are not white listed
        if (!in_array($tagName, $this->allowedTags)) {
            return;
        }
        if (!in_array($tagName, $this->selfCloseTags)) {
            array_push($this->tagStack, $tagName);
        }
        $this->output .= '<' . $tagName;
        $allowedAttributes = $this->allowedAttributes[$tagName];
        if (isset($allowedAttributes) && is_array($allowedAttributes)) {
            foreach ($attributes as $name => $value) {
                if (in_array($name, $allowedAttributes)) {
                    // If its a protocol attribute, check if its an allowed protocol
                    if (in_array($name, $this->urlAttributes)) {
                        $urlComponents = parse_url($value);
                        if ((isset($urlComponents['scheme']) &&
                            in_array($urlComponents['scheme'], $this->allowedProtocols)) ||
                            (!isset($urlComponents['scheme']) && $this->urlPathAllowed)) {
                            $this->output .= ' ' . $name . '="' . $value . '"';
                        }
                    } else {
                        $this->output .= ' ' . $name . '="' . $value . '"';
                    }
                }
            }
        }
        if (in_array($tagName, $this->selfCloseTags)) {
            $this->output .= ' /';
        }
        $this->output .= '>';
    }
 
    public function closeTag($tagName) {
        if (!in_array($tagName, $this->tagStack)) {
            // Orphan close tag, ignore
            return;
        }
        while (true) {
            if (count($this->tagStack) === 0) {
                break;
            }
            $popTag = array_pop($this->tagStack);
            if ($popTag === $tagName) {
                break;
            }
            $this->output .= '</' . $popTag . '>';
        }
        $this->output .= '</' . $tagName . '>';
    }
 
    public function comment($comment) {
        if ($this->stripComments) {
            return;
        }
        $this->output .= '<!--' . $comment . '-->';
    }
 
    public function text($text) {
        $this->output .= $text;
    }
 
    private $parser;
 
    public function __construct() {
        $this->parser = new HtmlParser;
    }
 
    public function filter($html) {
        $this->parser->parse($html, $this);
        // Close any remaining tags on the stack
        while ($tagName = array_pop($this->tagStack)) {
            $this->output .= '</' . $tagName . '>';
        }
        return $this->output;
    }
}