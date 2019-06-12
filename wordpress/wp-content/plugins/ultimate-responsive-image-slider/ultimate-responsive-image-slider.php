<?php
/*
Plugin Name: Ultimate Responsive Image Slider
Plugin URI:  https://wordpress.org/plugins/ultimate-responsive-image-slider/
Description: Add unlimited image slides using Ultimate Responsive Image Slider in any Page and Post content to give an attractive mode to represent contents.
Version:     3.2.13
Author:      WP Frank
Author URI:  https://wpfrank.com/
Text Domain: ultimate-responsive-image-slider
Domain Path: /languages
License:     GPL2

Ultimate Responsive Image Slider is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or any later version.
 
Ultimate Responsive Image Slider is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License 
along with Ultimate Responsive Image Slider. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/

//Constant Variable
define("URIS_TD", "ultimate-responsive-image-slider" );
define("URIS_PLUGIN_URL", plugin_dir_url(__FILE__));

// Apply default settings on activation
register_activation_hook( __FILE__, 'WRIS_DefaultSettingsPro' );
function WRIS_DefaultSettingsPro() {
    $DefaultSettingsProArray = serialize( array(
		//layout 3 settings
		"WRIS_L3_Title_Align" 			=> 2,
		"WRIS_L3_Slide_Title"   		=> 1,
		"WRIS_L3_Set_slide_Title"		=> 0,
		"WRIS_L3_Auto_Slideshow"   		=> 1,
		"WRIS_L3_Transition"   			=> 1,
		"WRIS_L3_Transition_Speed"   	=> 5000,
		"WRIS_L3_Sliding_Arrow"   		=> 1,
		"WRIS_L3_Slider_Navigation"   	=> 1,
		"WRIS_L3_Navigation_Button"   	=> 1,
		"WRIS_L3_Slider_Width"   		=> "1000",
		"WRIS_L3_Slider_Height"   		=> "500",
		"WRIS_L3_Font_Style"			=> "Arial",
		"WRIS_L3_Title_Color"   		=> "#FFFFFF",
		"WRIS_L3_Slider_Scale_Mode"		=> "cover",
		"WRIS_L3_Slider_Auto_Scale"		=> 1,
		"WRIS_L3_Title_BgColor"   		=> "#000000",
		"WRIS_L3_Desc_Color"   			=> "#FFFFFF",
		"WRIS_L3_Desc_BgColor"  		=> "#000000",
		"WRIS_L3_Navigation_Color"  	=> "#000000",
		"WRIS_L3_Fullscreeen"  			=> 1,
		"WRIS_L3_Custom_CSS"   			=> "",

		'WRIS_L3_Slide_Order'   		=> "ASC",
		'WRIS_L3_Navigation_Position'  	=> "bottom",
		'WRIS_L3_Slide_Distance'   		=> 5,
		'WRIS_L3_Thumbnail_Style'   	=> "border",
		'WRIS_L3_Thumbnail_Width'   	=> 120,
		'WRIS_L3_Thumbnail_Height'   	=> 120,
		'WRIS_L3_Width'   				=> "custom",
		'WRIS_L3_Height'   				=> "custom",
		'WRIS_L3_Navigation_Bullets_Color' => "#000000",
		'WRIS_L3_Navigation_Pointer_Color' => "#000000",
    ));
    add_option("WRIS_Settings", $DefaultSettingsProArray);
}

// Image Crop Size Function 
add_image_size( 'rpggallery_admin_thumb', 300, 300, true ); 
add_image_size( 'rpggallery_admin_large', 500,9999 );

// Add settings link on plugin page
function ris_links($links) {
	$ris_pro_link = '<a href="http://wpfrank.com/" target="_blank">WP Frank</a>';
	array_unshift($links, $ris_pro_link);	
	$ris_settings_link = '<a href="edit.php?post_type=ris_gallery">Settings</a>'; 
	array_unshift($links, $ris_settings_link);	
	return $links; 
} 
$uris_plugin_name = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$uris_plugin_name", 'ris_links' );

// Slider Text Widget Support
add_filter( 'widget_text', 'do_shortcode' );

class URIS {

    private static $instance;
    private $admin_thumbnail_size = 150;
    private $thumbnail_size_w = 150;
    private $thumbnail_size_h = 150;
	var $counter;

    public static function forge() {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }
	
	private function __construct() {
		
		$this->counter = 0;
		
		// image crop function
		add_image_size('rpg_gallery_admin_thumb', $this->admin_thumbnail_size, $this->admin_thumbnail_size, true);
		add_image_size('rpg_gallery_thumb', $this->thumbnail_size_w, $this->thumbnail_size_h, true);
		
		// Translate plugin
		add_action('plugins_loaded', array(&$this, 'WRIS_Translate'), 1);
		
		// CPT Function
		add_action('init', array(&$this, 'ResponsiveImageSlider'),1);
		
		// generate metabox funtion
		add_action('add_meta_boxes', array(&$this, 'add_all_ris_meta_boxes'));
		add_action('admin_init', array(&$this, 'add_all_ris_meta_boxes'), 1);
		
		// metabox setting save function
		add_action('save_post', array(&$this, 'add_image_meta_box_save'), 9, 1);
		add_action('save_post', array(&$this, 'ris_settings_meta_save'), 9, 1);
		
		// add new slide function
		add_action('wp_ajax_uris_get_thumbnail', array(&$this, 'ajax_get_thumbnail_uris'));
		
		add_shortcode('rpggallery', array(&$this, 'shortcode'));
    }
	
	/**
	 * Translate Plugin
	 */
	public function WRIS_Translate() {
		load_plugin_textdomain('ultimate-responsive-image-slider', FALSE, dirname( plugin_basename(__FILE__)).'/languages/' );
	}
	
	// Register Custom Post Type
	public function ResponsiveImageSlider() {
		$labels = array(
			'name' => _x( 'Ultimate Responsive Image Slider', URIS_TD ),
			'singular_name' => _x( 'Ultimate Responsive Image Slider', URIS_TD ),
			'add_new' => __( 'Add New Slider', URIS_TD ),
			'add_new_item' => __( 'Add New Slider', URIS_TD ),
			'edit_item' => __( 'Edit Slider', URIS_TD ),
			'new_item' => __( 'New Slider', URIS_TD ),
			'view_item' => __( 'View Slider', URIS_TD ),
			'search_items' => __( 'Search Slider', URIS_TD ),
			'not_found' => __( 'No Slider found', URIS_TD ),
			'not_found_in_trash' => __( 'No Slider Found in Trash', URIS_TD ),
			'parent_item_colon' => __( 'Parent Slider:', URIS_TD ),
			'all_items' => __( 'All Sliders', URIS_TD ),
			'menu_name' => _x( 'UR Image Slider', URIS_TD ),
		);

		$args = array(
			'labels' => $labels,
			'hierarchical' => false,
			'supports' => array( 'title', 'revisions' ),
			'public' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 10,
			'menu_icon' => 'dashicons-format-gallery',
			'show_in_nav_menus' => false,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'has_archive' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => false,
			'capability_type' => 'post'
		);
        
        register_post_type( 'ris_gallery', $args );
        add_filter( 'manage_edit-ris_gallery_columns', array(&$this, 'ris_gallery_columns' )) ;
        add_action( 'manage_ris_gallery_posts_custom_column', array(&$this, 'ris_gallery_manage_columns' ), 10, 2 );
	}
	
	function ris_gallery_columns( $columns ){
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __( 'UR Image Slider Title' ),
            'shortcode' => __( 'UR Image Slider Shortcode' ),
            'date' => __( 'Date' )
        );
        return $columns;
    }

    function ris_gallery_manage_columns( $column, $post_id ){
        global $post;
        switch( $column ) {
          case 'shortcode' :
            echo '<input type="text" value="[URIS id='.$post_id.']" readonly="readonly" />';
            break;
          default :
            break;
        }
    }
	
	public function add_all_ris_meta_boxes() {
		add_meta_box( __('Add Slides', URIS_TD), __('Add Slides', URIS_TD), array(&$this, 'ris_generate_add_image_meta_box_function'), 'ris_gallery', 'normal', 'low' );
		add_meta_box( __('Apply Setting On Ultimate Responsive Image Slider', URIS_TD), __('Apply Setting On Ultimate Responsive Image Slider', URIS_TD), array(&$this, 'ris_settings_meta_box_function'), 'ris_gallery', 'normal', 'low');
		add_meta_box ( __('Copy Slider Shortcode', URIS_TD), __('Copy Slider Shortcode', URIS_TD), array(&$this, 'ris_shotcode_meta_box_function'), 'ris_gallery', 'side', 'low');
		add_meta_box('Show US Some Love & Rate Us', 'Show US Some Love & Rate Us', array(&$this, 'uris_Rate_us_meta_box_function'), 'ris_gallery', 'side', 'low');
		add_meta_box( __('Upgrade To Pro Plugin', URIS_TD), __('Upgrade To Pro Plugin', URIS_TD), array(&$this, 'ris_upgrade_to_pro_meta_box_function'), 'ris_gallery', 'side', 'low');
	}
	
	//Rate Us Meta Box
	public function uris_Rate_us_meta_box_function() { ?>
		<style>
		.urisp-rate-us span.dashicons {
			width: 30px;
			height: 30px;
		}
		.urisp-rate-us span.dashicons-star-filled:before {
			content: "\f155";
			font-size: 30px;
		}
		.wpf_uris_fivestar{
			width: 80%;
		}

		a.wpf_fs_btn {
		    text-decoration: none;
		    background-color: #d72323;
		    padding-left: 20px;
		    padding-right: 20px;
		    border-radius: 5px;
		    color: #fff;
		    padding-top: 8px;
		    padding-bottom: 8px;
		}
		a:focus, a:hover {
		    color: #fff !important;
		    text-decoration: none !important;
		}
		</style>
		<div align="center">
			<p>Please Review & Rate Us On WordPress</p>
			<a class="upgrade-to-pro-demo urisp-rate-us" style=" text-decoration: none; height: 40px; width: 40px;" href="https://wordpress.org/plugins/ultimate-responsive-image-slider/#reviews" target="_blank">
				<img class="wpf_uris_fivestar" src="<?php $path = URIS_PLUGIN_URL."/img/5star.jpg" ; echo $path; ?>">			
			</a>
		</div>
		<div class="upgrade-to-pro" style="text-align:center;margin-bottom:10px;margin-top:10px;">
			<a href="https://wordpress.org/support/view/plugin-reviews/ultimate-responsive-image-slider" target="_blank" class="wpf_fs_btn">RATE US</a>
		</div>
		<?php
	}
	
	/**
	 * Upgrade To Meta Box
	 */
	public function ris_upgrade_to_pro_meta_box_function() { ?>
		<div class="welcome-panel-column">
			<h3>Unlock More Features in Ultimate Responsive Image Slider Pro</h3>
			<p><strong>5 Design Layouts, Transition Effect, Color Customizations, 500+ Google Fonts For Slide Title & Description, Slides Ordering, Link On Slides, 2 Light Box Style, Various Slider Control Settings</strong></p>
			<a class="button button-primary button-hero load-customize hide-if-no-customize" target="_blank" href="https://wpfrank.com/demo/ultimate-responsive-image-slider-pro/">Check Pro Plugin Demo</a>
			<a class="button button-primary button-hero load-customize hide-if-no-customize" target="_blank" href="https://wpfrank.com/account/signup/ultimate-responsive-image-slider-pro/">Buy Pro Plugin $21</a>
		</div>
		<?php
	}
	
	/**
	 * This function display Add New Image interface
	 * Also loads all saved gallery photos into photo gallery
	 */
    public function ris_generate_add_image_meta_box_function($post) { ?>
		<div id="rpggallery_container">
			<input type="hidden" id="risp_wl_action" name="risp_wl_action" value="wl-save-settings">
            <ul id="rpg_gallery_thumbs" class="clearfix">
				<?php
				/* load saved photos into ris */
				$WRIS_AllPhotosDetails = unserialize(base64_decode(get_post_meta( $post->ID, 'ris_all_photos_details', true)));
				$i = 0;
				$TotalImages =  get_post_meta( $post->ID, 'ris_total_images_count', true );
				if($TotalImages) {
					if(is_array($WRIS_AllPhotosDetails)){
						foreach($WRIS_AllPhotosDetails as $WRIS_SinglePhotoDetails) {
							$name = $WRIS_SinglePhotoDetails['rpgp_image_label'];
							$desc = $WRIS_SinglePhotoDetails['rpgp_image_desc'];						
							$UniqueString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
							$url = $WRIS_SinglePhotoDetails['rpgp_image_url'];
							$url1 = $WRIS_SinglePhotoDetails['rpggallery_admin_thumb'];
							$url3 = $WRIS_SinglePhotoDetails['rpggallery_admin_large']; ?>
							<li class="rpg-image-entry" id="rpg_img">
								<a class="gallery_remove rpggallery_remove" href="#gallery_remove" id="rpg_remove_bt" ><img src="<?php echo  URIS_PLUGIN_URL.'img/Close-icon-uris.ico'; ?>" /></a>
								<div class="rpp-admin-inner-div1" >
									<img src="<?php echo esc_url ( $url1 ); ?>" class="rpg-meta-image" alt=""  style="">
									<input type="hidden" id="unique_string[]" name="unique_string[]" value="<?php echo esc_attr( $UniqueString ); ?>" />
								</div>
								<div class="rpp-admin-inner-div2" >
									<input type="text" id="rpgp_image_url[]" name="rpgp_image_url[]" class="rpg_label_text"  value="<?php echo esc_url( $url ); ?>"  readonly="readonly" style="display:none;" />
									<input type="text" id="rpggallery_admin_thumb[]" name="rpggallery_admin_thumb[]" class="rpg_label_text"  value="<?php echo esc_url( $url1 ); ?>"  readonly="readonly" style="display:none;" />
									<input type="text" id="rpggallery_admin_large[]" name="rpggallery_admin_large[]" class="rpg_label_text"  value="<?php echo esc_url( $url3 ); ?>"  readonly="readonly" style="display:none;" />
									<p>
										<label><?php _e('Slide Title', URIS_TD); ?></label>
										<input type="text" id="rpgp_image_label[]" name="rpgp_image_label[]" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php _e('Enter Slide Title', URIS_TD); ?>" class="rpg_label_text">
									</p>
									<p>
										<label><?php _e('Slide Descriptions', URIS_TD); ?></label>
										<textarea rows="4" cols="50" id="rpgp_image_desc[]" name="rpgp_image_desc[]" placeholder="<?php _e('Enter Slide Description', URIS_TD); ?>" class=" urisp_richeditbox_<?php echo $i; ?> rpg_label_text"><?php echo htmlentities( $desc ); ?></textarea>
										<button type="button" class="btn btn-md btn-info btn-block" data-toggle="modal" data-target="#myModal" onclick="urisp_richeditor(<?php echo $i; ?>)"><?php _e('Use Rich Text Editor', URIS_TD); ?> <i class="fa fa-edit"></i></button>
									</p>
								</div>
							</li>
							<?php
							$i++;
						} // end of foreach
					}	
				} else {
					$TotalImages = 0;
				}
				?>
            </ul>
			
			<!--rich editor modal-->
			<div class="modal fade" id="myModal" role="dialog">
				<div class="modal-dialog">
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
						   <h4 class="modal-title"><i class="fa fa-edit" style="font-size:23px"></i> Rich Editor</h4>
						    <button type="button" class="close" data-dismiss="modal">&times;</button>
						</div>
						<div class="modal-body">
						  <p>
							<?php
								$urisp_box = '';
								$urisp_editor_id = 'fetch_wpeditor_data';
								$settings  = array( 'media_buttons' => false ,'quicktags' => array( 'buttons' => 'strong,em,del,link,close' ) ); // for remove media button from editor
								//wp_editor( $urisp_box, $urisp_editor_id); // with media button
								wp_editor( $urisp_box, $urisp_editor_id, $settings); // without media button
							?>
							<input type="hidden" value="" id="fetch_wpelement" name="fetch_wpelement" />
						  </p>
						</div>
						<div class="modal-footer">
						  <button type="button" class="btn btn-default" onclick="urisp_richeditor_putdata()" data-dismiss="modal">Continue</button>
						  <button type="button" class="btn btn-default" data-dismiss="modal">Exit</button>
						</div>
					</div>
				</div>
			</div>
			<!--rich editor modal-->
        </div>
		
		<!--Add New Image Button-->
		<div class="rpg-image-entry add_rpg_new_image" id="uris_gallery_upload_button" data-uploader_title="Upload Image" data-uploader_button_text="Select" >
			<div class="dashicons dashicons-plus"></div>
			<p><?php _e('Add New Slide', URIS_TD); ?></p>
		</div>
		<div class="rpg-image-entry del_rpg_image" id="uris_delete_all_button">
			<div class="dashicons dashicons-trash"></div>
			<p><?php _e('Delete All Slides', URIS_TD); ?></p>
		</div>
		<div style="clear:left;"></div>
        <style>
		.review-notice {
			background-color: #27A4DD !important;
			color: #FFFFFF !important;
		}
        </style>
		<script>
		function urisp_richeditor(id){			
			var richeditdata = jQuery(".urisp_richeditbox_"+id).val();
			jQuery("#fetch_wpeditor_data").val(richeditdata);
			jQuery("#fetch_wpeditor_data-html").click();
			jQuery("#fetch_wpelement").val(id);
		}	
		function urisp_richeditor_putdata(){			
			jQuery("#fetch_wpeditor_data").click();
			jQuery("#fetch_wpeditor_data-html").click();
			var fetch_wpelement_id = jQuery("#fetch_wpelement").val();
			var fetched_data = jQuery("#fetch_wpeditor_data").val();
			jQuery(".urisp_richeditbox_"+fetch_wpelement_id).val(fetched_data);
		}
		</script>
		<?php
    }	

	/**
	 * This function display Add New Image interface
	 * Also loads all saved gallery photos into photo gallery
	 */
    public function ris_settings_meta_box_function($post) {
		wp_enqueue_script('uris-bootstrap-js', URIS_PLUGIN_URL.'js/bootstrap.min.js');
		wp_enqueue_style('uris-bootstrap-min', URIS_PLUGIN_URL.'css/bootstrap-latest/bootstrap.css');
		wp_enqueue_style('uris-font-awesome-5.0.8', URIS_PLUGIN_URL.'css/font-awesome-latest/css/fontawesome-all.min.css');
		wp_enqueue_style('uris-editor-modal', URIS_PLUGIN_URL.'css/editor-modal.css');			
		wp_enqueue_script('media-upload');
		wp_enqueue_script('uris-media-uploader-js', URIS_PLUGIN_URL . 'js/uris-multiple-media-uploader.js', array('jquery'));
		wp_enqueue_media();
	
		//custom add image box css
		wp_enqueue_style('ris-meta-css', URIS_PLUGIN_URL.'css/ris-meta.css');
	
		//font awesome css
		wp_enqueue_style('ris-font-awesome-5.0.8', URIS_PLUGIN_URL.'css/font-awesome-latest/css/fontawesome-all.min.css');

		//tool-tip js & css
		wp_enqueue_script('ris-tool-tip-js',URIS_PLUGIN_URL.'tooltip/jquery.darktooltip.min.js', array('jquery'));
		wp_enqueue_style('ris-tool-tip-css', URIS_PLUGIN_URL.'tooltip/darktooltip.min.css');
		
		//color-picker css n js
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'ris-color-picker-script', plugins_url('js/wl-color-picker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );

		// code-mirror css & js for custom css section
		wp_enqueue_style('ris_codemirror-css', URIS_PLUGIN_URL.'css/codemirror/codemirror.css');
		wp_enqueue_style('ris_blackboard', URIS_PLUGIN_URL.'css/codemirror/blackboard.css');
		wp_enqueue_style('ris_show-hint-css', URIS_PLUGIN_URL.'css/codemirror/show-hint.css');
		
		wp_enqueue_script('ris_codemirror-js',URIS_PLUGIN_URL.'css/codemirror/codemirror.js',array('jquery'));
		wp_enqueue_script('ris_css-js',URIS_PLUGIN_URL.'css/codemirror/ris-css.js',array('jquery'));
		wp_enqueue_script('ris_css-hint-js',URIS_PLUGIN_URL.'css/codemirror/css-hint.js',array('jquery'));

		require_once('ultimate-responsive-image-slider-settings-meta-box.php');		
	}
	
	public function ris_shotcode_meta_box_function() { ?>
		<p><?php _e("Use below shortcode in any Page/Post to publish your slider", URIS_TD);?></p>
		<input readonly="readonly" type="text" value="<?php echo "[URIS id=".get_the_ID()."]"; ?>">
		<?php 
	}

	public function admin_thumb_uris($id) {
		$image  = wp_get_attachment_image_src($id, 'rpggallery_admin_original', true);
        $image1 = wp_get_attachment_image_src($id, 'rpggallery_admin_thumb', true);
        $image3 = wp_get_attachment_image_src($id, 'rpggallery_admin_large', true);
		$UniqueString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
        ?>
		<li class="rpg-image-entry" id="rpg_img">
			<a class="gallery_remove rpggallery_remove" href="#gallery_remove" id="rpg_remove_bt" ><img src="<?php echo  URIS_PLUGIN_URL.'img/Close-icon-uris.ico'; ?>" /></a>
			<div class="rpp-admin-inner-div1" >
				<img src="<?php echo esc_url( $image1[0] ); ?>" class="rpg-meta-image" alt=""  style="">
				</div>
			<div class="rpp-admin-inner-div1" >
				<input type="text" id="rpgp_image_url[]" name="rpgp_image_url[]" class="rpg_label_text"  value="<?php echo esc_url( $image[0] ); ?>"  readonly="readonly" style="display:none;" />
				<input type="text" id="rpggallery_admin_thumb[]" name="rpggallery_admin_thumb[]" class="rpg_label_text"  value="<?php echo esc_url( $image1[0] ); ?>"  readonly="readonly" style="display:none;" />
				<input type="text" id="rpggallery_admin_large[]" name="rpggallery_admin_large[]" class="rpg_label_text"  value="<?php echo esc_url( $image3[0] ); ?>"  readonly="readonly" style="display:none;" />
				<p>
					<label><?php _e('Slide Title', URIS_TD); ?></label>
					<input type="text" id="rpgp_image_label[]" name="rpgp_image_label[]" placeholder="<?php _e('Enter Slide Title Here', URIS_TD); ?>" class="rpg_label_text">
				</p>
				<p>
					<label><?php _e('Slide Description', URIS_TD); ?></label>
					<textarea rows="4" cols="50" id="rpgp_image_desc[]" name="rpgp_image_desc[]" placeholder="<?php _e('Enter Slide Description Here', URIS_TD); ?>" class="urisp_richeditbox_<?php echo $id; ?> rpg_label_text"></textarea>
					<button type="button" class="btn btn-md btn-info btn-block" data-toggle="modal" data-target="#myModal" onclick="urisp_richeditor(<?php echo $id; ?>)"><?php _e('Use Rich Text Editor', URIS_TD); ?> <i class="fa fa-edit"></i></button>
				</p>
			</div>
		</li>
        <?php
    }
	
    public function ajax_get_thumbnail_uris() {
        echo $this->admin_thumb_uris($_POST['imageid']);
        die;
    }

    public function add_image_meta_box_save($PostID) {
	if(isset($PostID) && isset($_POST['risp_wl_action'])) {
			$TotalImages = count($_POST['rpgp_image_url']);
			$ImagesArray = array();
			if($TotalImages) {
				for($i=0; $i < $TotalImages; $i++) {
					$image_label =stripslashes(sanitize_text_field($_POST['rpgp_image_label'][$i]));
					$image_desc = stripslashes($_POST['rpgp_image_desc'][$i]);
					$url = sanitize_text_field( $_POST['rpgp_image_url'][$i] );
					$url1 = sanitize_text_field($_POST['rpggallery_admin_thumb'][$i] );
					$url3 = sanitize_text_field($_POST['rpggallery_admin_large'][$i] );
					$ImagesArray[] = array(
						'rpgp_image_label' => $image_label,
						'rpgp_image_desc' => $image_desc,
						'rpgp_image_url' => $url,
						'rpggallery_admin_thumb' => $url1,
						'rpggallery_admin_large' => $url3,
					);
				}
				update_post_meta($PostID, 'ris_all_photos_details', base64_encode(serialize($ImagesArray)));
				update_post_meta($PostID, 'ris_total_images_count', $TotalImages);
			} else {
				$TotalImages = 0;
				update_post_meta($PostID, 'ris_total_images_count', $TotalImages);
				$ImagesArray = array();
				update_post_meta($PostID, 'ris_all_photos_details', base64_encode(serialize($ImagesArray)));
			}
		}	
	}
	
	//save settings meta box values
	public function ris_settings_meta_save($PostID) {
		if(isset($PostID) && isset($_POST['wl_action']) == "wl-save-settings") {
			$WRIS_L3_Title_Align				=     $_POST['wl-l3-title-align'] ;
			$WRIS_L3_Slide_Title				=	 sanitize_option ( 'title', $_POST['wl-l3-slide-title'] );
			$WRIS_L3_Set_slide_Title			=	 sanitize_option ( 'set_slide_title', $_POST['wl-l3-set-slide-title'] );
			$WRIS_L3_Auto_Slideshow				=	 sanitize_option ( 'autoplay', $_POST['wl-l3-auto-slide'] );
			$WRIS_L3_Transition					=	 sanitize_option ( 'transition', $_POST['wl-l3-transition'] );
			$WRIS_L3_Transition_Speed			=	 sanitize_text_field( $_POST['wl-l3-transition-speed'] );
			$WRIS_L3_Sliding_Arrow				=	 sanitize_option ( 'arrow', $_POST['wl-l3-sliding-arrow'] );
			$WRIS_L3_Slider_Navigation			=	 sanitize_option ( 'navigation', $_POST['wl-l3-navigation'] );
			$WRIS_L3_Navigation_Button			=	 sanitize_option ( 'navigation_button', $_POST['wl-l3-navigation-button'] );
			$WRIS_L3_Slider_Width				=	 sanitize_option ( 'slider_width', $_POST['wl-l3-slider-width'] );
			$WRIS_L3_Slider_Height				=	 sanitize_option ( 'slider_height', $_POST['wl-l3-slider-height'] );
			$WRIS_L3_Font_Style					=	 sanitize_option ( 'font_style', $_POST['wl-l3-font-style'] );
			$WRIS_L3_Title_Color   				=	 sanitize_option ( 'title_color', $_POST['wl-l3-title-color'] );
			$WRIS_L3_Slider_Scale_Mode   		=	 sanitize_option ( 'slider_scale_mode', $_POST['wl-l3-slider_scale_mode'] );
			$WRIS_L3_Slider_Auto_Scale   		=	 sanitize_option ( 'slider_auto_scale', $_POST['wl-l3-slider-auto-scale'] );
			$WRIS_L3_Title_BgColor   			=	 sanitize_option ( 'title_bgcolor', $_POST['wl-l3-title-bgcolor'] );
			$WRIS_L3_Desc_Color   				=	 sanitize_option ( 'desc_color', $_POST['wl-l3-desc-color'] );
			$WRIS_L3_Desc_BgColor  				=	 sanitize_option ( 'desc_bgcolor', $_POST['wl-l3-desc-bgcolor'] );
			$WRIS_L3_Navigation_Color  			=	 sanitize_option ( 'navigation_color', $_POST['wl-l3-navigation-color'] );
			$WRIS_L3_Fullscreeen  				=	 sanitize_option ( 'fullscreen', $_POST['wl-l3-fullscreen'] );
			$WRIS_L3_Custom_CSS					=	 sanitize_text_field( $_POST['wl-l3-custom-css'] );
			$WRIS_L3_Slide_Order   				= 	 sanitize_option ( 'slide_order', $_POST['wl-l3-slide-order'] );
			$WRIS_L3_Navigation_Position   		= 	 sanitize_option ( 'navigation_position', $_POST['wl-l3-navigation-position'] );
			$WRIS_L3_Slide_Distance				= 	 sanitize_option ( 'slide_distance', $_POST['wl-l3-slide-distance'] );
			$WRIS_L3_Thumbnail_Style   			= 	 sanitize_option ( 'thumbnail_style', $_POST['wl-l3-thumbnail-style'] );
			$WRIS_L3_Thumbnail_Width   			= 	 sanitize_text_field( $_POST['wl-l3-navigation-width'] );
			$WRIS_L3_Thumbnail_Height   		= 	 sanitize_text_field( $_POST['wl-l3-navigation-height'] );
			$WRIS_L3_Width   					= 	 sanitize_text_field( $_POST['wl-l3-width'] );
			$WRIS_L3_Height   					= 	 sanitize_text_field( $_POST['wl-l3-height'] );
			$WRIS_L3_Navigation_Bullets_Color	= 	 sanitize_option ( 'navigation_bullet_color', $_POST['wl-l3-navigation-bullets-color'] );
			$WRIS_L3_Navigation_Pointer_Color	=	 sanitize_option ( 'navigation_pointer_color', $_POST['wl-l3-navigation-pointer-color'] );
			
			$WRIS_Settings_Array = serialize( array(
				'WRIS_L3_Title_Align'			=>  $WRIS_L3_Title_Align,
				'WRIS_L3_Slide_Title'  			=> 	$WRIS_L3_Slide_Title,
				'WRIS_L3_Set_slide_Title'		=>  $WRIS_L3_Set_slide_Title,
				'WRIS_L3_Auto_Slideshow'  		=> 	$WRIS_L3_Auto_Slideshow,
				'WRIS_L3_Transition'  			=> 	$WRIS_L3_Transition,
				'WRIS_L3_Transition_Speed'  	=> 	$WRIS_L3_Transition_Speed,
				'WRIS_L3_Sliding_Arrow'  		=> 	$WRIS_L3_Sliding_Arrow,
				'WRIS_L3_Slider_Navigation'  	=> 	$WRIS_L3_Slider_Navigation,
				'WRIS_L3_Navigation_Button'  	=> 	$WRIS_L3_Navigation_Button,
				'WRIS_L3_Slider_Width'  		=> 	$WRIS_L3_Slider_Width,
				'WRIS_L3_Slider_Height'  		=> 	$WRIS_L3_Slider_Height,
				'WRIS_L3_Font_Style'  			=> 	$WRIS_L3_Font_Style,
				'WRIS_L3_Title_Color'   		=> 	$WRIS_L3_Title_Color,
				'WRIS_L3_Slider_Scale_Mode'		=>  $WRIS_L3_Slider_Scale_Mode,
				'WRIS_L3_Slider_Auto_Scale'		=>  $WRIS_L3_Slider_Auto_Scale,
				'WRIS_L3_Title_BgColor'   		=> 	$WRIS_L3_Title_BgColor,
				'WRIS_L3_Desc_Color'   			=> 	$WRIS_L3_Desc_Color,
				'WRIS_L3_Desc_BgColor'  		=> 	$WRIS_L3_Desc_BgColor,
				'WRIS_L3_Navigation_Color' 		=> 	$WRIS_L3_Navigation_Color,
				'WRIS_L3_Fullscreeen' 			=> 	$WRIS_L3_Fullscreeen,
				'WRIS_L3_Custom_CSS'  			=> 	$WRIS_L3_Custom_CSS,
				'WRIS_L3_Slide_Order'   		=> 	$WRIS_L3_Slide_Order,
				'WRIS_L3_Navigation_Position'   => 	$WRIS_L3_Navigation_Position,
				'WRIS_L3_Slide_Distance'   		=> 	$WRIS_L3_Slide_Distance,
				'WRIS_L3_Thumbnail_Style'   	=> 	$WRIS_L3_Thumbnail_Style,
				'WRIS_L3_Thumbnail_Width'   	=> 	$WRIS_L3_Thumbnail_Width,
				'WRIS_L3_Thumbnail_Height'   	=> 	$WRIS_L3_Thumbnail_Height,
				'WRIS_L3_Width'   				=> 	$WRIS_L3_Width,
				'WRIS_L3_Height'   				=> 	$WRIS_L3_Height,
				'WRIS_L3_Navigation_Bullets_Color'      => 	$WRIS_L3_Navigation_Bullets_Color,
				'WRIS_L3_Navigation_Pointer_Color'      => 	$WRIS_L3_Navigation_Pointer_Color,
			));
			
			$WRIS_Gallery_Settings = "WRIS_Gallery_Settings_".$PostID;
			update_post_meta($PostID, $WRIS_Gallery_Settings, $WRIS_Settings_Array);
		}
	}
}

global $URIS;
$URIS = URIS::forge();

// Review Notice Box
// add_action( "admin_notices", "uris_admin_notice_resport" );
function uris_admin_notice_resport() {
	global $pagenow;
	$uris_screen = get_current_screen();
	if ( $pagenow == 'edit.php' && $uris_screen->post_type == "ris_gallery" ) {
		require_once ( 'uris-feature-admin-notice.php' );
	}
}

/**
 * upgrade to pro
 */
add_action('admin_menu' , 'uris_pro_SettingsPage');
function uris_pro_SettingsPage() {
	add_submenu_page('edit.php?post_type=ris_gallery', 'Help & Support', 'Help & Support', 'administrator', 'RIS-Help-page', 'RIS_Help_and_Support_page');
	function RIS_Help_and_Support_page() {
		wp_enqueue_style('bootstrap-admin.css', URIS_PLUGIN_URL.'css/bootstrap-latest/bootstrap-admin.css');
		require_once('help-and-support.php');
	}
}

/**
 * URIS Short Code
 */
require_once("ultimate-responsive-image-slider-short-code.php");
require_once( 'products.php' );
?>