**Version 1.1** (24.02.2020)

Custom class "WPintranet" to use http://www.directorylister.com in combination with Wordpress and select subfolder access in backend.

## CONFIGURATION OPTIONS
* $wp_roles: define wp roles. if it is a string and folder access is by role, add folderaccess to array
* $root: root directory
* $mode: folder settings
* $directory: main directory
* $folders: folders to create inside the main directory

## CONFIGURATION FILE
```
"WPintranet": {
  "roles": "intranet",
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
