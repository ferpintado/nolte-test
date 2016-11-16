<?php 

class MoviesNolte {
    
    public function __construct() {
        
        add_action('init', array($this, 'registerPostType'));
        add_action('init', array($this, 'rewriteUrl'));
        
        add_action('template_redirect', array($this, 'jsonOutput'));
        
        add_action('add_meta_boxes', array($this, 'registerMetaBox'));
        add_action('save_post', array($this, 'saveMeta'));
    }
    
    /**
	 * Register movies custom post type
	 */
    public function registerPostType() {

        
        $labels = array(
		'name'               => _x('Movies', 'post type general name', 'movies_noite'),
		'singular_name'      => _x('Movie', 'post type singular name', 'movies_noite'),
		'menu_name'          => _x('Movies', 'admin menu', 'movies_noite'),
		'name_admin_bar'     => _x('Movie', 'add new on admin bar', 'movies_noite'),
		'add_new'            => _x('Add New', 'movie', 'movies_noite'),
		'add_new_item'       => __('Add New Movie', 'movies_noite'),
		'new_item'           => __('New Movie', 'movies_noite'),
		'edit_item'          => __('Edit Movie', 'movies_noite'),
		'view_item'          => __('View Movie', 'movies_noite'),
		'all_items'          => __('All Movies', 'movies_noite'),
		'search_items'       => __('Search Movies', 'movies_noite'),
		'parent_item_colon'  => __('Parent Movies:', 'movies_noite'),
		'not_found'          => __('No Movies found.', 'movies_noite'),
		'not_found_in_trash' => __('No Movies found in Trash.', 'movies_noite')
    	);

    	$args = array(
    		'labels'             => $labels,
            'description'        => __('Movies.', 'movies_noite'),
    		'public'             => true,
    		'publicly_queryable' => true,
    		'show_ui'            => true,
    		'show_in_menu'       => true,
    		'query_var'          => true,
    		'capability_type'    => 'post',
    		'has_archive'        => true,
    		'hierarchical'       => false,
    		'menu_position'      => null,
    		'supports'           => array('title', 'author', 'thumbnail', 'comments')
    	);
    	
    	register_post_type('movies', $args);
    }
    
    /**
	 * Adding metabox to movies custom post type
	 */
    public function registerMetaBox() {
        add_meta_box(
            'movie_information',
            __('Movie Information', 'movies_noite'),
            array($this, 'metaBoxContent'),
            'movies',
            'normal',
            'high'
       );
        
    }
    
    
    /**
	 * Creating output for custom metabox
	 * @param object $post The post object.
	 */
    public function metaBoxContent($post) {
        
        // Creating nonce to valid when the form was submitted
        wp_nonce_field('movie_information_form', 'movie_information_nonce'); ?>
        
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="poster_url"><?php _e('Poster URL:', 'movies_noite'); ?></label>
                    </th>
                    <td>
                        <input id="poster_url" style="width:100%" type="text"  name="poster_url" value="<?php echo get_post_meta($post->ID, 'poster_url', true);?>" />
                        <br>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="rating"><?php _e('Rating:', 'movies_noite'); ?></label>
                    </th>
                    <td>
                        <?php
                        $rating = get_post_meta($post->ID, 'rating', true);
                        ?>
                        <select id="rating" name="rating">
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            $selected = ($i == $rating)? 'selected="selected"': '';
                            echo '<option value="'. $i .'" '. $selected .'>'. $i .'</option>';
                        }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="year"><?php _e('Year:', 'movies_noite'); ?></label>
                    </th>
                    <td>
                        <input id="year" type="number" min="1" max="9999" maxlength="4" name="year" value="<?php echo get_post_meta($post->ID, 'year', true);?>" />
                        <br>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="short_description"><?php _e('Short description:', 'movies_noite'); ?></label>
                    </th>
                    <td>
                        
                        <textarea id="short_description" name="short_description" rows="4" cols="50"><?php echo get_post_meta($post->ID, 'short_description', true);?></textarea>
                        <br>
                    </td>
                </tr>
            </tbody>
        </table>
        
    <?php     
    }
    /**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function saveMeta($post_id) {
		// Check if our nonce is set.
        if (! isset($_POST['movie_information_nonce'])) {
            return $post_id;
        }
 
        $nonce = $_POST['movie_information_nonce'];
 
        // Verify that the nonce is valid.
        if (! wp_verify_nonce($nonce, 'movie_information_form')) {
            return $post_id;
        }
 
        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
 
        // Check the user's permissions.
        if ('movies' == $_POST['post_type']) {
            if (! current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } else {
            if (! current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }
        
        // Updating metadata
        
		if (isset($_POST['poster_url'])) {
			update_post_meta($post_id, 'poster_url', sanitize_text_field($_POST['poster_url']));
		}
		if (isset($_POST['rating'])) {
			update_post_meta($post_id, 'rating', sanitize_text_field($_POST['rating']));
		}
		if (isset($_POST['year'])) {
			update_post_meta($post_id, 'year', sanitize_text_field($_POST['year']));
		}
		if (isset($_POST['short_description'])) {
			update_post_meta($post_id, 'short_description', sanitize_text_field($_POST['short_description']));
		}
		
		delete_transient('cacheMoviesQuery');
	}
	
	/**
	 * Rewriting url so that it can be accessible from /movies.json
	 */
	public function rewriteUrl() {
	    add_rewrite_rule('/movies.json', 'index.php?movies=all', 'top');
	}
	
	/**
	 * Getting movies to display results as json format
	 */
	public function jsonOutput() {
	    
	   global $wp_query;
	    
	    $moviesTag = $wp_query->get('movies');
	    
	    
	    if (!$moviesTag) {
	        return;
	    }
	    
	    $moviesArray = array();
	    
	    $args = array(
	        'post_type' => 'movies',
	        'posts_per_page' => 100,
	    );
	    

	    // Get any existing copy of our transient data
        if ( false === ( $moviesQuery = get_transient( 'cacheMoviesQuery' ) ) ) {
          // It wasn't there, so regenerate the data and save the transient
          $moviesQuery = new WP_Query($args);
          set_transient( 'cacheMoviesQuery', $moviesQuery );
        }

	    if ($moviesQuery->have_posts()) : while ($moviesQuery->have_posts()) : $moviesQuery->the_post();
	        $postID = get_the_ID();
	        
	        $moviesArray['data'][] = array(
	           'id' => $postID,
	           'title' => get_the_title(),
	           'poster_url'=> get_post_meta($postID, 'poster_url', true),
	           'rating' => get_post_meta($postID, 'rating', true),
	           'year' => get_post_meta($postID, 'year', true),
	           'short_description' => get_post_meta($postID, 'short_description', true)
	        );
	        
        endwhile;
	    
        wp_reset_postdata(); 
	    
        endif;
        
        header('Content-Type: application/json');
	    wp_send_json($moviesArray);
	    
	}
}
?>