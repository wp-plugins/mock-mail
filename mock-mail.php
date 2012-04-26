<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Plugin Name: Mock Mail
 * Description: Email testing for WordPress.
 * Author: ampt
 * Version: 0.0.1
 * Author URI: http://notfornoone.com/
 */

if ( ! class_exists( 'Mock_Mail' ) ) :

class Mock_Mail {
	protected $transport;
	protected $host;
	protected $port;
	protected $username;
	protected $password;
	protected $smtp_auth;
	protected $smtp_secure;

	/**
	 * Setup hooks.
	 *
	 * @param array $config Configuration options.
	 */
	public function __construct( $config = array() ) {
		foreach ( $config as $key => $val ) {
			if ( property_exists( $this, $key ) ) {
				$this->$key = $val;
			}
		}

		add_action( 'phpmailer_init', array( $this, 'phpmailer_init' ) );
	}

	/**
	 * Set PHPMailer configuration options
	 *
	 * @param object $mail PHPMailer instance
	 */
	public function phpmailer_init( $mail ) {
		switch( $this->transport ) {
			case 'sendmail':
				$mail->IsSendmail();
				break;

			case 'qmail':
				$mail->IsQmail();
				break;

			case 'smtp':
				$mail->IsSMTP();
				$mail->set( 'Host', $this->host );
				$mail->set( 'Port', $this->port );
				$mail->set( 'Username', $this->username );
				$mail->set( 'Password', $this->password );
				$mail->set( 'SMTPAuth', $this->smtp_auth );
				$mail->set( 'SMTPSecure', $this->smtp_secure );
				break;
		}
	}

	/**
	 * Get property, used in unit tests.
	 *
	 * @param string $name The property name.
	 * @return mixed The property value.
	 */
	public function get( $name ) {
		if ( ! isset( $this->$name ) ) {
			return false;
		}

		return $this->$name;
	}
}

// Let's roll, unless we are running tests
if ( ! isset( $_SERVER['WP_ENV'] ) || 'test' != $_SERVER['WP_ENV'] ) {
	// Defaults
	$config = array(
		'transport'   => 'mail',
		'host'        => 'localhost',
		'port'        => 25,
		'username'    => '',
		'password'    => '',
		'smtp_auth'   => false,
		'smtp_secure' => '',
	);

	// Defined variables from wp-config
	$define = array(
		'transport'   => 'MOCK_MAIL_TRANSPORT',
		'host'        => 'MOCK_MAIL_HOST',
		'port'        => 'MOCK_MAIL_PORT',
		'username'    => 'MOCK_MAIL_USERNAME',
		'password'    => 'MOCK_MAIL_PASSWORD',
		'smtp_auth'   => 'MOCK_MAIL_SMTP_AUTH',
		'smtp_secure' => 'MOCK_MAIL_SMTP_SECURE',
	);

	foreach ( $config as $key => $val ) {
		if ( defined( $define[$key] ) ) {
			$config[$key] = constant( $define[$key] );
		}
	}

    $GLOBALS['mock_mail'] = new Mock_Mail( $config );
}

endif;
