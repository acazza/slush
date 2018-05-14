<?php
/**
 * Slush Wordpress MegaMenu Admin Custom Fields
 * @uses config/walker-nav-menu-edit.php
 * @package com.soundlush.slush.v1
 */



/**
 * create fields list for the megamenu admin area
 * @since 1.0.0
 */

function wpsl_create_fields_list()
{
    //note that menu-item- gets prepended to field names
    return array(
        'megamenu'                => 'Activate MegaMenu',
        'megamenu-column-divider' => 'Column Divider',
        'megamenu-inline-divider' => 'Inline Divider',
        'megamenu-featured-image' => 'Featured Image',
        'megamenu-description'    => 'Description',
    );

}



/**
 * setup fields list for the megamenu admin area
 * @param int     |  $id     |
 * @param object  |  $item   |
 * @param int     |  $depth  |
 * @param array   |  $args   |
 * @since 1.0.0
 */

function wpsl_setup_fields_list( $id, $item, $depth, $args )
{
    $fields = wpsl_create_fields_list();

    foreach( $fields as $_key => $label )
    {
        $key   = sprintf( 'menu-item-%s', $_key );
        $id    = sprintf( 'edit-%s-%s', $key, $item->ID );
        $name  = sprintf( '%s[%s]', $key, $item->ID );
        $value = get_post_meta( $item->ID, $key, true );
        $class = sprintf( 'field-%s', $_key );
        ?>

        <p class="description description-wide <?php echo esc_attr( $class ); ?>">
          <label for="<?php echo esc_attr( $id ); ?>">
            <input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="1" <?php echo ( $value == 1 ) ? 'checked="checked"' : ''; ?>><?php echo esc_attr( $label ); ?></label>
        </p>

        <?php
    }
}
add_action( 'wp_nav_menu_item_custom_fields', 'wpsl_setup_fields_list', 10, 4 );




/**
 * show columns on the megamenu admin area
 * @param array   |  $columns  |  array of megamenu columns
 * @return array  |  $columns  |  array of megamenu updated columns
 * @since 1.0.0
 */

function wpsl_show_columns( $columns )
{
    $fields  = wpsl_create_fields_list();
    $columns = array_merge( $columns, $fields );
    return $columns;
}
add_filter( 'manage_nav-menus_columns', 'wpsl_show_columns', 99 );



/**
 * save and update the megamenu admin area options
 * @param int    |  $menu_id          |  required  |  The ID of the menu.
 * @param int    |  $menu_item_db_id  |  required  |  The ID of the menu item.
 * @param array  |  $menu_item_args   |  optional  |  The menu item's data.
 * @since 1.0.0
 */

function wpsl_save_fields( $menu_id, $menu_item_db_id, $menu_item_args )
{
    //stop function if auto-save is in progress
    if( defined( 'DOING_AJAX' ) && DOING_AJAX )
    {
        return;
    }

    //check if action is triggered from admin panel of your website
    check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

    $fields = wpsl_create_fields_list();

    foreach( $fields as $_key => $label )
    {

        $key = sprintf( 'menu-item-%s', $_key );

        //sanitize fields (checkbox)
        if( !empty( $_POST[$key][$menu_item_db_id] ) )
        {
            $value = $_POST[$key][$menu_item_db_id];
        }
        else
        {
            $value = null;
        }

        //update
        if( !is_null( $value ) )
        {
            update_post_meta( $menu_item_db_id, $key, $value );
        }
        else
        {
            delete_post_meta( $menu_item_db_id, $key );
        }

    }
}
add_action( 'wp_update_nav_menu_item', 'wpsl_save_fields', 10, 3 );



/**
 * update walker nav class
 * @uses config/walker-nav-menu-edit.php
 *
 * @since 1.0.0
 */

function wpsl_update_megamenu_walker_nav( $walker )
{
    $walker = 'MegaMenu_Walker_Nav_Menu_Edit';

    if( !class_exists( $walker ) )
    {
        require_once dirname(__FILE__) . '/config/walker-nav-menu-edit.php';
    }

    return $walker;
}
add_filter( 'wp_edit_nav_menu_walker', 'wpsl_update_megamenu_walker_nav', 99 );
