# Drupal Tooltip Taxonomy

Automatically attach explanation tooltip for taxonomies.

- [Entity Reference Tree]
  * [Overview]
  * [Requirements](#requirements)
  * [Installation](#installation)
  * [Settings](#settings)
  * [Features]
    
## Overview

This module delivers a capability to attach explanation tooltip to taxonomy terms automatically. You can specify which taxonomies will have the explanation tooltip in certain pages, content types or all page across entire website. You also can apply the tooltip to certain field rather than all text fields. It also provides a solution to cope with ambiguous terms.

Features:

  - Specify taxonomy tooltip by path, content types, view modes and fields.
  - Ambiguous terms.
  - Attach tooltip automatically.
  - Simple accessible tooltip compatible for all browsers.

### Requirements

- Drupal 8.5 or greater
- PHP 7.0 or greater

### Installation
- Install this module using the normal Drupal module installation process.
- The JavaScrip and CSS has already been included in module, you don't need to install them separately.

## Settings
 
- Go to the configuration page: /admin/config/content/tooltip_taxonomy

- Create filter conditions for explanation tooltip using one or multiple vocabularies attached to certain path, content types, view modes or fields. 

- For ambiguous meaning taxonomy terms, you need to create respective filter condition for each meaning with different weight value. The one has bigger weight value will overwrite those have smaller weight value. For instance, taxonomy term of 'CMS' has different meaning in different pages or content types or fields. You can create a filter condition with a low weight applied to all pages across whole site that has a general  meaning, for example CMS means 'Content Management System' in most of pages. But in certain pages or content types or even fields, CMS means something else. Then you can  create another filter condition using different vocabulary with higher weight that will overwrite the general meaning in specific pages or fields.

- HTML limit filter setting: In order to make the tooltip working, the span tag and class attribute must be allowed for those text fields. You can go to the text format setting UI to check and update the settings. For example, you can to go '/admin/config/content/formats/manage/basic_html' and add following into the 'Allowed HTML tag' settings.
<span class="tooltip tooltiptext">

User guide for format setting:
https://www.drupal.org/docs/user_guide/en/structure-text-format-config.html
