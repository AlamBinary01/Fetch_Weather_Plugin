<?php
/**
 * Plugin Name: Dynamic Weather Fetcher
 * Plugin URI: https://github.com/AlamBinary01/Fetch_Weather_Plugin
 * Description: A plugin that fetches weather data from OpenWeatherMap API based on city input and displays it.
 * Version: 1.2
 * Author: Haseeb Mushtaq
 * Author URI: https://github.com/AlamBinary01
 * Text Domain: dynamic-weather-fetcher
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'DAP_PLUGIN_VERSION', '1.2' );
define( 'DAP_PLUGIN_SLUG', 'dynamic-weather-fetcher' );

// Activation and Deactivation Hooks
function dap_activate_plugin() {
    if ( ! get_option( 'dap_plugin_activated' ) ) {
        add_option( 'dap_plugin_activated', true );
    }
}
register_activation_hook( __FILE__, 'dap_activate_plugin' );

function dap_deactivate_plugin() {
    delete_option( 'dap_plugin_activated' );
}
register_deactivation_hook( __FILE__, 'dap_deactivate_plugin' );

// Add Plugin Menu in Admin
function dap_add_admin_menu() {
    add_menu_page(
        __( 'Dynamic Weather Fetcher', 'dynamic-weather-fetcher' ),
        __( 'Weather Fetcher', 'dynamic-weather-fetcher' ),
        'manage_options',
        DAP_PLUGIN_SLUG,
        'dap_weather_fetcher_page',
        'dashicons-cloud',
        100
    );
    add_submenu_page(
        DAP_PLUGIN_SLUG,
        __( 'Weather Fetcher Settings', 'dynamic-weather-fetcher' ),
        __( 'Settings', 'dynamic-weather-fetcher' ),
        'manage_options',
        'dap-weather-settings',
        'dap_weather_settings_page'
    );
}
add_action( 'admin_menu', 'dap_add_admin_menu' );

// Enqueue CSS Styles
function dap_enqueue_styles( $hook ) {
    // Only load on plugin pages
    if ( 'toplevel_page_dynamic-weather-fetcher' === $hook || 'weather-fetcher_page_dap-weather-settings' === $hook ) {
        wp_enqueue_style( 'dap-styles', plugin_dir_url( __FILE__ ) . 'style.css' );
    }
}
add_action( 'admin_enqueue_scripts', 'dap_enqueue_styles' );

// Settings Page for API Key
function dap_weather_settings_page() {
    if ( isset( $_POST['dap_save_settings'] ) ) {
        $api_key = sanitize_text_field( $_POST['dap_api_key'] );
        update_option( 'dap_api_key', $api_key );
        echo '<div class="notice notice-success"><p>' . __( 'Settings saved successfully.', 'dynamic-weather-fetcher' ) . '</p></div>';
    }
    $api_key = get_option( 'dap_api_key', '' );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( __( 'Weather Fetcher Settings', 'dynamic-weather-fetcher' ) ); ?></h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th><label for="dap_api_key"><?php echo esc_html( __( 'API Key', 'dynamic-weather-fetcher' ) ); ?></label></th>
                    <td><input type="text" name="dap_api_key" id="dap_api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text" /></td>
                </tr>
            </table>
            <p class="submit"><input type="submit" name="dap_save_settings" class="button button-primary" value="<?php echo esc_attr( __( 'Save Settings', 'dynamic-weather-fetcher' ) ); ?>"></p>
        </form>
    </div>
    <?php
}

// Main Plugin Page
function dap_weather_fetcher_page() {
    ?>
    <div class="dap-wrap">
        <h1><?php echo esc_html( __( 'Dynamic Weather Fetcher', 'dynamic-weather-fetcher' ) ); ?></h1>
        <form method="post" action="" class="dap-form">
            <label for="city"><?php echo esc_html( __( 'Enter City:', 'dynamic-weather-fetcher' ) ); ?></label>
            <input type="text" id="city" name="city" required placeholder="<?php echo esc_attr( __( 'Enter city name', 'dynamic-weather-fetcher' ) ); ?>" />
            <input type="submit" name="fetch_weather" value="<?php echo esc_attr( __( 'Fetch Weather', 'dynamic-weather-fetcher' ) ); ?>" class="button button-primary" />
        </form>

        <?php
        if ( isset( $_POST['fetch_weather'] ) && ! empty( $_POST['city'] ) ) {
            $city = sanitize_text_field( $_POST['city'] );
            dap_fetch_and_display_weather( $city );
        }
        ?>
    </div>
    <?php
}

// Fetch and Display Weather
function dap_fetch_and_display_weather( $city ) {
    $api_key = get_option( 'dap_api_key' );
    if ( ! $api_key ) {
        echo '<div class="notice notice-error"><p>' . __( 'Please set your API key in the settings page.', 'dynamic-weather-fetcher' ) . '</p></div>';
        return;
    }

    // Use Transient to Cache Data
    $transient_key = 'dap_weather_' . sanitize_title( $city );
    $data_array = get_transient( $transient_key );

    if ( ! $data_array ) {
        $api_url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&units=metric&appid={$api_key}";
        $response = wp_remote_get( $api_url );

        if ( is_wp_error( $response ) ) {
            echo '<div class="notice notice-error"><p>' . __( 'Failed to retrieve data:', 'dynamic-weather-fetcher' ) . ' ' . esc_html( $response->get_error_message() ) . '</p></div>';
            return;
        }

        $data = wp_remote_retrieve_body( $response );
        $data_array = json_decode( $data, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            echo '<div class="notice notice-error"><p>' . __( 'Invalid JSON response:', 'dynamic-weather-fetcher' ) . ' ' . esc_html( json_last_error_msg() ) . '</p></div>';
            return;
        }

        if ( isset( $data_array['cod'] ) && $data_array['cod'] != 200 ) {
            echo '<div class="notice notice-warning"><p>' . __( 'Weather data not available for this city.', 'dynamic-weather-fetcher' ) . '</p></div>';
            return;
        }

        // Cache data for 30 minutes
        set_transient( $transient_key, $data_array, 30 * MINUTE_IN_SECONDS );
    }

    $temp = $data_array['main']['temp'];
    $weather = $data_array['weather'][0]['description'];
    $icon = $data_array['weather'][0]['icon'];
    $icon_url = "http://openweathermap.org/img/wn/{$icon}@2x.png";

    echo '<div class="dap-weather-box">';
    echo '<h2>' . esc_html( sprintf( __( 'Weather in %s:', 'dynamic-weather-fetcher' ), $city ) ) . '</h2>';
    echo '<p>' . esc_html( sprintf( __( 'Temperature: %s°C', 'dynamic-weather-fetcher' ), $temp ) ) . '</p>';
    echo '<p>' . esc_html( sprintf( __( 'Condition: %s', 'dynamic-weather-fetcher' ), ucfirst( $weather ) ) ) . '</p>';
    echo '<img src="' . esc_url( $icon_url ) . '" alt="' . esc_attr( $weather ) . '" />';
    echo '</div>';
}

// Shortcode for Displaying Weather
function dap_weather_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'city' => 'London',
    ), $atts );

    ob_start();
    dap_fetch_and_display_weather( $atts['city'] );
    return ob_get_clean();
}
add_shortcode( 'dynamic_weather_fetcher', 'dap_weather_shortcode' );

