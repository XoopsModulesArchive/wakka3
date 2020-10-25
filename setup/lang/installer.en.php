<?php

$lang = [
    'DBError' => 'The database you configured was not found. Remember, it needs to exist before you can install/upgrade WackoWiki!',
'TestSql' => 'Testing MySQL connection settings...',
'Testing Configuration' => 'Testing Configuration',
'Looking for database...' => 'Looking for database...',
'pages alter' => 'Just very slightly altering the pages table...',
'0.1.1' => 'Sending hatemail to the WackoWiki developers...',
'useralter' => 'Just very slightly altering the users table...',
'NextStep' => 'In the next step, the installer will try to write the updated configuration file,',
'MakeWrite' => 'Please make sure the web server has write access to the file, or you will have to edit it manually',
'ForDetailsSee' => 'Once again, see <a href="http://wackowiki.com/WackoDocumentation/Installation" target="_blank">WackoWiki:WackoDocumentation/Installation</a> for details',
'Continue' => 'Continue',
'Installing Stuff' => 'Installing Stuff',
'Creating table...' => 'Creating %1 table...',
'Already exists?' => 'Already exists?',
'to' => 'to',
'Adding some pages...' => 'Adding some pages...',
'Hmm!' => 'Hmm!',
'Claiming all your base...' => 'Claiming all your base...',
'And table...' => 'And %1 table (wait!)...',
'writtenAt' => 'written at ',
'dontchange' => 'do not change wakka_version manually!',
'writing' => "<strong>Writing configuration</strong><br>\n",
'writing2' => 'Writing configuration file',
'ready' => "<p>That's all! You can now",
'return' => 'return to your WackoWiki site',
'SecurityRisk' => "However, you are advised to remove write access to <tt>wakka.config.php</tt> again now that it's been written. Leaving the file writable can be a security risk!",
'warning' => '<span class="failed">WARNING:</span> The configuration file',
'GivePrivileges' => "could not be written. You will need to give your web server temporary write access to either your WackoWiki directory, or a blank file called <tt>wakka.config.php</tt> (<tt>touch wakka.config.php ; chmod 666 wakka.config.php</tt>; don't forget to remove write access again later, ie <tt>chmod 644 wakka.config.php</tt>). If, for any reason, you can't do this, you'll have to copy the text below into a new file and save/upload it as <tt>wakka.config.php</tt> into the WackoWiki directory. Once you've done this, your WackoWiki site should work. If not, please visit <a href=\"http://wackowiki.com/WackoDocumentation/Installation\">WackoWiki:WackoDocumentation/Installation</a>",
'try again' => 'Try again',
'title' => 'WackoWiki Installation',
'failed' => 'FAILED',
'note' => "NOTE: This installer will try to write the configuration data to the file <tt>wakka.config.php</tt>, located in your WackoWiki directory. In order for this to work, you must make sure the web server has write access to that file! If you can't do this, you will have to edit the file manually (the installer will tell you how).<br><br>See <a href=\"http://wackowiki.com/WackoDocumentation/Installation\" target=\"_blank\">WackoWiki:WackoDocumentation/Installation</a> for details.",
'databaseConf' => 'Database Configuration',
'mysqlHostDesc' => 'The host your MySQL server is running on. Usually "localhost" (ie, the same machine your WackoWiki site is on).',
'mysqlHost' => 'MySQL host',
'dbDesc' => 'The MySQL database WackoWiki should use. This database needs to exist already once you continue!',
'db' => 'MySQL database',
'mysqlPasswDesc' => 'Name and password of the MySQL user used to connect to your database.',
'mysqlUser' => 'MySQL user name',
'mysqlPassw' => 'MySQL password',
'prefixDesc' => 'Prefix of all tables used by WackoWiki. This allows you to run multiple WackoWiki installations using the same MySQL database by configuring them to use different table prefixes.',
'prefix' => 'Table prefix',
'SiteConf' => 'WackoWiki Site Configuration',
'nameDesc' => 'The name of your WackoWiki site. It usually is a WikiName and looks SomethingLikeThis.',
'name' => "Your WackoWiki's name",
'homeDesc' => "Your WackoWiki site's home page. Should be a WikiName.",
'home' => 'Home page',
'metaDesc' => 'META Keywords/Description that get inserted into the HTML headers.',
'meta1' => 'Meta Keywords',
'meta2' => 'Meta Description',
'UrlConf' => 'WackoWiki URL Configuration',
'baseDesc' => "Your WackoWiki site's base URL. Page names get appended to it, so it should include the \"?wakka=\" parameter stuff if the funky URL rewriting stuff doesn't work on your server.",
'base' => 'Base URL',
'rewriteDesc' => 'Rewrite mode should be enabled if you are using WackoWiki with URL rewriting.',
'rewrite' => 'Rewrite Mode',
'enabled' => 'Enabled',
'installed' => 'Your installed WackoWiki is reporting itself as ',
'toUpgrade' => 'You are about to <strong>upgrade</strong> to WackoWiki ',
'review' => 'Please review your configuration settings below.',
'fresh' => 'Since there is no existing WackoWiki configuration, this probably is a fresh WackoWiki install. You are about to install WackoWiki ',
'pleaseConfigure' => 'Please configure your WackoWiki site using the form below.',
'langConf' => 'Language Configuration',
'langDesc' => 'Choose a language for installation process. The same language will be default language of your WackoWiki installation.',
'lang' => 'Choose a language',
'VeryBad' => 'Very bad. Call developer now! Possible data loss.',
'Moving data to revisions table...' => 'Moving data to revisions table...',
'AdminConf' => 'Administrative account configuration',
'adminDesc' => 'Enter admin username. Should be a WikiName.',
'admin' => 'Admin name',
'passwDesc' => 'Choose a password for administrator (5+ chars)',
'password' => 'Enter password',
'password2' => 'Repeat password',
'mailDesc' => 'Administrator email.',
'mail' => 'Email',
'adding pages' => 'Adding some pages...',
'incorrect wikiname' => 'You have to enter correct WikiName as admin username!',
'incorrect email' => 'You have to enter correct admin email address!',
"passwords don't match" => 'Passwords don`t match, please re-enter password.',
'password too short' => 'Password too short, please re-enter password.',
'adding admin' => 'Adding admin user...',
'Doubles' => "If you'll use <a href=\"http://wackowiki.com/WackoDocumentation/CleanupScript\" target=\"_blank\">WackoWiki:WackoDocumentation/CleanupScript</a>, you will speedup your Wacko.",
'newinstall' => "Since this is a new installation, the installer tried to guess the proper values. Change them only if you know what you're doing!",
'multilangDesc' => 'Multilanguage mode allows to have pages with different language settings within single installation. If this mode is enabled, installer will create initial pages for all languages available in distribution.',
'multilang' => 'Multilanguage mode',
'PleaseBackup' => 'Please, backup your database, config file and all changed files (themes may be) before starting upgrade process. This can save you from big headache.',
];
