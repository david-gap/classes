**Version 1.3.6** (05.05.2020)

Custom class "prefix_FileEmbed" to set file content as a global

## CONFIGURATION OPTIONS
* $main_directory: file directory
* $files: files to insert

## CONFIGURATION FILE
```
"FileEmbed": {
  "directory": 'main_directory/',
  "files": {
    "global_name": {
      "file": "add_path/file_name.csv",
      "title": true,
      "file_coding": "UTF-8",
      "encoding": "Windows-1252",
      "id_column": false,
      "order_column": "",
      "order_direction": "ASC"
    }
  }
}
```

## USAGE
Call given globals with the file key
```
gloabl $demo
```
