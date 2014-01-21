# Directory listing for Dokuwiki

Specify a directory and this plugin will list all contained items and made them downloadable.

This is mostly used for intranets.

**Information:** Please be aware, that a wiki user can pick ANY directory on your maschine. I am not responsible for any problems.

## Example

![Image](example1.png?raw=true)

## Requirements

  * PHP >= 5.3
  * PHP fileinfo extension

## Usage

````
<directorylist: path="value" ignore="value">
````

### Path argument

The path argument specifies the path which the directorylist plugin will list.

This can be an unix or windows path. It can be absolute or relative.

**Example:**

````
<directorylist: path="./relative/path/in/dokuwiki">
````

### Ingore argument

You can ignore directories or files with the ignore argument.

The ignore argument has to be a comma separated [shell pattern](http://www.php.net/manual/en/function.fnmatch.php).

**Example:**

````
<directorylist: ignore="*.pdf,*.js,specialfile.doc,some_*_files.xlsx">
````