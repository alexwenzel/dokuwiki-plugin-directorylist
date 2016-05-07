<?php
/**
 * DokuWiki Plugin directorylist (Syntax Component)
 *
 * @author  alexwenzel <alexander.wenzel.berlin@gmail.com>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) {
    die();
}

class Syntax_Plugin_Directorylist_Directorylist extends DokuWiki_Syntax_Plugin
{
    /**
     * Doku_Renderer
     * @var Doku_Renderer
     */
    private $renderer;

    /**
     * @return string Syntax mode type
     */
    public function getType()
    {
        return 'disabled';
    }

    /**
     * @return string Paragraph type
     */
    public function getPType()
    {
        return 'stack';
    }

    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort()
    {
        return 10;
    }

    /**
     * Connect lookup pattern to lexer
     * @param  string $mode Parser mode
     * @return void
     */
    public function connectTo($mode)
    {
        $this->Lexer->addSpecialPattern('<directorylist.+?>', $mode, 'plugin_directorylist_directorylist');
    }

    /**
     * Handle matches of the directorylist syntax
     * @param  string $match The match of the syntax
     * @param  int $state The state of the handler
     * @param  int $pos The position in the document
     * @param  Doku_Handler $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        // default value
        $parameters = array();

        // regex
        preg_match_all('#(\w+)\s*=\s*"(.*?)"#', $match, $return);

        if (is_array($return) && isset($return[1]) && is_array($return[1])) {
            foreach ($return[1] as $index => $name) {
                $parameters[$name] = $return[2][$index];
            }
        }

        return $parameters;
    }

    /**
     * Render xhtml output or metadata
     * @param  string $mode Renderer mode (supported modes: xhtml)
     * @param  Doku_Renderer $renderer The renderer
     * @param  array $data The data from the handler() function
     * @return bool                    If rendering was successful.
     */
    public function render($mode, Doku_Renderer $renderer, $data)
    {
        // do not render if not in xhtml mode
        if ($mode != 'xhtml') {
            return false;
        }

        // safe reference
        $this->renderer = $renderer;

        try {

            // check and set default: recursive argument
            if (!isset($data['recursive']) || empty($data['recursive'])) {
                $data['recursive'] = true;
            }

            // check and set default: ignore argument
            if (!isset($data['ignore']) || empty($data['ignore'])) {
                $data['ignore'] = '';
            }

            // check and set default: fileorder argument
            if (!isset($data['fileorder']) || empty($data['fileorder'])) {
                $data['fileorder'] = 'asc';
            }

            // check and set default: type argument
            if (!isset($data['type']) || empty($data['type'])) {
                $data['type'] = 'download';
            }

            // check: path argument
            if (!isset($data['path']) || empty($data['path'])) {
                throw new Exception("A path is missing!");
            }

            // get all directories and files
            require_once "SplFileArray.php";
            $fs = new SplFileArray($data['path'], (bool)$data['recursive'], $data['fileorder']);

            // get files
            $files = $fs->get();


            // start walking down
            $this->renderer->doc .= '<ul class="directorylist">';
            $this->walkDirArray($files, $data);
            $this->renderer->doc .= '</ul>';

        } catch (Exception $e) {

            $this->renderer->doc .= '<strong>directorylist error:</strong> ';
            $this->renderer->doc .= $e->getMessage();
        }

        // finished
        return true;
    }

    /**
     * Walks down the directory array
     * @param  array $dirArray
     * @return void
     */
    private function walkDirArray(array $dirArray, array $data)
    {
        foreach ($dirArray as $key => $value) {

            // check if is ignored
            if (!$this->isIgnored($data['ignore'], $key)) {

                // check if is directory
                if (is_array($value)) {

                    // this is the start of a new sub directory
                    $this->renderer->doc .= '<li class="folder">'.$key.'<ul>';
                    $this->walkDirArray($value, $data);
                    $this->renderer->doc .= '</ul></li>';
                } else {
                    if ($value instanceof SplFileInfo) {

                        // not directory, but file
                        $this->renderer->doc .= '<li class="file">'.$this->formatLink($value, $data).$this->formatBytes(
                                $value
                            ).'</li>';
                    }
                }
            }
        }
    }

    /**
     * Returns, whether the file is ignored or not
     * @see    http://www.php.net/manual/en/function.fnmatch.php
     * @param  string $ignorePattern
     * @param  string $name
     * @return bool
     */
    private function isIgnored($ignorePattern, $name)
    {
        // not ignored if emtpy
        if (empty($ignorePattern)) {
            return false;
        }

        // explode the ignore argument
        $patternArray = explode(',', $ignorePattern);

        // iterate through all given patterns
        foreach ($patternArray as $pattern) {

            // is there a match
            if (fnmatch($pattern, $name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the filesize for a given file
     * @param  SplFileInfo $file
     * @param  integer $precision
     * @return string
     */
    private function formatBytes(SplFileInfo $file, $precision = 2)
    {
        $base = log($file->getSize()) / log(1024);
        $suffixes = array('B', 'kB', 'MB', 'GB', 'TB');

        $return = round(pow(1024, $base - floor($base)), $precision).' '.$suffixes[floor($base)];

        return '<span class="size">'.$return.'</span>';
    }


    /**
     * Returns the link tag for a given file
     * @param  string $filepath
     * @return string
     */
    private function formatLink(SplFileInfo $file, array $data)
    {
        global $conf;

        $link = '<a href="';
        if ($data['type'] == 'download') {
            $link .= '?do=download&file='.rawurlencode($file->getRealPath());
        } else {
            if ($data['type'] == 'direct') {
                $link .= 'file://'.$file->getRealPath();
            } else {
                if ($data['type'] == 'link') {
                    $startpos = strpos($file->getRealPath(), $conf['basedir']);
                    $link .= $conf['baseurl'].substr($file->getRealPath(), $startpos);
                } else {
                    $link .= '';
                }
            }
        }
        $link .= '"';
        if ($data['type'] == 'download') {
            $link .= ' target="_blank"';
        }
        $link .= ' title="'.$file->getFilename().'"';
        $link .= '>';
        $link .= utf8_encode($file->getFilename());
        $link .= '</a>';

        return $link;
    }
}
