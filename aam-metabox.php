<?php
/**
 * Class handles author selection on the post edit page.
 */
class AAM_Author_Metabox {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {

		add_action( 'wp_ajax_author_search', array( $this, 'ajax_author_search' ), 10 );

		add_action( 'load-post.php', array( $this, 'pre_register' ), 10 );
		add_action( 'load-post-new.php', array( $this, 'pre_register' ), 10 );
	}


	/**
	 * Pre register the metabox.
	 *
	 * @return void
	 */
	final public function pre_register() {

		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
	}


	/**
	 * Registers metabox.
	 *
	 * @param string  $post_type Post type.
	 * @param WP_Post $post      Post object.
	 *
	 * @return void
	 */
	public function register_metabox( $post_type, $post ) {

		if ( ! current_user_can( 'edit_others_posts' ) ) {
			return;
		}

		remove_meta_box( 'authordiv', $post_type, 'normal' );
		add_meta_box( 'authorboxdiv', __( 'Author', AAM_TD ), array( $this, 'display_metabox' ), $post_type, 'normal', 'default' );
	}


	/**
	 * Enqueue admin scripts required for author metabox.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		// auto-suggest
		wp_register_script( 'author-metabox', plugins_url( '/aam-metabox.js', __FILE__ ), array( 'jquery', 'jquery-ui-autocomplete' ), '0.1' );
		wp_enqueue_script( 'author-metabox' );

		/* Script variables */
		$params = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'author-search' ),
		);
		wp_localize_script( 'author-metabox', 'author_metabox_params', $params );
	}


	/**
	 * Display Languages metabox.
	 *
	 * @param WP_Post $post Post object.
	 *
	 * @return void
	 */
	public function display_metabox( $post ) {
		$user_id = $post->post_author;
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$user = get_user_by( 'id', $user_id );

		$user_label = $user->display_name . ' (' . $user->user_email . ')';

		// label field
		$args_label_field = array(
			'id' => 'post_author_override_label',
			'name' => 'post_author_override_label',
			'class' => 'large-text',
			'type' => 'text',
			'value' => $user_label,
		);
		$input_label_field = html( 'input', $args_label_field );

		// hidden field
		$args_hidden_field = array(
			'id' => 'post_author_override',
			'name' => 'post_author_override',
			'type' => 'hidden',
			'value' => $user_id,
		);
		$input_hidden_field = html( 'input', $args_hidden_field );

		$div = $input_label_field . $input_hidden_field;

		echo html( 'div', array( 'id' => 'author-metabox-autocomplete', 'class' => 'authordiv' ), $div );
	}


	/**
	 * Handles authors search via ajax.
	 *
	 * @return void
	 */
	public function ajax_author_search() {
		$results = array();

		if ( 'GET' != $_SERVER['REQUEST_METHOD'] ) {
			die( -1 );
		}

		if ( empty( $_GET['term'] ) || strlen( trim( $_GET['term'] ) ) < 3 ) {
			die( -1 );
		}

		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'author-search' ) ) {
			die( -1 );
		}

		// filter used to make it possible to search by 'display_name', passing it via 'search_columns' arg will not work ;(
		add_filter( 'user_search_columns', array( $this, 'user_search_columns' ), 10, 3 );
		$users = get_users( array(
			'search'  => '*' . trim( $_REQUEST['term'] ) . '*',
			'number' => 15,
		) );
		remove_filter( 'user_search_columns', array( $this, 'user_search_columns' ), 10, 3 );

		foreach ( $users as $user ) {
			$results[] = array(
				'label' => $user->display_name . ' (' . $user->user_email . ')',
				'value' => $user->ID,
			);
		}


		die( json_encode( $results ) );
	}


	/**
	 * Modifies columns for user search. 
	 *
	 * @param array         $search_columns Array of column names to be searched.
	 * @param string        $search         Text being searched.
	 * @param WP_User_Query $user_query     The current WP_User_Query instance.
	 *
	 * @return array
	 */
	public function user_search_columns( $search_columns, $search, $user_query ) {
		return array( 'display_name', 'user_email' );
	}


}
