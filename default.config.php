<?php

// Change this to the name of the site that will appear in the header section
// of each page.
$site_name = 'Parent Teacher Scheduler';

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
// 15 minutes. However, columns on the scheduling page only split when the
// minutes are at 00.
$time_boundaries['start'] = (8*60*60);
$time_boundaries['end'] = (16*60*60) + (30*60);
$time_increments = (15*60);

// Change the following to the number of simultaneous appointments for one
// teacher. Defaults to one appointment in one time slot.
$simultaneous_appointments = 1;

// Change the following variables to restrict when parents and teachers can
// login. Administrators may login at any time. These values are in UNIXTIME
// format and are in the UTC timezone. This value defaults to no restrictions
// for both parents and teachers.
$parent_restrict = 0;
$teacher_restrict = 0;

// Change the following to modify the format in which dates are displayed when
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
$auth[] = array(
  'method' => 'test',
);

// Set the following variable to a PDO compliant path. Defaults to an sqlite3
// database within the current directory.
$db_url = 'sqlite:db.sqlite';

// Change the following to an undisclosed, random string. This string is used
// as a hash to encrypt POST requests and avoid man-in-the-middle attacks.
// @see https://www.grc.com/passwords.htm
$secure_hash = 'VQCMnwedl7W5jxpyxuUbOLImvShFCu0vQfp1SbYeX26fV8CESQxGpXzdjKB198X';

// Set the following variable to TRUE if you wish to enable debugging output.
$debug = FALSE;
