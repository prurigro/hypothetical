# Hypothetical Template

A Hypothetical website template for bootstrapping new projects.

* Written and maintained by Kevin MacMartin

## Features

* A choice between an SPA on top of a PHP backend or a pure SSR site (See the [Public](#public) section below for more information)
* A flexible dashboard for managing data/assets and displaying collected form data
* Great defaults and popular libraries to build on top of
* A cookie-based language toggle feature that allows multiple languages to be placed inline
* A custom Sass function allowing values to be specified in `px` and output in `rem`
* A function available in the SPA version of the site that loads webp if supported by the browser and jpg if it isn't

## Major Components

* Bootstrap 5
* Browsersync
* Fontawesome
* Gsap
* Gulp
* Jquery
* Laravel 12.11.1
* Sass
* Vue 3 (Optional)

## Setup

The following steps can be followed to get things running for the first time:

1. Copy the `.env.example` file to `.env` and configure its values as required.
2. Create a new database named whatever you've set `DB_DATABASE` to in the `.env` file.
3. Run the `init.sh` script and wait for it to complete.

### Environment File

The `.env` file includes deployment-specific configuration options, and Laravel has documentation explaining it in further detail [HERE](https://laravel.com/docs/configuration).

The `APP_ENV` and `APP_DEBUG` variables should be configured in one of the following combinations depending on the scenario:

* Local development should be configured with `APP_ENV=local` and `APP_DEBUG=true`.
* A remote staging server should be configured with `APP_ENV=staging` and `APP_DEBUG=true`.
* A remote production server should be configured with `APP_ENV=production` and `APP_DEBUG=false`.

### Init Script

The `init.sh` script is located in the root of the project and is used to keep the database and compiled assets in sync with the codebase.

It's the recommended way to handle the initial project setup, and can also be run manually or by deployment scripts to keep things up to date after pulling in changes.

The following steps are performed in this order when run:

1. Checks the local system for dependencies required by the script and exits with an error if any are missing.
2. Checks to see if the `.env` file exists and exits with an error if it doesn't.
3. (artisan) Puts the website in maintenance mode.
4. Downloads and updates non-development composer dependencies.
5. Checks to see if the `APP_KEY` variable in the `.env` file is empty, and if it is, generates a value for it.
6. Clears the route and blade cache to ensure everything will be build fresh against the current codebase and dependencies.
7. (artisan) Run new database migrations.
8. Cleans, downloads and updates npm dependencies.
9. Runs `gulp --production` to build project files and copy fonts to `public/fonts` (uses the local version of gulp installed in `node_modules`).
10. (artisan) Takes the website out of maintenance mode.

**NOTE**: Items with `(artisan)` prepended to them won't be run if `init.sh` is run with the `--no-artisan` flag.

## Utilities

### Gulp

In the root of the project is a file named `gulpfile.js` that can be used by `gulp` to copy fonts, compile javascript and sass, and watch files for changes during development.

Reading through its contents is encouraged for a complete understanding of what it does, but the following commands should handle most of what it's needed for out of the box:

* `gulp`: Update the compiled javascript and css in `public/js` and `public/css`, and copy fonts to `public/fonts`.
* `gulp --production`: Does the same as `gulp` except the compiled javascript and css is minified, and console logging is removed from the javascript (good for production deployments).
* `gulp default watch`: Does the same as `gulp` but continues running to watch for changes to files so it can recompile updated assets and reload them in the browser using Browsersync (good for development environments).

**NOTE**: If `gulp` isn't installed globally or its version is less than `4`, you should use the version included in `node_modules` by running `"$(npm bin)/gulp"` in place of the `gulp` command.

### Browsersync

Browsersync is used to keep the browser in sync with your code when running the `watch` task with gulp.

## Public

The default public facing website is an SPA using Vue.js. To configure a non-SPA traditional SSR website remove the following files before moving the contents of `traditional-bootstrap` into the root project:

* `package-lock.json`
* `resources/components`
* `resources/js/mixins`
* `resources/js/imports`

The following list of files and directories are where various pieces of the public website are located:

* `resources/views/templates/base.blade.php`: The outer template for the entire website
* `resources/views/templates/public.blade.php`: The inner template for the public site
* `resources/fonts`: The folder containing website fonts (these get loaded into `public/fonts/` by the gulpfile)
* `resources/js/app.js`: The main javascript file that loads the public site
* `resources/js/mixins`: The folder containing Vue.js mixins that can be applied globally in `resources/js/app.js` or in individual components
* `resources/js/mixins/base-page.js`: The base-page mixin with page functionality that should be imported into all page components
* `resources/components`: The folder containing Vue.js components
    * `resources/components/pages`: Page components that should be imported into vue-router in `resources/js/app.js`
    * `resources/components/sections`: Section components (single-use per page) that should be imported into mixins or page components
    * `resources/components/partials`: Partial components (multi-use per page or section) that should be imported into mixins and/or page and section components
* `resources/sass/app.scss`: The main sass file for the public site
* `resources/sass/_fonts.scss`: Stylesheet containing font declarations and mixins declared to use those fonts in other stylesheets
* `resources/sass/_var.scss`: Stylesheet containing variables to be used in other stylesheets
* `resources/sass/pages`: Stylesheets for page-specific styles wrapped in the respective page component class
* `resources/sass/sections`: Stylesheets for section-specific styles wrapped in the respective section component class
* `resources/sass/partials`: Stylessheets for partial-specific styles wrapped in the respective partial component class
* `resources/sass/classes`: General stylesheets for classes that can be used anywhere
* `resources/sass/mixins`: Stylesheets declaring SCSS mixins for use in other stylesheets
* `public/favicon.ico` and `public/favicon.png`: Placeholders for the favicon files
* `public/img/logo.png`: Placeholder for the website logo
* `public/img/social-image-card.jpg` and `public/img/social-image-opengraph.jpg`: Placeholders for images that will show up in social shares

Dependencies can be included with npm and loaded either into the `jsPublicLibs` array in the gulpfile or imported in the javascript.

Other information about database interaction, routing, controllers, etc can be viewed in the [Laravel Documentation](https://laravel.com/docs).

### Language

The default language is set by the `APP_LOCALE` variable in the `.env` file; this will be the language used until the cookie has been updated.

The language cookie can be updated a number of ways:

* Visiting a link to `/language/{lang}` will update the language to whatever `{lang}` is set to and then reload the current page.
* Running `Language::setSessionLanguage($lang)` in PHP will update the language to whatever `$lang` is.
* Running `this.$store.commit("setAppLang", lang);` in a Vue.js component will update the language to whatever `lang` is as well as update component text to the current language on-the-fly.

A multi-language text block can be included in a number of ways depending where it's being done:

In PHP or a Laravel blade:

```php
    {{ Language::select([ 'en' => 'This is a sentence', 'fr' => 'C’est une phrase' ]) }}
```

In a Laravel blade:

```php
    @lang([
        'en' => 'This is a sentence',
        'fr' => 'C’est une phrase'
    ])
```

In a Vue.js component:

```html
    <lang :c-strings="{ en: 'This is a sentence', fr: 'C’est une phrase' }" />
```

## Dashboard

### Important Note

The naming convention of dashboard database tables and model classes should be the following:

* Database table names should be lower case with hyphen separators: `your_table_name`
* Model classes should be the same name but in camel case with its first character capitalized: `YourTableName.php` and `class YourTableName extends DashboardModel`

### Registration

The `REGISTRATION` variable in the `.env` file controls whether a new dashboard user can be registered.

The system admin can control registration by configuring the `REGISTRATION` variable in the following ways:

* `REGISTRATION=false`: Registration is disabled
* `REGISTRATION=true`: Registration is enabled for everyone
* `REGISTRATION=192.168.1.123`: Registration is selectively enabled for the IP address `192.168.1.123`


### Updating the dashboard menu

The dashboard menu can be edited by changing the `$menu` array in `app/Dashboard.php`.

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

* `$dashboard_type`: The dashboard type:
    * `view`: Display a viewable table showing the data
    * `edit`: Provides a list of rows and the option to edit their contents
    * `list`: Allows another model to use this one to create an editable list of one or more items
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
* `$dashboard_sort_column`: A string containing the column used to sort the list (this column should be an `integer` when `$dashboard_reorder` is true)
* `$dashboard_sort_direction`: When `$dashboard_reorder` is false this determines the sort direction (this can be `desc` for descending or `asc` ascending)
* `$dashboard_button`: Add a dashboard button with custom functionality by populating an array containing the following items in this order:
    * The title
    * Confirmation text asking the user to confirm
    * A "success" message to display when the response is `success`
    * A "failure" message to display when the response is not `success`
    * The URL to send the POST request to with the respective `id` in the request variable
* `$dashboard_id_link`: Add a dashboard button linking to another list filtered by the current item by populating an array containing the following items in this order:
    * The title
    * The URL to link to where the id will come after the rest

##### Configuring the columns

All `DashboardModel` models require a `$dashboard_columns` array that declares which columns to show and how to treat them.

All models use the following attributes:

* `name`: The name of the model
* `title`: (optional) The title that should be associated with the model; when unset this becomes the model name with its first letter capitalized

Models with their `$dashboard_type` set to `edit` also use:

* `type`: The column type which can be any of the following:
    * `hidden`: Fields that will contain values to pass to the update function but won't appear on the page (this must be used for the sort column)
    * `user`: This should point to a foreign key that references the id on the users table; setting this will bind items to the user that created them
    * `string`: Single-line text input field
    * `text`: Multi-line text input field
    * `currency`: Text input field for currency data
    * `date`: Date selection tool for date/time data
    * `date-time`: Date and time selection tool for date/time data
    * `mkd`: Multi-line text input field with a markdown editor
    * `select`: Text input via option select
    * `list`: One or more `text` or `image` items saved to a connected table
    * `image`: Fields that contain image uploads
    * `file`: Fields that contains file uploads
    * `display`: Displayed information that can't be edited
* `required`: If set an error will be displayed if the field has no value
* `unique`: If set an error will be displayed if another row in the table has the same value for a given column
* `type-new`: This takes the same options as `type` and overrides it when creating new items (eg: to allow input on a field during creation but not after)
* `options` (required by `select`) Takes an array of options that are either strings or arrays containing the keys `title` (for what will display with the option) and `value` (for what will be recorded)
* `name`: (required by `file` and `image`) Used along with the record id to determine the filename
* `delete`: (optional for `file` and `image`) Enables a delete button for the upload when set to true
* `ext`: (required by `file` and optional for `image`) Configures the file extension of the upload (`image` defaults to `jpg`)
* `model`: (required by `list`) The class name of the model that the list will be generated from
* `foreign` (required by `list`) The name of the list table's foreign id column that references the id on the current table
* `sort` (required by `list`) The name of the list table's column that the order will be stored in
* `max_width`: (optional for `image`) Configures the maximum width of an image upload (defaults to `0` which sets no maximum width)
* `max_height`: (optional for `image`) Configures the maximum height of an image upload (defaults to `0` which sets no maximum height)

Models with their `$dashboard_type` set to `list` also use:

* `type`: The column type which can be any of the following:
    * `string`: Single-line text input field
    * `image`: Fields that contain image uploads
* `ext`: (optional for `image`) Configures the file extension of the upload (`image` defaults to `jpg`)

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
        [ 'name' => 'title', 'required' => true, 'unique' => true, 'type' => 'string' ],
        [ 'name' => 'body', 'required' => true,  'type' => 'mkd' ],
        [ 'name' => 'header-image', 'title' => 'Header Image', 'type' => 'image', 'delete' => true, 'ext' => 'jpg' ],
        [ 'name' => 'tags', 'type' => 'list', 'model' => 'BlogTags', 'foreign' => 'blog_id', 'sort' => 'order' ]
    ];
```

An example of the `$dashboard_columns` array in a model with its `$dashboard_type` set to `list`:

```php
    public static $dashboard_columns = [
        [ 'type' => 'string', 'name' => 'name' ],
        [ 'type' => 'image', 'name' => 'photo' ]
    ];
```
