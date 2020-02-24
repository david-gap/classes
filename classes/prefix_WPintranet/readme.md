**Version 1.1.1** (24.02.2020)

Custom class "WPintranet" to use http://www.directorylister.com in combination with Wordpress and select subfolder access in backend.

## CONFIGURATION OPTIONS
* $WPintranet_roles: define wp roles. if it is a string and folder access is by role, add folderaccess to array
* $WPintranet_editor: basic access for WP
* $WPintranet_roles_arg: add access slugs
* $WPintranet_root: root directory
* $WPintranet_mode: folder settings
* $WPintranet_directory: main directory
* $WPintranet_folders: folders to create inside the main directory

## CONFIGURATION FILE
```
"WPintranet": {
  "roles": "intranet",
  "editor": false,
  "access": {
    "intranet": true
  },
  "root": "ABSPATH",
  "mode": 0777,
  "directory": "intranet",
  "folders": {
    "0": "Folder 1",
    "1": "Folder 2",
    "2": "Folder 3"
  },
}
```

## USAGE
After configuration, define user access in WP backend and insert Shortcode.
```
[intranet]
```
