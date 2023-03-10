CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Recommended modules
 * Installation
 * Configuration
 * Troubleshooting
 * FAQ
 * Maintainers


INTRODUCTION
------------

The Masquerade Role module allows the user to swap roles on the fly.
This is especially useful during development and QA phases of websites.

Some cache tags are invalidated when masquerading as a different role (e.g. local task),
this is only done because this module behaves strangely in combination 'user 1'.
Invalidating said cache tags (also configurable in the UI) solves this issue.

 * For a full description of the module, visit the project page:
   https://www.drupal.org/project/msqrole

 * To submit bug reports and feature suggestions, or track changes:
   https://www.drupal.org/project/issues/msqrole


REQUIREMENTS
------------

This module has no special requirements.


RECOMMENDED MODULES
-------------------

 * Masquerade ( https://www.drupal.org/project/masquerade ) this is a very similar
   module that provides functionality that crosses paths with msqrole. The difference
   with this module is that you actually swap user, instead of only your active roles.


INSTALLATION
------------

 * Install as you would normally install a contributed Drupal module. Visit
   https://www.drupal.org/node/1897420 for further information.


CONFIGURATION
-------------

 * Configure the user permissions in Administration » People » Permissions
 * Configure the cache tags to be invalidated at Administration » Configuration » People » Masquerade Role Settings


TROUBLESHOOTING
---------------

 * If some blocks/items are incorrectly appearing/disappearing (if the role has no permission to it):

   - Check the cache tags this block/item uses
   - Add them via the configuration page to be cleared when
     masquerading as a different role.


MAINTAINERS
-----------

Current maintainers:
 * Randal V. - https://www.drupal.org/user/3612901
