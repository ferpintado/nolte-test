<?php 

class Movies_Nolte {
    
    public function __construct() {
        
        add_action( 'init', array( $this, 'register_movies_post_type') );
        add_action( 'init', array( $this, 'rewrite_url') );
        add_action( 'init', array( $this, 'register_shortcode') );
        
        add_action( 'template_redirect', array( $this, 'json_output' ) );
        
        add_action( 'add_meta_boxes', array( $this, 'register_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_meta' ) );
        
        add_action( 'wp_enqueue_scripts', array($this, 'import_script_files' ) );
        
       
    }
    
    /**
	 * Register movies custom post type
	 */
    public function register_movies_post_type() {

        
        $labels = array(
		'name'               => _x( 'Movies', 'post type general name', 'movies_noite' ),
		'singular_name'      => _x( 'Movie', 'post type singular name', 'movies_noite' ),
		'menu_name'          => _x( 'Movies', 'admin menu', 'movies_noite' ),
		'name_admin_bar'     => _x( 'Movie', 'add new on admin bar', 'movies_noite' ),
		'add_new'            => _x( 'Add New', 'movie', 'movies_noite' ),
		'add_new_item'       => __( 'Add New Movie', 'movies_noite' ),
		'new_item'           => __( 'New Movie', 'movies_noite' ),
		'edit_item'          => __( 'Edit Movie', 'movies_noite' ),
		'view_item'          => __( 'View Movie', 'movies_noite' ),
		'all_items'          => __( 'All Movies', 'movies_noite' ),
		'search_items'       => __( 'Search Movies', 'movies_noite' ),
		'parent_item_colon'  => __( 'Parent Movies:', 'movies_noite' ),
		'not_found'          => __( 'No Movies found.', 'movies_noite' ),
		'not_found_in_trash' => __( 'No Movies found in Trash.', 'movies_noite' )
    	);

    	$args = array(
            'labels'             => $labels,
            'description'        => __( 'Movies.', 'movies_noite' ),
            'public'             => true,
            'publicly_queryable' => true,
    		'show_ui'            => true,
    		'show_in_menu'       => true,
    		'query_var'          => true,
    		'capability_type'    => 'post',
    		'has_archive'        => true,
    		'hierarchical'       => false,
    		'menu_position'      => null,
    		'supports'           => array( 'title', 'author', 'thumbnail', 'comments' )
    	);
    	
    	register_post_type( 'movies', $args );
    }
    
    /**
	 * Adding metabox to movies custom post type
	 */
    public function register_meta_box() {
        add_meta_box(
            'movie_information',
            __('Movie Information', 'movies_noite'),
            array($this, 'meta_box_content'),
            'movies',
            'normal',
            'high'
       );
        
    }
    
    
    /**
	 * Creating output for custom metabox
	 * @param object $post The post object.
	 */
    public function meta_box_content( $post ) {
        
        // Creating nonce to valid when the form was submitted
        wp_nonce_field( 'movie_information_form', 'movie_information_nonce' ); ?>
        
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="poster_url"><?php _e( 'Poster URL:', 'movies_noite' ); ?></label>
                    </th>
                    <td>
                        <input id="poster_url" style="width:100%" type="text"  name="poster_url" value="<?php echo get_post_meta( $post->ID, 'poster_url', true );?>" />
                        <br>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="rating"><?php _e( 'Rating:', 'movies_noite' ); ?></label>
                    </th>
                    <td>
                        <?php
                        $rating = get_post_meta( $post->ID, 'rating', true );
                        ?>
                        <select id="rating" name="rating">
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            $selected = ( $i == $rating ) ? 'selected="selected"': '';
                            echo '<option value="'. $i .'" '. $selected .'>'. $i .'</option>';
                        }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="year"><?php _e( 'Year:', 'movies_noite' ); ?></label>
                    </th>
                    <td>
                        <input id="year" type="number" min="1" max="9999" maxlength="4" name="year" value="<?php echo get_post_meta( $post->ID, 'year', true );?>" />
                        <br>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="short_description"><?php _e( 'Short description:', 'movies_noite' ); ?></label>
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
    public function save_meta($post_id) {
		// Check if our nonce is set.
        if (! isset($_POST['movie_information_nonce']) ) {
            return $post_id;
        }
 
        $nonce = $_POST['movie_information_nonce'];
 
        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'movie_information_form' )) {
            return $post_id;
        }
 
        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
 
        // Check the user's permissions.
        if ( 'movies' == $_POST['post_type'] ) {
            if ( ! current_user_can('edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if (! current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }
        
        // Updating metadata
        
		if ( isset( $_POST['poster_url'] ) ) {
			update_post_meta( $post_id, 'poster_url', sanitize_text_field( $_POST['poster_url'] ) );
		}
		if ( isset( $_POST['rating'] ) ) {
			update_post_meta( $post_id, 'rating', sanitize_text_field( $_POST['rating'] ) );
		}
		if ( isset( $_POST['year'] ) ) {
			update_post_meta( $post_id, 'year', sanitize_text_field( $_POST['year'] ) );
		}
		if ( isset( $_POST['short_description'] ) ) {
			update_post_meta( $post_id, 'short_description', sanitize_text_field( $_POST['short_description'] ) );
		}
		
		delete_transient( 'cacheMoviesQuery' );
	}
	
	/**
	 * Rewriting url so that it can be accessible from /movies.json
	 */
	public function rewrite_url() {
	    global $wp_rewrite;
	    add_rewrite_tag( '%movies%', '([^&]+)' );
	    add_rewrite_rule( 'movies.json', 'index.php?movies=all', 'top' );
	}
	
	/**
	 * Getting movies to display results as json format
	 */
	public function json_output() {
	    
	   global $wp_query;
	    
	    $movies_tag = $wp_query->get( 'movies' );
	    
	    
	    if ( ! $movies_tag ) {
	        return;
	    }
	    
	    $movies_array = array();
	    
	    $args = array(
	        'post_type' => 'movies',
	        'posts_per_page' => 100,
	    );
	    

	    // Get any existing copy of our transient data
        if ( false === ( $movies_query = get_transient( 'cacheMoviesQuery' ) ) ) {
          // It wasn't there, so regenerate the data and save the transient
          $movies_query = new WP_Query($args);
          set_transient( 'cacheMoviesQuery', $movies_query );
        }

	    if ( $movies_query->have_posts() ) : while ( $movies_query->have_posts() ) : $movies_query->the_post();
	        $post_id = get_the_ID();
	        
	        $movies_array['data'][] = array(
	           'id' => $post_id,
	           'title' => get_the_title(),
	           'poster_url'=> get_post_meta($post_id, 'poster_url', true),
	           'rating' => get_post_meta($post_id, 'rating', true),
	           'year' => get_post_meta($post_id, 'year', true),
	           'short_description' => get_post_meta($post_id, 'short_description', true)
	        );
	        
        endwhile;
	    
        wp_reset_postdata(); 
	    
        endif;
        
        header( 'Content-Type: application/json' );
	    wp_send_json( $movies_array );
	    
	}
	
	public function register_shortcode() {
	    add_shortcode('list-movies', array( $this, 'render_shortcode' ) );
	}
	
    public function render_shortcode() {
        $output = "<div ng-app='moviesNolte'><div data-movies-nolte-list></div></div>";
        echo $output;
    }
    
    public function import_script_files() {
        wp_enqueue_script( 'angular-js', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js', array(), false, true );
        wp_enqueue_script( 'movies-nolte-js', plugins_url( 'js/movies-nolte.js', __FILE__ ), array(), false, true );
    }
}
?>