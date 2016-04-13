<?php

class View
{

    /**
     * Path where view files exist.
     * @var string
     */
    private static $view_path = './';
    /**
     * Vars to tmp extract.
     * @var array
     */
    private $vars = array();
    /**
     * Layout for recursive calling of view.
     * If null, dont use layout.
     * @var View
     */
    private $layout = null;
    /**
     * Vars to use in over layouts.
     * First index is layout name, second is vars.
     * @var array
     */
    private $layout_vars = array(array());
    /**
     * Name of var collecing by functions begin/end.
     * Use ob_* funtions.
     * @var string
     */
    private $output_name = 'content';
    /**
     * Name of current tmp.
     * @var string
     */
    private $file = '';
    /**
     * Render engine.
     * @var AbstractRender
     */
    private $engine = null;
    /**
     * If not null, this will be return by render() if file does not exist.
     * @var string
     */
    private $default = null;
    /**
     * Auto escape of input vars.
     * @var boolean
     */
    private $autoescape = false;
    /**
     * Using skin in all of View's example.
     * @var Skin
     */
    private static $skin = null;

    public function __construct($_file, $_vars = array())
    {
        $this->vars = $_vars;
        $this->file = $_file;
        $this->engine = Elf::Get(DEFAULT_RENDER);
    }

    public function set_render_engine($engine)
    {
        $this->engine = $engine;
    }

    /**
     *
     * @return Skin
     */
    public static function get_skin()
    {
        return self::$skin;
    }

    public static function set_skin(Skin & $skin)
    {
        self::$skin = $skin;
        self::$view_path = self::$skin->view_path;
    }

    public static function set_view_path($path)
    {
        self::$view_path = $path;
    }

    public static function get_include($file, $path = null)
    {
        $path = $path === null ? self::$view_path : $path;
        $viewreplace = self::$skin === null ? null : self::$skin->view_replace;
        if($viewreplace === null)
        {
            return $path . '/' . $file . '.tmp.php';
        }
        else
        {
            if(isset($viewreplace[$file]))
            {
                return ELFCHAT_ROOT . '/' . $viewreplace[$file] . '.tmp.php';
            }
            else
            {
                return $path . '/' . $file . '.tmp.php';
            }
        }
    }

    public function set_vars($_vars)
    {
        $this->vars = $_vars;
    }

    public function add_vars($_vars)
    {
        $this->vars = array_merge($this->vars, $_vars);
    }

    public function __set($name, $value)
    {
        $this->vars[$name] = $value;
    }

    public function &__get($name)
    {
        return $this->vars[$name];
    }

    public function set_default($default)
    {
        $this->default = $default;
    }

    public function set_autoescape($autoescape)
    {
        $this->autoescape = $autoescape;
    }

    public function render()
    {
        $_include = self::get_include($this->file);
        if(file_exists($_include))
        {
            // Apply htmlspecialchars to all strings and arrays.
            if($this->autoescape)
                $this->escape($this->vars);

            // Pass current view to tmp.
            $this->vars['view'] = &$this;

            // Render tmp using render engine.
            $html = $this->engine->render($_include, $this->vars);

            // If layout is setted, render it.
            if($this->layout !== null)
            {
                // Add vars for layout from layout_vars array.
                if(array_key_exists($this->layout->file, $this->layout_vars))
                    $this->layout->add_vars($this->layout_vars[$this->layout->file]);

                // Pass layout_vars deeper.
                $this->layout->layout_vars = &$this->layout_vars;

                // If no content puted in, use as content html to current view.
                if(!array_key_exists('content', $this->layout->vars))
                    $this->layout->content = $html;

                // Render and replace current view by layout view.
                $html = $this->layout->render();
            }
            return $html;
        }
        else
        {
            if($this->default === null)
                throw new Exception("No view file: $_include.");
            else
                return $this->default;
        }
    }

    /**
     * Set and/or return layout for this view tmp.
     * @param string $file
     * @return View
     */
    public function layout($file = null)
    {
        // Creating of new view for layout.
        if($this->layout === null)
        {
            if($file !== null)
                $this->layout = new self($file);
            else
                throw new Exception("Layout has not been appointed.");
        }

        // Recreating of view for layout
        if($file !== null && $this->layout !== null)
        {
            if($file !== $this->layout->file)
                $this->layout = new self($file);
        }

        return $this->layout;
    }

    public function begin($name = 'content')
    {
        $this->output_name = $name;
        $tmp = ob_get_clean();
        ob_start();
    }

    public function end()
    {
        $output = ob_get_clean();
        ob_start();
        $this->add_vars(array($this->output_name => $output));
    }

    public function set_layout_vars($layout, $vars)
    {
        $this->layout_vars[$layout] = $vars;
    }

    /**
     * Decorator for setting layout vars.
     * @param string $layout
     * @return VarsForView
     */
    public function set_for($layout)
    {
        return new VarsForView($this->layout_vars[$layout]);
    }

    public function escape(& $array)
    {
        foreach($array as &$var)
        {
            if(is_string($var))
                $var = htmlspecialchars($var);
            else if(is_array($var))
                $this->escape($var);
        }
    }

    public static function Call($file, $vars = array())
    {
        $view = new self($file);
        $view->set_default('');
        $view->set_vars($vars);
        return $view->render();
    }

}

class VarsForView
{

    private $vars;

    public function __construct(& $array)
    {
        $this->vars = & $array;
    }

    public function set_vars($_vars)
    {
        $this->vars = $_vars;
    }

    public function add_vars($_vars)
    {
        $this->vars = array_merge($this->vars, $_vars);
    }

    public function __set($name, $value)
    {
        $this->vars[$name] = $value;
    }

}

/**
 * Abscract class to describe the functionality of renders.
 */
abstract class AbstractRender
{

    abstract public function render($_include, $_vars);
}

class IncludeRender extends AbstractRender
{

    public function render($_include, $_vars)
    {
        ob_start();

        extract($_vars);

        include ($_include);

        return ob_get_clean();
    }

}

class FunctionRender extends AbstractRender
{

    static private $func = array();

    public function render($_include, $_vars)
    {
        if(!isset(self::$func[$_include]))
        {
            $code = file_get_contents($_include, true);

            /**
             * Convet short tags (<?=, <?) to full form (<?php echo, <?php)
             */
            $code = preg_replace('/\<\?\=/', "<?php echo ", $code);
            $code = preg_replace('/<\?(?!xml|php)/s', '<?php ', $code);

            /**
             * Create function and save to func array.
             */
            $code = 'extract($_vars); ob_start(); ?>' . $code . '<?php return ob_get_clean();';
            self::$func[$_include] = create_function('$_vars', $code);
        }
        $f = self::$func[$_include];
        return $f($_vars);
    }

}

class PregRender extends AbstractRender
{

    static private $func = array();

    public function render($_include, $_vars)
    {
        if(!isset(self::$func[$_include]))
        {
            $code = file_get_contents($_include, true);

            /**
             * {% code %}    -->     <?php code ?>
             */
            $code = preg_replace('/{%([^{}]+)%}/', "<?php \\1 ?>", $code);

            /**
             * {$var}    -->     <?php echo $var; ?>
             */
            $code = preg_replace('/{([^{}]+)}/', "<?php echo \\1; ?>", $code);

            /**
             * Create function and save to func array.
             */
            $code = 'extract($_vars); ob_start(); ?>' . $code . '<?php return ob_get_clean();';
            self::$func[$_include] = create_function('$_vars', $code);
        }
        $f = self::$func[$_include];
        return $f($_vars);
    }

}

?>
