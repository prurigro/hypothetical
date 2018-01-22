# Hypothetical Template

The Hypothetical website template

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

## Dashboard

Unless otherwise stated all examples in this section are to be added to `app/Http/Controllers/DashboardController.php`.

### Adding a Viewable Model to the Dashboard

#### Viewable List of Rows

First add a function to generate the page:

```php
    public function getContact()
    {
        return view('dashboard.view', [
            'heading' => 'Contact Form Submissions',
            'model'   => 'contact',
            'rows'    => Contact::getContactSubmissions(),
            'columns' => Contact::$dashboard_columns
        ]);
    }
```

* `heading`: The title that will appear for this page
* `model`: The model that will be accessed on this page
* `rows`: A function returning an array containing the data to be shown on this page
* `columns`: Expects a variable called `$dashboard_columns` in the respective model that contains an array:

```php
    public static $dashboard_columns = [
        [ 'Date', 'created_at' ],
        [ 'Name', 'name' ],
        [ 'Email', 'email' ],
        [ 'Message', 'message' ]
    ];
```

### Adding an Editable Model to the Dashboard

#### Editable List of Rows

##### Editable List for Unsortable Model

```php
    public function getShows()
    {
        return view('dashboard.edit-list', [
            'heading' => 'Shows',
            'model'   => 'shows',
            'path'    => 'shows-page',
            'rows'    => Shows::getShowsList(),
            'column'  => 'title',
            'button'  => [ 'Email Show', 'Are you sure you want to send an email?', 'Email successfully sent', 'Failed to send email', '/email-show' ],
            'sortcol' => false,
            'delete'  => true,
            'create'  => true,
            'export'  => true,
            'filter'  => true
        ]);
    }
```

##### Editable List for Sortable Model

**NOTE**: Sortable models must have an entry configured in the `postReorder` function (details below)

```php
    public function getNews()
    {
        return view('dashboard.edit-list', [
            'heading' => 'News',
            'model'   => 'news',
            'rows'    => News::getNewsList(),
            'column'  => 'title',
            'button'  => [ 'Email Show', 'Are you sure you want to send an email?', 'Email successfully sent', 'Failed to send email', '/email-show' ],
            'sortcol' => 'order',
            'delete'  => false,
            'create'  => true,
            'export'  => true,
            'filter'  => true
        ]);
    }
```

* `heading`: The title that will appear for this page
* `model`: The model that will be accessed on this page
* `path`: (optional) This can be used to set a different URL path than the default of the model name
* `rows`: A function returning an array containing the data to be shown on this page
* `column`: The column name in the array that contains the data to display in each row (an array can be used to specify multiple columns)
* `button`: Add a button with a title, confirmation, success and error messages, and a post request path that takes an id and returns `success` on success
* `sortcol`: The name of the column containing the sort order or `false` to disable
* `delete`: A `delete` button will appear in the list if this is set to `true`
* `create`: A `new` button will appear in the heading if this is set to `true`
* `export`: An `export` button will appear in the heading if this is set to `true`
* `filter`: An input box will appear below the heading that can filter rows by input if this is set to `true`

#### Editable Item

This function should be named the same as the one above except with `Edit` at the end

##### Editable Item for Unsortable Model

```php
    public function getShowsEdit($id = 'new')
    {
        if ($id != 'new') {
            if (Shows::where('id', $id)->exists()) {
                $item = Shows::where('id', $id)->first();
            } else {
                return view('errors.no-such-record');
            }
        } else {
            $item = null;
        }

        return view('dashboard.edit-item', [
            'heading' => 'Shows',
            'model'   => 'shows',
            'id'      => $id,
            'item'    => $item,
            'help_text' => '<strong>NOTE:</strong> This is some help text for the current page',
            'columns' => $dashboard_columns
        ]);
    }
```

##### Editable Item for Sortable Model

```php
    public function getNewsEdit($id = 'new')
    {
        if ($id != 'new') {
            if (News::where('id', $id)->exists()) {
                $item = News::where('id', $id)->first();
            } else {
                return view('errors.no-such-record');
            }
        } else {
            $item = new News();
            $item['order'] = News::count();
        }

        return view('dashboard.edit-item', [
            'heading' => 'News',
            'model'   => 'news',
            'id'      => $id,
            'item'    => $item,
            'columns' => News::$dashboard_columns
        ]);
    }
```

* `heading`: The title that will appear for this page
* `model`: The model that will be accessed on this page
* `id`: Always set this to `$id`
* `item`: Always set this to `$item`
* `help_text`: An optional value that will add a box containing help text above the form if set
* `columns`: Expects a variable called `$dashboard_columns` in the respective model that contains an array:
  * `name` is the name of the column to be edited
  * `type` is the type of column (details below)
  * `label` is an optional value that overrides the visible column name

```php
    public static $dashboard_columns = [
        [ 'name' => 'title',  'type' => 'text', 'label' => 'The Title' ],
        [ 'name' => 'iframe', 'type' => 'text' ],
        [ 'name' => 'halign', 'type' => 'select', 'options' => [ 'left', 'center', 'right' ] ],
        [ 'name' => 'story',  'type' => 'mkd' ],
        [ 'label' => 'Header Image', 'name' => 'headerimage', 'type' => 'image' ],
        [ 'name' => 'order',  'type' => 'hidden' ],
        [ 'label' => 'PDF File', 'name' => 'pdf', 'type' => 'file', 'ext' => 'pdf' ]
    ];
```

###### Editable Column Types

The following is a list of possible `types` in the `columns` array for Editable Items:

* `text`: Text input field for text data
* `mkd`: Markdown editor for text data containing markdown
* `date`: Date and time selection tool for date/time data
* `select`: Text input via option select with possible options in an `options` array
* `hidden`: Fields that will contain values to pass to the update function but won't appear on the page (this must be used for the sort column)
* `image`: Fields that contain image uploads
  * `name`: not part of the database and is instead used in the filename
* `file`: Fields that contains file uploads
  * `name`: not part of the database and is instead used in the filename
  * `ext` required key containing the file extension
* `display`: Displayed information that can't be edited

#### Edit Item Functionality

Editable models must have an entry in the switch statement of the `postEdit` function to make create and edit functionality work:

```php
    switch ($request['model']) {
        case 'shows':
            $item = $id == 'new' ? new Shows : Shows::find($id);
            break;
        case 'news':
            $item = $id == 'new' ? new News : News::find($id);
            break;
        default:
            return 'model-access-fail';
    }
```

#### Additional Requirement for Sortable Models

Sortable models must have an entry in the switch statement of the `postReorder` function to make sorting functionality work:

```php
    switch ($request['model']) {
        case 'news':
            $items = new News();
            break;
        default:
            return 'model-access-fail';
    }
```

#### Additional Requirements for Image Upload

If the value of `imgup` has been set to `true`, ensure `public/uploads/model_name` exists (where `model_name` is the name of the given model) and contains a `.gitkeep` that exists in version control.

By default, uploaded images are saved in JPEG format with the value of the `id` column of the respective row as its name and `.jpg` as its file extension.

When a row is deleted, its respective image will be deleted as well if it exists.

### Adding to the Dashboard Menu

Edit the `$menu` array in `app/Models/DashboardMenu.php` where the first column of each item is the title and the second is either a path, or an array of submenu items.

```php
    public static $menu = [
        [ 'Contact', 'contact' ],
        [ 'Subscriptions', 'subscriptions' ],

        [
            'Projects', [
                [ 'Residential', 'projects-residential' ],
                [ 'Commercial', 'projects-commercial' ]
            ]
        ]
    ];
```

#### Additional Requirement for Delete Functionality

Editable models with `delete` set to `true` must have an entry in the switch statement of the `deleteDelete` function to make deletion functionality work:

```php
    switch ($request['model']) {
        case 'shows':
            $items = new Shows();
            break;
        case 'news':
            $items = new News();
            break;
        default:
            return 'model-access-fail';
    }
```

#### Additional Requirement for Export Functionality

Viewable models and editable models with `export` set to `true` must have an entry in the switch statement of the `getExport` function to make the export button work:

```php
    switch ($model) {
        case 'contact':
            $headings = [ 'Date', 'Name', 'Email', 'Message' ];
            $items = Contact::select('created_at', 'name', 'email', 'message')->get();
            break;
        default:
            abort(404);
    }
```

* `$headings`: The visible column names in the same order as the array containing the items to be exported
* `$items`: A function returning an array containing the data to be exported
