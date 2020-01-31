**Version 1.0.2** (31.01.2020)

Custom class "WPintranet" to use http://www.directorylister.com in combination with Wordpress and select subfolder access in backend.

## CONFIGURATION OPTIONS
* $wp_roles: define wp roles. if it is a string and folder access is by role, add folderaccess to array
* $root: root directory
* $mode: define user rights
* $directory: main directory
* $folders: folders to create inside the main directory

## USAGE
After configuration, define user access in WP backend and insert Shortcode.
```
[intranet]
```
