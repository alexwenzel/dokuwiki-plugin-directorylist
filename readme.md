# Requirements

  * PHP >= 5.3
  * PHP fileinfo extension

# Usage

````
<directorylist: argument="value">
````

## Path argument

The path argument specifies the path to list.

This can be an unix or windows path.

It can be absolute or relative.

````
<directorylist: path="./relative/path/in/dokuwiki">
````

## Ingore argument

You can ignore directories or files with the ignore argument.

The ignore argument has to be a comma separated [shell pattern](http://www.php.net/manual/en/function.fnmatch.php).

````
<directorylist: ignore="*.pdf,*.js,specialfile.doc,some_*_files.xlsx">
````