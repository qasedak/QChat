<?php

// Future-friendly json_encode
if (!function_exists('json_encode'))
{

    function json_encode($data)
    {
        $json = new Services_JSON();
        return( $json->encode($data) );
    }

}

// Future-friendly json_decode
if (!function_exists('json_decode'))
{

    function json_decode($data)
    {
        $json = new Services_JSON();
        return( $json->decode($data) );
    }

}
?>
