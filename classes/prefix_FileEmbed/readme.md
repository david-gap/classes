**Version 2.1** (24.07.2020)

Custom class "prefix_FileEmbed" to set file content as a global

## CONFIGURATION OPTIONS
* $main_directory: file directory
* $files: files to insert

## CONFIGURATION FILE
```
"FileEmbed": {
  "directory": "/",
  "files": {
    "0": {
      "key": "global name",
      "file": "add_path/file_name.csv",
      "title": 1,
      "file_coding": "UTF-8",
      "encoding": "Windows-1252",
      "id_column": '',
      "order_column": "",
      "order_direction": "ASC",
      "order_bydate": 0,
      "seperator": ",",
      "ssl_stream": 1
    }
  }
}
```

## USAGE
Call given globals with the file key
```
gloabl $demo
```
