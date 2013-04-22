<?php
//============================================================+
// File name   : tce_functions_authorization.php
// Begin       : 2001-09-26
// Last Update : 2013-03-27
//
// Description : Functions for Authorization / LOGIN
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//
// License:
//    Copyright (C) 2004-2013 Nicola Asuni - Tecnick.com LTD
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU Affero General Public License as
//    published by the Free Software Foundation, either version 3 of the
//    License, or (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU Affero General Public License for more details.
//
//    You should have received a copy of the GNU Affero General Public License
//    along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
//    Additionally, you can't remove, move or hide the original TCExam logo,
//    copyrights statements and links to Tecnick.com and TCExam websites.
//
//    See LICENSE.TXT file for more information.
//============================================================+

/**
 * @file
 * Functions for Authorization / LOGIN
 * @package com.tecnick.tcexam.shared
 * @author Nicola Asuni
 * @since 2001-09-26
 */

/**
 * Returns XHTML / CSS formatted string for login form.<br>
 * The CSS classes used are:
 * <ul>
 * <li>div.login_form : container for login box</li>
 * <li>div.login_form div.login_row : container for label + input field or button</li>
 * <li>div.login_form div.login_row span.label : container for input label</li>
 * <li>div.login_form div.login_row span.formw : container for input form</li>
 * </ul>
 * @param faction String action attribute
 * @param fid String form ID attribute
 * @param fmethod String method attribute (get/post)
 * @param fenctype String enctype attribute
 * @param username String user name
 * @return XHTML string for login form
 */
function F_loginForm($faction, $fid, $fmethod, $fenctype, $username) {
	global $l;
	require_once('../config/tce_config.php');
	require_once('../../shared/config/tce_user_registration.php');
	$str = '';
	$str .= '<div class="container">'.K_NEWLINE;
	if (K_USRREG_ENABLED) {
		$str .= '<small><a href="../../public/code/tce_user_registration.php" title="'.$l['t_user_registration'].'">'.$l['w_user_registration_link'].'</a></small>'.K_NEWLINE;
	}
	$str .= '<div class="tceformbox">'.K_NEWLINE;
	$str .= '<form action="'.$faction.'" method="'.$fmethod.'" id="'.$fid.'" enctype="'.$fenctype.'">'.K_NEWLINE;
	// user name
	$str .= getFormRowTextInput('xuser_name', $l['w_username'], $l['h_login_name'], '', $username, '', 255, false, false, false, '');
	// password
	$str .= getFormRowTextInput('xuser_password', $l['w_password'], $l['h_password'], '', '', '', 255, false, false, true, '');
	// One Time Password code (OTP)
	if (K_OTP_LOGIN) {
		$str .= getFormRowTextInput('xuser_otpcode', $l['w_otpcode'], $l['h_otpcode'], '', '', '', 255, false, false, true, '');
	}
	if (defined('K_PASSWORD_RESET') AND K_PASSWORD_RESET) {
		// print a link to password reset page
		$str .= '<div class="row">'.K_NEWLINE;
		$str .= '<span class="formw"><a href="../../public/code/tce_password_reset.php" title="'.$l['h_reset_password'].'" style="font-size:90%;">'.$l['w_forgot_password'].'</a></span>'.K_NEWLINE;
		$str .= '</div>'.K_NEWLINE;
	}
	// buttons
	$str .= '<div class="row">'.K_NEWLINE;
	$str .= '<input type="submit" name="login" id="login" value="'.$l['w_login'].'" title="'.$l['h_login_button'].'" />'.K_NEWLINE;
	// the following field is used to check if the form has been submitted
	$str .= '<input type="hidden" name="logaction" id="logaction" value="login" />'.K_NEWLINE;
	$str .= '</div>'.K_NEWLINE;
	$str .= '</form>'.K_NEWLINE;
	$str .= '</div>'.K_NEWLINE;
	$str .= '<div class="pagehelp">'.$l['hp_login'].'</div>'.K_NEWLINE;
	$str .= '</div>'.K_NEWLINE;
	return $str;
}

/**
 * Display login page.
 * NOTE: This function calls exit() after execution.
 */
function F_login_form() {
	global $l, $thispage_title;
	global $xuser_name, $xuser_password;
	require_once('../config/tce_config.php');
	// HTTP-Basic authentication
	require_once('../../shared/config/tce_httpbasic.php');
	if (K_HTTPBASIC_ENABLED AND (!isset($_SESSION['logout']) OR !$_SESSION['logout'])) {
		// force HTTP Basic Authentication
		header('WWW-Authenticate: Basic realm="TCExam"');
		header('HTTP/1.0 401 Unauthorized');
		require_once('../code/tce_page_header.php');
		F_print_error('WARNING', $l['m_authorization_denied']);
		require_once('../code/tce_page_footer.php');
		exit(); //break page here
	}
	// Shibboleth authentication
	require_once('../../shared/config/tce_shibboleth.php');
	if (K_SHIBBOLETH_ENABLED AND (!isset($_SESSION['logout']) OR !$_SESSION['logout'])) {
		// redirect to Shibboleth Login Page
		header('Location: '.K_SHIBBOLETH_LOGIN);
		// html redirect
		echo '<'.'?xml version="1.0" encoding="'.$l['a_meta_charset'].'"?'.'>'.K_NEWLINE;
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'.K_NEWLINE;
		echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$l['a_meta_language'].'" lang="'.$l['a_meta_language'].'" dir="'.$l['a_meta_dir'].'">'.K_NEWLINE;
		echo '<head>'.K_NEWLINE;
		echo '<title>LOGIN</title>'.K_NEWLINE;
		echo '<meta http-equiv="refresh" content="0" />'.K_NEWLINE; //reload page
		echo '</head>'.K_NEWLINE;
		echo '<body>'.K_NEWLINE;
		echo '<a href="'.K_SHIBBOLETH_LOGIN.'">LOGIN</a>'.K_NEWLINE;
		echo '</body>'.K_NEWLINE;
		echo '</html>'.K_NEWLINE;
		exit(); //break page here
	}
	require_once('../../shared/code/tce_functions_form.php');
	$thispage_title = $l['t_login_form']; //set page title
	require_once('../code/tce_page_header.php');
	echo F_loginForm($_SERVER['SCRIPT_NAME'], 'form_login', 'post', 'multipart/form-data', $xuser_name);
	require_once('../code/tce_page_footer.php');
	exit(); //break page here
}


/**
 * Display logout form.
 * @return XHTML string for logout form.
 */
function F_logout_form() {
	global $l;
	require_once('../config/tce_config.php');
	require_once('../../shared/code/tce_functions_form.php');
	$str = K_NEWLINE;
	$str .= '<div class="container">'.K_NEWLINE;
	$str .= '<div class="tceformbox">'.K_NEWLINE;
	$str .= '<form action="../code/tce_logout.php" method="post" id="form_logout" enctype="multipart/form-data">'.K_NEWLINE;
	// description
	$str .= '<div class="row">'.K_NEWLINE;
	$str .= $l['d_logout_desc'].K_NEWLINE;
	$str .= '</div>'.K_NEWLINE;
	// buttons
	$str .= '<div class="row">'.K_NEWLINE;
	// the following field is used to check if form has been submitted
	$str .= '<input type="hidden" name="current_page" id="current_page" value="'.$_SERVER['SCRIPT_NAME'].'" />'.K_NEWLINE;
	$str .= '<input type="hidden" name="logaction" id="logaction" value="" />'.K_NEWLINE;
	$str .= '<input type="submit" name="login" id="login" value="'.$l['w_logout'].'" />'.K_NEWLINE;
	$str .= '</div>'.K_NEWLINE;
	$str .= '</form>'.K_NEWLINE;
	$str .= '</div>'.K_NEWLINE;
	return $str;
}

/**
 * Display logout page.
 * NOTE: This function calls exit() after execution.
 */
function F_logout_page() {
	global $l, $thispage_title;
	require_once('../config/tce_config.php');
	$thispage_title = $l['t_logout_form']; // set page title
	require_once('../code/tce_page_header.php');
	echo F_logout_form();
	require_once('../code/tce_page_footer.php');
	exit();
}

/**
 * Returns true if the current user is authorized to update and delete the selected database record.
 * @author Nicola Asuni
 * @since 2006-03-11
 * @param $table (string) table to be modified
 * @param $field_id_name (string) name of the main ID field of the table
 * @param $value_id (int) value of the ID field of the table
 * @param $field_user_id (string) name of the foreign key to to user_id
 * @return boolean true if the user is authorized, false otherwise
 */
function F_isAuthorizedUser($table, $field_id_name, $value_id, $field_user_id) {
	global $l,$db;
	require_once('../config/tce_config.php');
	$table = F_escape_sql($table);
	$field_id_name = F_escape_sql($field_id_name);
	$value_id = intval($value_id);
	$field_user_id = F_escape_sql($field_user_id);
	$user_id = intval($_SESSION['session_user_id']);
	// check for administrator
	if (defined('K_AUTH_ADMINISTRATOR') AND ($_SESSION['session_user_level'] >= K_AUTH_ADMINISTRATOR)) {
		return true;
	}
	// check for original author
	if (F_count_rows($table.' WHERE '.$field_id_name.'='.$value_id.' AND '.$field_user_id.'='.$user_id.' LIMIT 1') > 0) {
		return true;
	}
	// check for author's groups
	// get author ID
	$author_id = 0;
	$sql = 'SELECT '.$field_user_id.' FROM '.$table.' WHERE '.$field_id_name.'='.$value_id.' LIMIT 1';
	if($r = F_db_query($sql, $db)) {
		if($m = F_db_fetch_array($r)) {
			$author_id = $m[0];
		}
	} else {
		F_display_db_error();
	}
	if (($author_id > 1)
		AND (F_count_rows(K_TABLE_USERGROUP.' AS ta, '.K_TABLE_USERGROUP.' AS tb
		WHERE ta.usrgrp_group_id=tb.usrgrp_group_id
			AND ta.usrgrp_user_id='.$author_id.'
			AND tb.usrgrp_user_id='.$user_id.'
			LIMIT 1') > 0)) {
		return true;
	}
	return false;
}

/**
 * Returns a comma separated string of ID of the users that belong to the same groups.
 * @author Nicola Asuni
 * @since 2006-03-11
 * @param $user_id (int) user ID
 * @return string
 */
function F_getAuthorizedUsers($user_id) {
	global $l,$db;
	require_once('../config/tce_config.php');
	$str = ''; // string to return
	$user_id = intval($user_id);
	$sql = 'SELECT tb.usrgrp_user_id
		FROM '.K_TABLE_USERGROUP.' AS ta, '.K_TABLE_USERGROUP.' AS tb
		WHERE ta.usrgrp_group_id=tb.usrgrp_group_id
			AND ta.usrgrp_user_id='.$user_id.'';
	if($r = F_db_query($sql, $db)) {
		while($m = F_db_fetch_array($r)) {
			$str .= $m[0].',';
		}
	} else {
		F_display_db_error();
	}
	// add the user
	$str .= $user_id;
	return $str;
}

/**
 * Sync user groups with the ones specified on the configuration file for alternate authentication.
 * @param $usrid (int) ID of the user to update.
 * @param $grpids (mixed) Group ID or comma separated list of group IDs (0=all available groups).
 * @author Nicola Asuni
 * @since 2012-09-11
 */
function F_syncUserGroups($usrid, $grpids) {
	global $l,$db;
	require_once('../config/tce_config.php');
	$usrid = intval($usrid);
	// select new group IDs
	$newgrps = array();
	if (is_string($grpids)) {
		// comma separated list of group IDs
		$newgrps = explode(',', $grpids);
		array_walk($newgrps, 'intval');
		$newgrps = array_unique($newgrps, SORT_NUMERIC);
	} elseif ($grpids == 0) {
		// all available groups
		$sqlg = 'SELECT group_id FROM '.K_TABLE_GROUPS.'';
		if ($rg = F_db_query($sqlg, $db)) {
			while ($mg = F_db_fetch_array($rg)) {
				$newgrps[] = $mg['group_id'];
			}
		} else {
			F_display_db_error();
		}
	} elseif ($grpids > 0) {
		// single default group
		$newgrps[] = intval($grpids);
	}
	if (empty($newgrps)) {
		return;
	}
	// select existing group IDs
	$usrgrps = array();
	$sqlu = 'SELECT usrgrp_group_id FROM '.K_TABLE_USERGROUP.' WHERE usrgrp_user_id='.$usrid.'';
	if ($ru = F_db_query($sqlu, $db)) {
		while ($mu = F_db_fetch_array($ru)) {
			$usrgrps[] = $mu['usrgrp_group_id'];
		}
	} else {
		F_display_db_error();
	}
	// extract missing groups
	$diffgrps = array_values(array_diff($newgrps, $usrgrps));
	// add missing groups
	foreach ($diffgrps as $grpid) {
		if ($grpid > 0) {
			// add user to default user groups
			$sql = 'INSERT INTO '.K_TABLE_USERGROUP.' (
				usrgrp_user_id,
				usrgrp_group_id
				) VALUES (
				\''.$usrid.'\',
				\''.$grpid.'\'
				)';
			if (!$r = F_db_query($sql, $db)) {
				F_display_db_error();
			}
		}
	}
}

/**
 * Check if the client has a valid SSL certificate.
 * @return true if the client has a valid SSL certificate, false otherwise.
 * @author Nicola Asuni
 * @since 2013-03-26
 */
function F_isSslCertificateValid() {
	if (!isset($_SERVER['SSL_CLIENT_M_SERIAL']) // The serial of the client certificate
		OR !isset($_SERVER['SSL_CLIENT_I_DN']) // Issuer DN of client's certificate
		OR !isset($_SERVER['SSL_CLIENT_V_END']) // Validity of server's certificate (end time)
		OR !isset($_SERVER['SSL_CLIENT_VERIFY']) // NONE, SUCCESS, GENEROUS or FAILED:reason
		OR ($_SERVER['SSL_CLIENT_VERIFY'] !== 'SUCCESS')
		OR !isset($_SERVER['SSL_CLIENT_V_REMAIN']) // Number of days until client's certificate expires
		OR ($_SERVER['SSL_CLIENT_V_REMAIN'] <= 0)) {
		// invalid certificate
		return false;
	}
	// valid certificate
	return true;
}

//============================================================+
// END OF FILE
//============================================================+
