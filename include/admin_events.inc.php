<?php
defined('BIRTHDATE_PATH') or die('Hacking attempt!');

/**
 * admin plugins menu link
 */
function birthdate_admin_plugin_menu_links($menu) 
{
  array_push($menu, array(
    'NAME' => 'Birthdate',
    'URL' => BIRTHDATE_ADMIN,
  ));
  return $menu;
}
?>