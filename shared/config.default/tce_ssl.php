<?php
//============================================================+
// File name   : tce_ssl.php
// Begin       : 2013-03-27
// Last Update : 2013-03-27
//
// Description : Configuration file for SSL Authentication
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
//    Copyright (C) 2013-2013 Nicola Asuni - Tecnick.com LTD
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
 * Configuration file for SSL Authentication
 * If support for SSL Authentication is enabled, TCExam trusts this mechanism and replicates any authenticated user into the TCExam database.
 * @package com.tecnick.tcexam.shared.cfg
 * @author Nicola Asuni
 * @since 2013-03-27
 */

/**
 * If true trust SSL Auth
 */
define ('K_SSL_ENABLED', false);

/**
 * Default user level
 */
define ('K_SSL_USER_LEVEL', 1);

/**
 * Default user group ID
 * This is the TCExam group id to which the accounts belong.
 * You can also set 0 for all available groups or a string containing a comma-separated list of group IDs.
 */
define ('K_SSL_USER_GROUP_ID', 1);

//============================================================+
// END OF FILE
//============================================================+
