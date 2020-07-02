<?php
/**
 * Class Test_Wp_Auto_Updater_Notification_Send_Mail
 *
 * @package Wp_Auto_Updater
 */

class Test_Wp_Auto_Updater_Notification_Send_Mail extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->wp_auto_updater_notification = new WP_Auto_Updater_Notification();
	}

	/**
	 * @test
	 * @group notification
	 */
	public function send_mail() {
		$info_success = array();
		$info_failed = array();
		$result = $this->wp_auto_updater_notification->send_email( 'theme', $info_success, $info_failed );
		$this->assertNull( $result );


		$info_success[] = 'test v1.0.0 (upgraded from v0.0.0)';
		$info_failed[] = 'test v2.0.0';

		$options = array(
			'notification' => array(
				'core'        => false,
				'theme'       => false,
				'plugin'      => false,
				'translation' => false,
			),
			'mail' => array(
				'from'        => '',
				'admin_email' => true,
				'recipients'  => array(),
			),
		);

		update_option( 'wp_auto_updater_notification_options', $options );

		$result = $this->wp_auto_updater_notification->send_email( 'theme', $info_success, $info_failed );
		$this->assertNull( $result );

		$options = array(
			'notification' => array(
				'core'        => false,
				'theme'       => true,
				'plugin'      => true,
				'translation' => true,
			),
			'mail' => array(
				'from'        => '',
				'admin_email' => true,
				'recipients'  => array(),
			),
		);

		update_option( 'wp_auto_updater_notification_options', $options );

		$result = $this->wp_auto_updater_notification->send_email( 'theme', $info_success, $info_failed );
		$this->assertNull( $result );

		$result = $this->wp_auto_updater_notification->send_email( 'plugin', $info_success, $info_failed );
		$this->assertNull( $result );

		$result = $this->wp_auto_updater_notification->send_email( 'translation', $info_success, $info_failed );
		$this->assertNull( $result );
	}

	/**
	 * @test
	 * @group notification
	 */
	public function mail_body() {
		$info_success[] = 'test v1.0.0 (upgraded from v0.0.0)';
		$info_failed[] = 'test v2.0.0';

		$options = array(
			'notification' => array(
				'core'        => false,
				'theme'       => true,
				'plugin'      => true,
				'translation' => true,
			),
			'mail' => array(
				'from'        => '',
				'admin_email' => true,
				'recipients'  => array(),
			),
		);

		update_option( 'wp_auto_updater_notification_options', $options );

		add_filter( 'wp_auto_updater_notification/wp_mail', array( $this, '_wp_body' ), 10, 3 );
		$result = $this->wp_auto_updater_notification->send_email( 'theme', $info_success, $info_failed );
		$this->assertNull( $result );
		remove_filter( 'wp_auto_updater_notification/wp_mail', array( $this, '_wp_body' ) );
	}

	public function _wp_body( $email, $info_success, $info_failed ) {
		$this->assertRegExp( '/test v1\.0\.0 \(upgraded from v0\.0\.0\)/', $email['body'] );
		$this->assertRegExp( '/test v2\.0\.0/', $email['body'] );
	}

	/**
	 * @test
	 * @group notification
	 */
	public function change_mail_from() {
		$options = array(
			'notification' => array(
				'core'        => true,
				'theme'       => false,
				'plugin'      => false,
				'translation' => false,
			),
			'mail' => array(
				'from'        => '',
				'admin_email' => true,
				'recipients'  => array(),
			),
		);

		update_option( 'wp_auto_updater_notification_options', $options );

		$from = $this->wp_auto_updater_notification->change_mail_from( 'somebody@example.com' );
		$this->assertEquals( 'somebody@example.com', $from );

		$options = array(
			'notification' => array(
				'core'        => true,
				'theme'       => false,
				'plugin'      => false,
				'translation' => false,
			),
			'mail' => array(
				'from'        => 'test@example.com',
				'admin_email' => true,
				'recipients'  => array(),
			),
		);

		update_option( 'wp_auto_updater_notification_options', $options );

		$from = $this->wp_auto_updater_notification->change_mail_from( 'test@example.com' );
		$this->assertEquals( 'test@example.com', $from );
	}

	/**
	 * @test
	 * @group notification
	 */
	public function change_core_update_email() {
		$options = array(
			'notification' => array(
				'core'        => true,
				'theme'       => false,
				'plugin'      => false,
				'translation' => false,
			),
			'mail' => array(
				'from'        => '',
				'admin_email' => false,
				'recipients'  => array(),
			),
		);

		update_option( 'wp_auto_updater_notification_options', $options );

		$email_array = array(
			'to'      => 'somebody@example.com',
			'subject' => 'test',
			'body'    => 'test',
			'headers' => '',
		);

		$email = $this->wp_auto_updater_notification->change_core_update_email( $email_array, 'success', '', '' );
		$this->assertEquals( $email_array, $email );
		$this->assertEquals( 'somebody@example.com', $email['to'] );
	}

	/**
	 * @test
	 * @group notification
	 */
	public function change_email() {
		$options = array(
			'notification' => array(
				'core'        => true,
				'theme'       => false,
				'plugin'      => false,
				'translation' => false,
			),
			'mail' => array(
				'from'        => '',
				'admin_email' => false,
				'recipients'  => array(),
			),
		);

		update_option( 'wp_auto_updater_notification_options', $options );

		$email_array = array(
			'to'      => 'somebody@example.com',
			'subject' => 'test',
			'body'    => 'test',
			'headers' => '',
		);

		$email = $this->wp_auto_updater_notification->change_email( $email_array, array(), array() );
		$this->assertEquals( $email_array, $email );
		$this->assertEquals( 'somebody@example.com', $email['to'] );


		$options = array(
			'notification' => array(
				'core'        => true,
				'theme'       => false,
				'plugin'      => false,
				'translation' => false,
			),
			'mail' => array(
				'from'        => '',
				'admin_email' => true,
				'recipients'  => array(),
			),
		);

		update_option( 'wp_auto_updater_notification_options', $options );

		$email = $this->wp_auto_updater_notification->change_email( $email_array, array(), array() );
		$this->assertEquals( 'somebody@example.com', $email['to'] );
	}

	/**
	 * @test
	 * @group notification
	 */
	public function set_update_notification_core() {
		$this->wp_auto_updater_notification->set_update_notification_core();
		$this->assertEquals( 10, has_filter( 'auto_core_update_send_email', '__return_true' ) );

		$options = array(
			'notification' => array(
				'core'        => false,
				'theme'       => false,
				'plugin'      => false,
				'translation' => false,
			),
			'mail' => array(
				'from'        => 'test@example.com',
				'admin_email' => true,
				'recipients'  => array(),
			),
		);

		update_option( 'wp_auto_updater_notification_options', $options );

		$this->wp_auto_updater_notification->set_update_notification_core();
		$this->assertEquals( 10, has_filter( 'auto_core_update_send_email', '__return_false' ) );
	}

}