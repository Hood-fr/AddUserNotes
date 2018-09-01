<?php
/*
Plugin Name: Add Users Notes
Version: 2.9.a
Description: Adds admin notes to users profiles
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=580
Author: ddtddt
Author URI: 
*/

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

global $prefixeTable;

// +-----------------------------------------------------------------------+
// | Define plugin constants                                               |
// +-----------------------------------------------------------------------+

define('USERNOTES_ID', basename(dirname(__FILE__)));
define('USERNOTES_PATH', PHPWG_PLUGINS_PATH.USERNOTES_ID.'/');

// init the plugin
add_event_handler('init', 'usernotes_init');

/**
 * plugin initialization
 *   - check for upgrades
 *   - load language
 */
function usernotes_init()
{
  // load plugin language file
  load_language('plugin.lang', USERNOTES_PATH);
}

add_event_handler('loc_begin_admin_page', 'usernotes_add_column');
function usernotes_add_column()
{
  global $template;
  
	$template->set_prefilter('user_list', 'usernotes_add_column_prefilter');
}

function usernotes_add_column_prefilter($content, &$smarty)
{
  // add the "Notes" column in the user table
  $search = '<th>{\'registration date\'|@translate}</th>';
  $content = str_replace($search, $search.'<th>{\'Notes\'|@translate}</th>', $content);

  // add the "Notes" field in user profile form
  $search = '#</div>\s*<div class="userPropertiesSet userPrefs">#ms';
  $replace = '<div class="userProperty"><strong>{\'Notes\'|translate}</strong><br><input type="text" name="usernotes" value="<%- user.usernotes %>" style="width:338px;"></div></div><div class="userPropertiesSet userPrefs">';
  $content = preg_replace($search, $replace, $content);

  return $content;
}

add_event_handler('user_list_columns', 'usernotes_user_list_columns', EVENT_HANDLER_PRIORITY_NEUTRAL, 1);
function usernotes_user_list_columns($aColumns)
{
  $aColumns[] = 'usernotes';
  
  return $aColumns;
}

add_event_handler('ws_invoke_allowed', 'usernotes_ws_users_setInfo', EVENT_HANDLER_PRIORITY_NEUTRAL, 3);
function usernotes_ws_users_setInfo($res, $methodName, $params)
{
  if ($methodName != 'pwg.users.setInfo')
  {
    return $res;
  }

  if (!isset($_POST['usernotes']))
  {
    return $res;
  }
  
  if (count($params['user_id']) == 0)
  {
    return $res;
  }
  
  $updates = array();

  foreach ($params['user_id'] as $user_id)
  {
    $updates[] = array(
      'user_id' => $user_id,
      'usernotes' => $_POST['usernotes'],
      );
  }
  
  if (count($updates) > 0)
  {
    mass_updates(
      USER_INFOS_TABLE,
      array(
        'primary' => array('user_id'),
        'update'  => array('usernotes')
        ),
      $updates
      );
  }
  
  return $res;
}

add_event_handler('ws_users_getList', 'usernotes_ws_users_getList', EVENT_HANDLER_PRIORITY_NEUTRAL, 1);
function usernotes_ws_users_getList($users)
{
  $user_ids = array();
  foreach ($users as $user_id => $user)
  {
    $user_ids[] = $user_id;
  }

  if (count($user_ids) == 0)
  {
    return $users;
  }
  
  $query = '
SELECT
    user_id,
    usernotes
  FROM '.USER_INFOS_TABLE.'
  WHERE user_id IN ('.implode(',', $user_ids).')
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $users[$row['user_id']]['usernotes'] = $row['usernotes'];
  }

  return $users;
}
?>