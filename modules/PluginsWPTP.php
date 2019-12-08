<?php

if (!defined('ABSPATH')) exit;

class PluginsWPTP extends WPTelegramPro
{
    protected $tabID = 'plugins-wptp-tab',
        $plugins = array(
        'contact-form-7' => array(
            'class' => 'ContactForm7WPTP',
            'path' => 'contact-form-7/wp-contact-form-7.php'
        ),
        'wpforms' => array(
            'class' => 'WPFormsWPTP',
            'path' => 'wpforms/wpforms.php'
        ),
        'wpforms-lite' => array(
            'class' => 'WPFormsWPTP',
            'path' => 'wpforms-lite/wpforms.php'
        ),
        'formidable' => array(
            'class' => 'FormidableFormsWPTP',
            'path' => 'formidable/formidable.php'
        ),
        'gravityforms' => array(
            'class' => 'GravityFormsWPTP',
            'path' => 'gravityforms/gravityforms.php'
        ),
    ),
        $currentActivePlugins = array();
    public static $instance = null;

    public function __construct()
    {
        if (!$this->check_plugins()) return;
        parent::__construct(true);

        add_filter('wptelegrampro_settings_tabs', [$this, 'settings_tab'], 35);
        add_action('wptelegrampro_settings_content', [$this, 'settings_content']);
    }

    function settings_tab($tabs)
    {
        $tabs[$this->tabID] = __('Plugins', $this->plugin_key);
        return $tabs;
    }

    function settings_content()
    {
        ?>
        <div id="<?php echo $this->tabID ?>-content" class="wptp-tab-content hidden">
            <table>
                <?php do_action('wptelegrampro_plugins_settings_content'); ?>
            </table>
        </div>
        <?php
    }

    /**
     * Check active support plugins
     *
     * @return bool
     */
    function check_plugins()
    {
        foreach ($this->plugins as $plugin => $info)
            if ($this->check_plugin_active($info['path'])) {
                $this->currentActivePlugins[] = $plugin;
                require_once WPTELEGRAMPRO_PLUGINS_DIR . $info['class'] . '.php';
            }
        return count($this->currentActivePlugins) > 0;
    }

    /**
     * Returns an instance of class
     * @return PluginsWPTP
     */
    static function getInstance()
    {
        if (self::$instance == null)
            self::$instance = new PluginsWPTP();
        return self::$instance;
    }
}

$PluginsWPTP = PluginsWPTP::getInstance();