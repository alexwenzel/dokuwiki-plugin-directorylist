# Directory listing for Dokuwiki

Specify a directory and this plugin will list all contained items and made them downloadable.

This is mostly used for intranets.

**Information #1:** This is still under development and not finished or secured.

**Information #2:** Please be aware, that a wiki user can pick ANY directory on your maschine. I am not responsible for any problems.

## Example

![Image](example1.png?raw=true)

## Requirements

  * PHP >= 5.3
  * PHP fileinfo extension

## Usage

````
<directorylist: path="value" ignore="value" recursive="1" fileorder="asc">
````

### Path argument

The path argument specifies the path which the directorylist plugin will list.

This can be an unix or windows path. It can be absolute or relative.

**Examples:**

````
<directorylist: path="./relative/path/in/dokuwiki">
<directorylist: path="C:\Users\Public">
````

### Ingore argument

You can ignore directories or files with the ignore argument.

The ignore argument has to be a comma separated [shell pattern](http://www.php.net/manual/en/function.fnmatch.php).

This argument is optional, the  value is empty.

**Example:**

````
<directorylist: [...] ignore="*.pdf,*.js,specialfile.doc,some_*_files.xlsx">
````

### Recursive argument

You can specify if you want to list your directory recursive or not.

This argument is optional, the default value is: ``true``.

**Examples:**

````
<directorylist: [...] recursive="0">
<directorylist: [...] recursive="1">
````

````
<directorylist: [...] recursive="false">
<directorylist: [...] recursive="true">
````

### Fileorder argument

You can specify the order of files inside a directory.

This argument is optional, the default value is: ``asc``.

**Examples:**

````
<directorylist: [...] fileorder="asc">
<directorylist: [...] fileorder="desc">
````

### Type argument

Adding a new 'type' option to the directorylist syntax to enable different types of href link.

This allows 'direct' links using the file:// protocal.
(NOTE: this will usually not be a good choice since most browsers, eg. Chrome, forbid the openning of file:// links from non-local pages)

Also allows 'link' links that, using the absolute path and dokuwiki basedir, serve weblinks to content.
(NOTE: assumes the data directory is reachable by your webserver. This is a big security issue!)

Default option is 'download';

**Examples:**

````
<directorylist: [...] type="download">
<directorylist: [...] type="direct">
<directorylist: [...] type="link">
````

## NOCACHE

When the directory listing is not updating as you like, try to use the ``~~NOCACHE~~`` tag.

https://www.dokuwiki.org/caching

## Styling

You can style this plugin with the following elements:

  * ``ul.directorylist``
  * ``ul.directorylist li.file``
  * ``ul.directorylist li.folder``

## Information

Author: Alexander Wenzel (alexander.wenzel.berlin@gmail.com)

Plugin page: http://www.dokuwiki.org/plugin:directorylist

Icons: [genericons.com](http://genericons.com)

## Changelog
* 0.2.2
    * updated readme
    * fixed sorting bug
    * fixed problems with chinese characters
* 0.2.1
    * PHP7 compatibility
    * changed default 'type' to 'download'
* 0.2.0
    * added 'type' argument