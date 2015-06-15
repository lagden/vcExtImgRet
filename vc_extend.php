<?php
/*
Plugin Name: Add image retina
Plugin URI: http://lagden.webfaction.com/wp/addons/vcExtImgRet
Description: Easy way to add images retina
Version: 0.1.1
Author: Thiago Lagden
Author URI: http://lagden.in
License: GPLv2 or later
*/

if (!defined('ABSPATH')) die('-1');

class VCExtendImageRetinaClass {
  public function __construct() {
    add_action('init', [$this,'integrateWithVC']);
    add_shortcode('imgret', [$this,'renderImgRetina']);
  }

  public function integrateWithVC() {
    if (!defined('WPB_VC_VERSION')) {
      add_action('admin_notices', [$this, 'showVcVersionNotice']);
      return;
    }

    vc_map([
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
          "heading" => __("Image 1x", 'vc_extend'),
          "param_name" => "img1x",
          "description" => __("Choose a small image", 'vc_extend'),
        ],
        [
          "type" => "attach_image",
          "heading" => __("Image 2x", 'vc_extend'),
          "param_name" => "img2x",
          "description" => __("Choose a retina image", 'vc_extend'),
        ],
        [
          "type" => "textfield",
          "class" => "",
          "heading" => __("Put a caption for image", 'vc_extend'),
          "param_name" => "alt",
          "value" => "",
          "description" => __("Set a caption for image", 'vc_extend'),
        ],
        [
          "type" => "textfield",
          "class" => "",
          "heading" => __("Inline style", 'vc_extend'),
          "param_name" => "style",
          "value" => "",
          "description" => __("You can set the custom width and/or height here", 'vc_extend'),
        ],
        [
          "type" => "textfield",
          "class" => "",
          "heading" => __("Extra Class", 'vc_extend'),
          "param_name" => "css",
          "value" => "",
          "description" => __("Set extra class separated by space", 'vc_extend')
        ],
        [
          "type" => "textfield",
          "class" => "",
          "heading" => __("Image Link", 'vc_extend'),
          "param_name" => "link",
          "value" => "http://",
          "description" => __("Add link for image", 'vc_extend')
        ],
        [
          "type" => "dropdown",
          "class" => "",
          "heading" => __("Target", 'vc_extend'),
          "param_name" => "target",
          "value" => [
            "Blank" => "_blank",
            "Self" => "_self",
            "Parent" => "_parent",
            "Top" => "_top",
          ],
          "description" => __("Set extra class separated by space", 'vc_extend')
        ],
      ]
    ]);
  }

  public function renderImgRetina($atts, $content = null) {
    extract(shortcode_atts([
      'img1x'  => '',
      'img2x'  => '',
      'alt'    => '',
      'style'  => '',
      'css'    => '',
      'link'   => '',
      'target' => '',
    ], $atts));
    $content = wpb_js_remove_wpautop($content, true);

    $img = wp_get_attachment_image_src($img1x, 'full');
    $retina = wp_get_attachment_image_src($img2x, 'full');

    $dados = [
      'img' => $img[0],
      'retina' => $retina[0],
      'caption' => $alt,
      'style' => $style,
      'css' => $css,
    ];

    $outputImg = preg_replace_callback(
      '/\{(.*?)\}/i',
      function($matches) use ($dados) {
        return $dados[$matches[1]];
      },
      static::templateRetina());

    $output = '';

    if ($link !== '') {
      $output = "<a href=\"{$link}\" target=\"{$target}\">{$outputImg}</a>";
    } else {
      $output = $outputImg;
    }

    return $output;
  }

  static private function templateRetina() {
    return $template = implode('', [
      '<img ',
      'scr="{img}" ',
      'srcset="{retina} 2x" ',
      'alt="{caption}" ',
      'style="{style}" ',
      'class="{css}" />',
    ]);
  }

  public function showVcVersionNotice() {
    $plugin_data = get_plugin_data(__FILE__);
    echo '
    <div class="updated">
      <p>'.sprintf(__('<strong>%s</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'vc_extend'), $plugin_data['Name']).'</p>
    </div>';
  }
}

if(class_exists('VCExtendImageRetinaClass')) {
  $VCExtendImageRetinaClass = new VCExtendImageRetinaClass;
}
