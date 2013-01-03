<?php 
/*
Plugin Name: Birthdate
Version: auto
Description: Set a birthdate on a tag and Piwigo will display the age on any photo.
Plugin URI: auto
Author: plg
Author URI: http://le-gall.net/pierrick
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

global $prefixeTable;

// +-----------------------------------------------------------------------+
// | Define plugin constants                                               |
// +-----------------------------------------------------------------------+

defined('BIRTHDATE_ID') or define('BIRTHDATE_ID', basename(dirname(__FILE__)));
define('BIRTHDATE_PATH' ,   PHPWG_PLUGINS_PATH . BIRTHDATE_ID . '/');
define('BIRTHDATE_TABLE',   $prefixeTable . 'birthdate');
define('BIRTHDATE_ADMIN',   get_root_url() . 'admin.php?page=plugin-' . BIRTHDATE_ID);
define('BIRTHDATE_PUBLIC',  get_absolute_root_url() . make_index_url(array('section' => 'birthdate')) . '/');
define('BIRTHDATE_DIR',     PWG_LOCAL_DIR . 'birthdate/');
define('BIRTHDATE_VERSION', 'auto');


// +-----------------------------------------------------------------------+
// | Add event handlers                                                    |
// +-----------------------------------------------------------------------+
// init the plugin
add_event_handler('init', 'birthdate_init');

if (defined('IN_ADMIN'))
{
  // admin plugins menu link
  add_event_handler('get_admin_plugin_menu_links', 'birthdate_admin_plugin_menu_links');
  
  // file containing all previous handlers functions
  include_once(BIRTHDATE_PATH . 'include/admin_events.inc.php');
}
else
{
  // add age on tags
  add_event_handler('loc_end_picture', 'birthdate_loc_end_picture');
  
  // file containing all previous handlers functions
  include_once(BIRTHDATE_PATH . 'include/public_events.inc.php');
}

// files containing specific plugin functions
include_once(BIRTHDATE_PATH . 'include/functions.inc.php');

/**
 * plugin initialization
 *   - check for upgrades
 *   - unserialize configuration
 *   - load language
 */
function birthdate_init()
{
  global $conf, $pwg_loaded_plugins;
  
  // apply upgrade if needed
  if (
    BIRTHDATE_VERSION == 'auto' or
    $pwg_loaded_plugins[BIRTHDATE_ID]['version'] == 'auto' or
    version_compare($pwg_loaded_plugins[BIRTHDATE_ID]['version'], BIRTHDATE_VERSION, '<')
  )
  {
    // call install function
    include_once(BIRTHDATE_PATH . 'include/install.inc.php');
    birthdate_install();
    
    // update plugin version in database
    if ( $pwg_loaded_plugins[BIRTHDATE_ID]['version'] != 'auto' and BIRTHDATE_VERSION != 'auto' )
    {
      $query = '
UPDATE '. PLUGINS_TABLE .'
SET version = "'. BIRTHDATE_VERSION .'"
WHERE id = "'. BIRTHDATE_ID .'"';
      pwg_query($query);
      
      $pwg_loaded_plugins[BIRTHDATE_ID]['version'] = BIRTHDATE_VERSION;
      
      if (defined('IN_ADMIN'))
      {
        $_SESSION['page_infos'][] = 'Birthdate updated to version '. BIRTHDATE_VERSION;
      }
    }
  }
  
  // load plugin language file
  load_language('plugin.lang', BIRTHDATE_PATH);
}

?>