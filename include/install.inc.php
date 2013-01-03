<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

/**
 * The installation function is called by main.inc.php and maintain.inc.php
 * in order to install and/or update the plugin.
 *
 * That's why all operations must be conditionned :
 *    - use "if empty" for configuration vars
 *    - use "IF NOT EXISTS" for table creation
 *
 * Unlike the functions in maintain.inc.php, the name of this function must be unique
 * and not enter in conflict with other plugins.
 */

function birthdate_install() 
{
  global $conf, $prefixeTable;
  
  // add a new column to existing table
  $result = pwg_query('SHOW COLUMNS FROM `'.TAGS_TABLE.'` LIKE "birthdate";');
  if (!pwg_db_num_rows($result))
  {      
    pwg_query('ALTER TABLE `' . TAGS_TABLE . '` ADD `birthdate` datetime default NULL;');
  }
}
?>