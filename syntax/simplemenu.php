<?php
/**
 * DokuWiki Plugin simplemenu (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  alexwenzel <alexander.wenzel.berlin@gmail.com>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_simplemenu_simplemenu extends DokuWiki_Syntax_Plugin {
    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'disabled';
    }
    /**
     * @return string Paragraph type
     */
    public function getPType() {
        return 'stack';
    }
    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        return 10;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('<simplemenu:.+?>',$mode,'plugin_simplemenu_simplemenu');
       // $this->Lexer->addEntryPattern('<FIXME>',$mode,'plugin_simplemenu_simplemenu');
    }

   // public function postConnect() {
   //     $this->Lexer->addExitPattern('</FIXME>','plugin_simplemenu_simplemenu');
   // }

    /**
     * Handle matches of the simplemenu syntax
     *
     * @param string $match The match of the syntax
     * @param int    $state The state of the handler
     * @param int    $pos The position in the document
     * @param Doku_Handler    $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler &$handler)
    {
        // default value
        $parameters = array();

        // regex
        preg_match_all('#(\w+)\s*=\s*"(.*?)"#', $match, $return);

        if (is_array($return) && isset($return[1]) && is_array($return[1]))
        foreach($return[1] as $index => $name)
        {
            $parameters[$name] = $return[2][$index];
        }

        return $parameters;
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string         $mode      Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer  $renderer  The renderer
     * @param array          $data      The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer &$renderer, $data)
    {
        if($mode != 'xhtml') return false;

        $startpath = './data/pages/';

        $walker = new SimplemenuWalker($this->convert($startpath));

        foreach ($walker as $key => $item) {
            # code...
            $renderer->doc .= $item.'<br>';
                var_dump($item);
            if ( $walker->isDir() ) {
            }
        }


        return true;
    }

    private function convert($startpath)
    {
        $ritit = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($startpath), RecursiveIteratorIterator::CHILD_FIRST); 
        $r = array(); 
        foreach ($ritit as $splFileInfo) { 
           $path = $splFileInfo->isDir() 
                 ? array($splFileInfo->getFilename() => array()) 
                 : array($splFileInfo->getFilename()); 

           for ($depth = $ritit->getDepth() - 1; $depth >= 0; $depth--) { 
               $path = array($ritit->getSubIterator($depth)->current()->getFilename() => $path); 
           } 
           $r = array_merge_recursive($r, $path); 
        }

        return $r;
    }
}

class SimplemenuWalker implements Iterator
{
    private $items = array();
    private $position = 0;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function rewind() {
    $this->position = 0;
    }

    public function valid() {
        return $this->position < sizeof($this->items);
    }

    public function key() {
        return $this->position;
    }

    public function current() {
        return $this->items[$this->position];
    }

    public function isDir()
    {
        return is_array($this->items[$this->position]);
    }

    public function next() {
        $this->position++;
    }
}

// vim:ts=4:sw=4:et:
