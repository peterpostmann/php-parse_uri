# parse_uri

[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]

Parse a URI and return its components

This function parses a URI ([RFC3986][1] URL, URN or Windows path) and returns an associative array containing any of the various components of the URL that are present. The values of the array elements are not URL decoded.

This function is not meant to validate the given URL, it only breaks it up into the above listed parts. 

## Install

### Via Composer

```bash
composer require peterpostmann/parse_uri
```
If you dont want to use composer just copy the `parse_uri.php` file and include it into your project.

## Why

This function extends the capabailities of parse_url. It parses rfc complient URLs and URNs and (windows) file paths (basically everything which can be passed to file functions (e.g. fopen, file_get_contents)).

## Usage

~~~PHP
use function peterpostmann\uri\parse_uri;

array parse_uri ( string uri [, int $component = -2 [, bool $convertUrlToUrn = null ]]) 

~~~

Via option the output can be reduced to the output of `parse_url`. Otherwise additonal components are provided:

URL

~~~PHP
schema://user:pass@host:port/path?query#fragment

array (size=14)
  'scheme' => string 'schema' (length=6)
  'host' => string 'host' (length=4)
  'port' => string '' (length=0)
  'user' => string 'user' (length=4)
  'pass' => string 'pass' (length=4)
  'path' => string 'port/path' (length=9)
  'query' => string 'query' (length=5)
  'fragment' => string 'fragment' (length=8)
  '_protocol' => string 'schema' (length=6)
  '_userinfo' => string 'user:pass@' (length=10)
  '_authority' => string 'user:pass@host:' (length=15)
  '_document' => string 'schema://user:pass@host:port/path' (length=33)
  '_ressource' => string 'schema://user:pass@host:port/path?query' (length=39)
  '_uri' => string 'schema://user:pass@host:port/path?query#fragment' (length=48)
~~~

URI
~~~PHP
schema:path?query#fragment

array (size=8)
  'scheme' => string 'schema' (length=6)
  'path' => string 'path' (length=4)
  'query' => string 'query' (length=5)
  'fragment' => string 'fragment' (length=8)
  '_protocol' => string 'schema' (length=6)
  '_document' => string 'schema:path' (length=11)
  '_ressource' => string 'schema:path?query' (length=17)
  '_uri' => string 'schema:path?query#fragment' (length=26)
~~~

`_protocol` will return 
- {schema} if present 
- 'file' if it is an absolute path ('/path', 'C:\path')
- false if ti is a file or relative path ('path/file', 'file')
- null if there is no path

### Example

#### parse URIs

~~~PHP
use function peterpostmann\uri\parse_uri;

echo "# URIs (with standard components)\n\n";

var_dump(parse_uri('/path/to/file.ext', peterpostmann\uri\PARSE_URI_DEFAULT));
var_dump(parse_uri('relative/path/to/file.ext', peterpostmann\uri\PARSE_URI_DEFAULT));
var_dump(parse_uri('fileInCwd.ext', peterpostmann\uri\PARSE_URI_DEFAULT));
var_dump(parse_uri('C:/path/to/winfile.ext', peterpostmann\uri\PARSE_URI_DEFAULT));
var_dump(parse_uri('C:\path\to\winfile.ext', peterpostmann\uri\PARSE_URI_DEFAULT));
var_dump(parse_uri('\\\\smbserver\share\path\to\winfile.ext', peterpostmann\uri\PARSE_URI_DEFAULT));
var_dump(parse_uri('file:///path/to/file.ext', peterpostmann\uri\PARSE_URI_DEFAULT));
var_dump(parse_uri('http://user:pass@example.org:8888/path/to/file', peterpostmann\uri\PARSE_URI_DEFAULT));
var_dump(parse_uri('news:comp.infosystems.www.servers.unix', peterpostmann\uri\PARSE_URI_DEFAULT));

echo "# URIs (with additional components)\n\n";

var_dump(parse_uri('C:\path\to\winfile.ext'));
var_dump(parse_uri('\\\\smbserver\share\path\to\winfile.ext'));
var_dump(parse_uri('file:///path/to/file.ext'));
var_dump(parse_uri('http://user:pass@example.org:8888/path/to/file'));
var_dump(parse_uri('news:comp.infosystems.www.servers.unix'));

~~~


The above example will output:

```PHP
# URIs (with standard components)

array (size=1)
  'path' => string '/path/to/file.ext' (length=17)
 
array (size=1)
  'path' => string 'relative/path/to/file.ext' (length=25)

array (size=1)
  'path' => string 'fileInCwd.ext' (length=13)

array (size=1)
  'path' => string 'C:\path\to\winfile.ext' (length=22)

array (size=1)
  'path' => string 'C:\path\to\winfile.ext' (length=22)

array (size=3)
  'scheme' => string 'file' (length=4)
  'host' => string 'smbserver' (length=9)
  'path' => string '/share/path/to/winfile.ext' (length=26)

array (size=3)
  'scheme' => string 'file' (length=4)
  'host' => string '' (length=0)
  'path' => string '/path/to/file.ext' (length=17)

array (size=6)
  'scheme' => string 'http' (length=4)
  'host' => string 'example.org' (length=11)
  'port' => int 8888
  'user' => string 'user' (length=4)
  'pass' => string 'pass' (length=4)
  'path' => string '/path/to/file' (length=13)

array (size=2)
  'scheme' => string 'news' (length=4)
  'path' => string 'comp.infosystems.www.servers.unix' (length=33)

# URIs (with additional components)

array (size=5)
  'path' => string 'C:\path\to\winfile.ext' (length=22)
  '_protocol' => string 'file' (length=4)
  '_document' => string 'C:\path\to\winfile.ext' (length=22)
  '_ressource' => string 'C:\path\to\winfile.ext' (length=22)
  '_uri' => string 'C:\path\to\winfile.ext' (length=22)

array (size=8)
  'scheme' => string 'file' (length=4)
  'host' => string 'smbserver' (length=9)
  'path' => string '/share/path/to/winfile.ext' (length=26)
  '_protocol' => string 'file' (length=4)
  '_authority' => string 'smbserver' (length=9)
  '_document' => string '/share/path/to/winfile.ext' (length=26)
  '_ressource' => string '/share/path/to/winfile.ext' (length=26)
  '_uri' => string '/share/path/to/winfile.ext' (length=26)

array (size=7)
  'scheme' => string 'file' (length=4)
  'host' => string '' (length=0)
  'path' => string '/path/to/file.ext' (length=17)
  '_protocol' => string 'file' (length=4)
  '_document' => string 'file:///path/to/file.ext' (length=24)
  '_ressource' => string 'file:///path/to/file.ext' (length=24)
  '_uri' => string 'file:///path/to/file.ext' (length=24)

array (size=12)
  'scheme' => string 'http' (length=4)
  'host' => string 'example.org' (length=11)
  'port' => int 8888
  'user' => string 'user' (length=4)
  'pass' => string 'pass' (length=4)
  'path' => string '/path/to/file' (length=13)
  '_protocol' => string 'http' (length=4)
  '_userinfo' => string 'user:pass@' (length=10)
  '_authority' => string 'user:pass@example.org:8888' (length=26)
  '_document' => string 'http://user:pass@example.org:8888/path/to/file' (length=46)
  '_ressource' => string 'http://user:pass@example.org:8888/path/to/file' (length=46)
  '_uri' => string 'http://user:pass@example.org:8888/path/to/file' (length=46)

array (size=6)
  'scheme' => string 'news' (length=4)
  'path' => string 'comp.infosystems.www.servers.unix' (length=33)
  '_protocol' => string 'news' (length=4)
  '_document' => string 'news:comp.infosystems.www.servers.unix' (length=38)
  '_ressource' => string 'news:comp.infosystems.www.servers.unix' (length=38)
  '_uri' => string 'news:comp.infosystems.www.servers.unix' (length=38)

```

#### patch URIs

~~~PHP
use function peterpostmann\uri\parse_uri;
use function peterpostmann\uri\build_uri;

$uri = 'https://example.org/path/to/file?query#fragment':
$patch = [ 'path' => '/path/to/otherfile']
echo build_uri($patch + parse_uri($uri));

~~~

The above example will output:

```PHP
https://example.org/path/to/otherfile?query#fragment

```

#### Load References

~~~PHP
function resolveReference($reference)
{
    $components = parse_uri($reference);

    if(!isset($components['fragment']))
        return null;

    // absolute reference
    if(isset($components['_document'])) {
        $document = $this->getLoader($components['_protocol'])->load($components['_document']);
    } else { // relative reference
        $document = $this->currentDocument();
    }

    return $this->getReference($document, $components['fragment']);
}
~~~

`$components['_document']` returns the URI without the fragment. (`$components['_ressource']` without fragment and query).
`$components['_protocol']` indicates the protocol. It usualy is the same as scheme, except for file:/// URIs. For absolute, relative and windows path, scheme is not set, but `$components['_protocol']` is set to file. For URIs without schema `\\example.org\path` the variable is set, but empty. 


### Helper Functions

#### build_uri

The function creates a uri (string) from its components.

~~~PHP

use function peterpostmann\uri\build_uri;

echo build_uri([
          'scheme' => 'ssh2.sftp',
          'host' => 'example.com',
          'port' => 422,
          'user' => 'user',
          'pass' => 'pass',
          'path' => '/file.php',
          'query' => 'var1=val1&var2=val2',
          'fragment' => 'anchor'
      ])."\n";
      
echo build_uri([
          'scheme' => 'news',
          'path' => 'comp.infosystems.www.servers.unix',
      ])."\n";

~~~

The above example will output:

```PHP
ssh2.sftp://user:pass@example.com:422/file.php?var1=val1&var2=val2#anchor
news:comp.infosystems.www.servers.unix

```

#### convert_url2urn

The function converts php URLs which are not real URLs to URNs (what they should have been in first place).


~~~PHP
use function peterpostmann\uri\build_uri;

echo convert_url2urn('data://text/plain;base64,SSBsb3ZlIFBIUAo=')."\n";
echo convert_url2urn('zlib://archive.zip#dir/file.txt')."\n";
echo convert_url2urn('php://stdin')."\n";
~~~

The above example will output:

```PHP
data:text/plain;base64,SSBsb3ZlIFBIUAo='
zlib:archive.zip#dir/file.txt
php:stdin
```

If the URIs are parsed as-is, the parser will look for a host part which is not present. `convert_url2urn` accepts a second parameter to control the conversion:
 - null: Dont convert, except Formats from http://php.net/manual/en/wrappers.php (php://, compress.*://, zip://, zlib://, bzip2://, data://, glob://, phar://, rar://, ogg://, expect://)
 - true: convert all
 - false: convert nothing

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-travis]: https://travis-ci.org/peterpostmann/php-parse_uri

[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/peterpostmann/php-parse_uri/master.svg?style=flat-square

[1]: https://tools.ietf.org/html/rfc3986/