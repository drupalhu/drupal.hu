CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Features
 * Requirements
 * Installation
 * Configuration
 * API
 * Maintainers

INTRODUCTION
------------

This small module helps theme-developers to deal with cross-browser
compatibility. It makes easier to handle different types of non-widespread
browsers just as much as it helps with using different versions of Internet
Explorer.

 * For a full description of the module, visit the project page:
   https://www.drupal.org/project/browserclass

 * To submit bug reports and feature suggestions, or to track changes:
   https://www.drupal.org/project/issues/browserclass

FEATURES
--------

The module extends the $body_classes variable in page.tpl.php based on the
enduser's browser, with the following:
 * ie
 * ie[version]
 * opera
 * safari
 * chrome
 * netscape
 * ff
 * konqueror
 * dillo
 * chimera
 * beonex
 * aweb
 * amaya
 * icab
 * lynx
 * galeon
 * operamini

and with the following platforms:
 * win
 * ipad
 * ipod
 * iphone
 * mac
 * android
 * linux
 * nokia
 * blackberry
 * freebsd
 * openbsd
 * netbsd

The module checks if the device is mobile and adds "mobile" or "desktop" class.

The module also makes a $browser_classes variable available in page.tpl.php,
which stores the data in an array, this way the developer can make use of it as
needed, if he does not wish to use the $body_classes variable.

REQUIREMENTS
------------

None.

INSTALLATION
------------

 * Install as you would normally install a contributed Drupal module. Visit:
   https://www.drupal.org/documentation/install/modules-themes/modules-7
   for further information.

CONFIGURATION
-------------

The module has a settings page (admin/config/user-interface/browserclass), where
the administrator can choose between these options:
 * always add the class with JavaScript
 * only use JavaScript if page cache is enabled
This page is available only users with "administer browser class" permission.

API
---

Developers can add their own classes with hook_browserclass_classes(). More
information in browserclass_hook.php file.


MAINTAINERS
-----------

Current maintainers:
 * Norman Kämper-Leymann - https://www.drupal.org/u/leymannx

Former maintainers:
 * Kálmán Hosszu – https://www.drupal.org/u/kalman.hosszu
 * Dániel Kalmár - https://www.drupal.org/u/kdani

This project has originally been developed by:
 * Kálmán Hosszu – https://www.drupal.org/u/kalman.hosszu
