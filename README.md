# Facebook messenger extension
Integration with Facebook messenger API. You will be able to chat with Facebook page users directly in lhc back office.
 * Supports multiple pages at once.
 * Each page chat can be assigned to custom department.

# Example of callback url
https://example.com/fbmessenger/callback/<id>

# Installation in your LHC server
* Upload the files to your /extension folder
* Install database either by executing doc/install.sql file or executing this command php _"cron.php -s site_admin -e fbmessenger -c cron/update_structure"_
* Activate extension in settings/settings.ini.php extension section "fbmessenger" by Adding lines: 
<code>'extensions' =>  array (  'fbmessenger',  ),	</code> 
* Now you can create facebook page in **Modules -> Facebook chat -> Facebook pages -> Register new page** (later you will have this info from facebook developer section) _if you dont see this in Module, check your settings.ini.php_
* Once page is created you will see what callback url you have to put in facebook webhook. URL is presented in list.

# Installation in Developers.Facebook.com
 
 * You have to configure facebook app according to this tutorial https://developers.facebook.com/docs/messenger-platform/guides/quick-start/
 * Your facebook application has to have "pages_messaging" permission for lhc to be able to extract visitor information and be able to send messages back to lhc. For that you will have to submit application and wait for FB to review it.
 
 ![alt text](http://chatconclientes.info/github/submit.jpg)
 
 * Before facebook validates your application keep settings "verified" false (in facebook page configuration).
 
 * After facebook has reviewed your application set "verified" to true. So you will be able to send a messages. During testing, if you add some developer, you can set it to true to see how it works.
 
# How it works
Once visitor writes a message in facebook page. You will receive a chat with visitor.

# How to debug
in <code>extension/fbmessenger/settings/settings.ini.php</code> change setting to <code>'enable_debug' => true</code> if you have verified site. Check cache/default.log for more detailed error.

# Todo
 * Add support for images, not just plain messages.
 * Add support for automated hosting environment.
