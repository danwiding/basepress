<?php
/**
 *
 * Date: 5/11/12
 * Time: 2:14 PM
 *
 */
class JuntoUtilities
{
    /**
     * @param $var The variable to be displayed
     * @param bool $dump Flag to determine fi the var_dump function should be used.
     */
    public static function print_nice($var, $dump = false){
        echo '<pre>';
        if ($dump)
            var_dump($var);
        else
            print_r($var);
        echo '</pre>';
    }

    public static function force_ssl(){
        if (!is_ssl() && defined('VIA_ENVIRONMENT') && (VIA_ENVIRONMENT == 'prod' || (defined('FORCE_SSL_LOGIN')&&FORCE_SSL_LOGIN==true))){
            if (0 === strpos($_SERVER['REQUEST_URI'], 'http')){
                wp_redirect(preg_replace('|^http://|', 'https://', $_SERVER['REQUEST_URI']));
                exit();
            }
            else{
                wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
                exit();

            }
        }
    }
}
