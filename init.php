<?php

error_reporting(E_ERROR);
// Paths and Includes
if(!defined('ELFCHAT_ROOT'))
{
    define('ELFCHAT_ROOT', dirname(__FILE__));
}
ini_set("include_path", ELFCHAT_ROOT);

// Version

$sub_version = "Pro";


define('ELFCHAT_VERSION', "1.1.1 $sub_version");

// Magic Quotes
$mqr = get_magic_quotes_runtime();
if($mqr)
{
    set_magic_quotes_runtime(0);
}

// Over Global Defines
define('BASE_URL', 'BASE_URL');
define('REQUEST_TYPE', 'REQUEST_TYPE');
define('ACTION_POST_HASH', 'ACTION_POST_HASH');
define('DEFAULT_RENDER', 'DEFAULT_RENDER');

// require system elements
require_once 'system/ActiveRecord.php';
require_once 'system/Ajax.php';
require_once 'system/Auth.php';
require_once 'system/Db.php';
require_once 'system/ElfRegistry.php';
require_once 'system/Functions.php';
require_once 'system/Inputs.php';
require_once 'system/Lang.php';
require_once 'system/Request.php';
require_once 'system/Skin.php';
require_once 'system/Sql.php';
require_once 'system/Settings.php';
require_once 'system/Validation.php';
require_once 'system/View.php';

if(!defined('NO_CONTROLER'))
{
    require_once 'controller/BaseController.php';
    Elf::Set(ACTION_POST_HASH, 'hash');
}

if(!defined('NO_MODEL'))
{
    require_once 'models/User.php';
    require_once 'models/Room.php';
    require_once 'models/Ban.php';
}

if(!defined('NO_REQUEST'))
{
    $requestType = new RequestType();
    Elf::Set(REQUEST_TYPE, & $requestType);

    // Init request for be able to use _GET(),_POST(),_COOKIE(),_REQUEST() functions
    Request::Init();
}

// Db Mysql Init
if(!defined('NO_DB'))
{
    require_once 'cache/config.php'; // require config
    $db = Db::Connect($_CONFIG);
    Elf::Set('db', & $db);
}

//View Init
if(!defined('NO_VIEW'))
{
    if(ini_get('short_open_tag') == 1)
        Elf::Set(DEFAULT_RENDER, new IncludeRender());
    else
        Elf::Set(DEFAULT_RENDER, new FunctionRender());
}

// Settings Init
if(!defined('NO_SETTINGS'))
{
    $settings = new Settings();
    $settings->LoadDefaultSettings(ELFCHAT_ROOT . '/settings.default.php');
    $settings->LoadSettings(ELFCHAT_ROOT . '/cache/settings.php');
    Elf::Set('settings', & $settings);

    if(!Elf::Settings('show_errors'))
    {
        error_reporting(0);
    }

    if(Elf::Settings('censure'))
    {
        include_once 'lib/Censure/Censure.php';
        include_once 'lib/Censure/ReflectionTypehint.php';
        include_once 'lib/Censure/UTF8.php';
    }


    // Skin Init
    if(!defined('NO_SKIN'))
    {
        if(!defined('SKIN_PATH'))
        {
            define('SKIN_PATH', ELFCHAT_ROOT . '/skin/' . Elf::Settings('skin'));
        }
        $skin = new Skin(SKIN_PATH);
        View::set_skin($skin);
    }

    // Date Init
    if(!defined('NO_DATE'))
    {
        date_default_timezone_set(Elf::Settings('timezone'));
    }
}

// Lang Init
if(!defined('NO_LANG'))
{
    if(!defined('LANG'))
    {
        try
        {
            define('LANG', Elf::Settings('lang'));
        }
        catch (ElfException $e)
        {
            define('LANG', 'fa');
        }
    }
    if(!defined('LANG_FILE'))
    {
        define('LANG_FILE', ELFCHAT_ROOT . '/lang/' . LANG . '/lang_general.php');
    }
    $lang = new Lang();
    $lang->load_file(LANG_FILE);
    Elf::Set('lang', & $lang);
}
?>
