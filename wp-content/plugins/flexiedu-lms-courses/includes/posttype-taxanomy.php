<?php
    if( !class_exists( 'FlexiEdu_Courses_Posttype_Taxonomy_Module') ){

        class FlexiEdu_Courses_Posttype_Taxonomy_Module {

            public function __construct(){

                add_action( 'init', array( $this, 'create_product_type_taxonomy'));
            
            }

            function create_product_type_taxonomy() {

            $labels = array(
            'name' => _x( 'Product Types', 'flexiedu-lms-courses' ),
            'singular_name' => _x( 'Product Type', 'flexiedu-lms-courses' ),
            'search_items' =>  __( 'Search  Product Types' ),
            'all_items' => __( 'All  Product Types' ),
            'parent_item' => __( 'Parent Product Type' ),
            'parent_item_colon' => __( 'Parent Product Type:' ),
            'edit_item' => __( 'Edit Product Type' ), 
            'update_item' => __( 'Update Product Type' ),
            'add_new_item' => __( 'Add New Product Type' ),
            'new_item_name' => __( 'New Product Type Name' ),
            'menu_name' => __( ' Product Types' ),
            );    
                
            // Now register the taxonomy
                register_taxonomy('product-type',array('product'), array(
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