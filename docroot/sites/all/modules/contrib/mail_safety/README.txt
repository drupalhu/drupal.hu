--- Summary ---

A simple and safe way to test and debug outgoing emails 
without having to worry that all your users will get unwanted 
emails. Mail Safety provides a dashboard which catches and logs all 
outgoing mails.

--- Features ---

- Dashboard with email overview
- Catch emails before they go to their recipients
- View the e-mail in your browser
- Choose to send the e-mail to an e-mail address of your choice
- Choose to send the e-mail to the original recipients

--- How to use ---

- Give the required permissions to the roles
- Go to admin/config/development/mail_safety/settings to enable 
  the module and configure other options
- Go to admin/config/development/mail_safety/dashboard to view all 
  the mails that are sent by Drupal with mail safety enabled

--- Extra Safety ---

For extra safety to never send mails on a test environment etc. 
Add and configure these settings to your settings.php:

$conf['mail_safety_enabled'] = TRUE;
$conf['mail_safety_send_mail_to_dashboard'] = TRUE;

Or on your production environment you can always keep mail_safety 
disabled by adding the following to your settings.php:

$conf['mail_safety_enabled'] = FALSE;

--- Similar modules ---
This module is similar to some existing mail modules but more focused 
on being a safety net and more precise debugging and testing.

Mail Logger
Reroute Email

--- Contact ---
To contact me you can send an e-mail to bvdhoek@gmail.com or find me 
on drupal.org with the username barthje.

This module is is sponsored by Synetic: http://www.synetic.nl
