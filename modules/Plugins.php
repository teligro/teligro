<?php

namespace teligro;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Plugins extends Teligro {
	protected $tabID = 'plugins-teligro-tab',
		$plugins = array(
		'contact-form-7'                      => array(
			'class' => 'ContactForm7',
			'path'  => 'contact-form-7/wp-contact-form-7.php'
		),
		'wpforms'                             => array(
			'class' => 'WPForms',
			'path'  => 'wpforms/wpforms.php'
		),
		'wpforms-lite'                        => array(
			'class' => 'WPForms',
			'path'  => 'wpforms-lite/wpforms.php'
		),
		'formidable'                          => array(
			'class' => 'FormidableForms',
			'path'  => 'formidable/formidable.php'
		),
		'gravityforms'                        => array(
			'class' => 'GravityForms',
			'path'  => 'gravityforms/gravityforms.php'
		),
		'ninja-forms'                         => array(
			'class' => 'NinjaForms',
			'path'  => 'ninja-forms/ninja-forms.php'
		),
		'caldera-forms'                       => array(
			'class' => 'CalderaForms',
			'path'  => 'caldera-forms/caldera-core.php'
		),
		'everest-forms'                       => array(
			'class' => 'EverestForms',
			'path'  => 'everest-forms/everest-forms.php'
		),
		'happyforms'                          => array(
			'class' => 'HappyForms',
			'path'  => 'happyforms/happyforms.php'
		),
		'weforms'                             => array(
			'class' => 'WeForms',
			'path'  => 'weforms/weforms.php'
		),
		'visual-form-builder'                 => array(
			'class' => 'VisualFormBuilder',
			'path'  => 'visual-form-builder/visual-form-builder.php'
		),
		'quform'                              => array(
			'class' => 'QuForm',
			'path'  => 'quform/quform.php'
		),
		'html-forms'                          => array(
			'class' => 'HTMLForms',
			'path'  => 'html-forms/html-forms.php'
		),
		'forminator'                          => array(
			'class' => 'Forminator',
			'path'  => 'forminator/forminator.php'
		),
		'wp-sms'                              => array(
			'class' => 'WPSMS',
			'path'  => 'wp-sms/wp-sms.php'
		),
		'mailchimp-for-wp'                    => array(
			'class' => 'MailchimpForWP',
			'path'  => 'mailchimp-for-wp/mailchimp-for-wp.php'
		),
		'newsletter'                          => array(
			'class' => 'Newsletter',
			'path'  => 'newsletter/plugin.php'
		),
		'wordfence'                           => array(
			'class' => 'Wordfence',
			'path'  => 'wordfence/wordfence.php'
		),
		'better-wp-security'                  => array(
			'class' => 'IThemesSecurity',
			'path'  => 'better-wp-security/better-wp-security.php'
		),
		'all-in-one-wp-security-and-firewall' => array(
			'class' => 'AllInOneWPSecurityFirewall',
			'path'  => 'all-in-one-wp-security-and-firewall/wp-security.php'
		),
		'wp-cerber'                           => array(
			'class' => 'WPCerberSecurity',
			'path'  => 'wp-cerber/wp-cerber.php'
		),
		'dologin'                             => array(
			'class' => 'Dologin',
			'path'  => 'dologin/dologin.php'
		),
		'backwpup'                            => array(
			'class' => 'BackWPup',
			'path'  => 'backwpup/backwpup.php'
		),
		'backupwordpress'                     => array(
			'class' => 'BackUpWordPress',
			'path'  => 'backupwordpress/backupwordpress.php'
		),
		'wp-statistics'                       => array(
			'class' => 'WPStatistics',
			'path'  => 'wp-statistics/wp-statistics.php'
		),
		'wp-user-avatar'                      => array(
			'class' => 'WPUserAvatar',
			'path'  => 'wp-user-avatar/wp-user-avatar.php'
		),
	),
		$currentActivePlugins = array();
	public static $instance = null;

	public function __construct() {
		parent::__construct();
		//$this->plugins_loaded();
		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ], 99999 );
	}

	function plugins_loaded() {
		if ( ! $this->check_plugins() )
			return;
		add_filter( 'teligro_settings_tabs', [ $this, 'settings_tab' ], 35 );
		add_action( 'teligro_settings_content', [ $this, 'settings_content' ] );
	}

	function settings_tab( $tabs ) {
		$tabs[ $this->tabID ] = __( 'Plugins', $this->plugin_key );

		return $tabs;
	}

	function settings_content() {
		?>
        <div id="<?php echo $this->tabID ?>-content" class="teligro-tab-content hidden">
            <table>
				<?php if ( ! $this->user ) { ?>
                    <tr>
                        <th><?php _e( 'Tip', $this->plugin_key ) ?></th>
                        <td><?php _e( 'You will need to connect your WordPress account to the Telegram account to receive the notification. To get started, enable telegram connectivity and go to your WordPress profile page.',
								$this->plugin_key ) ?></td>
                    </tr>
					<?php
				}
				do_action( 'teligro_plugins_settings_content' ); ?>
            </table>
        </div>
		<?php
	}

	/**
	 * Check active support plugins
	 *
	 * @return bool
	 */
	function check_plugins() {
		foreach ( $this->plugins as $plugin => $info ) {
			if ( file_exists( TELIGRO_PLUGINS_DIR . $info['class'] . '.php' ) && $this->check_plugin_active( $info['path'] ) ) {
				if ( isset( $info['preneed'] ) ) {
					foreach ( $info['preneed'] as $file ) {
						if ( file_exists( TELIGRO_PLUGINS_DIR . $file ) )
							require_once TELIGRO_PLUGINS_DIR . $file;
					}
				}
				$this->currentActivePlugins[] = $plugin;
				require_once TELIGRO_PLUGINS_DIR . $info['class'] . '.php';
			}
		}

		return count( $this->currentActivePlugins ) > 0;
	}

	/**
	 * Returns an instance of class
	 * @return Plugins
	 */
	static function getInstance() {
		if ( self::$instance == null )
			self::$instance = new Plugins();

		return self::$instance;
	}
}

Plugins::getInstance();