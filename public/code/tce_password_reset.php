<?php
//============================================================+
// File name   : tce_password_reset.php
// Begin       : 2012-04-14
// Last Update : 2012-04-14
//
// Description : Password Reset form.
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
//    Copyright (C) 2004-2012 Nicola Asuni - Tecnick.com LTD
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
 * Display password reset form.
 * @package com.tecnick.tcexam.public
 * @author Nicola Asuni
 * @since 2008-03-30
 */

/**
 */

require_once('../config/tce_config.php');

if (!defined('K_PASSWORD_RESET') OR !K_PASSWORD_RESET) {
	// password reset is disabled, redirect to main page
	header('Location: '.K_PATH_HOST.K_PATH_TCEXAM);
	exit;
}

$pagelevel = 0;
require_once('../../shared/code/tce_authorization.php');

$thispage_title = $l['t_password_assistance'];
require_once('../code/tce_page_header.php');
require_once('../../shared/code/tce_functions_form.php');

if (isset($_POST['resetpassword'])) { // process submited data

	if ($formstatus = F_check_form_fields()) { // check submitted form fields


		mt_srand((double) microtime() * 1000000);
		$user_verifycode = md5(uniqid(mt_rand(), true)); // verification code
		$user_verifycode[0] = '@';
		// get user ID
		$user_id = 0;
		$sql = 'SELECT user_id FROM '.K_TABLE_USERS.' WHERE user_email=\''.F_escape_sql($user_email).'\'';
		if ($r = F_db_query($sql, $db)) {
			if ($m = F_db_fetch_array($r)) {
				$user_id = $m['user_id'];
			}
		} else {
			F_display_db_error();
		}
		if ($user_id > 0) {
			// update verification code
			$sqlu = 'UPDATE '.K_TABLE_USERS.' SET user_verifycode=\''.F_escape_sql($user_verifycode).'\' WHERE user_id='.$user_id.'';
			if (!$ru = F_db_query($sqlu, $db)) {
				F_display_db_error();
			}
			// send email confirmation
			require_once('../../shared/code/tce_functions_user_registration.php');
			F_send_user_reg_email($user_id, $user_email, $user_verifycode);
			F_print_error('MESSAGE', $user_email.': '.$l['m_user_verification_sent']);
			echo '<div class="container">'.K_NEWLINE;
			echo '<strong><a href="index.php" title="'.$l['h_index'].'">'.$l['h_index'].' &gt;</a></strong>'.K_NEWLINE;
			echo '</div>'.K_NEWLINE;
			require_once('../code/tce_page_footer.php');
			exit;
		} else {
			F_print_error('ERROR', $l['w_unknown_email']);
		}

	}
} //end of add

echo '<div class="container">'.K_NEWLINE;

echo '<div class="tceformbox">'.K_NEWLINE;
echo '<form action="'.$_SERVER['SCRIPT_NAME'].'" method="post" enctype="multipart/form-data" id="form_usereditor">'.K_NEWLINE;

echo '<p>'.$l['d_reset_password'].'</p>'.K_NEWLINE;

echo getFormRowTextInput('user_email', $l['w_email'], $l['h_usered_email'], '', '', '^([a-zA-Z0-9_\.\-]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$', 255, false, false, false, '');

echo '<div class="row">'.K_NEWLINE;

F_submit_button('resetpassword', $l['w_submit'], $l['h_submit']);

// comma separated list of required fields
echo '<input type="hidden" name="ff_required" id="ff_required" value="user_email" />'.K_NEWLINE;
echo '<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="'.$l['w_email'].'" />'.K_NEWLINE;
echo '</div>'.K_NEWLINE;

echo '</form>'.K_NEWLINE;
echo '</div>'.K_NEWLINE;

echo '</div>'.K_NEWLINE;

require_once('../code/tce_page_footer.php');

//============================================================+
// END OF FILE
//============================================================+
