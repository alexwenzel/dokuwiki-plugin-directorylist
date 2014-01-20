<?php
/**
 * DokuWiki Plugin directorylist (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  alexwenzel <alexander.wenzel.berlin@gmail.com>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_directorylist_directorylist extends DokuWiki_Syntax_Plugin
{
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
	 * Connect lookup pattern to lexer.
	 *
	 * @param  string $mode Parser mode
	 * @return void
	 */
	public function connectTo($mode)
	{
		$this->Lexer->addSpecialPattern('<directorylist.+?>',$mode,'plugin_directorylist_directorylist');
	   // $this->Lexer->addEntryPattern('<FIXME>',$mode,'plugin_directorylist_directorylist');
	}

	/**
	 * Handle matches of the directorylist syntax
	 *
	 * @param  string $match The match of the syntax
	 * @param  int    $state The state of the handler
	 * @param  int    $pos The position in the document
	 * @param  Doku_Handler    $handler The handler
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
	 * @param  string         $mode      Renderer mode (supported modes: xhtml)
	 * @param  Doku_Renderer  $renderer  The renderer
	 * @param  array          $data      The data from the handler() function
	 * @return bool 					If rendering was successful.
	 */
	public function render($mode, Doku_Renderer &$renderer, $data)
	{
		if($mode != 'xhtml') return false;

		// get all directories and files
		$dirArray = $this->convert($data['path'], $data['ignore']);

		// start walking down
		$renderer->doc .= '<ul>';
		$this->walkDirArray($renderer, $dirArray);
		$renderer->doc .= '</ul>';

		// finished
		return true;
	}

	/**
	 * Walks down the directory array
	 * @param  Doku_Renderer $renderer
	 * @param  array         $dirArray
	 * @return void
	 */
	private function walkDirArray(Doku_Renderer &$renderer, array $dirArray)
	{
		foreach ($dirArray as $key => $value) {

			if ( is_array($value) ) {

				// this is the start of a new sub directory
				$renderer->doc .= '<li>'.$key.'<ul>';
				$this->walkDirArray($renderer, $value);
				$renderer->doc .= '<ul></li>';
			}
			else {

				// no sub directory
				$renderer->doc .= '<li>'.$value.'</li>';
			}
		}
	}

	/**
	 * Reads the directory recursivly and returns all items packed in an array
	 * @see    http://www.php.net/manual/pt_BR/class.recursivedirectoryiterator.php#111142
	 * @see    http://www.php.net/manual/en/function.fnmatch.php
	 * @param  string $path
	 * @return array
	 */
	private function convert($path, $ignore)
	{
		$ritit = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);

		$r = array();

		foreach ($ritit as $splFileInfo) {

			if ( ! $this->fileIsIgnored($ignore, $splFileInfo->getFilename()) ) {

				$path = $splFileInfo->isDir()
					? array($splFileInfo->getFilename() => array())
					: array($splFileInfo->getFilename());

				for ($depth = $ritit->getDepth() - 1; $depth >= 0; $depth--) {
					$path = array($ritit->getSubIterator($depth)->current()->getFilename() => $path);
				}

				$r = array_merge_recursive($r, $path);
			}
		}

		return $r;
	}

	/**
	 * Returns, whether the file is ignored or not
	 * @param  string $ignorePattern
	 * @param  string $filename
	 * @return bool
	 */
	private function fileIsIgnored($ignorePattern, $filename)
	{
		// explode the ignore argument
		$patternArray = explode(',', $ignorePattern);

		// iterate through all given patterns
		foreach ($patternArray as $pattern) {

			// is there a match
			if ( fnmatch($pattern, $filename) )
				return true;
		}

		return false;
	}

	private function showError($description)
	{

	}
}

// vim:ts=4:sw=4:et:
