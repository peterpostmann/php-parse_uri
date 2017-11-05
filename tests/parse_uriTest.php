<?php

use function peterpostmann\uri\parse_uri;
use function peterpostmann\uri\build_uri;
use function peterpostmann\uri\convert_url2urn;

class Parse_uriTest extends \PHPUnit_Framework_TestCase
{
    public function phpnetExampleUris()
    {
        return [
            [
                'file:///path/to/file.ext',
                [
                    'scheme' => 'file',
                    'host' => '',
                    'path' => '/path/to/file.ext'
                ],
                true,
                'file'
            ],
            [
                'http://example.com',
                [
                    'scheme' => 'http',
                    'host' => 'example.com',
                    'path' => ''
                ],
                true,
                'http'
            ],
            [
                'http://example.com/file.php?var1=val1&var2=val2',
                [
                    'scheme' => 'http',
                    'host' => 'example.com',
                    'path' => '/file.php',
                    'query' => 'var1=val1&var2=val2'
                ],
                true,
                'http'
            ],
            [
                'http://user:password@example.com',
                [
                    'scheme' => 'http',
                    'host' => 'example.com',
                    'user' => 'user',
                    'pass' => 'password',
                    'path' => ''
                ],
                true,
                'http'
            ],
            [
                'https://example.com',
                [
                    'scheme' => 'https',
                    'host' => 'example.com',
                    'path' => ''
                ],
                true,
                'https'
            ],
            [
                'https://example.com/file.php?var1=val1&var2=val2',
                [
                    'scheme' => 'https',
                    'host' => 'example.com',
                    'path' => '/file.php',
                    'query' => 'var1=val1&var2=val2'
                ],
                true,
                'https'
            ],
            [
                'https://user:password@example.com',
                [
                    'scheme' => 'https',
                    'host' => 'example.com',
                    'user' => 'user',
                    'pass' => 'password',
                    'path' => ''
                ],
                true,
                'https'
            ],
            [
                '//www.example.com/path/to/soundstream.ogg',
                [
                    'host' => 'www.example.com',
                    'path' => '/path/to/soundstream.ogg',
                ],
                true,
                ''
            ],
            [
                'ftp://example.com/pub/file.txt',
                [
                    'scheme' => 'ftp',
                    'host' => 'example.com',
                    'path' => '/pub/file.txt'
                ],
                true,
                'ftp'
            ],
            [
                'ftp://user:password@example.com/pub/file.txt',
                [
                    'scheme' => 'ftp',
                    'host' => 'example.com',
                    'user' => 'user',
                    'pass' => 'password',
                    'path' => '/pub/file.txt'
                ],
                true,
                'ftp'
            ],
            [
                'ftps://example.com/pub/file.txt',
                [
                    'scheme' => 'ftps',
                    'host' => 'example.com',
                    'path' => '/pub/file.txt'
                ],
                true,
                'ftps'
            ],
            [
                'ftps://user:password@example.com/pub/file.txt',
                [
                    'scheme' => 'ftps',
                    'host' => 'example.com',
                    'user' => 'user',
                    'pass' => 'password',
                    'path' => '/pub/file.txt'
                ],
                true,
                'ftps'
            ],
            [
                'ssh2.shell://user:pass@example.com:22/xterm',
                [
                    'scheme' => 'ssh2.shell',
                    'host' => 'example.com',
                    'port' => 22,
                    'user' => 'user',
                    'pass' => 'pass',
                    'path' => '/xterm'
                ],
                true,
                'ssh2.shell'
            ],
            [
                'ssh2.exec://user:pass@example.com:22/usr/local/bin/somecmd',
                [
                    'scheme' => 'ssh2.exec',
                    'host' => 'example.com',
                    'port' => 22,
                    'user' => 'user',
                    'pass' => 'pass',
                    'path' => '/usr/local/bin/somecmd'
                ],
                true,
                'ssh2.exec'
            ],
            [
                'ssh2.tunnel://user:pass@example.com:22/192.168.0.1:14',
                [
                    'scheme' => 'ssh2.tunnel',
                    'host' => 'example.com',
                    'port' => 22,
                    'user' => 'user',
                    'pass' => 'pass',
                    'path' => '/192.168.0.1:14'
                ],
                true,
                'ssh2.tunnel'
            ],
            [
                'ssh2.sftp://user:pass@example.com:22/path/to/filename',
                [
                    'scheme' => 'ssh2.sftp',
                    'host' => 'example.com',
                    'port' => 22,
                    'user' => 'user',
                    'pass' => 'pass',
                    'path' => '/path/to/filename'
                ],
                true,
                'ssh2.sftp'
            ],
            // File scheme
            [
                'file:///foo/bar',
                [
                    'scheme' => 'file',
                    'host' => '',
                    'path' => '/foo/bar'
                ],
                true,
                'file'
            ],
            [
                'news:comp.infosystems.www.servers.unix',
                [
                    'scheme' => 'news',
                    'path' => 'comp.infosystems.www.servers.unix'
                ],
                true,
                'news'
            ],
            [
                'vfs:///somefile',
                [
                    'scheme' => 'vfs',
                    'host' => '',
                    'path' => '/somefile'
                ],
                true,
                'vfs'
            ],
            [
                '/path/to/file.ext',
                [
                    'path' => '/path/to/file.ext'
                ],
                true,
                'file'
            ],
            [
                'relative/path/to/file.ext',
                [
                    'path' => 'relative/path/to/file.ext'
                ],
                true,
                false
            ],
            [
                'fileInCwd.ext',
                [
                    'path' => 'fileInCwd.ext'
                ],
                true,
                false
            ],
            [
                'C:/path/to/winfile.ext',
                [
                    'path' => 'C:\path\to\winfile.ext'
                ],
                false,
                'file'
            ],
            [
                'C:\path\to\winfile.ext',
                [
                    'path' => 'C:\path\to\winfile.ext'
                ],
                true,
                'file'
            ],
            [
                '\\\\smbserver\share\path\to\winfile.ext',
                [
                    'scheme' => 'file',
                    'host' => 'smbserver',
                    'path' => '/share/path/to/winfile.ext'
                ],
                false,
                'file'
            ],
            [
                'file://smbserver/share/path/to/winfile.ext',
                [
                    'scheme' => 'file',
                    'host' => 'smbserver',
                    'path' => '/share/path/to/winfile.ext'
                ],
                false,
                'file'
            ],
            [
                '?query',
                [
                    'query' => 'query'
                ],
                true,
                null
            ],
            [
                '#fragment',
                [
                    'fragment' => 'fragment'
                ],
                true,
                null
            ]
        ];
    }

    // initaly from https://github.com/thephpleague/uri-parser/blob/master/tests/ParserTest.php
    public function validUriProvider()
    {
        return [
            'complete URI' => [
                'scheme://user:pass@host:81/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'host' => 'host',
                    'port' => 81,
                    'user' => 'user',
                    'pass' => 'pass',
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI is not normalized' => [
                'ScheMe://user:pass@HoSt:81/path?query#fragment',
                [
                    'scheme' => 'ScheMe',
                    'host' => 'HoSt',
                    'port' => 81,
                    'user' => 'user',
                    'pass' => 'pass',
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI without scheme' => [
                '//user:pass@HoSt:81/path?query#fragment',
                [
                    'host' => 'HoSt',
                    'port' => 81,
                    'user' => 'user',
                    'pass' => 'pass',
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI without empty authority only' => [
                '//',
                [
                    'host' => '',
                    'path' => '',
                ],
                true
            ],
            'URI without userinfo' => [
                'scheme://HoSt:81/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'host' => 'HoSt',
                    'port' => 81,
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI with empty userinfo' => [
                'scheme://@HoSt:81/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'host' => 'HoSt',
                    'port' => 81,
                    'user' => '',
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI without port' => [
                'scheme://user:pass@host/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'host' => 'host',
                    'user' => 'user',
                    'pass' => 'pass',
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI with an empty port' => [
                'scheme://user:pass@host:/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'host' => 'host',
                    'port' => '',
                    'user' => 'user',
                    'pass' => 'pass',
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI with an empty host and port' => [
                'scheme://user:pass@:/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'host' => '',
                    'port' => '',
                    'user' => 'user',
                    'pass' => 'pass',
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI with an empty user, host and port' => [
                'scheme://:pass@:/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'host' => '',
                    'port' => '',
                    'user' => '',
                    'pass' => 'pass',
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI with an empty user, pass, host and port' => [
                'scheme://:@:/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'host' => '',
                    'port' => '',
                    'user' => '',
                    'pass' => '',
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI with an empty host, port' => [
                'scheme://:/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'host' => '',
                    'port' => '',
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI with an empty user, host and port' => [
                'scheme://@:/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'host' => '',
                    'port' => '',
                    'user' => '',
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI without user info and port' => [
                'scheme://host/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'host' => 'host',
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'Windows URI with query and fragment' => [
                'C:\path\file#123\data?query#fragment',
                [
                    'path' => 'C:\path\file#123\data',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI with host IP' => [
                'scheme://10.0.0.2/p?q#f',
                [
                    'scheme' => 'scheme',
                    'host' => '10.0.0.2',
                    'path' => '/p',
                    'query' => 'q',
                    'fragment' => 'f',
                ],
                true
            ],
            'URI with scoped IP' => [
                'scheme://[fe80:1234::%251]/p?q#f',
                [
                    'scheme' => 'scheme',
                    'host' => '[fe80:1234::%251]',
                    'path' => '/p',
                    'query' => 'q',
                    'fragment' => 'f',
                ],
                true
            ],
            'URI without authority' => [
                'scheme:path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'path' => 'path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'single letter URI without authority' => [
                'C:windows?query#fragment',
                [
                    'scheme' => 'C',
                    'path' => 'windows',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI without authority and scheme' => [
                '/path',
                [
                    'path' => '/path',
                ],
                true
            ],
            'URI with empty host' => [
                'scheme:///path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'host' => '',
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI with empty host and without scheme' => [
                '///path?query#fragment',
                [
                    'host' => '',
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI without path' => [
                'scheme://[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]?query#fragment',
                [
                    'scheme' => 'scheme',
                    'host' => '[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]',
                    'path' => '',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI without path and scheme' => [
                '//[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]?query#fragment',
                [
                    'host' => '[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]',
                    'path' => '',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI without scheme with IPv6 host and port' => [
                '//[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]:42?query#fragment',
                [
                    'host' => '[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]',
                    'port' => 42,
                    'path' => '',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'complete URI without scheme' => [
                '//user@[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]:42?q#f',
                [
                    'host' => '[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]',
                    'port' => 42,
                    'user' => 'user',
                    'path' => '',
                    'query' => 'q',
                    'fragment' => 'f',
                ],
                true
            ],
            'URI without authority and query' => [
                'scheme:path#fragment',
                [
                    'scheme' => 'scheme',
                    'path' => 'path',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI with empty query' => [
                'scheme:path?#fragment',
                [
                    'scheme' => 'scheme',
                    'path' => 'path',
                    'query' => '',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI with query only' => [
                '?query',
                [
                    'query' => 'query',
                ],
                true
            ],
            'URI without fragment' => [
                'tel:05000',
                [
                    'scheme' => 'tel',
                    'path' => '05000',
                ],
                true
            ],
            'URI with empty fragment' => [
                'scheme:path#',
                [
                    'scheme' => 'scheme',
                    'path' => 'path',
                    'fragment' => '',
                ],
                true
            ],
            'URI with fragment only' => [
                '#fragment',
                [
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI with empty fragment only' => [
                '#',
                [
                    'fragment' => '',
                ],
                true
            ],
            'URI without authority 2' => [
                'path#fragment',
                [
                    'path' => 'path',
                    'fragment' => 'fragment',
                ],
                true
            ],
            'URI with empty query and fragment' => [
                '?#',
                [
                    'query' => '',
                    'fragment' => '',
                ],
                true
            ],
            'URI with absolute path' => [
                '/?#',
                [
                    'path' => '/',
                    'query' => '',
                    'fragment' => '',
                ],
                true
            ],
            'URI with absolute authority' => [
                'https://thephpleague.com./p?#f',
                [
                    'scheme' => 'https',
                    'host' => 'thephpleague.com.',
                    'path' => '/p',
                    'query' => '',
                    'fragment' => 'f',
                ],
                true
            ],
            'URI with absolute path only' => [
                '/',
                [
                    'path' => '/',
                ],
                true
            ],
            'URI with empty query only' => [
                '?',
                [
                    'query' => '',
                ],
                true
            ],
            'relative path' => [
                '../relative/path',
                [
                    'path' => '../relative/path',
                ],
                true
            ],
            'complex authority' => [
                'http://a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word@www.zend.com',
                [
                    'scheme' => 'http',
                    'host' => 'www.zend.com',
                    'user' => 'a_.!~*\'(-)n0123Di%25%26',
                    'pass' => 'pass;:&=+$,word',
                    'path' => '',
                ],
                true
            ],
            'complex authority without scheme' => [
                '//a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word@www.zend.com',
                [
                    'host' => 'www.zend.com',
                    'user' => 'a_.!~*\'(-)n0123Di%25%26',
                    'pass' => 'pass;:&=+$,word',
                    'path' => '',
                ],
                true
            ],
            'single word is a path' => [
                'http',
                [
                    'path' => 'http',
                ],
                true
            ],
            'URI scheme with an empty authority' => [
                'http://',
                [
                    'scheme' => 'http',
                    'host' => '',
                    'path' => '',
                ],
                true
            ],
            'single word is a path, no' => [
                'http:::/path',
                [
                    'scheme' => 'http',
                    'path' => '::/path',
                ],
                true
            ],
            'fragment with pseudo segment' => [
                'http://example.com#foo=1/bar=2',
                [
                    'scheme' => 'http',
                    'host' => 'example.com',
                    'path' => '',
                    'fragment' => 'foo=1/bar=2',
                ],
                true
            ],
            'empty string' => [
                '',
                [
                ],
                true
            ],
            'null' => [
                null,
                [
                ],
                false
            ],
            'complex URI' => [
                'htà+d/s:totot',
                [
                    'path' => 'htà+d/s:totot',
                ],
                true
            ],
            'scheme only URI' => [
                'http:',
                [
                    'scheme' => 'http',
                    'path' => '',
                ],
                true
            ],
            'single letter scheme only URI' => [
                'C:',
                [
                    'scheme' => 'C',
                    'path' => '',
                ],
                true
            ],
            'Windows URI' => [
                'C:\\',
                [
                    'path' => 'C:\\',
                ],
                true
            ],
            'Windows URI' => [
                'C:\\\\',
                [
                    'path' => 'C:\\\\',
                ],
                true
            ],
            'Windows URI' => [
                'C:/',
                [
                    'path' => 'C:\\',
                ],
                false
            ],
            'single letter URI scheme with an empty authority' => [
                'C://',
                [
                    'scheme' => 'C',
                    'host' => '',
                    'path' => '',
                ],
                true
            ],
            'RFC3986 LDAP example' => [
                'ldap://[2001:db8::7]/c=GB?objectClass?one',
                [
                    'scheme' => 'ldap',
                    'host' => '[2001:db8::7]',
                    'path' => '/c=GB',
                    'query' => 'objectClass?one',
                ],
                true
            ],
            'RFC3987 example' => [
                'http://bébé.bé./有词法别名.zh',
                [
                    'scheme' => 'http',
                    'host' => 'bébé.bé.',
                    'path' => '/有词法别名.zh',
                ],
                true
            ],
            'colon detection respect RFC3986 (1)' => [
                'http://example.org/hello:12?foo=bar#test',
                [
                    'scheme' => 'http',
                    'host' => 'example.org',
                    'path' => '/hello:12',
                    'query' => 'foo=bar',
                    'fragment' => 'test',
                ],
                true
            ],
            'colon detection respect RFC3986 (2)' => [
                '/path/to/colon:34',
                [
                    'path' => '/path/to/colon:34'
                ],
                true
            ],
            'scheme with hyphen' => [
                'android-app://org.wikipedia/http/en.m.wikipedia.org/wiki/The_Hitchhiker%27s_Guide_to_the_Galaxy',
                [
                    'scheme' => 'android-app',
                    'host' => 'org.wikipedia',
                    'path' => '/http/en.m.wikipedia.org/wiki/The_Hitchhiker%27s_Guide_to_the_Galaxy',
                ],
                true
            ],
            'Windows URI with spaces' => [
                'C:\\Documents and Settings\\user\\FileSchemeURIs.doc',
                [
                    'path' => 'C:\Documents and Settings\user\FileSchemeURIs.doc'
                ],
                true
            ],
            'Windows file URI with spaces' => [
                'file:///C:/Documents%20and%20Settings/user/FileSchemeURIs.doc',
                [
                    'path' => 'C:\Documents and Settings\user\FileSchemeURIs.doc'
                ],
                false
            ],
            'Windows URI with unicode char' => [
                'C:\\exampleㄓ.txt',
                [
                    'path' => 'C:\exampleㄓ.txt'
                ],
                true
            ],
            'Windows file URI with unicode char' => [
                'file:///C:/exampleㄓ.txt',
                [
                    'path' => 'C:\exampleㄓ.txt'
                ],
                false
            ],
            'Windows URI with special chars' => [
                'C:\ #%{}^`.txt',
                [   
                    'path' => 'C:\ #%{}^`.txt'
                ],
                true
            ],
            'Windows file URI with special chars' => [
                'file:///C:/%20%23%25%7B%7D%5E%60.txt',
                [   
                    'path' => 'C:\ #%{}^`.txt'
                ],
                false
            ]
        ];
    }

    public function trimTest()
    {
        return [

            'trim 1' => [
                '  http://example.com  ',
                [
                    'scheme' => 'http',
                    'host' => 'example.com  ',
                    'path' => ''
                ],
            ],
            'trim 2' => [
                ' ',
                [
                ],
            ],
            'trim 3' => [
                '   C:\  file  ',
                [
                    'path' => 'C:\  file  '
                ],
            ],
        ];
    }

    /**
     * @dataProvider phpnetExampleUris
     * @dataProvider validUriProvider
     * @dataProvider php_urns
     * @dataProvider trimTest
     */
    function test_result_matches_expected_results($uri, $expectedResult)
    {
        $this->assertSame($expectedResult, parse_uri($uri, peterpostmann\uri\PARSE_URI_DEFAULT));
    }

    /**
     * @dataProvider phpnetExampleUris
     * @dataProvider validUriProvider
     * @dataProvider php_urns
     */
    function test_reversal_matches_uri($uri, $expectedResult, $isReversible)
    {
        if($isReversible) $this->assertSame($uri, build_uri($expectedResult));
    }

    /**
     * @dataProvider phpnetExampleUris
     * @dataProvider validUriProvider
     * @dataProvider php_urns
     */
    function test_more_components_uri($uri, $expectedResult, $isReversible)
    {
        if($isReversible) {
            $this->assertSame($uri, parse_uri($uri)['_uri']);
        }
    }

    /**
     * @dataProvider phpnetExampleUris
     * @dataProvider php_urns
     */
    function test_more_components_protocol($uri, $expectedResult, $isReversible, $protocol)
    {
        if($protocol === null) {
            $this->assertFalse(isset(parse_uri($uri)['_protocol']));
        } else {
            $this->assertSame($protocol, parse_uri($uri)['_protocol']);
        }
    }

    /**
     * @dataProvider phpnetExampleUris
     * @dataProvider validUriProvider
     * @dataProvider php_urns
     */
    function test_there_is_only_but_always_a_path_if_there_is_a_protocol($uri)
    {
        $result = parse_uri($uri);

        $this->AssertTrue(isset($result['_protocol']) === isset($result['path']));
    }
    
    public function windows_paths_with_fragments()
    {
        return [
            'Windows path with fragment 1' => [
                [
                    'path' => 'C:\Documents and Settings\user\FileSchemeURIs.doc',
                    'query' => 'query',
                    'fragment' => 'fragment'
                ],
                'C:\Documents and Settings\user\FileSchemeURIs.doc?query#fragment',
                false
            ],
            'Windows path with fragment 2' => [
                [
                    'path' => 'C:\Documents and Settings\user\FileSchemeURIs.doc',
                    'query' => '',
                    'fragment' => 'fragment'
                ],
                'C:\Documents and Settings\user\FileSchemeURIs.doc?#fragment',
                false
            ],
            'Windows path with fragment 3' => [
                [
                    'path' => 'C:\Documents and Settings\user\FileSchemeURIs.doc',
                    'fragment' => 'fragment'
                ],
                'C:\Documents and Settings\user\FileSchemeURIs.doc?#fragment',
                false
            ],
        ];
    }
    
    /**
     * @dataProvider windows_paths_with_fragments
     */
    function test_it_converts_fragments_correctly_if_windows_path($components, $expectedResult)
    {
        $this->assertSame(build_uri($components), $expectedResult);
    }

    public function more_uri_components()
    {
        return [
            [
                'ssh2.sftp://user:pass@example.com:422/file.php?var1=val1&var2=val2#anchor',
                [
                    'scheme' => 'ssh2.sftp',
                    'host' => 'example.com',
                    'port' => 422,
                    'user' => 'user',
                    'pass' => 'pass',
                    'path' => '/file.php',
                    'query' => 'var1=val1&var2=val2',
                    'fragment' => 'anchor'
                ],
                [
                    '_protocol' => 'ssh2.sftp',
                    '_userinfo' => 'user:pass@',
                    '_authority' => 'user:pass@example.com:422',
                    '_document' => 'ssh2.sftp://user:pass@example.com:422/file.php',
                    '_ressource' => 'ssh2.sftp://user:pass@example.com:422/file.php?var1=val1&var2=val2',
                    '_uri' => 'ssh2.sftp://user:pass@example.com:422/file.php?var1=val1&var2=val2#anchor'
                ]
            ],
            [
                '//user:pass@example.com:422/file.php?var1=val1&var2=val2#anchor',
                [
                    'host' => 'example.com',
                    'port' => 422,
                    'user' => 'user',
                    'pass' => 'pass',
                    'path' => '/file.php',
                    'query' => 'var1=val1&var2=val2',
                    'fragment' => 'anchor'
                ],
                [
                    'scheme' => null,
                    '_protocol' => '',
                    '_userinfo' => 'user:pass@',
                    '_authority' => 'user:pass@example.com:422',
                    '_document' => '//user:pass@example.com:422/file.php',
                    '_ressource' => '//user:pass@example.com:422/file.php?var1=val1&var2=val2',
                    '_uri' => '//user:pass@example.com:422/file.php?var1=val1&var2=val2#anchor'
                ]
            ],
            [
                'C:\file.php?var1=val1&var2=val2#anchor',
                [
                    'path' => 'C:\file.php',
                    'query' => 'var1=val1&var2=val2',
                    'fragment' => 'anchor'
                ],
                [    
                    'scheme' => null,
                    'host' => null,
                    'port' => null,
                    'user' => null,
                    'pass' => null,
                    '_protocol' => 'file',
                    '_userinfo' => null,
                    '_authority' => null,
                    '_document' => 'C:\file.php',
                    '_ressource' => 'C:\file.php?var1=val1&var2=val2',
                    '_uri' => 'C:\file.php?var1=val1&var2=val2#anchor'
                ]
            ]
        ];
    }

    /**
     * @dataProvider more_uri_components
     */
    function test_more_components_result($uri, $components, $moreComponents)
    {
        $this->assertSame(parse_uri($uri), $components + $moreComponents);
    }

    public function uri_components()
    {
        return [
            [
                'ssh2.sftp://user:pass@example.com:422/file.php?var1=val1&var2=val2#anchor',
                [
                    'scheme' => 'ssh2.sftp',
                    'host' => 'example.com',
                    'port' => 422,
                    'user' => 'user',
                    'pass' => 'pass',
                    'path' => '/file.php',
                    'query' => 'var1=val1&var2=val2',
                    'fragment' => 'anchor'
                ]
            ]
        ];
    }

    /**
     * @dataProvider uri_components
     */
    function test_it_returns_scheme($uri, $components)
    {
        $this->assertSame(parse_uri($uri, PHP_URL_SCHEME), $components['scheme']);
    }

    /**
     * @dataProvider uri_components
     */
    function test_it_returns_host($uri, $components)
    {
        $this->assertSame(parse_uri($uri, PHP_URL_HOST), $components['host']);
    }

    /**
     * @dataProvider uri_components
     */
    function test_it_returns_port($uri, $components)
    {
        $this->assertSame(parse_uri($uri, PHP_URL_PORT), $components['port']);
    }

    /**
     * @dataProvider uri_components
     */
    function test_it_returns_user($uri, $components)
    {
        $this->assertSame(parse_uri($uri, PHP_URL_USER), $components['user']);
    }

    /**
     * @dataProvider uri_components
     */
    function test_it_returns_pass($uri, $components)
    {
        $this->assertSame(parse_uri($uri, PHP_URL_PASS), $components['pass']);
    }

    /**
     * @dataProvider uri_components
     */
    function test_it_returns_path($uri, $components)
    {
        $this->assertSame(parse_uri($uri, PHP_URL_PATH), $components['path']);
    }

    /**
     * @dataProvider uri_components
     */
    function test_it_returns_query($uri, $components)
    {
        $this->assertSame(parse_uri($uri, PHP_URL_QUERY), $components['query']);
    }

    /**
     * @dataProvider uri_components
     */
    function test_it_returns_fragment($uri, $components)
    {
        $this->assertSame(parse_uri($uri, PHP_URL_FRAGMENT), $components['fragment']);
    }

    public function php_urns()
    {
        return [
            [
                'php:stdin',
                [
                    'scheme' => 'php',
                    'path' => 'stdin'
                ],
                true,
                'php'
            ],
            [
                'php:fd/3',
                [
                    'scheme' => 'php',
                    'path' => 'fd/3'
                ],
                true,
                'php'
            ],
            [
                'compress.zlib:file.gz',
                [
                    'scheme' => 'compress.zlib',
                    'path' => 'file.gz'
                ],
                true,
                'compress.zlib'
            ],
            [
                'compress.zlib:http://www.example.com/myarchive.gz#dir/file.txt',
                [
                    'scheme' => 'compress.zlib',
                    'path' => 'http://www.example.com/myarchive.gz',
                    'fragment' => 'dir/file.txt'
                ],
                true,
                'compress.zlib'
            ],
            [
                'compress.bzip2:file.bz2',
                [
                    'scheme' => 'compress.bzip2',
                    'path' => 'file.bz2'
                ],
                true,
                'compress.bzip2'
            ],
            [
                'zip:archive.zip#dir/file.txt',
                [
                    'scheme' => 'zip',
                    'path' => 'archive.zip',
                    'fragment' => 'dir/file.txt'
                ],
                true,
                'zip'
            ],
            [
                'data:text/plain;base64,SSBsb3ZlIFBIUAo=',
                [
                    'scheme' => 'data',
                    'path' => 'text/plain;base64,SSBsb3ZlIFBIUAo='
                ],
                true,
                'data'
            ],
            [
                'glob:ext/spl/examples/*.php',
                [
                    'scheme' => 'glob',
                    'path' => 'ext/spl/examples/*.php'
                ],
                true,
                'glob'
            ],
            [
                'phar:my.phar/somefile.php',
                [
                    'scheme' => 'phar',
                    'path' => 'my.phar/somefile.php'
                ],
                true,
                'phar'
            ],
            [
                'rar:archive',
                [
                    'scheme' => 'rar',
                    'path' => 'archive'
                ],
                true,
                'rar'
            ],
            [
                'ogg:http://www.example.com/path/to/soundstream.ogg',
                [
                    'scheme' => 'ogg',
                    'path' => 'http://www.example.com/path/to/soundstream.ogg'
                ],
                true,
                'ogg'
            ],
            [
                'expect:command',
                [
                    'scheme' => 'expect',
                    'path' => 'command'
                ],
                true,
                'expect'
            ]
        ];
    }

    public function php_urn_urls()
    {
        return [
            [
                'php://stdin',
                [
                    'scheme' => 'php',
                    'path' => 'stdin'
                ]
            ],
            [
                'php://fd/3',
                [
                    'scheme' => 'php',
                    'path' => 'fd/3'
                ]
            ],
            [
                'compress.zlib://file.gz',
                [
                    'scheme' => 'compress.zlib',
                    'path' => 'file.gz'
                ]
            ],
            [
                'compress.zlib://http://www.example.com/myarchive.gz#dir/file.txt',
                [
                    'scheme' => 'compress.zlib',
                    'path' => 'http://www.example.com/myarchive.gz',
                    'fragment' => 'dir/file.txt'
                ]
            ],
            [
                'compress.bzip2://file.bz2',
                [
                    'scheme' => 'compress.bzip2',
                    'path' => 'file.bz2'
                ]
            ],
            [
                'zlib://archive.zip#dir/file.txt',
                [
                    'scheme' => 'zlib',
                    'path' => 'archive.zip',
                    'fragment' => 'dir/file.txt'
                ]
            ],
            [
                'bzip2://archive.zip#dir/file.txt',
                [
                    'scheme' => 'bzip2',
                    'path' => 'archive.zip',
                    'fragment' => 'dir/file.txt'
                ]
            ],
            [
                'zip://archive.zip#dir/file.txt',
                [
                    'scheme' => 'zip',
                    'path' => 'archive.zip',
                    'fragment' => 'dir/file.txt'
                ]
            ],
            [
                'data://text/plain;base64,SSBsb3ZlIFBIUAo=',
                [
                    'scheme' => 'data',
                    'path' => 'text/plain;base64,SSBsb3ZlIFBIUAo='
                ]
            ],
            [
                'glob://ext/spl/examples/*.php',
                [
                    'scheme' => 'glob',
                    'path' => 'ext/spl/examples/*.php'
                ]
            ],
            [
                'phar://my.phar/somefile.php',
                [
                    'scheme' => 'phar',
                    'path' => 'my.phar/somefile.php'
                ]
            ],
            [
                'rar://archive',
                [
                    'scheme' => 'rar',
                    'path' => 'archive'
                ]
            ],
            [
                'ogg://http://www.example.com/path/to/soundstream.ogg',
                [
                    'scheme' => 'ogg',
                    'path' => 'http://www.example.com/path/to/soundstream.ogg'
                ]
            ],
            [
                'expect://command',
                [
                    'scheme' => 'expect',
                    'path' => 'command'
                ]
            ]
        ];
    }

    /**
     * @dataProvider phpnetExampleUris
     * @dataProvider validUriProvider
     * @dataProvider php_urns
     * @dataProvider php_urn_urls
     */
    function test_php_url_to_urn_auto_conversion_converts_positive_match($uri, $expectedResult)
    {
        $result = parse_uri(convert_url2urn($uri), peterpostmann\uri\PARSE_URI_DEFAULT);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider phpnetExampleUris
     * @dataProvider validUriProvider
     * @dataProvider php_urns
     * @dataProvider php_urn_urls
     */
    function test_php_url_to_urn_auto_conversion_does_not_converts_negative_match($uri, $expectedResult)
    {
        $result = parse_uri(convert_url2urn($uri), peterpostmann\uri\PARSE_URI_DEFAULT);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider php_urn_urls
     */
    function test_php_url_to_urn_conversion_converts_if_true($uri, $expectedResult)
    {
        $result = parse_uri(convert_url2urn($uri, true), peterpostmann\uri\PARSE_URI_DEFAULT);


        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider php_urn_urls
     */
    function test_php_url_to_urn_conversion_does_not_converts_if_false($uri, $expectedResult)
    {
        $result = parse_uri(convert_url2urn($uri, false), peterpostmann\uri\PARSE_URI_DEFAULT);

        $this->assertNotEquals($expectedResult, $result);
    }
    
    public function patch_uri()
    {
        return [
            [
                'ssh2.sftp://user:pass@example.com:422/file.php?var1=val1&var2=val2#anchor',
                [
                    'path' => '/file2.php',
                ],
                'ssh2.sftp://user:pass@example.com:422/file2.php?var1=val1&var2=val2#anchor'
            ]
        ];
    }

    /**
     * @dataProvider patch_uri
     */
    function test_patch_uri($uri, $patch, $expectedResult)
    {
        $result = build_uri($patch + parse_uri($uri));

        $this->assertSame($expectedResult, $result);
    }
}
