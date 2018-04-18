# Hypothetical Template

The Hypothetical website template based on Laravel 5.6

## Utilities

### Language

The default language is set by the `DEFAULT_LANGUAGE` variable in the `.env` file. This will be the language used until it is changed, which can be done using the `/language/{lang}` route or directly using `Language::setSessionLanguage($lang)` where in both cases `lang` is the language code for a given language.

In the view, a block of text can be configured with multiple languages using the following syntax:

```php
    @lang([
        'en' => "This is a sentence",
        'fr' => "C'est une phrase"
    ])
```

or

```php
    {{ Language::select([ 'en' => "This is a sentence", 'fr' => "C'est une phrase" ]) }}
```

## Public

The default public facing website uses vue.js. To configure a non-SPA traditional website, look at the files in `traditional-bootstrap`.

The following list of files and directories are where various pieces of the public website are located:

* `resources/views/templates/base.blade.php`: The outer template for the entire website
* `resources/views/templates/public.blade.php`: The inner template for the public site
* `resources/assets/fonts`: The folder containing website fonts (these get loaded into `public/fonts/` by the gulpfile)
* `resources/assets/js/app.js`: The main javascript file that loads the public site
* `resources/assets/js/mixins`: The folder containing vue.js mixins that can be applied globally in `resources/assets/js/app.js` or in individual components
* `resources/assets/js/mixins/base-page.js`: The base-page mixin with page functionality that should be imported into all page components
* `resources/components`: The folder containing vue.js components
    * `resources/components/pages`: Page components that should be imported into vue-router in `resources/assets/js/app.js`
    * `resources/components/sections`: Section components (single-use per page) that should be imported into mixins or page components
    * `resources/components/partials`: Partial components (multi-use per page or section) that should be imported into mixins and/or page and section components
* `resources/assets/sass/app.scss`: The main sass file for the public site
* `resources/assets/sass/_fonts.scss`: Stylesheet containing font declarations and mixins declared to use those fonts in other stylesheets
* `resources/assets/sass/_var.scss`: Stylesheet containing variables to be used in other stylesheets
* `resources/assets/sass/pages`: Stylesheets for page-specific styles wrapped in the respective page component class
* `resources/assets/sass/sections`: Stylesheets for section-specific styles wrapped in the respective section component class
* `resources/assets/sass/partials`: Stylessheets for partial-specific styles wrapped in the respective partial component class
* `resources/assets/sass/classes`: General stylesheets for classes that can be used anywhere
* `resources/assets/sass/mixins`: Stylesheets declaring SCSS mixins for use in other stylesheets

Dependencies can be included with bower or npm and loaded either into the `jsPublicLibs` array in the gulpfile or imported in the javascript.

Other information about database interaction, routing, controllers, etc can be viewed in the [Laravel Documentation](https://laravel.com/docs).

## Dashboard

### Updating the dashboard menu

The dashboard menu can be edited by changing the `$menu` array in `app/Models/Dashboard.php`.

The each item in the array is itself an array, containing either a menu item or a dropdown of menu items.

Dropdowns should contain the following keys:

* `title`: The text that appears on the dropdown item
* `submenu`: This is an array of menu items.

Menu items should contain the following keys:

* `title`: The text that appears on the menu item
* `type`: The dashboard type (this can be `view` for a viewable table or `edit` for an editable list)
* `model`: The lowercase name of the database model

### Adding a new model to the dashboard

Create a model that extends the `DashboardModel` class and override variables that don't fit the defaults.

#### DashboardModel variables

* `$dashboard_type`: The dashboard type (this can be `view` for a viewable table or `edit` for an editable list)
* `$dashboard_heading`: This sets the heading that appears on the dashboard page; not setting this will use the model name
* `$export`: This enables a button that allows the table to be exported as a spreadsheet

##### Edit variables

These are variables that only function when the `$dashboard_type` variable is set to `edit`.

* `$create`: A boolean determining whether to enable a button that allows new records to be created
* `$delete`: A boolean determining whether to enable a button that allows records to be deleted
* `$filter`: A boolean determining whether to enable an input field that allows records to be searched
* `$dashboard_help_text`: An html string that will add a help box to the top of the edit-item page
* `$dashboard_display`: An array to configure what column data to show on each item in the edit-list
* `$dashboard_reorder`: A boolean determining whether to render drag handles to reorder the items in the list
* `$dashboard_sort_column`: A string containing the column used to sort the list (this should be an `integer` when `$dashboard_reorder` is true)
* `$dashboard_sort_direction`: When `$dashboard_reorder` is false this determines the sort direction (this can be `desc` for descending or `asc` ascending)
* `$dashboard_button`: An array containing the following items in this order:
    * The title
    * Confirmation text asking the user to confirm
    * A "success" message to display when the response is `success`
    * A "failure" message to display when the response is not `success`
    * The URL to send the POST request to with the respective `id` in the request variable

##### Configuring the columns

All `DashboardModel` models require a `$dashboard_columns` array that declares which columns to show and how to treat them.

All models use the following attributes:

* `name`: The name of the model
* `title`: (optional) The title that should be associated with the model; when unset this becomes the model name with its first letter capitalized

Models with their `$dashboard_type` set to `edit` also use:

* `type`: The column type which can be any of the following:
    * `text`: Text input field for text data
    * `mkd`: Markdown editor for text data containing markdown
    * `date`: Date and time selection tool for date/time data
    * `select`: Text input via option select with possible options in an `options` array
    * `hidden`: Fields that will contain values to pass to the update function but won't appear on the page (this must be used for the sort column)
    * `image`: Fields that contain image uploads
    * `file`: Fields that contains file uploads
    * `display`: Displayed information that can't be edited
    * `user`: This should point to a foreign key that references the id on the users table; setting this will bind items to the user that created them
* `name`: (required by `file` and `image`) Used along with the record id to determine the filename
* `delete`: (optional for `file` and `image`) Enables a delete button for the upload when set to true
* `ext`: (required by `file`) Configures the file extension of the upload

An example of the `$dashboard_columns` array in a model with its `$dashboard_type` set to `view`:

```php
    public static $dashboard_columns = [
        [ 'title' => 'Date', 'name' => 'created_at' ],
        [ 'name' => 'email' ],
        [ 'name' => 'name' ]
    ];
```

An example of the `$dashboard_columns` array in a model with its `$dashboard_type` set to `edit`:

```php
    public static $dashboard_columns = [
        [ 'name' => 'user_id', 'type' => 'user' ],
        [ 'name' => 'created_at', 'title' => 'Date', 'type' => 'display' ],
        [ 'name' => 'title',  'type' => 'text' ],
        [ 'name' => 'body',  'type' => 'mkd' ],
        [ 'name' => 'tags', 'type' => 'text' ],
        [ 'name' => 'header-image', 'title' => 'Header Image', 'type' => 'image', 'delete' => true ]
    ];
```
