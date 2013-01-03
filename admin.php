<?php
/**
 * This is the main administration page, if you have only one admin page you can put 
 * directly its code here or using the tabsheet system like bellow
 */

defined('BIRTHDATE_PATH') or die('Hacking attempt!');
 
global $template, $page, $conf;


// get current tab
$page['tab'] = (isset($_GET['tab'])) ? $_GET['tab'] : $page['tab'] = 'birthdates';

// tabsheet
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
$tabsheet = new tabsheet();
$tabsheet->set_id('birthdate');

$tabsheet->add('birthdates', l10n('Birthdates'), BIRTHDATE_ADMIN . '-birthdates');
$tabsheet->select($page['tab']);
$tabsheet->assign();

// include page
include(BIRTHDATE_PATH . 'admin/' . $page['tab'] . '.php');

// template vars
$template->assign(array(
  'BIRTHDATE_PATH'=> get_root_url() . BIRTHDATE_PATH, // used for images, scripts, ... access
  'BIRTHDATE_ABS_PATH'=> realpath(BIRTHDATE_PATH),    // used for template inclusion (Smarty needs a real path)
  'BIRTHDATE_ADMIN' => BIRTHDATE_ADMIN,
  ));
  
// send page content
$template->assign_var_from_handle('ADMIN_CONTENT', 'birthdate_content');

?>