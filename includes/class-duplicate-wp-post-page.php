<?php
/**
 *
 * @link       https://sohilchamadia8.wordpress.com/
 * @since      1.0.0
 * @package    Duplicate_WP_Post_Page
 * @subpackage Duplicate_WP_Post_Page/includes
 * @author     Sohil B. Chamadia <sohilchamadia8@gmail.com>
 */

class Duplicate_WP_Post_Page {

	 
	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Call the require filter or action that is needed when plugin's loaded
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'DUPLICATE_WP_PAGE_POST_VERSION' ) ) {
			$this->version = DUPLICATE_WP_PAGE_POST_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		
		add_filter( 'post_row_actions', array( $this, 'set_link_in_row' ), 10, 2 );
		add_filter( 'page_row_actions', array( $this, 'set_link_in_row' ), 10, 2 );
		add_action( 'admin_action_duplicate_post_page', array( $this,'duplicate_post_page' ) );

	}


	/**
     * Add the duplicate link to action list for post_row_actions
      * @since     1.0.0
     * @param string $actions
     * @param type $post
     * @return string
     */
    public function set_link_in_row( $actions, $post ) {

            if (current_user_can('edit_posts')) {
                    $actions['clone'] = '<a href="admin.php?action=duplicate_post_page&amp;post=' . $post->ID . '&amp;nonce='.wp_create_nonce( 'duplicate-post-page-'.$post->ID ).'" title="'.__('Duplicate as draft', 'duplicate-wp-post-page').'" rel="permalink">'.__('Duplicate as draft', 'duplicate-post-page').'</a>';
            }

            return $actions;
    }


    /**
     * Duplicate the post or page and make it as a draft
      * @since     1.0.0
     */
    public function duplicate_post_page() {
            global $wpdb;
            if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'duplicate_post_page' == $_REQUEST['action'] ) ) ) {
                    wp_die('Not any post or page to clone has been supplied!');
            }

            /*
            * get Nonce value
            */
            $nonce = $_REQUEST['nonce'];

            if(!wp_verify_nonce( $nonce, 'duplicate-post-page-'.$post_id) && !current_user_can('edit_posts')){
            	 wp_die('Something went wrong. Please Try after sometime!');
            }

			$post_id=isset($_GET['post']) ? $_GET['post'] : $_POST['post'];
            $post_id = (int)$post_id;
            $post = get_post( $post_id );
            $current_user = wp_get_current_user();
            $post_author = $current_user->ID;

            if (isset( $post ) && $post != null) {

                    $dupl_post_args = array(
                            'comment_status' => $post->comment_status,
                            'ping_status'    => $post->ping_status,
                            'post_author'    => $post_author,
                            'post_content'   => $post->post_content,
                            'post_excerpt'   => $post->post_excerpt,
                            'post_name'      => $post->post_name,
                            'post_parent'    => $post->post_parent,
                            'post_password'  => $post->post_password,
                            'post_status'    => 'draft',
                            'post_title'     => $post->post_title,
                            'post_type'      => $post->post_type,
                            'to_ping'        => $post->to_ping,
                            'menu_order'     => $post->menu_order
                    );

                    $clone_pp_id = wp_insert_post( $dupl_post_args );

                    $taxonomies = get_object_taxonomies($post->post_type);
                    foreach ($taxonomies as $taxonomy) {
                            $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                            wp_set_object_terms($clone_pp_id, $post_terms, $taxonomy, false);
                    }

                    $post_meta_data = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
                    if (count($post_meta_data)!=0) {
                            $clone_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                            foreach ($post_meta_data as $meta_data) {
                                    $meta_key = $meta_data->meta_key;
                                    $meta_value = addslashes($meta_data->meta_value);
                                    $clone_query_select[]= "SELECT $clone_pp_id, '$meta_key', '$meta_value'";
                            }
                            $clone_query.= implode(" UNION ALL ", $clone_query_select);
                            $wpdb->query($clone_query);
                    }

                    wp_redirect( admin_url( 'post.php?action=edit&post=' . $clone_pp_id ) );
                    exit;

            } else {

                    wp_die(__('Post or Page clone failed, could not find original data:', 'duplicate-post-page') . $post_id);

            }
    }


	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
new Duplicate_WP_Post_Page();