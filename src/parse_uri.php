<?php

namespace peterpostmann\uri;

/**
 * Return default components (same as parse_urL)
 */
define(__NAMESPACE__.'\PARSE_URI_DEFAULT', -1);

/**
 * Return composited components
 */
define(__NAMESPACE__.'\PARSE_URI_MORE', -2);

/**
 * Parse a URI and return its components
 *
 * @param string $uri              The URI to parse
 * @param int    $component        Specify one of PHP_URL_SCHEME, PHP_URL_HOST, PHP_URL_PORT, PHP_URL_USER,
 *                                 PHP_URL_PASS, PHP_URL_PATH, PHP_URL_QUERY or PHP_URL_FRAGMENT to retrieve
 *                                 just a specific URL component as a string (except when PHP_URL_PORT is given,
 *                                 in which case the return value will be an integer).
 *
 * @return array
 */
function parse_uri($uri, $component = null)
{
    $uri = ltrim($uri);

    // Check if URI has a protocol
    if (preg_match('%
        ^
        (

            (?<has_path>
                # match Windows Path like C:\
                (
                    (file:\/\/\/)?
                    (?<windows_path> [a-zA-Z]:(\/(?![\/])|\\\\)[^?]*)
                )
                #or
                |
                # match rfc3986 uri
                (
                    (?<has_scheme>
                        (?<scheme>[^:#\/?\\\\]+)
                        \:
                    )?
                    (?<has_host>
                        (\/\/|(?<is_smb_path>\\\\\\\\))
                        (?<authority>
                            (?<has_user> (?<user> [^:^#@]*)? (?<has_pass>: (?<pass> [^@]*))? @ )?
                            (?<host> ( \[ [^\]]+ \] | [^:^#\/\\\\]* ))
                            (?<has_port> : (?<port> [0-9]+)? )?
                        )
                    )?

                    # match until ? or # is found
                    (?<path> 
                        [^?#]*
                    )
                )
            )

            (?<has_query> \?(?<query>[^#]+)?)?
            (?<has_fragment> \#(?<fragment>.+)?)?
        )
        $
        %x', $uri, $matches)) {
        $protocol = isset($matches['scheme']) ? $matches['scheme'] : '';
        $document = $matches['has_path'];

        // Add file-scheme to absolute/relative files
        $isFileUri = (isset($matches['has_path']) && isset($matches['path']) &&
                            $matches['has_path']  && $matches['has_path'] == $matches['path']);

        // Add file-scheme to Windows and smb URIs and replace slashes
        $isWinUri  = (isset($matches['windows_path']) && $matches['windows_path']);
        $isSmbUri  = (isset($matches['is_smb_path']) && $matches['is_smb_path']);

        if ($isFileUri | $isWinUri | $isSmbUri) {
            if ($isWinUri) {
                $matches['path']   = str_replace('/', '\\', $matches['windows_path']);
            }
            if ($isSmbUri) {
                $matches['scheme'] = 'file';
                $matches['path']   = str_replace('\\', '/', $matches['path']);
            }

            // Decode
            $matches['path'] = rawurldecode($matches['path']);

            $document = $matches['path'];
            
            if ($isFileUri && isset($document[0]) && $document[0] != '/') {
                $protocol = false;
            } else {
                $protocol = 'file';
            }
        }

        // Convert port from string to int
        if (isset($matches['has_port'])) {
            $matches['port'] = intval($matches['port']) ?: $matches['port'];
        }

        $result = [];
        $schema = [
            'scheme'    => null,
            'host'      => null,
            'port'      => null,
            'user'      => null,
            'pass'      => null,
            'path'      => null,
            'query'     => null,
            'fragment'  => null
        ];

        // Build Result
        foreach ($schema as $key => $value) {
            if ((isset($matches[$key]) && $matches[$key])) {
                $result[$key] = $matches[$key];
            } elseif (isset($matches['has_'.$key]) && $matches['has_'.$key]
                 && (!isset($result[$key]) || !$result[$key])) {
                $result[$key] = '';
            }
        }
    } else {
        return false;
    }
    
    if ($component === null || $component < 0) {
        // Default option
        if ($component === null) {
            $component = constant(__NAMESPACE__.'\PARSE_URI_MORE');
        }

        // Default Components
        if ($component === constant(__NAMESPACE__.'\PARSE_URI_DEFAULT')) {
            return $result;
        }

        // More Components
        if ($component === constant(__NAMESPACE__.'\PARSE_URI_MORE')) {
            $result += $schema;

            $hasUserInfo  = isset($matches['has_user'])  && $matches['has_user'];
            $hasAuthority = isset($matches['authority']) && $matches['authority'];
            $hasDocument  = isset($matches['has_path'])  && $matches['has_path'];
            $hasQuery     = isset($result['query']);
            $hasRessource = $hasDocument | $hasQuery;
            $hasFragment  = isset($result['fragment']);
            $hasUri       = $hasRessource | $hasFragment;
            
            $ressource = $document.( $hasQuery    ? '?'.$result['query']    : '');
            $uri       = $ressource.($hasFragment ? '#'.$result['fragment'] : '');
            
            $result['_protocol']  = $hasDocument ? $protocol : null;
            $result['_userinfo']  = $hasUserInfo ? $matches['has_user'] : null;
            $result['_authority'] = $hasAuthority ? $matches['authority'] : null;
            $result['_document']  = $hasDocument ? $document : null;
            $result['_ressource'] = $hasRessource ? $ressource : null;
            
            $result['_uri']       = $hasUri       ? $uri       : '';

            return $result;
        }
    } else {
        $result += $schema;

        switch ($component) {
            case PHP_URL_SCHEME:
                return $result['scheme'];
            case PHP_URL_HOST:
                return $result['host'];
            case PHP_URL_PORT:
                return $result['port'];
            case PHP_URL_USER:
                return $result['user'];
            case PHP_URL_PASS:
                return $result['pass'];
            case PHP_URL_PATH:
                return $result['path'];
            case PHP_URL_QUERY:
                return $result['query'];
            case PHP_URL_FRAGMENT:
                return $result['fragment'];
        }
    }
    
    return null;
}


/**
 * Converts a URL without host to a URN
 *
 * @param string $uri              The URI to parse
 * @param bool   $convertUrlToUrn  Defaults to false, except for
 *                                 php://, compress.*://, zip://, data://, glob://, phar://, rar://, ogg://, expect://
 *
 * @return array
 */
function convert_url2urn($uri, $convertUrlToUrn = null)
{
    $match =    ($convertUrlToUrn === null || $convertUrlToUrn) ?
                    preg_match('%^(?<scheme>'.($convertUrlToUrn === null ?
                    '(?<protocol_php>php|zip|zlib|bzip2|data|glob|phar|rar|ogg|expect|compress\.?(?<wrapper>' : '((').
                    '[^:#\/?]*)?)\:)\/\/(?<path>.*)$%', $uri, $matches)
                : false;

    return $match ? $matches['scheme'].$matches['path'] : $uri;
}

/**
 * Build a URI from its components
 *
 * @param array $components
 * @return string
 */
function build_uri($components)
{
    $hasScheme    = isset($components['scheme']) && $components['scheme'];
    $hasHost      = isset($components['host']);
    $hasPort      = isset($components['port']);
    $hasUser      = isset($components['user']);
    $hasPass      = isset($components['pass']);
    $hasAuthority =  $hasHost | $hasPort | $hasUser | $hasPass;
    $hasPath      = isset($components['path']);
    $hasQuery     = isset($components['query']);
    $hasFragment  = isset($components['fragment']);
    
    // If Windows Uri with fragment, add query, because '#' is a valid char for windwos paths
    if (!$hasScheme && !$hasAuthority && $hasPath && !$hasQuery && $hasFragment &&
        preg_match('/^(?<windows_path> [a-zA-Z]:(\/(?![\/])|\\\\)[^?]*)$/x', $components['path'])) {
        $hasQuery = true;
        $components['query'] = '';
    }

    return  ($hasScheme    ?     $components['scheme'].':' :'').
            ($hasAuthority ? '//'.
            ($hasUser      ?     $components['user'].
            ($hasPass      ? ':'.$components['pass']      : '').'@' : '').
            ($hasHost      ?     $components['host']      : '').
            ($hasPort      ? ':'.$components['port']      : '') : '').
            ($hasPath      ?     $components['path']      : '').
            ($hasQuery     ? '?'.$components['query']     : '').
            ($hasFragment  ? '#'.$components['fragment']  : '');
}
