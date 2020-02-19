**Version 1.0.2** (19.02.2020)

Custom class "Formbuilder": Build a formular simply by creating a array and inserting it into a new class season.

## USAGE
* create a array for the form definition
```php
$args = array(
  "container" => array(
    "css" => "",         // css classes inside container div
    "add" => ""          // all inside container div tag except class and id
  ),
  "form" => "",          // all inside form tag
  "inputs" => array(),   // Build input
  "submit" => array(
    "name" => "",        // submit name add to prefix
    "id" => "",          // submit id
  )
);
```
* add sub array inside the $args array for each input inside "inputs"
```php
array(
  "type" => "",               // hidden, select, textarea, checkbox, radio, email, text
  "name" => "" ,              // field name
  "disabled" => "",           // disable field
  "value" => "",              // default value
  "id" => "",                 // field id
  "class" => "",              // field css class
  "label" => "",              // field label
  "placeholder" => "",        // placeholder
  "form_input_before" => "",  // html code before input container
  "form_input_after" => ""    // html code after input container
)
```
* return the formular with your arguments and if needed a prefix for form and inputs
```php
// without prefix
echo prefix_core_Formbuilder::FormBuilder($args;
// with prefix
echo prefix_core_Formbuilder::FormBuilder($args, "prefix");
```
