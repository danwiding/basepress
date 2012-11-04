<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dwiding
 * Date: 3/28/12
 * Time: 1:32 PM
 * To change this template use File | Settings | File Templates.
 */

class junto_loader{

    public static function LoadConfiguration($repositoryRootPath = null){
        if($repositoryRootPath===null)
            $repositoryRootPath=dirname(dirname(__DIR__));
        if(!defined('AUTOMATED_TESTING'))
            define('AUTOMATED_TESTING', 'Off');
        if(!defined('REPO_PATH'))
            define('REPO_PATH', $repositoryRootPath  );
        if(!defined('LIB_PATH'))
            define('LIB_PATH', $repositoryRootPath . '/juntobasepress/lib');
        if(!defined('JUNTO_COMMON_PATH'))
            define('JUNTO_COMMON_PATH', $repositoryRootPath . '/juntobasepress/junto-common');
        if(!defined('ERRLOG_PATH'))
            define('ERRLOG_PATH', $repositoryRootPath . '/juntobasepress/error_log.log');
        if(!defined('POLY_THEME_PATH'))
            define('POLY_THEME_PATH', $repositoryRootPath . '/themes');
        require_once(JUNTO_COMMON_PATH . '/junto_exception_handler.php');
        require_once(JUNTO_COMMON_PATH . '/sensitive-config-loader.php');

        SensitiveConfigLoader(REPO_PATH . '/config/sensitive/wp-sensitive-local.json');
    }


    /**
     * @static
     * @param $filePath
     */
    public static function LoadJuntoMVC($filePath=null){
        foreach (glob(JUNTO_COMMON_PATH . "/junto-framework/*.php") as $fileName){
            require_once $fileName;
        }

        require_once(LIB_PATH . "/php-object-generator/configuration.php");
        require_once(LIB_PATH . "/php-object-generator/objects/class.database.php");
        require_once(LIB_PATH . "/php-object-generator/objects/class.pog_base.php");
        if(function_exists('add_filter'))
            add_filter( 'show_admin_bar', '__return_false' );
        require_once(JUNTO_COMMON_PATH . "/junto-misc-functions/JuntoUtilities.php");
//        require_once(JUNTO_COMMON_PATH . '/poly_baseline.php');
        if ($filePath!=null){
            new ThemeMvcClassLoader($filePath);
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

    /**
     * @static Creates a theme when the theme is activated
     * @param string $pageName Name of the page and it's associated url
     * @param string $templateFileName Name of the file that contains the template
     * @param string $pageContent content of the file
     * @param int $authorId the author to associate or null which will get the current id
     * @param array $postMeta An array of postMeta keys and values
     * @return int|WP_Error the id of the page
     */
    public static function CreatePageOnThemeActivation($pageName, $templateFileName=null, $pageContent = '', $post_parent = 0, $authorId=null, array $postMeta = array(), array $otherPageKeys = array()){
        if ((isset($_GET['activated']) && is_admin())||AUTOMATED_TESTING=='On'){


            //don't change the code bellow, unless you know what you're doing

            $page_check = get_page_by_path($post_parent ? "${post_parent}/${pageName}" : $pageName);
            $new_page = array_merge( $otherPageKeys, array(
                'post_type' => 'page',
                'post_title' => $pageName,
                'post_content' => $pageContent,
                'post_status' => 'publish',
                'post_author' => $authorId ? $authorId : get_current_user_id(),
                'post_parent' => $post_parent
            ));
            if(isset($page_check->ID)){
                return null;
            }
            else{
                $new_page_id = wp_insert_post($new_page);
                if(!empty($templateFileName)){
                    update_post_meta($new_page_id, '_wp_page_template', $templateFileName);
                }
                foreach ($postMeta as $postMetaKey => $postMetaValue){
                    update_post_meta($new_page_id, $postMetaKey, $postMetaValue);
                }
                return $new_page_id;
            }
        }
    }

}

/**
 * This class is meant to be used as an autoloader for controllers, views and models
 */
class ThemeMvcClassLoader{
    /**
     * @the full file path to the theme
     */
    protected $filePath;

    /**
     * @var null
     */
    protected $classToPathArray;

    /**
     * Constructs and registers the loader no other function required
     * @param $filePath full file path to the theme path
     * @param null $predefinedClasses any classes and their associated relativepaths from the $filePath or from root
     */
    public function __construct($filePath, $predefinedClasses=array()){
        $this->filePath = $filePath;
        $this->classToPathArray = $predefinedClasses;
        spl_autoload_register(array($this, 'ThemeMVCAutoloader'));
    }

    /**
     * This function gets passed to spl_autoload_register
     * @param $className
     * @return bool success or fail
     */
    private function ThemeMVCAutoloader($className){
        if(array_key_exists($className,$this->classToPathArray)){
            if ($this->classToPathArray[$className][0]=='/')
                require ($this->classToPathArray[$className]);
            else
                require ($this->filePath . $this->classToPathArray[$className]);
            return true;
        }

        $requirePath = $this->filePath;
        if(stristr($className, "controller"))
            $requirePath.="/__controllers";
        else if(stristr($className, "model"))
            $requirePath.="/__models";
        else if(stristr($className, "class."))
            $requirePath.="/__models/class.";
        else if(stristr($className, "view"))
            $requirePath.="/__views";
        else if(stristr($className, "helper"))
        	$requirePath.="/__helpers";
        if(strcasecmp(substr($className,0,4), 'mock')==0)
            $requirePath.="/mockobjects";
        if(stristr($className, "interface"))
            $requirePath.="/interfaces";
        $requirePath.= "/{$className}.php";
        if(file_exists($this->filePath . "/__models/pog-objects/{$className}.php")){
            require $this->filePath . "/__models/pog-objects/{$className}.php";
            return true;
        }
        if(file_exists($requirePath)){
            require $requirePath;
            return true;
        }
//        if($className=='VendorModel')
//            print_r($requirePath);

        $files = glob("{$this->filePath}/*/{$className}.php");
        foreach($files as $file){
            require $file;
            if (next($files)===false)
                return true;
        }


        return false;
    }
}