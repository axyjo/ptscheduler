<?php

// Add each administrator's username in lowercase as a new element in the
// $admins array.
$admins['admin'] = TRUE;

// Add each teacher's username in lowercase as a new element in the $teachers
// array.
$teachers['teacher1'] = TRUE;
$teachers['teacher2'] = TRUE;

// Change this to the absolute path to the directory of this installation. Do
// not modify or uncomment if you aren't sure what this setting does.
// $base_path = __DIR__;

// Change the following to the email address of the person in charge of
// handling technical support queries. Please note that this email address may
// be visible to crawlbots and spam harvesters.
$support_email = 'somebody@example.com';

// Change the key of the following array to the date of the conferences in
// YYYY-MM-DD format. See the commented example below.
// $date_boundaries['2009-11-19'] = TRUE;
$date_boundaries[''] = TRUE;

// Change the following variables to indicate the start and the end of the day
// in your local timezone in seconds since midnight. These values default to
// 8:00 AM and 4:30 PM. Additionally, change the $time_increments variable to
// reflect the length of a meeting in seconds. $time_increments defaults to
// 15 minutes.
$time_boundaries['start'] = (8*60*60);
$time_boundaries['end'] = (16*60*60) + (30*60);
$time_increments = (15*60);

// Change the following variables to restrict when parents and teachers can
// login. Administrators may login at any time. These values are in UNIXTIME
// format and are in the UTC timezone. This value defaults to no restrictions
// for both parents and teachers.
$parent_restrict = 0;
$teacher_restrict = 0;

// Chane the following to modify the format in which dates are displayed when
// creating, updating or deleting appointments. This setting does not impact
// the format of the dates on the home page.
// @see http://www.php.net/manual/en/function.date.php
$date_format = 'D, j M Y H:i';

// This is the offset in timezone from UTC in seconds. Use negative values for
// places west of the UTC timezone. Defaults to server default.
$timezone_offset = (int)date('Z');

// Choose your preferred authentication method. Also, change the settings for
// your chosen method if there are any. The documentation for that method
// should help you determine what settings need to be changed. By default, the
// 'test' authentication method is enabled.
$auth['test'] = array();

// Set the following variable to a PDO compliant path. Defaults to an sqlite3
// database within the current directory.
$db_url = 'sqlite:db.sqlite';

// Change the following to an undisclosed, random string. This string is used
// as a hash to encrypt POST requests and avoid man-in-the-middle attacks.
// @see https://www.grc.com/passwords.htm
$secure_hash = 'VQCMnwedl7W5jxpyxuUbOLImvShFCu0vQfp1SbYeX26fV8CESQxGpXzdjKB198X';

// Set the following variable to TRUE if you wish to enable debugging output.
$debug = FALSE;
