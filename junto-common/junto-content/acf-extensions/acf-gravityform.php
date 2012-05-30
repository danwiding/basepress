<?php


/*
 *  Advanced Custom Fields - Gravity Form Select
 *
 *  Author: Jonathan Christopher mondaybynoon.com
 *
 */

class GravityForm_field extends acf_Field
{
    function __construct($parent)
    {
        // do not delete!
        parent::__construct($parent);

        // set name / title
        $this->name = 'gravityform';
        $this->title = __("Gravity Form", 'acf');

    }

    function create_field($field)
    {
        if (class_exists('RGFormsModel')) {
            $forms = RGFormsModel::get_forms(1, "title");
            if (count($forms) > 0) {
                ?>
            <select id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" class="<?php echo $field['class']; ?> <?php echo $field['name']; ?>">
                <option value="null">-- Select --</option>
                <?php foreach ($forms as $form) : ?>
                <?php
                $selected = '';
                if ($form->id == $field['value']) {
                    $selected = 'selected';
                }
                ?>
                <option value="<?php echo $form->id; ?>"<?php echo $selected; ?>><?php echo $form->title; ?></option>
                <?php endforeach ?>
            </select>
            <?php
            }
            else
            {
                ?>
            <input type="hidden" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="null"
                   style="display:none;"/>
            <p><?php _e( 'There are no forms available at this time. You may <a href="admin.php?page=gf_new_form">create one</a>
                now (<strong>warning</strong> entered data <em>will be lost</em> unless you save first)', 'advancedcustomfields' ); ?></p>
            <?php
            }
        }
        else
        {
            ?>
        <p><?php _e( 'Gravity Forms is currently not available. Please contact your system administrator.', 'advancedcustomfields' ); ?></p>
        <?php
        }
    }

    function update_value($post_id, $field, $value)
    {
        // do stuff with value
        $value = intval($value);

        // save value
        parent::update_value($post_id, $field, $value);
    }

}

?>
