<?php

namespace AxelSpringer\WP\S3;

use AxelSpringer\WP\Bootstrap\Plugin\AbstractPlugin;
use Aws\Credentials\CredentialProvider;

/**
 * Class Plugin
 *
 * @package AxelSpringer\WP\S3
 */
class Plugin extends AbstractPlugin
{
    /**
     * Client
     *
     * @var Client
     */
    public $client;

    /**
     * Filter
     *
     * @var Filter
     */
    public $filter;

    /**
     * Filter
     *
     * @var Actions
     */
    public $actions;


    /**
     * Initializes the plugin
     */
    public function init()
    {
        // load options
        $this->setup->load_options( 'AxelSpringer\WP\S3\__OPTION__' );
        $this->settings = new Settings(
            __( __TRANSLATE__::SETTINGS_PAGE_TITLE ),
            __( __TRANSLATE__::SETTINGS_MENU_TITLE ),
            __PLUGIN__::SETTINGS_PAGE,
            __PLUGIN__::SETTINGS_PERMISSION,
            $this->setup->version
        );
        // https://github.com/axelspringer/templeton
        $this->setup->load_options( 'AxelSpringer\WP\S3\__SSM__' );

        // check for options
        if ( ! $this->setup->options[ 'wps3_endpoint' ]
            || ! $this->setup->options[ 'wps3_region' ]
            || ! $this->setup->options[ 'wps3_bucket' ] )
            return; // check required options

        // use default provider
        $provider = $this->load_provider();

        // new S3 client
        $this->client = new Client( $this->setup->options, $provider );

        // load hooks
        $this->load_hooks();
    }

    /**
     * Activates the Bootstrap plugin
     *
     * @return bool
     */
    public static function activation()
    {
        // noop
		return true;
    }

    /**
     * Do actions after init
     */
    public function after_init()
    {
        // noop
    }

    /**
     * Deactivates the Bootstrap plugin
     *
     * @return bool
     */
    public static function deactivation()
    {
        // noop
		return true;
    }

    /**
     * Loads the required WP hooks
     *
     * @return
     */
    public function load_hooks()
    {
        $this->filters  = new Filters( $this->client );
        $this->actions  = new Actions( $this->client );
    }

    /**
     * Credentials
     *
     * @return
     */
    public function load_provider()
    {
        $secret_access_key = $this->setup->options[ 'wps3_secret_access_key' ];
        $access_key = $this->setup->options[ 'wps3_access_key' ];

        if ( $secret_access_key && $access_key ) // use access key
            return [
                'key'    => $access_key,
                'secret' => $secret_access_key,
            ];

        // use credentials provider
        return CredentialProvider::defaultProvider();
    }

    /**
     * Enqueue required scripts
     *
     * @return
     */
    public function enqueue_scripts()
    {

    }

    /**
     * Enqueue shared styles and scripts
     *
     * @return
     */
    public function enqueue_admin_scripts()
    {

	}
}
