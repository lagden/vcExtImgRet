<?php
/*
Plugin Name: Add image retina
Plugin URI: http://lagden.webfaction.com/wp/addons/vcExtImgRet.zip
Description: Easy way to add images retina
Version: 0.1.0
Author: Thiago Lagden
Author URI: http://lagden.in
License: GPLv2 or later
*/

// don't load directly
if (!defined('ABSPATH')) die('-1');

class VCExtendAddonClass {
  function __construct() {
    // We safely integrate with VC with this hook
    add_action( 'init', array( $this, 'integrateWithVC' ) );

    // Use this when creating a shortcode addon
    add_shortcode( 'imgret', array( $this, 'renderImgRetina' ) );

    // Register CSS and JS
    // add_action( 'wp_enqueue_scripts', array( $this, 'loadCssAndJs' ) );
  }

  public function integrateWithVC() {
    // Check if Visual Composer is installed
    if (!defined('WPB_VC_VERSION')) {
      // Display notice that Visual Compser is required
      add_action('admin_notices', [$this, 'showVcVersionNotice']);
      return;
    }

    /*
    Add your Visual Composer logic here.
    Lets call vc_map function to "register" our custom shortcode within Visual Composer interface.

    More info: http://kb.wpbakery.com/index.php?title=Vc_map
    */
    vc_map( [
      "name" => __("Add image retina", 'vc_extend'),
      "description" => __("Easy way to add images retina", 'vc_extend'),
      "base" => "imgret",
      "class" => "",
      "controls" => "full",
      "icon" => plugins_url('assets/retina.svg', __FILE__), // or css class name which you can reffer in your css file later. Example: "vc_extend_my_class"
      "category" => __('Content', 'js_composer'),
      "params" => [
        [
          "type" => "attach_image",
          "holder" => "div",
          "class" => "",
          "heading" => __("Image 1x", 'vc_extend'),
          "param_name" => "img1x",
          "value" => null,
          "description" => __("Choose a small image", 'vc_extend')
        ],
        [
          "type" => "attach_image",
          "holder" => "div",
          "class" => "",
          "heading" => __("Image 2x", 'vc_extend'),
          "param_name" => "img2x",
          "value" => null,
          "description" => __("Choose a retina image", 'vc_extend')
        ],
        [
          "type" => "textfield",
          "holder" => "div",
          "class" => "",
          "heading" => __("Put a caption for image", 'vc_extend'),
          "param_name" => "alt",
          "value" => null,
          "description" => __("Set a caption for image", 'vc_extend')
        ],
        [
          "type" => "textfield",
          "holder" => "div",
          "class" => "",
          "heading" => __("Custom CSS Class", 'vc_extend'),
          "param_name" => "css",
          "value" => null,
          "description" => __("Set a custom CSS Class", 'vc_extend')
        ]
      ]
    ]);
  }

  /*
  Shortcode logic how it should be rendered
  */
  public function renderImgRetina($atts, $content = null) {
    extract(shortcode_atts([
      'img1x' => null,
      'img2x' => null,
      'css' => null,
      'alt' => null,
    ], $atts));
    $content = wpb_js_remove_wpautop($content, true);
    $output = "<img src=\"{$img1x}\" srcset=\"{$img2x} 2x\" alt=\"{$alt}\" class=\"{$css}\">";
    return $output;
  }

    /*
    Load plugin css and javascript files which you may need on front end of your site
    */
    public function loadCssAndJs() {
      // wp_register_style( 'vc_extend_style', plugins_url('assets/vc_extend.css', __FILE__) );
      // wp_enqueue_style( 'vc_extend_style' );

      // If you need any javascript files on front end, here is how you can load them.
      //wp_enqueue_script( 'vc_extend_js', plugins_url('assets/vc_extend.js', __FILE__), array('jquery') );
    }

    /*
    Show notice if your plugin is activated but Visual Composer is not
    */
    public function showVcVersionNotice() {
      $plugin_data = get_plugin_data(__FILE__);
      echo '
      <div class="updated">
        <p>'.sprintf(__('<strong>%s</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'vc_extend'), $plugin_data['Name']).'</p>
      </div>';
    }
  }

// Finally initialize code
  new VCExtendAddonClass();