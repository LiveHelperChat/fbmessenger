# Facebook messenger extension
Integration with Facebook messenger API. You will be able to chat with Facebook page users directly in lhc back office.
 * Bot support. [New] https://www.youtube.com/watch?v=_rLPJAdn4Us
 * Support multiple pages without creating new app for each page.
 * Supports multiple pages at once.
 * Each page chat can be assigned to custom department.

# Installation in your LHC server
* Upload the files to your /extension folder
* Install database either by executing doc/install.sql file or executing this command php _"cron.php -s site_admin -e fbmessenger -c cron/update_structure"_
* Activate extension in settings/settings.ini.php extension section "fbmessenger" by Adding lines: 
<code>'extensions' =>  array (  'fbmessenger',  ),	</code> 
* Now you can create facebook page in **Modules -> Facebook chat -> Facebook pages -> Register new page** (later you will have this info from facebook developer section) _if you dont see this in Module, check your settings.ini.php_
* Once page is created you will see what callback url you have to put in facebook webhook. URL is presented in list. HTTPS is must!

# Installation in Developers.Facebook.com

 * You have to configure facebook app according to this tutorial https://developers.facebook.com/docs/messenger-platform/guides/quick-start/
## Enable pages_messaging
* Your facebook application has to have "pages_messaging" permission for lhc to be able to extract visitor information and be able to send messages back to lhc. For that you will have to submit application and wait for FB to review it.

**There you have to enter the url that your LHC gives you for callback**

If you use login workflow and subcribe to page you should set callback to url similar to this.
`https://example.com/fbmessenger/callbackgeneral`
 
If it's just one time callback you configuring by creating facebook page manually. Callback URL you will see in pages list.
`https://example.com/index.php/fbmessenger/callback/1`
 
 
## Then Submit to facebook to validate your app
*Before facebook validates your application keep settings "verified" false (in your LHC facebook page configuration)*
* After facebook has reviewed your application set "verified" to *YES*. So you will be able to send a messages. During testing, if you add some developer, you can set it to true to see how it works.
 
 ## Finally Make your app public. 
 * After facebook has reviewed your application you need to make your app live and available
 
# How it works
Once visitor writes a message in facebook page. You will receive a chat with visitor.

# How to debug
in <code>extension/fbmessenger/settings/settings.ini.php</code> change setting to <code>'enable_debug' => true</code> if you have verified site. Check cache/default.log for more detailed error.

# Todo
 * Add support for images, not just plain messages.
 * Add support for automated hosting environment.
 * Get facebook user details like email or phone.
