<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_enqueue_style( 'uris-feature-notice-css', URIS_PLUGIN_URL . 'css/uris-feature-notice.css' );
?>
<div class="wb_plugin_feature notice  is-dismissible">
    <div class="wb_plugin_feature_banner default_pattern pattern_ ">
        <div class="wb-col-md-6 wb-col-sm-12 box">
            <div class="ribbon"><span>Check Pro</span></div>
            <img class="wp-img-responsive" src="<?php echo URIS_PLUGIN_URL . 'img/ultimate.png'; ?>" alt="img">
        </div>
        <div class="wb-col-md-6 wb-col-sm-12 wb_banner_featurs-list">
            <span class="gp_banner_head"><h2><?php _e( 'Ultimate Responsive Image Slider Pro Features', URIS_TD ); ?> </h2></span>
            <ul>
                <li><?php _e( '5 Slider Layout', URIS_TD ); ?></li>
                <li><?php _e( 'Double Lightbox Effect', URIS_TD ); ?></li>
                <li><?php _e( '500 Plus Google Fonts', URIS_TD ); ?></li>
                <li><?php _e( 'Link Slide', URIS_TD ); ?></li>
                <li><?php _e( 'Multiple Image Uploader', URIS_TD ); ?></li>
                <li><?php _e( 'Hide or Show Gallery Title and Description', URIS_TD ); ?></li>
                <li><?php _e( 'Brand Slider Integrated', URIS_TD ); ?></li>
                <li><?php _e( 'Drag and Drop Slide Image Control', URIS_TD ); ?></li>
                <li><?php _e( 'Widget Slider Utility', URIS_TD ); ?></li>
               
            </ul>
            <div class="wp_btn-grup">
                <a class="wb_button-primary" href="https://wpfrank.com/demo/ultimate-responsive-image-slider-pro/"
                   target="_blank"><?php _e( 'Check Pro Demo', URIS_TD ); ?></a>
                <a class="wb_button-primary" href="https://wpfrank.com/account/signup/ultimate-responsive-image-slider-pro"
                   target="_blank"><?php _e( 'Buy Pro', URIS_TD ); ?> $21</a>
            </div>

        </div>
    </div>
</div>