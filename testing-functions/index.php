<?php
/**
 * ==============
 * testing_functions
 * ==============
 */

if ( ! class_exists( 'testing_functions' ) ) {

    class testing_functions{

        /**
         * ================================
         * The single instance of the class
         * ================================
         */
        protected static $_instance = null;

        /**
         * =============
         * Main Instance
         * =============
         */
        public static function get_instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * ===============
         * Register Hoooks
         * ===============
         */
        public function __construct() {

            /**
             * =======
             * Actions
             * =======
             */
            /**
             * =========
             * 1) Asstes
             * =========
             */
			add_action( 'wp_enqueue_scripts', [ $this, 'Blocksy_Admin_Assets' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'Blocksy_Admin_Products_Assets' ] );

            /**
             * ======================================================================================================
             * 2) In Admin Page [ Edit Post Type (Products) = In Select Option New Option Added (Add To Collection) ]
             * ======================================================================================================
             */
			add_filter( 'bulk_actions-edit-product', [ $this, 'testing_edit_products_select_option' ] );

            /**
             * =======================
             * 3) Localize AJAX Script
             * =======================
             */
			add_action( 'admin_enqueue_scripts', [ $this, 'Blocksy_Ajax_Script' ] );

            /**
             * ========================================================================================================
             * 4) In Single Collection [CPT] = Selected Products Add To Selected Collection Post [Ajax Action PHP Call]
             * ========================================================================================================
             */
			add_action( 'wp_ajax_' . 'Blocksy_Action_Add_To_Product_in_ajax', [ $this, 'Blocksy_Action_Add_To_Product_in_ajax' ] );

			add_action( 'wp_ajax_nopriv_' . 'Blocksy_Action_Add_To_Product_in_ajax', [ $this, 'Blocksy_Action_Add_To_Product_in_ajax' ] );

            add_action( 'wp_ajax_' . 'Blocksy_Action_Get_CPT_List_in_ajax', [ $this, 'Blocksy_Action_Get_CPT_List_in_ajax' ] );

            add_action( 'wp_ajax_nopriv_' . 'Blocksy_Action_Get_CPT_List_in_ajax', [ $this, 'Blocksy_Action_Get_CPT_List_in_ajax' ] );


        }

        /**
         * =========
         * 1) Asstes
         * =========
         */
        public function Blocksy_Admin_Assets(){

			/**
			 *  Testing Helper StyleSheet
			 */
        	wp_enqueue_style( 'testing-functions-stylesheet' , get_template_directory_uri() . '/testing-functions/assets/css/stylesheet.css' );

			/**
			 *  Testing Helper Script
			 */
        	wp_enqueue_script( 'testing-functions-script' , get_template_directory_uri() . "/testing-functions/assets/js/script.js", array( "jquery" ), rand( 111,9999 ), true);

        }

        /**
         * =========
         * 1) Asstes
         * =========
         */
        public function Blocksy_Admin_Products_Assets( $hook ){

        	global $pagenow;

			$check_page = ( 'edit.php' == $pagenow ) && ( 'product' == get_post_type() );

			if( $check_page != $hook ){

				return;

			}

			/**
			 *  Style
			 */
			wp_enqueue_style( 'admin-sweetalert2' , get_template_directory_uri() . '/testing-functions/assets/css/sweetalert2.css' );

            wp_enqueue_style( 'admin-sweetalert2-css' , get_template_directory_uri() . '/testing-functions/assets/css/stylesheet.css' );

			/**
			 *  Script
			 */
        	wp_enqueue_script( 'admin-script' , get_template_directory_uri() . "/testing-functions/assets/js/script.js", array( "jquery" ), rand( 111,9999 ), true);

        	wp_enqueue_script( 'admin-sweetalert2' , get_template_directory_uri() . "/testing-functions/assets/js/sweetalert2.min.js", array( "jquery" ), rand( 111,9999 ), true);

            wp_enqueue_script( array('jquery') );

        }

        /**
         * ======================================================================================================
         * 2) In Admin Page [ Edit Post Type (Products) = In Select Option New Option Added (Add To Collection) ]
         * ======================================================================================================
         */
        public function testing_edit_products_select_option() {

			$bulk_actions['add_to_services'] = __('Add To Service', 'testing');

			return $bulk_actions;
		}

        /**
         * =======================
         * 3) Localize AJAX Script
         * =======================
         */
         public function Blocksy_Ajax_Script(){

		    /**
		     *  blocksy-custom-script
		     *  ---------------------
		     */
		    wp_enqueue_script( 'testing-helper-script', get_template_directory_uri() . "/testing-functions/assets/js/ajax-load.js", array('jquery' ), '1.1.1', true );

		    /**
		     *  Localize Script
		     *  ---------------
		     */
		    wp_localize_script(

		        /**
		         *  Load After Script NAME
		         *  ----------------------
		         */
		        esc_attr( 'testing-helper-script' ),

		        /**
		         *  Localize Object
		         *  ---------------
		         */
		        esc_attr( 'BLOCKSY_AJAX_OBJ' ),

		        /**
		         *  Localize Object Data 
		         *  --------------------
		         */
		        array(

		            /**
		             *  WordPress AJAX File
		             *  -------------------
		             */
		            'ajax_url'       =>  admin_url( 'admin-ajax.php' ),

		        )
		    );

         }

        public function Blocksy_Action_Add_To_Product_in_ajax(){

                $cpt_id             =   $_POST[ 'cpt_id' ];
                $meta_key           =   esc_attr( 'products' );
                $products_ids       =   $_POST[ 'products_ids' ];

                if( isset( $cpt_id ) && isset( $products_ids ) ){

                        $_products_ids   =   explode( ',' , $products_ids );

                        if( is_array( $_products_ids ) ){

                            $old_data = get_post_meta( $cpt_id, 'products', true );

                            if( $old_data !== $_products_ids ){

                                update_post_meta( $cpt_id , 'products', $_products_ids, $old_data  );
                            }
                        }

                } // endif;

            wp_die(); // All ajax handlers die when finished
        }

        public function Blocksy_Action_Get_CPT_List_in_ajax(){

              $args = array(
                'post_type'=> 'services',
                'orderby'    => 'ID',
                'post_status' => 'publish',
                'order'    => 'DESC',
                'posts_per_page' => -1 
                );

                $posts = get_posts($args);
                $return = [];

                foreach( $posts as $post ) : setup_postdata($post);
                
                   $return[] = array('post_id' => $post->ID,'post_title' => $post->post_title );

                endforeach;  wp_reset_postdata();

              echo json_encode($return);

            wp_die(); // All ajax handlers die when finished

        }
    }

    testing_functions::get_instance();

}