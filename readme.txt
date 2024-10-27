=== aMiSTACX PageSpeed Redis Caching Purge ===
Contributors: amistacx
Donate link: https://amistacx.com/worpress/
Tags: Redis, PageSpeed, Cache Flush, Caching, Cache, Cache Purge, aMiSTACX
Requires at least: 4.9
Tested up to: 4.9.8
Requires PHP: 7.2
Stable tag: 1.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

aMiSTACX PageSpeed Redis Caching Flush is a simplistic cache purge for Apache PageSpeed and Redis. Additionally, a URL purge is provided for PageSpeed.

== Description ==

Designed to be used on the aMiSTACX Amazon AWS Platform, that comes with working and tested PageSpeed and Redis Cache modules, PageSpeed Redis Caching Flush WordPress plugin offers a simple solution to purge the entire cache at any time. You also have the option of purging individual URLs for PageSpeed. Simplicity and convenience.

Tested and designed for WordPress stacks that have PHP 7.2.x, Redis 3.0.6+, and PageSpeed 1.13.35.2-stable+ running on WordPress 4.9.8+.

More information: https://amistacx.com/wordpress

== Installation ==

= Minimum Requirements =

* PHP version 7.2.7 or greater
* MySQL version 5.7 or greater
* PHP Curl 7.x or greater
* Redis 3.0.6 or greater [Enabled, Running]
* PageSpeed 1.13.35 Stable or greater [Enabled]
* WordPress 4.9.8 or greater
* Default Distro PageSpeed & Redis Install Paths

Visit the [aMiSTACX WordPress F1 Build Specs for more information](https://amistacx.com/flavors/wordpress-f1-lamp-performance-edition/) for a detailed list of server requirements.

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of our plugin, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type “aMiSTACX” and click Search Plugins. Once you’ve found our "aMiSTACX PageSpeed Redis Caching Flush" plugin you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by simply clicking “Install Now”.

= Manual installation =

The manual installation method involves downloading our plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= Pre-Configured installation =

This module comes pre-loaded and configured with PageSpeed and Redis on the aMiSTACX WordPress F1 Performance series. You can find this stack on Amazon AWS Marketplace:

https://aws.amazon.com/marketplace/pp/B0787XYKQ1

= Updating =

Automatic updates should work like a swiss watch; as always though, ensure you backup your site just in case and test everything on a development server.

== Frequently Asked Questions ==

= Can this plugin work on a non-aMiSTACX WordPress Platform? =

Yes! As long as your Apache PageSpeed and/or Redis meets our specs, is working correctly, and uses default Linux [Ubuntu] distro paths, the plugin should work without issue.

= Do you offer plugin support? =

Yes and no. For aMiSTACX customers using this plugin on an aMiSTACX approved server - yes; otherwise, all other situations - NO.

= Are there feature requests? =

Yes; even if you are not using an aMiSTACX platform, please feel free to post feature requests and post any bugs encountered.

= Will this work on NGINX? =

Yes/No; We have not tested PageSpeed on NGINX as this was designed for the Apache PageSpeed Module; however, Redis should work with Apache and NGINX.

== Screenshots ==

1. Top PageSpeed & Redis Flush Buttons Enabled.
2. Example of successful PageSpeed ALL Purge.
3. aMiSTACX PageSpeed & Redis Flush settings panel.

== Changelog ==

= 1.0.0 - 2018-10-05 = Initial Plugin Release

== Upgrade Notice ==

N/A; Initial Release
