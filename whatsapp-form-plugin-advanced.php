<?php
/**
 * Plugin Name: WhatsApp Form Manager - Advanced
 * Description: Formulário para WhatsApp com interface visual para gerenciar campos (estilo CF7-like) e customização do estilo.
 * Version: 1.0.0
 * Author: Comércio do Site / Miguel
 * Text Domain: whatsapp-form-manager-advanced
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define('WFM_ADV_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WFM_ADV_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WFM_ADV_VERSION', '1.0.0');

require_once WFM_ADV_PLUGIN_DIR . 'includes/options.php';
require_once WFM_ADV_PLUGIN_DIR . 'includes/admin-pages.php';

/* Frontend shortcode & enqueue */
add_shortcode('whatsapp_form', 'wfm_adv_render_form');

function wfm_adv_render_form($atts = []) {
    $opts = wfm_adv_get_options();
    ob_start();
    ?>
    <div class="wfm-advanced-wrapper" style="text-align:<?php echo esc_attr($opts['style_alignment']); ?>;">
        <div class="contact-form" id="wfm-contact-form">
            <form id="whatsappForm" class="wfm-form">
                <?php foreach($opts['fields'] as $f):
                    $id = esc_attr($f['id']);
                    $label = esc_attr($f['label']);
                    $ph = esc_attr($f['placeholder'] ?? '');
                    $required = !empty($f['required']) ? 'required' : '';
                    if ($f['type'] === 'textarea'): ?>
                        <textarea id="<?php echo $id; ?>" name="<?php echo $id; ?>" rows="4" placeholder="<?php echo $ph; ?>" <?php echo $required; ?>></textarea>
                    <?php else: ?>
                        <input id="<?php echo $id; ?>" name="<?php echo $id; ?>" type="<?php echo $f['type']=='phone' ? 'text' : esc_attr($f['type']); ?>" placeholder="<?php echo $ph; ?>" <?php echo $required; ?> />
                    <?php endif;
                endforeach; ?>
                <?php if ($opts['recaptcha_enabled'] && !empty($opts['recaptcha_site_key'])): ?>
                    <div id="wfm-recaptcha-wrap" style="margin:10px 0;">
                        <div class="g-recaptcha" data-sitekey="<?php echo esc_attr($opts['recaptcha_site_key']); ?>"></div>
                    </div>
                <?php endif; ?>

                <p><button type="submit" id="wfm-submit" class="wfm-button"><?php echo esc_html($opts['style_button_text']); ?></button></p>
            </form>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

add_action('wp_enqueue_scripts', 'wfm_adv_enqueue_front_assets');
function wfm_adv_enqueue_front_assets(){
    $opts = wfm_adv_get_options();
    $should_load = true;
    if ($opts['load_on_home']) {
        $should_load = is_home() || is_front_page();
    }
    if (!$should_load) return;

    wp_enqueue_style('wfm-adv-front', WFM_ADV_PLUGIN_URL . 'assets/css/front.css', [], WFM_ADV_VERSION);
    // inject style variables
    $custom = wfm_adv_generate_css_from_styleopts($opts);
    if ($custom) wp_add_inline_style('wfm-adv-front', $custom);

    wp_enqueue_script('wfm-adv-front', WFM_ADV_PLUGIN_URL . 'assets/js/whatsapp-form.js', [], WFM_ADV_VERSION, true);
    wp_localize_script('wfm-adv-front', 'wfmAdvData', [
        'phone' => $opts['phone'],
        'fields' => $opts['fields'],
        'recaptcha_enabled' => $opts['recaptcha_enabled'],
        'recaptcha_site_key' => $opts['recaptcha_site_key'],
    ]);

    if ($opts['recaptcha_enabled'] && !empty($opts['recaptcha_site_key'])) {
        wp_enqueue_script('wfm-google-recaptcha','https://www.google.com/recaptcha/api.js', [], null, true);
    }
}

/* helper css generator */
function wfm_adv_generate_css_from_styleopts($opts){
    $bg = $opts['style_bg_color'] ?? '#ffffff';
    $input_bg = $opts['style_input_bg'] ?? '#ffffff';
    $btn_bg = $opts['style_btn_bg'] ?? '#fea45a';
    $btn_hover = $opts['style_btn_hover'] ?? '#e09450';
    $radius = intval($opts['style_radius'] ?? 10) . 'px';
    $maxw = intval($opts['style_max_width'] ?? 400) . 'px';
    $css = "
    .wfm-advanced-wrapper .contact-form{background:{$bg};padding:20px;border-radius:{$radius};box-shadow:0 4px 10px rgba(0,0,0,0.08);max-width:{$maxw};margin:auto;}
    .wfm-advanced-wrapper .contact-form input,.wfm-advanced-wrapper .contact-form textarea{width:100%;padding:10px;margin-bottom:12px;border:1px solid #ccc;border-radius:8px;background:{$input_bg};}
    .wfm-advanced-wrapper .wfm-button{width:100%;padding:12px;background:{$btn_bg};color:#fff;border:none;border-radius:8px;font-size:16px;cursor:pointer;}
    .wfm-advanced-wrapper .wfm-button:hover{background:{$btn_hover};}
    ";
    return $css;
}

/* Activation: set defaults */
register_activation_hook(__FILE__, function(){
    if ( get_option('wfm_adv_options') === false ) {
        add_option('wfm_adv_options', wfm_adv_get_default_options());
    }
});
