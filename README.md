# Yeti Admin
This is the Admin panel project for Yeti CMS

## Requirements
- PHP5
- Apache
- mod_rewrite

## Contents

### Router
The router consists of two files, `.htaccess` and `router.php`. The router injects the Javascript responsible for the Yeti CMS front-end into all `html` files uploaded into the Yeti CMS base directory.

### Editor
`Editor.php` is a local implementation of the `ACE Editor`, which provides a rich editor for modifying code (HTML, CSS, & Javascript).

### Yeti CMS Installer
The Yeti CMS Installer is located in `index.php`, and is removed once installation is complete. It is reponsible for writing two files, `/config.php` and `/config.js`, which contain the local CMS configuration. It also attempts to fix CHMOD for the Yeti CMS base directory.

### Yeti CMS File Manager
The Yeti CMS File Manager exists to facilitate basic file management tasks, including uploads. It exists under the `/yeti-admin/` directory. `scan.php` also assists in returning a directory listing (this is likely to be factored out of future versions).
