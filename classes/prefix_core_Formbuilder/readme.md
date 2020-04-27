**Version 1.2.2** (27.04.2020)

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
  "type" => "",                      // hidden, select, textarea, checkbox, radio, email, text
  "name" => "" ,                     // field name
  "disabled" => "",                  // disable field
  "value" => "",                     // default value
  "id" => "",                        // field id
  "autocomplete" => "autocomplete"   // field autocomplete
  "readonly" => "readonly"           // field readonly
  "attributes" => "",                // field additional attributes
  "class" => "",                     // field css class
  "label" => "",                     // field label
  "placeholder" => "",               // placeholder
  "form_input_before" => "",         // html code before input container
  "form_input_after" => ""           // html code after input container
)
```
* return the formular with your arguments and if needed a prefix for form and inputs
```php
// without prefix
echo prefix_core_Formbuilder::FormBuilder($args);
// with prefix
echo prefix_core_Formbuilder::FormBuilder($args, "prefix");
```

### JS FORM VALIDATION
1. Add JS from validation folder and add it to your project.
2. Style required class or add scss to your project.
3. Add data-validation and data-valtype to your required fields.
```php
"inputs" => array(
  array(
    "type" => "text",
    "attributes" => array(
      "data-validation" => "true",
      "data-valtype" => "text"
    )
  )
)
```
