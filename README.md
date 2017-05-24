# Facebook messenger extension
Integration with Facebook messenger API. You will be able to chat with Facebook page users directly in lhc back office.

# Example of callback url
https://example.com/fbmessenger/callback

# Installation
 * Make copy of settings.ini.default.php to settings.ini.php and edit file settings.
 * Actvate extension in settings/settings.ini.php extension section "fbmessenger"
 * Install database either by executing doc/install.sql file or executing this command php "cron.php -s site_admin -e fbmessenger -c cron/update_structure"
 
# How it works
Once visitor writes a message in facebook page. You will receive a chat with visitor.

# Todo
 * Show indication it's FB chat in chat window
 * Add supporte for multiple pages and option to dedicate what page goes to what department.
 * At the moment all chat's goes to default department.
 * Add support for images, not just plain messages.
 * Add support for automated hosting environment.