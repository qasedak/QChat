<?php
/**
 *
 *
 * Lang functions
 *
 *
 */

function tr($key)
{
    return Elf::Get('lang')->get($key);
}

function format($text, $param = '')
{
    if (is_array($param))
    {
        foreach ($param as $key => $value)
        {
            if (!is_array($value))
                $text = str_replace('%' . $key . '%', $value, $text);
        }
    }
    else
    {
        $text = str_replace('%%', $param, $text);
    }
    return $text;
}

/**
 *
 * 
 * View functions 
 * 
 * 
 */

function css($file)
{
    $path = View::get_skin()->css[$file];
    if($path != '')
        return '<link rel="stylesheet" type="text/css" href="' . $path . '">';
    else
        return '';
}

function imgpath()
{
    return View::get_skin()->img_path;
}

function call($file, $vars = array())
{
    $view = new View($file, $vars);
    $view->set_render_engine(new FunctionRender());
    return $view->render();
}

/**
 * Escape string with htmlspecialchars.
 * @param string $var
 * @return string
 */
function escape($var)
{
    return htmlspecialchars($var);
}

function url_params($params = array())
{
    if (defined('SECURE_LINKS'))
    {
        $signer = new HTTP_ParamSigner(Elf::Get(ADMIN_URL_SALT));
        return $signer->buildParam($params);
    }
    else
    {
        return http_build_query($params);
    }
}

function url($params = array(), $value = null)
{
    if($value !== null && is_string($params))
    {
        $params = array($params => $value);
    }
    return Elf::Get(BASE_URL) . url_params($params);
}

/**
 *
 *
 * SQL Where functions
 *
 */

function where($sql, $params)
{
    foreach ($params as $key => $value)
    {
        $param = mysql_real_escape_string($value);
        $sql = str_replace('?'.$key, $param, $sql);
    }
    return $sql;
}

?>
