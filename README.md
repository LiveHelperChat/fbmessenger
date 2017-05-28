# Facebook messenger extension
Integration with Facebook messenger API. You will be able to chat with Facebook page users directly in lhc back office.

# Example of callback url
https://example.com/fbmessenger/callback

# Installation
 * Make copy of settings.ini.default.php to settings.ini.php and edit file settings.
 * Actvate extension in settings/settings.ini.php extension section "fbmessenger"
 * Install database either by executing doc/install.sql file or executing this command php "cron.php -s site_admin -e fbmessenger -c cron/update_structure"
 * You have to configure facebook app according to this tutorial https://developers.facebook.com/docs/messenger-platform/guides/quick-start/
 * Your facebook application has to have "pages_messaging" permission for lhc to be able to extract visitor information and be able to send messages back to lhc. For that you will have to submit application and wait for FB to review it.
 * Before facebook validates your application keep settings "pages_messaging_enabled" false. After facebook has reviewed your application set "pages_messaging_enabled" to true. So you will be able to send a messages. During testing, if you add some developer, you can set it to true to see how it works.
 
# How it works
Once visitor writes a message in facebook page. You will receive a chat with visitor.

# Todo
 * Add supporte for multiple pages and option to dedicate what page goes to what department.
 * At the moment all chat's goes to default department.
 * Add support for images, not just plain messages.
 * Add support for automated hosting environment.