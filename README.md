# Facebook messenger extension
Integration with Facebook messenger API. You will be able to chat with Facebook page users directly in lhc back office.
 * Bot support. [New] https://www.youtube.com/watch?v=_rLPJAdn4Us Supported bot elements
    * Text messages including Quick Replies - `Send Text`
    * Typing - `Send typing`
    * Carrousel - `Send Carrousel`
    * Buttons - `Button list`
    * All other triggers including internal operations also will work.
    * To listed for "Get Started" button action just listen for text message with content "GET_STARTED" also see demo bot in official demo.
 * Support multiple pages without creating new app for each page.
 * Supports multiple pages at once.
 * Each page chat can be assigned to custom department.

# Installation in your LHC server
* Upload the files to your `lhc_web/extension/fbmessenger` folder
* Install database either by executing `doc/install.sql` file or executing this command `php cron.php -s site_admin -e fbmessenger -c cron/update_structure`
* Install dependencies using composer
    * `cd extension/fbmessenger && composer install`
    * Just for newbies if your webhosting does not have composer see https://www.vultr.com/docs/install-composer-on-centos-7
    * don't run composer as root, login in your ssh as your hosting normal user.
* Activate extension in main settings file `lhc_web/settings/settings.ini.php` extension section `fbmessenger` by Adding lines: 
```
'extensions' =>  array (  'fbmessenger',  ),
```
* If you don't see this in Module, check your `lhc_web/settings/settings.ini.php` and also click `Clean Cache` from back office
* copy `extension/fbmessenger/settings/settings.ini.default.php` to `extension/fbmessenger/settings/settings.ini.php`

# One page one app installation workflow

This method is usefull if you are planning to use this extension by creating separate apps for each page you manage.

* Now you can create facebook page in `Modules -> Facebook chat -> Facebook pages -> Register new page` (later you will have this info from facebook developer section) 
* While creating facebook page check `Application was verified by facebook` otherwise we will not send request to facebook. Save page.
* Once page is created you will see what callback url you have to put in facebook webhook. URL is presented in list. HTTPS is must!
* Facebook APP has to use 8.0v or newer

## Actions to do in developers.facebook.com and Live Helper Chat back office

* `APP secret` - Copy App Secret from `Settings -> Basic`
* `Verify token` - put any random string without spaces.
* `Page token` - follow steps bellow 
 * Click `Products +` in facebook back office and choose `Messenger` as product you want to add to your APP
 * Click `Messenger -> Settings` your app page
 * In `Access Tokens` section click `Add or Removes pages` there you will get `Token` which you have to put in `Page token` field.
* Now in `Webhooks` section of `Messenger -> Settings` page `Edit Callback URL`. Facebook to verify callback URL will ask you to enter `Verify token` and callback url. Callback URL you will see in pages list. 
* In same `Webhooks` add Page from which you want to receive messages. As subscription fields choose `messages, messaging_postbacks, message_deliveries, message_reads, messaging_pre_checkouts, messaging_checkout_updates, messaging_referrals, message_echoes, standby, messaging_handovers, message_reactions`

So at the end everything should look like

![See image](https://raw.githubusercontent.com/LiveHelperChat/fbmessenger/master/doc/access_token.png)

![See image](https://raw.githubusercontent.com/LiveHelperChat/fbmessenger/master/doc/webhooks.png)

# Once account multiple page installation workflow

This workflow is usefull if you are planning to use more than one page per facebook account.

* Your facebook application has to have "pages_messaging" permission for lhc to be able to extract visitor information and be able to send messages back to lhc. For that you will have to submit application and wait for FB to review it.
* Set webhook callback to url similar to this. `https://example.com/fbmessenger/callbackgeneral` verify token you have to put in `extension/fbmessenger/settings/settings.ini.php` file.
* `Valid OAuth Redirect URLs` should look like `https://example.com/site_admin/fbmessenger/fbcallback`
* We request these scopes `email, pages_show_lis, pages_messaging, pages_messaging_subscriptions`
* If you did everything correctly you should be able to login from facebook and grant access Live Helper Chat to see your pages and subscribe to messages events.

## Then Submit to facebook to validate your app
* Before facebook validates your application keep settings `verified` false (in your LHC facebook page configuration)*
* After facebook has reviewed your application set "verified" to *YES*. So you will be able to send a messages. During testing, if you add some developer, you can set it to true to see how it works.
 
## Finally Make your app public. 
 * After facebook has reviewed your application you need to make your app live and available
 
# How it works
Once visitor writes a message in facebook page. You will receive a chat with visitor.

# How to debug
in `extension/fbmessenger/settings/settings.ini.php` change setting to `'enable_debug' => true` if you have verified site. Check `cache/default.log` for more detailed error.

# Todo
 * Add support for images, not just plain messages.
 * Add support for automated hosting environment.
 * Get facebook user details like email or phone.
