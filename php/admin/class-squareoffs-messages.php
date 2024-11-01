<?php
/**
 * Admin messages handler class.
 *
 * @package squareoffs
 */

/**
 * Squareoffs Admin Messages.
 *
 * Easily create and retrieve messages in the WP admin.
 */
class Squareoffs_Messages {

	/**
	 * Reference to *Singleton* instance of this class
	 *
	 * @var SQUAREOFFS_Messages Instance.
	 */
	private static $instance;

	/**
	 * WP User ID.
	 *
	 * @var numeric
	 */
	private $user_id = 0;

	/**
	 * Option name.
	 *
	 * @var string
	 */
	private $option = 'squareoffs_messages';

	/**
	 * Returns the instance of this class.
	 *
	 * @return Singleton The *Singleton* instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor. Do not call directly.
	 *
	 * @return void
	 */
	protected function __construct() {
		$this->user_id = get_current_user_id();
	}

	/**
	 * Add new message.
	 *
	 * @param string $message Message text.
	 * @param string $status  Status.
	 * @return void
	 */
	public function add( $message, $status = 'error' ) {
		$messages = get_option( $this->option . '_' . $this->user_id, array() );
		$messages[] = array(
			'status'  => $status,
			'message' => $message,
		);
		update_option( $this->option . '_' . $this->user_id, $messages );
	}

	/**
	 * Get messages.
	 *
	 * @return array Messages.
	 */
	public function get() {
		$messages = get_option( $this->option . '_' . $this->user_id, array() );
		return is_array( $messages ) ? $messages : array();
	}

	/**
	 * Delete all messages.
	 *
	 * @return void
	 */
	public function clear() {
		delete_option( $this->option . '_' . $this->user_id );
	}

}
