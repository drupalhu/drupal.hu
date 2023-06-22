ABOUT

This module is a replacement for the 'access rules' functionality which was removed from Drupal 7.

The module implement an API and custom hooks that allow third-party modules to add additional restrictions, or implement new type of restrictions.

USAGE

- Go to /admin/modules and enable User Restrictions and User Restrictions UI module.
- Go to /admin/config/people/user-restrictions, and click Add rule.
- Select Access type, Rule type, Expire time, and fill the Mask field.
  - use Access type to specifically deny or allow the matched mask;
  - Rule type is used to tell the module to restrict/allow based on username or user email;
  - use wildcard % or _ in Mask field to match the username or email address. A % will match any number of characters, a _ will match precisely one character.
  - Set up the expired hours/days for the restriction or alternatively leave for unlimited restriction.
- Click Save rule button and the matched user account will be restricted.
- edit/delete the restriction rules in /admin/config/people/user-restrictions.
- You can also test usernames and emails in the CHECK RULES fieldset that appears after at least one rule has been created.

VERSION NOTE

- There will be no releases for Drupal 5 or Drupal 6. If you're looking for this functionality in either of those versions, try admin/user/rules. You are most likely to need this module if upgrading from Drupal 6.x or earlier with existing access rules in place. A stripped down version of IP address blocking was left in core, so this module will focus on usernames and e-mail addresses. Third-party modules can implement additional restrictions using the API exposed by the module.

- Updating from a different release of the development snapshot is not supported. To install a newer development snapshot, you always need to uninstall the previous version before copying the new one.

- Development snapshots are intended for testing only. Don't use them in a production site, or for other purposes. If used in a production site, or for other purposes, there will be no support for any resulting problems.

REQUIREMENTS

- Drupal 7

EXAMPLES

- Blocking all users with hotmail email addresses:
  - Access Type: Denied
  - Rule Type: Email
  - Mask: %@hotmail.com

- Blocking all users with names starting with the letter 'a'
  - Access Type: Denied
  - Rule Type: Name
  - Mask: a%

- Only allowing users to register from gmail.com email addresses
  - Access Type: Allowed
  - Rule Type: Email
  - Mask: %@gmail.com
  - Access Type: Denied
  - Rule Type: Email
  - Mask: %@%

- Only allowing users with a '.' in the middle of their name
  - Access Type: Allowed
  - Rule Type: Name
  - Mask: %.%
  - Access Type: Denied
  - Rule Type: Name
  - Mask: %
