<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dwiding
 * Date: 3/28/12
 * Time: 1:32 PM
 * To change this template use File | Settings | File Templates.
 */

class junto_loader{
    /**
     * @static
     * @param $filepath
     */
    public static function LoadJuntoMVC($filepath=null){
        require_once("junto-framework/junto-controller.php");
        require_once("junto-framework/junto-view.php");
        require_once("junto-framework/junto-model.php");
        require_once(LIB_PATH . "/php-object-generator/configuration.php");
        require_once(LIB_PATH . "/php-object-generator/objects/class.database.php");
        require_once(LIB_PATH . "/php-object-generator/objects/class.pog_base.php");
        if ($filepath!=null){
            new ThemeMvcClassLoader($filepath);
        }
    }

    /**
     * @static
     * @param null $loginAction
     * @param bool $includePhpSdk
     */
    public static function LoadFbApiLoader($loginAction=null, $includePhpSdk=true){
        require_once("facebook-api/load-fb-js.php");
        facebook_javascript_loader::init_loader_wordpress($loginAction);
        if($includePhpSdk){
            require_once(LIB_PATH . "/facebook-php-sdk/src/facebook.php");
            require_once("junto-generic-login-integration/facebook/facebook-adapter.php");
            facebook_adapter::getfb();
        }
    }

    /**
     * @static
     */
    public static function loadJuntoFunctions(){
        require_once("junto-misc-functions/default.php");
    }


    /**
     * @static
     */
    public static function loadCustomThemeOptions(){
        require_once("junto-misc-functions/theme-options.php");
    }

}

/**
 * This class is meant to be used as an autoloader for controllers, views and models
 */
class ThemeMvcClassLoader{
    /**
     * @the full file path to the theme
     */
    protected $filepath;

    /**
     * @var null
     */
    protected $classToPathArray;

    /**
     * Constructs and registers the loader no other function required
     * @param $filepath full file path to the theme path
     * @param null $predefinedClasses any classes and their associated relativepaths from the $filepath or from root
     */
    public function __construct($filepath, $predefinedClasses=null){
        $this->filepath = $filepath;
        $this->classToPathArray = $predefinedClasses;
        spl_autoload_register(array($this, 'ThemeMVCAutoloader'));
    }

    /**
     * This function gets passed to spl_autoload_register
     * @param $classname
     * @return bool success or fail
     */
    private function ThemeMVCAutoloader($classname){
        if(key_exists($classname,$this->classToPathArray)){
            if ($this->classToPathArray[$classname][0]=='/')
                require ($this->classToPathArray[$classname]);
            else
                require ($this->filepath . $this->classToPathArray[$classname]);
            return true;
        }
        $requirepath = $this->filepath;
        if(stristr($classname, "controller"))
            $requirepath.="/controllers";
        else if(stristr($classname, "model"))
            $requirepath.="/models";
        else if(stristr($classname, "class."))
            $requirepath.="/models/class.";
        else if(stristr($classname, "view"))
            $requirepath.="/views";
        if(strcasecmp(substr($classname,0,4), 'mock')==0)
            $requirepath.="/mockobjects";
        $requirepath.= "/{$classname}.php";
        if(file_exists($requirepath)){
            require $requirepath;
            return true;
        }
        $files = glob("{$this->filepath}/*/{$classname}.php");
        foreach($files as $file){
            require $file;
            if (next($files)===false)
                return true;
        }

        return false;
    }
}