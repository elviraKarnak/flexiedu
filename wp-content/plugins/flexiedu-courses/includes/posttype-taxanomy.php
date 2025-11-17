<?php
    if( !class_exists( 'FlexiEdu_Courses_Posttype_Taxonomy_Module') ){

        class FlexiEdu_Courses_Posttype_Taxonomy_Module {

            public function __construct(){

                add_action( 'init', array( $this, 'create_course_posttype'));
                add_action( 'init', array( $this, 'create_ccat_taxonomy'));
                add_action( 'init', array( $this, 'create_clevel_taxonomy'));
                

            
            }


            function create_course_posttype() {

                    //UI 
                        $labels = array(
                            'name'                => _x( 'Courses', 'Post Type General Name', 'flexiedu' ),
                            'singular_name'       => _x( 'Course', 'Post Type Singular Name', 'flexiedu' ),
                            'menu_name'           => __( 'Courses', 'flexiedu' ),
                            'parent_item_colon'   => __( 'Parent Course', 'flexiedu' ),
                            'all_items'           => __( 'All Courses', 'flexiedu' ),
                            'view_item'           => __( 'View Course', 'flexiedu' ),
                            'add_new_item'        => __( 'Add New Course', 'flexiedu' ),
                            'add_new'             => __( 'Add New', 'flexiedu' ),
                            'edit_item'           => __( 'Edit Course', 'flexiedu' ),
                            'update_item'         => __( 'Update Course', 'flexiedu' ),
                            'search_items'        => __( 'Search Course', 'flexiedu' ),
                            'not_found'           => __( 'Not Found', 'flexiedu' ),
                            'not_found_in_trash'  => __( 'Not found in Trash', 'flexiedu' ),
                        );
                            
                    // options 
                            
                        $args = array(
                            'label'               => __( 'Courses', 'flexiedu' ),
                            'description'         => __( 'ALL Course Details', 'flexiedu' ),
                            'labels'              => $labels,
                            'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'custom-fields', ),
                            'taxonomies'          => array( 'genres' ),
                            'hierarchical'        => true,
                            'public'              => true,
                            'show_ui'             => true,
                            'show_in_menu'        => true,
                            'show_in_nav_menus'   => true,
                            'show_in_admin_bar'   => true,
                            'menu_position'       => 20,
                            'menu_icon'           => 'dashicons-open-folder',
                            'can_export'          => true,
                            'has_archive'         => true,
                            'exclude_from_search' => false,
                            'publicly_queryable'  => true,
                            'capability_type'     => 'post',
                            'show_in_rest' => true,
                        // 'rewrite' => array( 'slug' => 'course' ),
                        
                        );
                            
                        // Registering your Custom Post Type
                        register_post_type( 'courses', $args );
                    
                    
                    }

                    function create_ccat_taxonomy() {
    
                        $labels = array(
                        'name' => _x( 'Course Categories', 'cutextdomain' ),
                        'singular_name' => _x( 'Course category', 'cutextdomain' ),
                        'search_items' =>  __( 'Search  Course Categories' ),
                        'all_items' => __( 'All  Course Categories' ),
                        'parent_item' => __( 'Parent Course category' ),
                        'parent_item_colon' => __( 'Parent Course category:' ),
                        'edit_item' => __( 'Edit Course category' ), 
                        'update_item' => __( 'Update Course category' ),
                        'add_new_item' => __( 'Add New Course category' ),
                        'new_item_name' => __( 'New Course category Name' ),
                        'menu_name' => __( ' Course Categories' ),
                        );    
                    
                    // Now register the taxonomy
                        register_taxonomy('c-cat',array('courses'), array(
                        'hierarchical' => true,
                        'labels' => $labels,
                        'show_ui' => true,
                        'show_in_rest' => true,
                        'show_admin_column' => true,
                        'show_in_menu' => true,
                        'query_var' => true,
                        // 'rewrite' => array( 'slug' => 'bookingtype' ),
                        ));
                    
                    }

                      function create_clevel_taxonomy() {
   
                        $labels = array(
                        'name' => _x( 'Course Levels', 'cutextdomain' ),
                        'singular_name' => _x( 'Course Level', 'cutextdomain' ),
                        'search_items' =>  __( 'Search  Course Levels' ),
                        'all_items' => __( 'All  Course Levels' ),
                        'parent_item' => __( 'Parent Course Level' ),
                        'parent_item_colon' => __( 'Parent Course Level:' ),
                        'edit_item' => __( 'Edit Course Level' ), 
                        'update_item' => __( 'Update Course Level' ),
                        'add_new_item' => __( 'Add New Course Level' ),
                        'new_item_name' => __( 'New Course Level Name' ),
                        'menu_name' => __( ' Course Levels' ),
                        );    
                    
                    // Now register the taxonomy
                        register_taxonomy('c-level',array('courses'), array(
                        'hierarchical' => true,
                        'labels' => $labels,
                        'show_ui' => true,
                        'show_in_rest' => true,
                        'show_admin_column' => true,
                        'show_in_menu' => true,
                        'query_var' => true,
                        // 'rewrite' => array( 'slug' => 'bookingtype' ),
                        ));
                    
                    }
        
        
        }
    }      