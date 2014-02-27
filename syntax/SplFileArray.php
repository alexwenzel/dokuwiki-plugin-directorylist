<?php

/**
 * Collects all files for a specified path hierachical,
 * recursive or non recursive.
 */
class SplFileArray
{
	/**
	 * Storage
	 * @var array
	 */
	private $filesystemarray = array();

	/**
	 * Constructor
	 * @param string  $path      Path to collect
	 * @param boolean $recursive Collect information recursivly or not
	 */
	public function __construct($path, $recursive = true)
	{
		$this->filesystemarray = $this->readdir($path, $recursive);
	}

	/**
	 * Returns the result
	 * @return array
	 */
	public function get()
	{
		return $this->filesystemarray;
	}

	/**
	 * Reads all files for a given path
	 * @param  string 	$path
	 * @param  bool 	$recursive
	 * @return array
	 */
	private function readDir($path, $recursive)
	{
		$filesystem = new FileSystemIterator($path, FileSystemIterator::KEY_AS_FILENAME);
		
		$array = array();

		foreach ($filesystem as $key => $value) {
			
			if ( $value->isDir() && $recursive ) {
				$array[$key] = $this->readDir($value->getRealPath(), $recursive);
			}
			else if ( ! $value->isDir() ) {
				$array[$key] = $value;
			}

		}

		uasort($array, array($this, 'sortDir'));

		return $array;
	}

	/**
	 * Custom sort method
	 * This sort down arrays (directories) and sorts up everything else
	 * @param  mixed $a
	 * @param  mixed $b
	 * @return int
	 */
	private function sortDir($a, $b)
	{
		if ( is_array($a) )
			return +1;

		if ( is_array($b) )
			return -1;

		if ( $a->getFilename() == $b->getFilename() )
			return 0;

		return ($a->getFilename() > $b->getFilename()) ? +1 : -1;
	}
}
