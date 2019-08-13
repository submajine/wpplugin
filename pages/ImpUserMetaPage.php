<?php
$impUserMetaPage = 'impUserMetaPage';
function metaPageOptions(){
    global $impUserMeta;
    add_users_page('Дополнительные метаданные пользователя','Дополнительные метаданные пользователя',2,$impUserMeta,'userMetaPageCB');
}
add_action('admin_menu','metaPageOptions');

function userMetaPageCB(){
?>
<div class="wrap">
    <h2> Additional data about user</h2>
    <form action="" method="post">
        <?php
        global $impUserMeta;
        settings_fields('impUserMetaSection');
        do_settings_sections($impUserMeta);
        submit_button();
        wp_nonce_field();
        if(!(empty($_POST))) {
            $dttreatment = new DataTreatment();
            $dttreatment->setPost($_POST);
            $dttreatment->addUserMeta(get_current_user_id());
        }
        ?>
    </form>
</div>
<?php
}
function metaPageSectionSettings()
{
    register_setting('metaPageOptions','metaPageOptions');

    global $impUserMeta;
    add_settings_section('impUserMetaSection', 'Additional information', 'metaPageCalbackF', $impUserMeta);

    $formFieldParams = [
        'type'=>'text',
        'id'=>'addressTextField',
        'label_for'=>'addressTextField'
    ];
    add_settings_field('addressField','Address','metaPageCalbackF',$impUserMeta,'impUserMetaSection', $formFieldParams);

    $formFieldParams = [
        'type'=>'text',
        'id'=>'phoneTextField',
        'label_for'=>'phoneTextField'
    ];
    add_settings_field('phoneField','Phone','metaPageCalbackF',$impUserMeta,'impUserMetaSection', $formFieldParams);


    $formFieldParams = [
        'type'=>'radio',
        'id'=>'genderRadioField',
        'vals'=> ['male'=>'Male','female'=>'Female']
    ];
    add_settings_field('genderField','Gender','metaPageCalbackF',$impUserMeta,'impUserMetaSection', $formFieldParams);

    $formFieldParams = [
        'type'=>'radio',
        'id'=>'marriedRadioField',
        'vals'=> ['single'=>'Single','married'=>'Married']
    ];
    add_settings_field('marriedField','Family status','metaPageCalbackF',$impUserMeta,'impUserMetaSection', $formFieldParams);

}
add_action('admin_init','metaPageSectionSettings');
function metaPageCalbackF($args)
{
    extract($args);
    $option_name = 'metaPageOptions';
    $o = get_option($option_name);


    switch ($type) {
        case 'text':
            $o[$id] = esc_attr(stripslashes($o[$id]));
            echo "<input class='regular-text' type='text' id='$id' name='" . $option_name . "[$id]' value='$o[$id]' />";
            echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
            break;

        case 'radio':
            echo "<fieldset>";
            foreach ($vals as $v => $l) {
                $checked = ($o[$id] == $v) ? "checked='checked'" : '';
                echo "<label><input type='radio' name='" . $option_name . "[$id]' value='$v' $checked />$l</label><br />";
            }
            echo "</fieldset>";
            break;

    }
}
