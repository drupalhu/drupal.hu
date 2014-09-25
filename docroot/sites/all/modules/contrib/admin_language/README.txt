Administration Language
================================================================================

Summary
--------------------------------------------------------------------------------

The Administration Language module makes sure all administration pages are
displayed in the preferred language of the administrator. Which pages are
considered administration pages can be configured. Users with the right
permissions can choose to use either the global administration language or a
language of their choice.


Requirements
--------------------------------------------------------------------------------

The Administration Language module requires the Locale (core) module.


Installation
--------------------------------------------------------------------------------

1. Copy the admin_language folder to sites/all/modules or to a site-specific
   modules folder.
2. Go to Modules and enable the Administration Language module.


Configuration
--------------------------------------------------------------------------------

1. Go to Administration > Configuration > Regional and language  > Languages and
   choose the language you want to use as the administration language.

2. Go to Administer > People > Permissions and grant your administrator role the
   'Select administration language' permission.

3. (Optional) Go to Administration > Configuration > Regional and language >
   Languages > Administration language and select which paths should use the
   selected administration language. By default, all pages on the site are
   affected.

4. (Optional) Go to Administration > Configuration > Regional and language >
   Languages > Administration language and select whether you want to remove the
   administration language in the language dropdown on the node edit form
   (requires that the Content translation module is enabled).

5. (Optional) Enable the 'Language switcher (interface text, without
   administration language)' block if you need a language switcher block which
   doesn't display the administration language.

6. (Optional) Go to My account or another user/<uid>/edit page to choose a
   different admin language than the global default.


Permissions
--------------------------------------------------------------------------------

The module offers the following permissions:

"Select administration language": Grant this permission to roles which
should be able to access the site using the selected administration language.

"Use all enabled languages ": The module makes it possible to hide the selected
administration language from certain parts of the interface (e.g. the node form
and the user form). Grant this permission to roles which should be able to use
all enabled languages on the site.


Supported modules
--------------------------------------------------------------------------------

Administration menu

The latest version of the module supports the Administration menu module. By
changing a setting you can use the current administration language to display
the administratione menu (e.g. having the content displayed in Danish and the
administration menu displayed in English).

Administration menu Toolbar style

The latest version of the module supports the Administration menu Toolbar style
module.


Known issues
--------------------------------------------------------------------------------

The module has not been thoroughly tested with the Internationalization (I18N)
module. If you encounter any issues when using these modules together, please
create an issue in the Administration language issue queue on drupal.org.


Support
--------------------------------------------------------------------------------

Please post bug reports and feature requests in the issue queue:

  http://drupal.org/project/admin_language


Credits
--------------------------------------------------------------------------------

Author: Morten Wulff <wulff@ratatosk.net>

Initial development sponsored by Morten.dk.

Current development and maintenance made possible in part by Peytz & Co. A/S.
