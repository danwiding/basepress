<?php
/**
 *
 * Date: 5/8/12
 * Time: 9:15 AM
 *
 */
class RewriteRulesModule
{
    /**
     * @var array private array containing the new rules that will be added
     */
    private $newRewriteRules;
    private $newQueryVars;

    /**
     * This function will automatically add the rules to wordpress on construction
     * @param array $newRules Are the rules that will be added to worpdress
     */
    public function __construct(array $newRules, array $allNewQueryVars=array()){
        $this->newRewriteRules = $newRules;
        $this->newQueryVars = $allNewQueryVars;
        add_filter( 'generate_rewrite_rules', array($this, 'AddJuntoRulesToWordpress') );
        add_filter( 'query_vars',array($this, 'AddNewQueryVariables'));
        if (isset($_GET['activated']) && is_admin()){
            global $wp_rewrite;
            $wp_rewrite->flush_rules();
        }
    }

    /**
     * @param $wp_rewrite
     */
    public function AddJuntoRulesToWordpress($wp_rewrite){
        $wp_rewrite->rules = $this->newRewriteRules + $wp_rewrite->rules;
    }
    public function AddNewQueryVariables($vars){
        return array_merge($vars, $this->newQueryVars);
    }
}
