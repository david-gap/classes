**Version 2.8.2** (17.09.2020)

Custom class "prefix_core_BaseFunctions" used as a library for useful functions


## 1.0 FOR DEVELOPMENT

### 1.1 EXPLODE COMMA SEPERATED ARRAY
Explode a comma separated string to a array
```php
core_BaseFunctions::AttrToArray("Attribute 1, Attribute 2, Attribute 3");
/* RESULTING: Array */
return array("Attribute 1" "Attribute 2" "Attribute 3");
```

### 1.2 PRICE FORMAT
Format a price string with custom separators
```php
core_BaseFunctions::formatPrice('1972,15', '.', '`');
/* RESULTING: string */
return "1`972.15"
```

### 1.3 BROWSER CHECK
Return the current browser name
```php
core_BaseFunctions::get_browser_name();
/* RESULTING: string */
return "CURRENT BROWSER NAME"
```

### 1.4 GENERATE SHORT ID
Generating a string with random content depends on attributes
```php
core_BaseFunctions::ShortID(12, 'letters');
/* RESULTING: string */
return "randomstring"
```

### 1.5 FILE EXISTS
Check if given file exists. Path can be absolute or relative
```php
core_BaseFunctions::CheckFileExistence('classes/prefix_core_BaseFunctions/class.core_BaseFunctions.php');
/* RESULTING: bool */
return true
```

### 1.6 CHECK IF FOLDER EXISTS
Check if given path exists
```php
core_BaseFunctions::CheckDir('classes/prefix_core_BaseFunctions');
/* RESULTING: bool */
return true
```

### 1.7 CREATE FOLDER
Create a new folder
```php
core_BaseFunctions::CreateDirectory('new_folder', 0777);
/* RESULTING: bool */
return true;
```

### 1.8 COPY FOLDER CONTENT AND SUB FOLDERS
Copy folder with subfolders and content to new destination
```php
core_BaseFunctions::copyDirectory('copy_this_folder', 'target_folder', 0777);
```

### 1.9 GET CONTENT FROM STRING BETWEEN TWO CHARS/CHAR GROUPS
Get text inside string that is between two char groups
```php
core_BaseFunctions::getBetween('the <strong>target</string> text', '<string>', '</string>');
/* RESULTING: string */
return "target"
```

### 1.10 FIND KEY IN MULTIDIMENSIONAL ARRAY
Search inside a multidimensional array for a key or value
```php
$array = array(
  "0" => "asdf",
  "1" => array(
    "0" => "findme"
  )
);
core_BaseFunctions::MultidArraySearch('findme', $array, 'value');
/* RESULTING: bool */
return true
```

### 1.11 CLEAN PHONE NUMBER
Clean given string from spaces, + or () so it can be used as phone number link
```php
core_BaseFunctions::cleanPhoneNr('+41(0)123456789');
/* RESULTING: string */
return "0041123456789"
```

### 1.12 DELETE FOLDER
Delete folder with files and subfolders inside
```php
core_BaseFunctions::deleteFolder('root/folder');
return true
```

### 1.13 SORT ARRAY
Sort multidimensional array by a key
```php
core_BaseFunctions::MultidArraySort($array, "keyname", "DESC", false);
return true
```

### 1.14 CLEAN ARRAY
Clean array from empty values.
Use second attribute for multidimensional arrays to repeat the cleaning.
```php
core_BaseFunctions::CleanArray($array, 3);
return true
```

### 1.15 SLUGIFY STRING
Slugify string
Use second attribute for multidimensional arrays to repeat the cleaning.
```php
core_BaseFunctions::Slugify("äöü ÄÖÜ+");
return 'aeoeue-aeoeue-'
```

### 1.16 INSERT TO ARRAY AT SPACIFIC POSITION
insert array into other array at specific position
```php
$new = array(
  "city" => "Atlantis"
);
$existing = array(
  "Name" => "Max",
  "Surname" => "Mustermann",
  "Street" => "Mainstreet",
  "ZIP" => "3000"
);
core_BaseFunctions::AddToArrayPosition($new, $existing, 4);
return array(
  "Name" => "Max",
  "Surname" => "Mustermann",
  "Street" => "Mainstreet",
  "city" => "Atlantis",
  "ZIP" => "3000"
)
```


## 2.0 DATES

### 2.1 CHECK IF VARS ARE OUT OF DATE
Check if given date/dates are in the future/past or in between both dates
```php
core_BaseFunctions::DateCheck("01.01.1900", "31.12.2100", "between");
/* RESULTING: bool */
return true
```

### 2.2 DATE RANGE FORMAT
Return the given dates formated
```php
core_BaseFunctions::DateRange("01.01.1900", "31.12.2100", "-");
/* RESULTING: Array */
return "01.01.1900 - 31.12.2100"
```


## 3.0 FOR FORMULARS

### 3.1 GET POST
Get given variable value by name if GET/POST/REQUEST with name exists, else return the fallback value
```php
core_BaseFunctions::getFormPost("name", "Max");
/* RESULTING: string */
return "VAR name VALUE OR Max AS DEFAULT";
```

### 3.2 CHECK IF OPTION IS SELECTED
Check if select option value is the same like returned variable value and return selected attribute
```php
core_BaseFunctions::setSelected('two', array("one", "two", "three"));
/* RESULTING: string */
return "selected='selected'"
```

### 3.3 CHECK IF CHECKBOX IS CHECKED
Check if checkbox value is the same like returned variable value and return checked attribute
```php
core_BaseFunctions::setChecked('two', array("one", "two", "three"));
/* RESULTING: Array */
return "checked='checked'"
```


## 4.0 FOR WORDPRESS

### 4.1 GET CURRENT LANGUAGE
Get the current language code. WPML and Polylang supported
```php
core_BaseFunctions::getCurrentLang();
/* RESULTING: string */
return "current language code"
```

### 4.2 ADD USER ROLE
Create a new wordpress user role. Give editor rights or additional capabilities
```php
core_BaseFunctions::setWProle("New Role Name", "editor rights" array("additional_capability"));
```

### 4.3 ADD CUSTOM TAXONOMY
Create a new wordpress taxonomy for selected post type by giving the new taxonomy slug (sub array key) and Label or other configurations inside array
```php
$taxonomies = array(
  "tax_slug" => (
    "label" => "Equipments",
    "hierarchical" => true,
    "query_var" => true
  )
);
core_BaseFunctions::register_cpt_taxonomy("post", $taxonomies)
```

### 4.4 RETURN TAXONOMY TERMS IN A LIST
List all taxonomy terms by taxonomy slug. Add a post ID for the post selected terms.
With the third attribute you can return only the first entry
Add fourth attribute with letters you would like to separate the entries
```php
core_BaseFunctions::ListTaxonomies("tax_slug", 22, false, ', ');
/* RESULTING: string */
return "<ul><li>LIST WITH ALL TAXONOMIES OF THE POST WITH THE ID 22</li></ul>"
```

### 4.5 LOGIN FORMULAR
Custom login formular
```php
core_BaseFunctions::WPLoginForm("login/taget/path");
/* RESULTING: string */
return "html WP Login Form"
```


## 5.0 COORDINATES

### 5.1 CONVERT: WGS lat/long TO CH1903 y
Convert WGS coordinates to CH1903+ y. Define third attribute as false to get CH1903 y.
```php
core_BaseFunctions::WGStoCHy("7.438637222222222", "46.9510811111111");
/* RESULTING: string */
return "2600000"
```

### 5.2 CONVERT: WGS lat/long TO CH1903 x
Convert WGS coordinates to CH1903+ x. Define third attribute as false to get CH1903 x.
```php
core_BaseFunctions::WGStoCHx("7.438637222222222", "46.9510811111111");
/* RESULTING: string */
return "1200000"
```

### 5.3 CONVERT: CH1903 y/x TO WGS lat
Convert CH1903+ coordinates to WGS latitude. Define third attribute as false if given coordinates are CH1903.
```php
core_BaseFunctions::CHtoWGSlat("600000", "200000", false);
/* RESULTING: string */
return "7.438637222222222"
```

### 5.4 CONVERT: CH1903 y/x TO WGS lng
Convert CH1903+ coordinates to WGS longitude. Define third attribute as false if given coordinates are CH1903.
```php
core_BaseFunctions::CHtoWGSlong("2600000", "1200000", true);
/* RESULTING: string */
return "46.9510811111111"
```
