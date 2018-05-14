<?php
/**
 * Slush Custom Post Types Class
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 * @package com.soundlush.slush.v1
 */

if( !class_exists( 'SlushPostType' ) )
{
    class SlushPostType
    {
        /**
         * The names passed to the PostType
         * @var array
         */

        public $names;

        /**
         * The name for the PostType
         * @var array
         */

        public $name;

        /**
         * The singular for the PostType
         * @var array
         */

        public $singular;

        /**
         * The plural name for the PostType
         * @var array
         */

        public $plural;

        /**
         * The slug for the PostType
         * @var array
         */

        public $slug;

        /**
         * Options for the PostType
         * @var array
         */

        public $options;

        /**
         * Labels for the PostType
         * @var array
         */

        public $labels;

        /**
         * Taxonomies for the PostType
         * @var array
         */

        public $taxonomies = [];

        /**
         * Filters for the PostType
         * @var mixed
         */

        public $filters;

        /**
         * The menu icon for the PostType
         * @var string
         */

        public $icon;

        /**
         * The column manager for the PostType
         * @var mixed
         */

        public $columns;

        /**
         * The custom field manager for the PostType
         * @var mixed
         */

        public $customfields;

        /**
         * The parent PostType
         * @var string
         */

        public $parent;



        /**
         * Create a PostType
         * @param mixed $names   A string for the name, or an array of names
         * @param array $options An array of options for the PostType
         * @since 1.0.0
         */

        public function __construct( $names, $options = [], $labels = [] )
        {
            //assign names to the PostType
            $this->names( $names );
            //assign custom options to the PostType
            $this->options( $options );
            //assign labels to the PostType
            $this->labels( $labels );
        }



        /**
         * Set the names for the PostType
         * @param  mixed $names A string for the name, or an array of names
         * @return $this
         * @since 1.0.0
         */

        public function names( $names )
        {
            //only the post type name is passed
            if( is_string( $names ) )
            {
                $names = ['name' => $names];
            }

            //set the names array
            $this->names = $names;
            //create names for the PostType
            $this->create_names();

            return $this;
        }



        /**
         * Set the options for the PostType
         * @param  array $options An array of options for the PostType
         * @return $this
         * @since 1.0.0
         */

        public function options( array $options )
        {
            $this->options = $options;
            return $this;
        }



        /**
         * Set the labels for the PostType
         * @param  array $labels An array of labels for the PostType
         * @return $this
         * @since 1.0.0
         */

        public function labels( array $labels )
        {
            $this->labels = $labels;
            return $this;
        }



        /**
         * Add a Taxonomy to the PostType
         * @param  string $taxonomy The Taxonomy name to add
         * @return $this
         * @since 1.0.0
         */

        public function taxonomy( $taxonomy )
        {
            $this->taxonomies[] = $taxonomy;
            return $this;
        }



        /**
         * Add filters to the PostType
         * @param  array $filters An array of Taxonomy filters
         * @return $this
         * @since 1.0.0
         */

        public function filters( array $filters )
        {
            $this->filters = $filters;
            return $this;
        }



        /**
         * Set the menu icon for the PostType
         * @param  string $icon A dashicon class for the menu icon
         * @return $this
         * @since 1.0.0
         */

        public function icon( $icon )
        {
            $this->icon = $icon;
            return $this;
        }



        /**
         * Flush
         * Flush rewrite rules on theme (de)activation
         * @link https://codex.wordpress.org/Function_Reference/flush_rewrite_rules
         * @param  boolean $hard
         * @return void
         * @since 1.0.0
         */

        public function flush( $hard = true )
        {
            add_action( 'after_switch_theme', function() use( $hard ){
                flush_rewrite_rules($hard);
            });
        }



        /**
         * Get the Column Manager for the PostType
         * @return Columns
         * @since 1.0.0
         */

        public function columns()
        {
            if( !isset( $this->columns ) )
            {
                $this->columns = new SlushColumns;
            }

            return $this->columns;
        }



        /**
         * Get the Custom Field Manager for the PostType
         * @return Custom Fields
         * @since 1.0.0
         */

        public function customfields()
        {
            if( !isset( $this->customfields ) )
            {
                $this->customfields = new SlushPostMeta( $this->name );
            }

            return $this->customfields;
        }



        /**
         * Register the PostType to WordPress
         * @return void
         * @since 1.0.0
         */

        public function register()
        {
            //register the PostType
            add_action( 'init', [&$this, 'register_posttype'] );
            //rewrite post type update messages
            add_filter( 'post_updated_messages', array( &$this, 'update_messages' ) );
            add_filter( 'bulk_post_updated_messages', array( &$this, 'bulk_update_messages' ), 10, 2 );
            //register Taxonomies to the PostType
            add_action( 'init', [&$this, 'register_taxonomies'] );
            //modify filters on the admin edit screen
            add_action( 'restrict_manage_posts', [&$this, 'modify_filters'] );
            //modify permalink if post parent if from a distinct Posttype;
            add_filter( 'post_type_link', [&$this, 'change_permalinks'], 10, 3 );

            if( isset( $this->columns ) )
            {
                //modify the admin edit columns.
                add_filter( "manage_{$this->name}_posts_columns", [&$this, 'modify_columns'], 10, 1 );
                //populate custom columns
                add_filter( "manage_{$this->name}_posts_custom_column", [&$this, 'populate_columns'], 10, 2 );
                //run filter to make columns sortable.
                add_filter( 'manage_edit-'.$this->name.'_sortable_columns', [&$this, 'set_sortable_columns'] );
                //run action that sorts columns on request.
                add_action( 'pre_get_posts', [&$this, 'sort_sortable_columns'] );
            }

            if( isset( $this->customfields ) )
            {
                //listen for the save post hook to save custom fields
                add_action( 'save_post', [&$this,'save_custom_fields'], 10, 1 );
            }

            //flush rewrite rules
            $this->flush();

        }



        /**
         * Set Parent
         * Create parent-child relationship between Post Types (parent must already be registered)
         * @return void
         * @since 1.0.0
         */

        function set_parent( $parent )
        {
            add_action( 'add_meta_boxes', function() use( $parent ){

                $box_id    = 'wpsl-'. $this->name .'-parent';
                $box_title = SlushHelpers::beautify( $parent );

                add_meta_box(
                    $box_id,                      //meta-box id
                    $box_title,                   //meta-box title
                    [&$this, 'create_attributes_meta_box'], //callback
                    $this->name,                  //screen (post-type)
                    'side',                       //context
                    'high',                       //priority
                    $parent                       //callback args
                );
            });
        }


        function create_attributes_meta_box( $post, $parent )
        {
           	$post_type_object = get_post_type_object( $post->post_type );

           	$pages = wp_dropdown_pages( array(
                'post_type'        => $parent,
                'selected'         => $post->post_parent,
                'name'             => 'parent_id',
                'show_option_none' => __( '(no parent)' ),
                'sort_column'      => 'menu_order, post_title',
                'echo'             => 0
            ));

           	if( !empty( $pages ) )
            {
           		  echo $pages;
           	}

            add_action( 'init', [&$this, 'add_rewrite_rules'] );

        }



        function add_rewrite_rules()
        {
          	add_rewrite_tag( '%lesson%', '([^/]+)', 'lesson=' );
          	add_permastruct( 'lesson', '/lesson/%course%/%lesson%', false );
          	add_rewrite_rule( '^lesson/([^/]+)/([^/]+)/?','index.php?lesson=$matches[2]','top' );
        }



        function change_permalinks( $permalink, $post, $leavename )
        {
          	$post_id = $post->ID;

          	if( $post->post_type != 'lesson' || empty($permalink) || in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) ) )
            {
                return $permalink;
            }

          	$parent      = $post->post_parent;
          	$parent_post = get_post( $parent );
          	$permalink   = str_replace( '%course%', $parent_post->post_name, $permalink );

          	return $permalink;
        }





        //public function setAsParent( $parent )
        //{
        //  if( !post_type_exists($parent) ) {
        //    $this->parent = $parent;
        //  } else {
        //    return;
        //  }
        //
        //  //perform filter on 'Edit $child' page
        //  add_filter( 'page_attributes_dropdown_pages_args', array( &$this, 'populateParent' ), 10, 2 );
        //  //also perform the same filter when doing a 'Quick Edit'
        //  add_filter( 'quick_edit_dropdown_pages_args', array( &$this, 'populateParent' ), 10, 2 );
        //  //clean up permalink
        //  add_filter( 'pre_get_posts', array( &$this, 'clean_permalink' ) );
        //}
        //
        //
        //
        ///**
        // * Populate Parent
        // * Populate parent dropdown with list of posts from parent Post Type
        // * @return array
        // */
        //
        //public function populateParent($dropdown_args)
        //{
        //    global $post;
        //    if ( $this->name == $post->post_type ){
        //      $dropdown_args['post_type'] = $this->parent;
        //    }
        //    return $dropdown_args;
        //}



        /**
         * Clean Permalink
         * Fix broken permalink after parent relationship is established
         * @return void
         * @since 1.0.0
         */

        public function clean_permalink( $query )
        {
          //run this code only when we are on the public archive
          if( ( isset($query->query_vars['post_type']) && $this->name != $query->query_vars['post_type']) || !$query->is_main_query() || is_admin() )
          {
              return;
          }

          //$parent = isset($query->query_vars['post_parent']) ? get_post_type($query->query_vars['post_parent']) . '/' : '';

          //fix query for hierarchical child permalinks
          if( isset( $query->query_vars['name']) && isset( $query->query_vars[$this->name] ) )
          {
            //remove the parent name (however, old permalink will still work)
            $query->set( 'name', basename( untrailingslashit( $query->query_vars['name'] ) ));
            //unset this ( $child query_var is a duplicate of name)
            $query->set( $this->name, null );
          }

          //flush_rewrite_rules();
        }



        /**
         * Register the PostType
         * @return void
         * @since 1.0.0
         */

        public function register_posttype()
        {
            //create options for the PostType
            $options = $this->create_options();

            //check that the post type doesn't already exist
            if( !post_type_exists( $this->name ) )
            {
                //register the post type
                register_post_type( $this->name, $options );
            }
        }



        /**
         * Create the required names for the PostType
         * @return void
         * @since 1.0.0
         */

        public function create_names()
        {
            //names required for the PostType
            $required = [
                'name',
                'singular',
                'plural',
                'slug',
           ];

            foreach( $required as $key )
            {
                //if the name is set, assign it
                if( isset( $this->names[$key] ) )
                {
                    $this->$key = $this->names[$key];
                    continue;
                }

                //if the key is not set and is singular or plural
                if( in_array( $key, ['singular', 'plural'] ) )
                {
                    //create a human friendly name
                    $name = ucwords( strtolower( str_replace( ['-', '_'], ' ', $this->names['name'] ) ) );
                }

                if( $key === 'slug' )
                {
                    //create a slug friendly name
                    $name = strtolower( str_replace( [' ', '_'], '-', $this->names['name'] ) );
                }

                //if is plural or slug, append an 's'
                if( in_array( $key, ['plural', 'slug'] ) )
                {
                    $name .= 's';
                }

                //assign the name to the PostType property
                $this->$key = $name;
            }
        }



        /**
         * Create options for PostType
         * @return array Options to pass to register_post_type
         * @since 1.0.0
         */

        public function create_options()
        {
            //default options
            $options = [
                'public'    => true,
                'rewrite'   => [
                    'slug'  => $this->slug
               ]
           ];

            //replace defaults with the options passed
            $options = array_replace_recursive( $options, $this->options );

            //create and set labels
            if( !isset( $options['labels'] ) )
            {
                $options['labels'] = $this->create_labels();
            }

            //set the menu icon
            if( !isset( $options['menu_icon'] ) && isset( $this->icon ) )
            {
                $options['menu_icon'] = $this->icon;
            }

            return $options;
        }



        /**
         * Create the labels for the PostType
         * @return array
         * @since 1.0.0
         */

        public function create_labels()
        {
            //default labels
            $labels = [
                'name'                => $this->plural,
                'singular_name'       => $this->singular,
                'menu_name'           => $this->plural,
                'all_items'           => $this->plural,
                'add_new'             => "Add New",
                'add_new_item'        => "Add New {$this->singular}",
                'edit_item'           => "Edit {$this->singular}",
                'new_item'            => "New {$this->singular}",
                'view_item'           => "View {$this->singular}",
                'search_items'        => "Search {$this->plural}",
                'not_found'           => "No {$this->plural} found",
                'not_found_in_trash'  => "No {$this->plural} found in Trash",
                'parent_item_colon'   => "Parent {$this->singular}:",
           ];

            return array_replace_recursive( $labels, $this->labels );
        }



        /**
         * Modifies the post type names in updated messages
         * @param  array $messages an array of updated messages.
         * @return array $messages an array of updated messages.
         * @since 1.0.0
         */

        public function update_messages( $messages )
        {
          global $post;

          $post_ID = $post->ID;

          $posttype = $this->name;

          $messages[$posttype] = array(
              0 => '', //unused. messages start at index 1.
              1 => sprintf( __( $this->singular . ' updated. <a href="%s">View ' . $this->singular . '</a>'), esc_url( get_permalink($post_ID) ) ),
              2 => __( $this->singular . ' field updated.' ),
              3 => __( $this->singular . ' field deleted.' ),
              4 => __( $this->singular . ' updated.' ),
              5 => isset($_GET['revision']) ? sprintf( __( $this->singular . ' restored to revision from %s' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
              6 => sprintf( __( $this->singular . ' published. <a href="%s">View run</a>' ), esc_url( get_permalink( $post_ID ) ) ),
              7 => __( $this->singular  . ' saved.' ),
              8 => sprintf( __( $this->singular . ' submitted. <a target="_blank" href="%s">Preview run</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
              9 => sprintf( __( $this->singular . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview run</a>' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
              10 => sprintf( __( $this->singular . ' draft updated. <a target="_blank" href="%s">Preview post type</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
          );

          return $messages;
        }



        /**
         * Modifies the post type names in bulk updated messages
         * @param array   $bulk_messages an array of bulk updated messages
         * @return array  $bulk_messages an array of bulk updated messages
         * @since 1.0.0
         */

        function bulk_update_messages( $bulk_messages, $bulk_counts )
        {
            $posttype = $this->name;

            $bulk_messages[$posttype] = array(
                'updated'   => _n( '%s ' . $this->singular . ' updated.', '%s ' . $this->plural . ' updated.', $bulk_counts['updated'] ),
                'locked'    => _n( '%s ' . $this->singular . ' not updated, somebody is editing it.', '%s ' . $this->plural . ' not updated, somebody is editing them.', $bulk_counts['locked'] ),
                'deleted'   => _n( '%s ' . $this->singular . ' permanently deleted.', '%s ' . $this->plural . ' permanently deleted.', $bulk_counts['deleted'] ),
                'trashed'   => _n( '%s ' . $this->singular . ' moved to the Trash.', '%s ' . $this->plural . ' moved to the Trash.', $bulk_counts['trashed'] ),
                'untrashed' => _n( '%s ' . $this->singular . ' restored from the Trash.', '%s ' . $this->plural . ' restored from the Trash.', $bulk_counts['untrashed'] ),
            );

            return $bulk_messages;
        }



        /**
         * Register Taxonomies to the PostType
         * @return void
         * @since 1.0.0
         */

        public function register_taxonomies()
        {
            if( !empty( $this->taxonomies ) )
            {
                foreach( $this->taxonomies as $taxonomy )
                {
                    register_taxonomy_for_object_type( $taxonomy, $this->name );
                }
            }
        }



        /**
         * Modify and display filters on the admin edit screen
         * @param  string $posttype The current screen post type
         * @return void
         * @since 1.0.0
         */

        public function modify_filters($posttype)
        {
            //first check we are working with the this PostType
            if( $posttype === $this->name )
            {
                //calculate what filters to add
                $filters = $this->get_filters();

                foreach( $filters as $taxonomy )
                {
                    //if the taxonomy doesn't exist, ignore it
                    if( !taxonomy_exists( $taxonomy ) )
                    {
                        continue;
                    }

                    //get the taxonomy object
                    $tax = get_taxonomy( $taxonomy );
                    //get the terms for the taxonomy
                    $terms = get_terms( [
                        'taxonomy'    => $taxonomy,
                        'orderby'     => 'name',
                        'hide_empty'  => false,
                    ] );

                    //if there are no terms in the taxonomy, ignore it
                    if( empty( $terms ) )
                    {
                        continue;
                    }

                    //start the html for the filter dropdown
                    $dropdown = sprintf( ' &nbsp;<select name="%s" class="postform">', $taxonomy );
                    //set 'Show all' option
                    $dropdown .= sprintf( '<option value="0">%s</option>', "Show all {$tax->label}" );

                    //create option for each taxonomy tern
                    foreach( $terms as $term )
                    {
                        $selected = '';

                        //if the current term is active, add selected attribute
                        if( isset( $_GET[$taxonomy] ) && $_GET[$taxonomy] === $term->slug )
                        {
                            $selected = ' selected="selected"';
                        }

                        //html for term option
                        $dropdown .= sprintf(
                            '<option value="%s"%s>%s (%s)</option>',
                            $term->slug,
                            $selected,
                            $term->name,
                            $term->count
                        );
                    }

                    //end the select field
                    $dropdown .= '</select>&nbsp;';
                    //display the dropdown filter
                    echo $dropdown;
                }
            }
        }



        /**
         * Calculate the filters for the PostType
         * @return array $filters
         * @since 1.0.0
         */

        public function get_filters()
        {
            //default filters are empty
            $filters = [];

            //if custom filters have been set, use them
            if( !is_null( $this->filters ) )
            {
                return $this->filters;
            }

            //if no custom filters have been set, and there are taxonomies assigned to the PostType
            if( is_null( $this->filters ) && !empty( $this->taxonomies ) )
            {
                //create filters for each taxonomy assigned to the PostType
                return $this->taxonomies;
            }

            return $filters;
        }



        /**
         * Modify the columns for the PostType
         * @param  array  $columns  Default WordPress columns
         * @return array  $columns  The modified columns
         * @since 1.0.0
         */

        public function modify_columns( $columns )
        {
            $columns = $this->columns->modify_columns( $columns );
            return $columns;
        }



        /**
         * Populate custom columns for the PostType
         * @param  string $column   The column slug
         * @param  int    $post_id  The post ID
         * @since 1.0.0
         */

        public function populate_columns( $column, $post_id )
        {
            if( isset( $this->columns->populate[$column] ) )
            {
                call_user_func_array( $this->columns()->populate[$column], [$column, $post_id] );
            }
        }



        /**
         * Make custom columns sortable
         * @param array  $columns  Default WordPress sortable columns
         * @since 1.0.0
         */

        public function set_sortable_columns( $columns )
        {
            if( !empty( $this->columns()->sortable ) )
            {
                $columns = array_merge( $columns, $this->columns()->sortable );
            }

            return $columns;
        }



        /**
         * Set query to sort custom columns
         * @param  WP_Query $query
         * @since 1.0.0
         */

        public function sort_sortable_columns( $query )
        {
            //don't modify the query if we're not in the post type admin
            if( !is_admin() || $query->get('post_type') !== $this->name )
            {
                return;
            }

            $orderby = $query->get( 'orderby' );

            //if the sorting a custom column
            if( array_key_exists( $orderby, $this->columns()->sortable ) )
            {
                //get the custom column options
                $meta = $this->columns()->sortable[$orderby];

                //determine type of ordering
                if( is_string( $meta ) )
                {
                    $meta_key   = $meta;
                    $meta_value = 'meta_value';
                }
                else
                {
                    $meta_key   = $meta[0];
                    $meta_value = 'meta_value_num';
                }

                //set the custom order
                $query->set( 'meta_key', $meta_key );
                $query->set( 'orderby', $meta_value );
            }
        }



        /**
         * Save Custom Fields for the PostType
         * @param  array  $name     The post type name
         * @since 1.0.0
         */

        public function save_custom_fields()
        {
            $this->customfields->save_custom_fields( $this->name );
        }
    }
}
