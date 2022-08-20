<?php
/*
Plugin Name: Add Users Notes
Version: 12.0.b
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
function usernotes_init(){
  load_language('plugin.lang', USERNOTES_PATH);
  global $template;
  $template->assign(
    array(
	 'USERNOTES_PATH2'=> get_root_url().USERNOTES_PATH,
    )
  );
}

add_event_handler('loc_begin_admin_page', 'usernotes_add_column');
function usernotes_add_column(){
  global $template;
	$template->set_prefilter('user_list', 'usernotes_add_column_prefilter');
}

function usernotes_add_column_prefilter($content){
  // add js link
  $search = '<div class="selection-mode-group-manager" style="right:30px">';
  $replace = '{combine_script id="jquery.usersnotes" load=\'footer\' path="$USERNOTES_PATH2/js/usersnotes.js"}';
  $content = str_replace($search, $replace.$search, $content);
	
	
	
  // add the "Notes" column in the user table
  $search = '<!-- groups -->
       <div class="user-header-col user-header-groups">
       <span>{\'Groups\'|@translate}</span>
     </div>';
  $content = str_replace($search, $search.'<!-- Notes -->
    <div class="user-header-col user-header-usernotes">
       <span>{\'Notes\'|@translate}</th></span>
    </div>'
	, $content);

  // add the "Notes" 
  $search = '<div class="user-col user-container-groups">
      <!-- groups -->
    </div>';
  $replace = '    <div class="user-col user-container-usernotes">
      <span><!-- usernotes --></span>
    </div>';
  $content = str_replace($search, $search.$replace, $content);

  // add the "Notes" field in user profile form
  $search = '<div id="UserList" class="UserListPopIn">

  <div class="UserListPopInContainer">

    <a class="icon-cancel CloseUserList"></a>
    <div class="summary-properties-update-container">
      <div class="summary-properties-container">
        <div class="summary-container">
          <div class="user-property-initials">
            <div>
              <span class="icon-blue"><!-- Initials (JP) --></span>
            </div>
          </div>
          <div class="user-property-username">
            <span class="edit-username-title"><!-- Name (Jessy Pinkman) --></span>
            <span class="edit-username-specifier"><!-- You specifire (you) --></span>
            <span class="edit-username icon-pencil"></span>
          </div>
          <div class="user-property-username-change">
            <div class="summary-input-container">
              <input class="user-property-input user-property-input-username" value="" placeholder="{\'Username\'|@translate}" />
            </div>
            <span class="icon-ok edit-username-validate"></span>
            <span class="icon-cancel-circled edit-username-cancel"></span>
          </div>
          <div class="user-property-password-container">
            <div class="user-property-password edit-password">
              <p class="user-property-button"><span class="icon-key user-edit-icon"> </span>{\'Change Password\'|@translate}</p>
            </div>
            <div class="user-property-password-change">
              <div class="summary-input-container">
              <input class="user-property-input user-property-input-password" value="" placeholder="{\'Password\'|@translate}" />
              </div>
              <span class="icon-ok edit-password-validate"></span>
              <span class="icon-cancel-circled edit-password-cancel"></span>
            </div>
            <div class="user-property-permissions">
              <p class="user-property-button"> <span class="icon-lock user-edit-icon"> </span><a href="#" >{\'Permissions\'|@translate}</a></p>
            </div>
          </div>
          <div class="user-property-register-visit">
            <span class="user-property-register"><!-- Registered date XX/XX/XXXX --></span>
            <span class="icon-calendar"></span>
            <span class="user-property-last-visit"><!-- Last Visit date XX/XX/XXXX --></span>
          </div>
        </div>
        <div class="properties-container">
          <div class="user-property-column-title">
            <p>{\'Properties\'|@translate}</p>
          </div>
          <div class="user-property-email">
            <p class="user-property-label">{\'Email Adress\'|@translate}</p>
            <input type="text" class="user-property-input" value="contact@jessy-pinkman.com" disabled="false"/>
          </div>
          <div class="user-property-status">
            <p class="user-property-label">{\'Status\'|@translate}
                <span class="icon-help-circled" 
                  title="<div class=\'tooltip-status-content\'>
                    <div class=\'tooltip-status-row\'><span class=\'tooltip-col1\'>{\'user_status_webmaster\'|translate}</span><span class=\'tooltip-col2\'>{\'Has access to all administration functionnalities. Can manage both configuration and content.\'|translate}</span></div>
                    <div class=\'tooltip-status-row\'><span class=\'tooltip-col1\'>{\'user_status_admin\'|translate}</span><span class=\'tooltip-col2\'>{\'Has access to administration. Can only manage content: photos/albums/users/tags/groups.\'|translate}</span></div>
                    <div class=\'tooltip-status-row\'><span class=\'tooltip-col1\'>{\'user_status_normal\'|translate}</span><span class=\'tooltip-col2\'>{\'No access to administration, can see private content with appropriate permissions.\'|translate}</span></div>
                    <div class=\'tooltip-status-row\'><span class=\'tooltip-col1\'>{\'user_status_generic\'|translate}</span><span class=\'tooltip-col2\'>{\'Can be shared by several individuals without conflict (they cannot change the password).\'|translate}</span></div>
                    <div class=\'tooltip-status-row\'><span class=\'tooltip-col1\'>{\'user_status_guest\'|translate}</span><span class=\'tooltip-col2\'>{\'Equivalent to deactivation. The user is still in the list, but can no longer log in.\'|translate}</span></div>
                  </div">
                </span>
            </p>
            <div class="user-property-select-container">
              <select name="status" class="user-property-select">
                <option value="webmaster">{\'user_status_webmaster\'|@translate}</option>
                <option value="admin">{\'user_status_admin\'|@translate}</option>
                <option value="normal">{\'user_status_normal\'|@translate}</option>
                <option value="generic">{\'user_status_generic\'|@translate}</option>
                <option value="guest">{\'user_status_guest\'|@translate}  ({\'Deactivated\'|@translate})</option>
              </select>
            </div>
          </div>
          <div class="user-property-level">
            <p class="user-property-label">{\'Privacy level\'|@translate}</p>
            <div class="user-property-select-container">
              <select name="privacy" class="user-property-select">
                <option value="0">{\'Level 0\'|@translate}</option>
                <option value="1">{\'Level 1\'|@translate}</option>
                <option value="2">{\'Level 2\'|@translate}</option>
                <option value="4">{\'Level 4\'|@translate}</option>
                <option value="8">{\'Level 8\'|@translate}</option>
              </select>
            </div>
          </div>
          <div class="user-property-group-container">
            <p class="user-property-label">{\'Groups\'|@translate}</p>
            <div class="user-property-select-container user-property-group">
              <select class="user-property-select" data-selectize="groups" placeholder="{\'Select groups or type them\'|translate}" 
                name="group_id[]" multiple style="box-sizing:border-box;"></select>
            </div>
          </div>';
  $replace = '<p class="user-property-label">{\'Notes\'|@translate}</p>
		  <div class="user-property-usernotes">
            <span class="usernotes-title"><!-- usernotes --></span>
			<span class="edit-usernotes icon-pencil"></span>
          </div>
		  <div class="user-property-usernotes-change">
            <div class="summary-input-container">
              <input class="usernotes-property-input user-property-input-usernotes" value="" placeholder="{\'Notes\'|@translate}" />
            </div>
            <span class="icon-ok edit-usernotes-validate"></span>
            <span class="icon-cancel-circled edit-usernotes-cancel"></span>
          </div>
		  ';
  $content = str_replace($search, $search.$replace, $content);
  
  //css
    $search = '</style>';
  $replace = '
  .user-property-usernotes-change {
    justify-content:center;
    align-items:center;
    display:none;
    margin-bottom:25px;
  }
  
  .user-property-usernotes {
    margin-bottom:34px;
    height:30px;
}

.edit-usernotes-validate {
    display: block;
    margin: auto 5px;
    cursor: pointer;
    background-color: #ffa744;
    color: #3c3c3c;
    font-size: 17px;
    font-weight: 700;
    padding: 7px;
}

.edit-usernotes-validate:hover {
    background-color: #f70;
    color: #000;
    cursor: pointer;
}
.edit-usernotes {
    font-size:1.4em;
    cursor:pointer;
}
.edit-usernotes-cancel {
    cursor:pointer;
    font-size:22px;
    padding-top: 4px;
}
.usernotes-property-input {
    width: 100%;
    box-sizing:border-box;
    font-size:1.1em;
    padding:8px 16px;
    border:none;
}
.edit-usernotes-title {
    font-size:1.4em;
}
.usernotes-property-input.user-property-input-usernotes {
    border: solid 2px #ffa744;
    padding: 9px;
}

.user-header-usernotes{
  width: 20%;
  max-width: 195px;
}
.user-container-usernotes {
  width: 20%;
  max-width: 195px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.user-container-usernotes span {
  max-width: 100%;

  overflow: hidden;
  text-overflow: ellipsis;
}

		  ';
  $content = str_replace($search, $replace.$search, $content);
    
  return $content;
}

add_event_handler('ws_invoke_allowed', 'usernotes_ws_users_setInfo', EVENT_HANDLER_PRIORITY_NEUTRAL, 3);
function usernotes_ws_users_setInfo($res, $methodName, $params){
  if ($methodName != 'pwg.users.setInfo'){
    return $res;
  }
  if (!isset($_POST['usernotes'])){
    return $res;
  }
  if (count($params['user_id']) == 0){
    return $res;
  }
  
  $updates = array();

  foreach ($params['user_id'] as $user_id){
    $updates[] = array(
      'user_id' => $user_id,
      'usernotes' => $_POST['usernotes'],
    );
  }
  if (count($updates) > 0){
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
function usernotes_ws_users_getList($users){
  $user_ids = array();
  foreach ($users as $user_id => $user){
    $user_ids[] = $user_id;
  }
  if (count($user_ids) == 0){
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
  while ($row = pwg_db_fetch_assoc($result)){
    $users[$row['user_id']]['usernotes'] = $row['usernotes'];
  }
  return $users;
}

?>