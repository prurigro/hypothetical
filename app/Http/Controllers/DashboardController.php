<?php namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Auth;
use File;
use Image;
use App\Models\User;
use App\Dashboard;

class DashboardController extends Controller {

    /**
     * Create a new controller instance
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard CMS
     */
    public function getIndex()
    {
        return view('dashboard.pages.home');
    }

    // Page to View Model Data
    public function getView($model)
    {
        $model_class = Dashboard::getModel($model, 'view');

        if ($model_class != null) {
            return view('dashboard.pages.view', [
                'heading' => $model_class->getDashboardHeading(),
                'column_headings' => $model_class::getDashboardColumnData('headings'),
                'model' => $model,
                'rows' => $model_class::getDashboardData(),
                'columns' => $model_class::$dashboard_columns
            ]);
        } else {
            abort(404);
        }
    }

    // Page to Edit List of Model Rows
    public function getEditList($model)
    {
        $model_class = Dashboard::getModel($model, 'edit');

        if ($model_class != null) {
            $data = $model_class::getDashboardData(true);

            return view('dashboard.pages.edit-list', [
                'heading'      => $model_class->getDashboardHeading(),
                'model'        => $model,
                'rows'         => $data['rows'],
                'paramdisplay' => $data['paramdisplay'],
                'query'        => $model_class::getQueryString(),
                'display'      => $model_class::$dashboard_display,
                'button'       => $model_class::$dashboard_button,
                'idlink'       => $model_class::$dashboard_id_link,
                'sortcol'      => $model_class::$dashboard_reorder ? $model_class::$dashboard_sort_column : false,
                'paginate'     => $model_class::$items_per_page !== 0,
                'create'       => $model_class::$create,
                'delete'       => $model_class::$delete,
                'filter'       => $model_class::$filter,
                'export'       => $model_class::$export
            ]);
        } else {
            abort(404);
        }
    }

    // Page to Create and Edit Model Item
    public function getEditItem($model, $id = 'new')
    {
        $model_class = Dashboard::getModel($model, 'edit');

        if ($model_class != null) {
            if ($id == 'new') {
                $item = new $model_class;
            } else {
                if ($model_class::where('id', $id)->exists()) {
                    $item = $model_class::find($id);

                    if (is_null($item) || !$item->userCheck()) {
                        return view('errors.no-such-record');
                    }
                } else {
                    return view('errors.no-such-record');
                }
            }

            foreach ($model_class::$dashboard_columns as $column) {
                if ($column['type'] === 'list') {
                    $list_model_class = 'App\\Models\\' . $column['model'];
                    $list_model_instance = new $list_model_class;

                    $item->{$column['name']} = [
                        'model' => $list_model_instance->getTable(),
                        'list' => $id == 'new' ? [] : $list_model_instance::where($column['foreign'], $item->id)->orderBy($column['sort'])->get()
                    ];
                }
            }

            return view('dashboard.pages.edit-item', [
                'heading'         => $model_class->getDashboardHeading(),
                'default_img_ext' => $model_class::$default_image_ext,
                'model'           => $model,
                'id'              => $id,
                'item'            => $item,
                'help_text'       => $model_class::$dashboard_help_text,
                'columns'         => $model_class::$dashboard_columns
            ]);
        } else {
            abort(404);
        }
    }

    // Export Spreadsheet of Model Data
    public function getExport($model)
    {
        $model_class = Dashboard::getModel($model);

        if ($model_class != null && $model_class::$export) {
            $filename = preg_replace([ '/\ /', '/[^a-z0-9\-]/' ], [ '-', '' ], strtolower(env('APP_NAME'))) . '-' . $model . '-' . date('m-d-Y') . '.xlsx';
            $headings = $model_class::getDashboardColumnData('headings', false);
            $items = $model_class::select($model_class::getDashboardColumnData('names', false))->get()->toArray();
            array_unshift($items, $headings);
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(25);
            $spreadsheet->getActiveSheet()->fromArray($items, NULL, 'A1');
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
        } else {
            abort(404);
        }
    }

    // Reorder Model Rows
    public function postReorder(Request $request)
    {
        $this->validate($request, [
            'order'  => 'required',
            'column' => 'required',
            'model'  => 'required'
        ]);

        $model_class = Dashboard::getModel($request['model'], 'edit');

        if ($model_class != null) {
            $order = $request['order'];
            $column = $request['column'];

            // update each row with the new order
            foreach (array_keys($order) as $order_id) {
                $item = $model_class::find($order_id);
                $item->$column = $order[$order_id];
                $item->save();
            }

            return 'success';
        } else {
            return 'model-access-fail';
        }
    }

    // Create and Update Model Item Data
    public function postUpdate(Request $request)
    {
        $this->validate($request, [
            'id'      => 'required',
            'model'   => 'required',
            'columns' => 'required'
        ]);

        $model_class = Dashboard::getModel($request['model'], 'edit');

        if ($model_class != null) {
            if ($request['id'] == 'new') {
                $item = new $model_class;

                if ($model_class::$dashboard_reorder) {
                    $item->{$model_class::$dashboard_sort_column} = $model_class::count();
                }
            } else {
                $item = $model_class::find($request['id']);

                if (is_null($item)) {
                    return 'record-access-fail';
                } else if (!$item->userCheck()) {
                    return 'permission-fail';
                }
            }

            // check to ensure required columns have values
            $empty = [];

            foreach ($model_class::$dashboard_columns as $column) {
                if ($request->has($column['name']) && array_key_exists('required', $column) && $column['required'] && ($request[$column['name']] == '' || $request[$column['name']] == null)) {
                    if (array_key_exists('title', $column)) {
                        array_push($empty, "'" . $column['title'] . "'");
                    } else {
                        array_push($empty, "'" . ucfirst($column['name']) . "'");
                    }
                }
            }

            if (count($empty) > 0) {
                return 'required:' . implode(',', $empty);
            }

            // check to ensure unique columns are unique
            $not_unique = [];

            foreach ($model_class::$dashboard_columns as $column) {
                if ($request->has($column['name']) && array_key_exists('unique', $column) && $column['unique'] && $model_class::where($column['name'], $request[$column['name']])->where('id', '!=', $item->id)->exists()) {
                    if (array_key_exists('title', $column)) {
                        array_push($not_unique, "'" . $column['title'] . "'");
                    } else {
                        array_push($not_unique, "'" . ucfirst($column['name']) . "'");
                    }
                }
            }

            if (count($not_unique) > 0) {
                return 'not-unique:' . implode(',', $not_unique);
            }

            // populate the eloquent object with the non-list items in $request
            foreach ($request['columns'] as $column) {
                if ($column['type'] !== 'list') {
                    $column_name = $column['name'];
                    $item->$column_name = $request[$column_name];
                }
            }

            // save the item if it's new so we can access its id
            if ($request['id'] == 'new') {
                $item->save();
            }

            // populate connected lists with list items in $request
            $lists = [];

            foreach ($request['columns'] as $column) {
                if ($column['type'] === 'list') {
                    $column_name = $column['name'];

                    foreach ($model_class::$dashboard_columns as $dashboard_column) {
                        if ($dashboard_column['name'] === $column_name) {
                            $foreign = $dashboard_column['foreign'];
                            $list_model_class = 'App\\Models\\' . $dashboard_column['model'];

                            if ($list_model_class::$dashboard_type == 'list') {
                                $ids = [];

                                if ($request->has($column_name)) {
                                    foreach ($request[$column_name] as $index => $row) {
                                        if ($row['id'] == 'new') {
                                            $list_model_item = new $list_model_class;
                                        } else {
                                            $list_model_item = $list_model_class::find($row['id']);
                                        }

                                        $list_model_item->$foreign = $item->id;
                                        $list_model_item->{$dashboard_column['sort']} = $index;

                                        foreach ($row['data'] as $key => $data) {
                                            if ($data['type'] == 'string') {
                                                $list_model_item->$key = $data['value'];
                                            }
                                        }

                                        $list_model_item->save();
                                        array_push($ids, $list_model_item->id);
                                    }
                                }

                                // delete any associated row that wasn't just created or edited
                                foreach ($list_model_class::where($foreign, $item->id)->whereNotIn('id', $ids)->get() as $list_item) {
                                    $list_item->delete();
                                }

                                // store the sets of ids for each list
                                $lists[$column_name] = $ids;

                                // stop looping through dashboard columns
                                break;
                            } else {
                                return 'invalid-list-model:' . $dashboard_column['model'];
                            }
                        }
                    }
                }
            }

            // update the item
            $item->save();

            // return the id number in the format '^id:[0-9][0-9]*$' on success
            return [
                'id' => $item->id,
                'lists' => $lists
            ];
        } else {
            return 'model-access-fail';
        }
    }

    // Upload Model Item Image
    public function postImageUpload(Request $request)
    {
        $this->validate($request, [
            'id'    => 'required',
            'model' => 'required',
            'name'  => 'required'
        ]);

        $model_class = Dashboard::getModel($request['model'], [ 'edit', 'list' ]);

        if ($model_class != null) {
            $item = $model_class::find($request['id']);

            if (is_null($item)) {
                return 'record-access-fail';
            } else if ($request->hasFile('file')) {
                $save_result = $item->saveImage($request['name'], $request->file('file'));

                if ($save_result == 'success') {
                    $item->touch();
                }

                return $save_result;
            } else {
                return 'file-upload-fail';
            }
        } else {
            return 'model-access-fail';
        }
    }

    // Upload Model Item File
    public function postFileUpload(Request $request)
    {
        $this->validate($request, [
            'id'    => 'required',
            'model' => 'required',
            'name'  => 'required'
        ]);

        $model_class = Dashboard::getModel($request['model'], [ 'edit', 'list' ]);

        if ($model_class != null) {
            $item = $model_class::find($request['id']);

            if (is_null($item)) {
                return 'record-access-fail';
            } else if ($request->hasFile('file')) {
                $save_result = $item->saveFile($request['name'], $request->file('file'));

                if ($save_result == 'success') {
                    $item->touch();
                }

                return $save_result;
            } else {
                return 'file-upload-fail';
            }
        } else {
            return 'model-access-fail';
        }
    }

    // Delete Model Item
    public function deleteDelete(Request $request)
    {
        $this->validate($request, [
            'id'    => 'required',
            'model' => 'required'
        ]);

        $model_class = Dashboard::getModel($request['model'], 'edit');

        if ($model_class != null) {
            $item = $model_class::find($request['id']);

            if (is_null($item)) {
                return 'record-access-fail';
            } else if (!$item->userCheck()) {
                return 'permission-fail';
            }

            // delete associated list items
            foreach ($item::$dashboard_columns as $column) {
                if ($column['type'] == 'list') {
                    $list_model_class = Dashboard::getModel($column['model'], 'list');

                    if ($list_model_class != null) {
                        foreach ($list_model_class::where($column['foreign'], $item->id)->get() as $list_item) {
                            $list_item->delete();
                        }
                    }
                }
            }

            // delete the row
            $item->delete();

            // update the order of the remaining rows if $dashboard_reorder is true
            if ($model_class::$dashboard_reorder) {
                foreach ($model_class::getDashboardData() as $index => $item) {
                    $item->{$model_class::$dashboard_sort_column} = $index;
                    $item->save();
                }
            }

            // Return a success
            return 'success';
        } else {
            return 'model-access-fail';
        }
    }

    // Delete Model Item Image
    public function deleteImageDelete(Request $request)
    {
        $this->validate($request, [
            'id'    => 'required',
            'model' => 'required',
            'name'  => 'required'
        ]);

        $model_class = Dashboard::getModel($request['model'], [ 'edit', 'list' ]);

        if ($model_class != null) {
            $item = $model_class::find($request['id']);

            if (is_null($item)) {
                return 'record-access-fail';
            }

            return $item->deleteImage($request['name'], true);
        } else {
            return 'model-access-fail';
        }
    }

    // Delete Model Item File
    public function deleteFileDelete(Request $request)
    {
        $this->validate($request, [
            'id'    => 'required',
            'model' => 'required',
            'name'  => 'required'
        ]);

        $model_class = Dashboard::getModel($request['model'], [ 'edit', 'list' ]);

        if ($model_class != null) {
            $item = $model_class::find($request['id']);

            if (is_null($item)) {
                return 'record-access-fail';
            }

            return $item->deleteFile($request['name'], true);
        } else {
            return 'model-access-fail';
        }
    }

    /**
     * Dashboard settings
     */
    public function getSettings()
    {
        return view('dashboard.pages.settings', [
            'user' => User::find(Auth::id())
        ]);
    }

    // User Password Update
    public function postUserPasswordUpdate(Request $request)
    {
        $this->validate($request, [
            'oldpass' => 'required|string|min:6',
            'newpass' => 'required|string|min:6|confirmed'
        ]);

        if (User::find(Auth::id())->updatePassword($request['oldpass'], $request['newpass'])) {
            return 'success';
        } else {
            return 'old-password-fail';
        }
    }

    // User Profile Update
    public function postUserProfileUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255'
        ]);

        $user = User::find(Auth::id());
        $user->name = $request['name'];
        $user->website = $request['website'];
        $user->facebook = $request['facebook'];
        $user->soundcloud = $request['soundcloud'];
        $user->instagram = $request['instagram'];
        $user->twitter = $request['twitter'];
        $user->save();
        return 'success';
    }

    // User Profile Image Upload
    public function postUserProfileImageUpload(Request $request)
    {
        if ($request->hasFile('file')) {
            $user = User::find(Auth::id());

            if ($user !== null) {
                $image = Image::make($request->file('file'));
                $max_width = User::$profile_image_max['width'];
                $max_height = User::$profile_image_max['height'];

                if ($image->width() > $max_width || $image->height() > $max_height) {
                    $new_width = $max_width;
                    $new_height = ($new_width / $image->width()) * $image->height();

                    if ($new_height > $max_height) {
                        $new_height = $max_height;
                        $new_width = ($new_height / $image->height()) * $image->width();
                    }

                    $image->resize($new_width, $new_height);
                }

                File::makeDirectory(base_path() . '/public' . User::$profile_image_dir, 0755, true, true);
                $image->save($user->profileImage(true, true));
                $user->touch();
                return $user->profileImage() . '?version=' . $user->timestamp();
            } else {
                return 'record-access-fail';
            }
        } else {
            return 'file-upload-fail';
        }
    }

    // User Profile Image Delete
    public function deleteUserProfileImageDelete(Request $request)
    {
        $user = User::find(Auth::id());

        if ($user !== null) {
            $profile_image = $user->profileImage(true);

            if ($profile_image === null) {
                return 'image-not-exists-fail';
            } else if (!unlink($profile_image)) {
                return 'image-delete-fail';
            }

            return 'success';
        } else {
            return 'record-access-fail';
        }
    }

    /**
     * Credits Page
     */
    public function getCredits()
    {
        return view('dashboard.pages.credits');
    }

}
