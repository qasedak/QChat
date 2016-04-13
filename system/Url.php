<?php

/**
 * Based on HTTP_UrlSinger
 */
class HTTP_ParamSigner
{

    /**
     * Replacements to convert base64 encoded string to safe URL.
     *
     * @var array
     */
    private $_safeBase64 = array(array('+', '/', "="), array('_', '~', '-'));
    /**
     * Secret code to make digital signature.
     *
     * @var string
     */
    private $_secret;

    /**
     *
     * @param string $secret
     * @param string $baseUrl
     */
    public function __construct($secret)
    {
        $this->_secret = $secret;
    }

    /**
     * Builds URL with data is token mixed in.
     *
     * @param array $params
     * @return string
     */
    public function buildParam(array $params)
    {
        $token = http_build_query($params);
        if (function_exists('gzdeflate'))
        {
            $deflated = gzdeflate($token);
            if ($this->_strlen($deflated) < $this->_strlen($token))
            {
                $token = "z" . $deflated;
            }
            else
            {
                $token = "a" . $token;
            }
        }
        else
        {
            $token = "a" . $token;
        }
        $token = base64_encode($token);
        $token = str_replace($this->_safeBase64[0], $this->_safeBase64[1], $token);
        $token = join("/", str_split($token, 80));
        $token = $this->_hash($this->_secret . $token) . "/" . $token;

        return $token;
    }

    /**
     * Parses passed URL and return extracted data items.
     *
     * @param string $url
     * @return array
     */
    public function parseParam($token)
    {
        $token = trim($token);
        if ($token == '')
        {
            return array();
        }
        @list ($sign, $token) = explode("/", $token, 2);
        $ok = $this->_hash($this->_secret . $token) === $sign;
        if (!$ok)
        {
            throw new ElfException(tr('Warning! This link outdated or probably composed fraudsters.'));
        }
        $token = str_replace('/', '', $token);
        $token = str_replace($this->_safeBase64[1], $this->_safeBase64[0], $token);
        $token = @base64_decode($token);
        if (!$token)
        {
            throw new ElfException("Invalid URL token encoding");
        }
        if (@$token[0] == "z")
        {
            $token = gzinflate($this->_substr($token, 1));
        }
        else
        {
            $token = $this->_substr($token, 1);
        }
        $params = null;
        parse_str($token, $params);
        return $params;
    }

    protected function _hash($data)
    {
        return md5($this->_secret . $data);
    }

    private function _strlen($s)
    {
        return function_exists('mb_orig_strlen') ? mb_orig_strlen($s) : strlen($s);
    }

    private function _substr($s, $from, $len = null)
    {
        if ($len !== null)
        {
            return function_exists('mb_orig_substr') ? mb_orig_substr($s, $from, $len) : substr($s, $from, $len);
        }
        else
        {
            return function_exists('mb_orig_substr') ? mb_orig_substr($s, $from) : substr($s, $from);
        }
    }

}
?>
