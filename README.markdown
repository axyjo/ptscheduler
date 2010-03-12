## Information

PTScheduler is a web-based scheduling application that allows parents, teachers
and administrators to schedule appointments for a parent-teacher conference
without the need to fill out forms or call in. It is intended to minimize the
amount of input needed by a school secretary into the data system of the
school.

## Licensing

This program is licensed under the terms of the MIT License. Please see the
LICENSE file for the terms.

## Server Configuration

PTScheduler requires the following components:
- a web server (Apache 2.0 or similar)
- PHP 5 (5.2 or greater)
- a database engine compatible with your PDO installation and its extensions

Optionally, you may want the following components as well:
- PHPUnit installed in PHP's include path for running tests
- Git for updating the code

## Installation

1. Copy the default.config.php to config.php.

1. Change the appropriate settings within the config.php file. These include
the security hash, list of teachers and administrators and the date and time
constraints.

1. Ensure that a database engine is installed and edit the $db_url variable
in config.php to match your settings.

1. Change permissions on the config.php file so that all write privileges have
been revoked. Leaving write privileges on this file is a security vulnerability
as users may be able to deface your site.

If using an sqlite database, please ensure that the web server user has write
privileges on the database file. In additon, the web server user also needs
write access for the directory that is being used to store the sqlite database.
