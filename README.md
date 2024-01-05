# ICUK Broadband Availability Checker
Integrate the ICUK broadband availability checker into your PHP application
## Installation

The PECL [mbstring](http://php.net/mbstring), zip and ext-curl extensions are required

Download and extract the latest release
```bash
wget https://github.com/BoronBGP/icuk-broadband-checker-php/releases/download/latest/icuk-broadband-checker.tar.gz
tar -xvf icuk-broadband-checker.tar.gz
```

That is is all that is needed to install the availability checker, if this fails then try installing from source below
At this point you should read the usage section which will show you how to configure the library

## Installation with Composer

Download composer.phar (if not already installed)
```bash
wget https://getcomposer.org/download/latest-stable/composer.phar
```

Create a composer.json file with this data (if you already have a composer.json file add the data from the require and repositories)
```json
{
    "name": "root/html",
    "type": "project",
    "minimum-stability": "dev",
    "require": {
        "icuk/broadband-availability-php": "dev-master"
    }
}
```

Install with all dependencies

```bash
php composer.phar install
```

## Usage

### api.php

This file will allow the user to make requests to the broadband availability api. You should in this format but with your own credentials and
you can and should use your own method of authentication beforehand to prevent unauthorised api access to the endpoint.

```php
<?php
require __DIR__ . '/vendor/autoload.php';

// Add user authentication here!

echo json_encode(\Icuk\BroadbandAvailabilityPhp\BroadbandAvailabilityProxy::handle_api("ExampleAPIUsername", "ExampleAPIPassword123"));
```

### index.php

This is a very simple example that shows how a page that presents the user with the broadband avaiability checker may be used.
The api.php endpoint is purely an example and that should just be a path to wherever you've put the api.php file

The render_styles line is also optional and you can apply your own stylesheet to the table, it also important to note that the results
panel will not appear until a search takes place.

```php
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Broadband Availability Example</title>
  <?php
    require __DIR__ . '/vendor/autoload.php';
    $ba = new \Icuk\BroadbandAvailabilityPhp\BroadbandAvailability("/api.php");
    $ba->render_scripts();
    $ba->render_styles();
  ?>
</head>
<body>
  <?php
    $ba->render_search();
    $ba->render_address_list();
    $ba->render_results();
  ?>
</body>
</html>
```

![Example of what the above code should result in](https://github.com/boronbritt/icuk-broadband-availability-checker/blob/master/assets/default_example.png "Should result in this")


### Edit error message
To swap out the error message that occurs when an availability check fails you can use the error message as a parameter of render_search
```php
$ba->render_search("Example error message");
```

## Custom Styles
### Method 1.
You can use the SearchStyleSettings and ResultsStyleSettings classes to edit the colour schemes of the search and results modules.
The example below shows using this to make the search button a red to green fade and the background of the results fields blue.


```php
$style = new \Icuk\BroadbandAvailabilityPhp\SearchStyleSettings();
$rstyle = new \Icuk\BroadbandAvailabilityPhp\ResultsStyleSettings();
$astyle = new \Icuk\BroadbandAvailabilityPhp\AddressSelectStyleSettings();

$style->button_gradient_low = "#f00";
$style->button_gradient_high = "#00ff00";
$rstyle->head_background_colour = "rgb(0, 0, 255)";
$astyle->background_colour = "rgb(0, 255, 0)";

$ba->render_styles($style, $rstyle, $astyle);
```
![Example of what the above code should result in](https://github.com/boronbritt/icuk-broadband-availability-checker/blob/master/assets/style_example.png "Should result in this")

Currently the rgb, rgba, hex, hsl, hsla, CIELab, and xyz colour formats are supported, while colour names such as "purple" or "darkgreen" are invalid.

**ResultsStyleSettings Properties**
* **background_colour**  : background colour of the results table
* **head_background_colour** : background colour of the head of the results table
* **left_background_colour** : background colour of the left side of the results table
* **seperators_colour** : colour of the seperators between cells
* **text_colour** : colour of the text of the results
* **head_text_colour** : colour of the text of the head of the results table
* **left_text_colour** : colour of the text of the left of the results table
* **available_label_colour** : colour of the available label
* **not_available_label_colour** : colour of the not available label
* **available_text_colour** : colour of the text on the available label
* **not_available_text_colour** : colour of the text on the not available label
* **loading_circle_primary_colour** : the colour of the spinner on the loading circle
* **loading_circle_secondary_colour** : the colour of static background of the loading circle
* **hide_results** : hide the results  of the result table before a search occurs

**SearchStyleSettings Properties**
 * **button_gradient_low** : colour of the button at the bottom of the gradient
 * **button_gradient_high** : colour of the button at the top of the gradient
 * **button_gradient_low_hover** : colour of the button while its being hovered over at the bottom of the gradient
 * **button_gradient_high_hover** : colour of the button while its being hovered over at the top of the gradient
 * **button_text_colour** : colour of the text on the button
 * **input_bg_colour** : colour of the the input box
 * **input_text_colour** : colour of the text on the input box
 * **input_hover_fade_colour** : colour of the fade around the input box while it is selected
 * **error_message_colour** : colour of the error message

**AddressSelectStyleSettings Properties**
* **background_colour**  : background colour of the address table
* **head_background_colour** : background colour of the head of the results table
* **filter_box_background_colour** : background colour of the filter/search box
* **address_text_colour** : colour of the seperators between cells
* **filter_box_text_colour** : colour of the text in the filter/search box
* **head_text_colour** : colour of the text of the head of the address table
* **nad_text_colour** : colour of the text of NAD in the address table
* **border_colour** : colour of the border of the address table
* **head_border_colour** : colour of the border of the head of the address table
* **filter_box_border_colour** : colour of the border of the filter/search box
* **filter_box_hover_fade_colour** : colour of filter box when pressed down

### Method 2.
You can develop your own stylesheet with relative ease as each module's elements are very simple to identify with their id's for example the button in the search
uses the id "broadband-availability-search-submit" and the input box uses "broadband-availability-search-input".
You can use the style template php file used by this library [here](src/modules/Styles.php) as a reference
