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
}
