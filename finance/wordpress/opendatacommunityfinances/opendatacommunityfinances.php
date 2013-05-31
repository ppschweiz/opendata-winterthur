<?php
/*
Plugin Name: OpenData Community Finances
Plugin URI: http://winterthur.zh.piratenpartei.ch/
Description: Access to public financial community data
Version: 0.1.0
Author: Marc Wäckerlin
Author URI: https://marc.waeckerlin.org/politik/index
License: GPL3
*/
if (!class_exists('OpenDataCommunityFinances')) {
  class OpenDataCommunityFinances {
    var $settings;
    var $dbpath;
    var $db;
    
    // Initialization
    function OpeDataCommunityFinances() {
      $dbpath='sqlite:finance.db';
      $db = new PDO($dbpath);
      $this->get_options();
      if (function_exists('load_plugin_textdomain'))
        load_plugin_textdomain('OpenDataCommunityFinances',
                               PLUGINDIR.'/'
                               .dirname(plugin_basename(__FILE__))
                               .'/languages',
                               dirname(plugin_basename( __FILE__ ))
                               .'/languages');

      // use [OpenDataCommunityFinances] in WordPress
      add_shortcode('OpenDataCommunityFinances', array(&$this, 'show'));

      if(is_admin()) {
        //add_action('admin_menu', array(&$this, 'add_menupages'));
      }
    }

    function get_options() {
      $this->settings = get_option('OpenDataCommunityFinances');
      //if (!array_key_exists('irgendwas',$this->settings)) {
      //  $this->settings['irgendwas'] = 'wert';
      //  update_option('TEMPLATE', $this->settings);
      //}
    }

    // calles on plugin activation
    function activate() {
      // Create Tables if needed or generate whatever on installation
    }

    // called before plugin removal
    function uninstall() {
      // Delete Tables or settings if needed be deinstallation
    }

    // admin menu
    function add_menupages() {
      // For Option Pages, see WordPress function:
      //   add_options_page()
      // For own Menu Pages, see WordPress function:
      //   add_menu_page() and add_submenu_page()
    }
    
    function show() {
      $out = wp_cache_get( 'archive', 'OpenDataCommunityFinances');
      if (!$out) {
        if ($db) {
          $years = $db->query('SELECT * FROM years ORDER BY year ASC, boa');
          $out = "<ul>";
          foreach ($years as $year) {
            $boa = $year['boa']==0 ? 'Budget ' : 'Rechnung ';
            $out .= "<li>".$boa.$year['year']."</li>";
          }
          $out .= "</ul>";
          wp_cache_set( 'archive', $out, 'OpenDataCommunityFinances', '86400');
        } else {
          $out=__("NO DATABASE", "OpenDataCommunityFinances");
        }
      }
      echo $out;
    }

  }

  add_action('init', 'init_OpenDataCommunityFinances');
  function init_OpenDataCommunityFinances() {
    global $OpenDataCommunityFinances;
    $OpenDataCommunityFinances = new $OpenDataCommunityFinances();
  }

}

if (function_exists('register_activation_hook')) {
  register_activation_hook(__FILE__, array('$OpenDataCommunityFinances',
                                           'activate'));
}

if (function_exists('register_uninstall_hook')) {
  register_uninstall_hook(__FILE__, array('$OpenDataCommunityFinances',
                                          'uninstall'));
}

?>