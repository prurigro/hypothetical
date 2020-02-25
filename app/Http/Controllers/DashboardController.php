<?php namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Auth;
use File;
use Image;
use App\User;
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

    // View Model Data
    public function getView($model)
    {
        $model_class = Dashboard::getModel($model, 'view');

        if ($model_class != null) {
            return view('dashboard.pages.view', [
                'heading' => $model_class::getDashboardHeading($model),
                'column_headings' => $model_class::getDashboardColumnData('headings'),
                'model' => $model,
                'rows' => $model_class::getDashboardData(),
                'columns' => $model_class::$dashboard_columns
            ]);
        } else {
            abort(404);
        }
    }

    // Edit List of Model Rows
    public function getEditList($model)
    {
        $model_class = Dashboard::getModel($model, 'edit');

        if ($model_class != null) {
            return view('dashboard.pages.edit-list', [
                'heading' => $model_class::getDashboardHeading($model),
                'model'   => $model,
                'rows'    => $model_class::getDashboardData(),
                'display' => $model_class::$dashboard_display,
                'button'  => $model_class::$dashboard_button,
                'sortcol' => $model_class::$dashboard_reorder ? $model_class::$dashboard_sort_column : false,
                'create'  => $model_class::$create,
                'delete'  => $model_class::$delete,
                'filter'  => $model_class::$filter,
                'export'  => $model_class::$export
            ]);
        } else {
            abort(404);
        }
    }

    // Create and Edit Model Item
    public function getEditItem($model, $id = 'new')
    {
        $model_class = Dashboard::getModel($model, 'edit');

        if ($model_class != null) {
            if ($id == 'new') {
                $item = null;
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

            return view('dashboard.pages.edit-item', [
                'heading'   => $model_class::getDashboardHeading($model),
                'model'     => $model,
                'id'        => $id,
                'item'      => $item,
                'help_text' => $model_class::$dashboard_help_text,
                'columns'   => $model_class::$dashboard_columns
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

            // populate the eloquent object with the remaining items in $request
            foreach ($request['columns'] as $column) {
                $item->$column = $request[$column];
            }

            // save the new or updated item
            $item->save();

            // return the id number in the format '^id:[0-9][0-9]*$' on success
            return 'id:' . $item->id;
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

        $model_class = Dashboard::getModel($request['model'], 'edit');

        if ($model_class != null) {
            $item = $model_class::find($request['id']);

            if (is_null($item)) {
                return 'record-access-fail';
            } else if (!$item->userCheck()) {
                return 'permission-fail';
            } else if ($request->hasFile('file')) {
                $directory = base_path() . '/public/uploads/' . $request['model'] . '/img/';
                File::makeDirectory($directory, 0755, true, true);
                $image = Image::make($request->file('file'));
                $image->save($directory . $request['id'] . '-' . $request['name'] . '.jpg');
                $item->touch();
                return 'success';
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
            'name'  => 'required',
            'ext'   => 'required'
        ]);

        $model_class = Dashboard::getModel($request['model'], 'edit');

        if ($model_class != null) {
            $item = $model_class::find($request['id']);

            if (is_null($item)) {
                return 'record-access-fail';
            } else if (!$item->userCheck()) {
                return 'permission-fail';
            } else if ($request->hasFile('file')) {
                $directory = base_path() . '/public/uploads/' . $request['model'] . '/files/';
                File::makeDirectory($directory, 0755, true, true);
                $request->file('file')->move($directory, $request['id'] . '-' . $request['name'] . '.' . $request['ext']);
                $item->touch();
                return 'success';
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

            // delete the row
            $item->delete();

            // delete associated files if they exist
            foreach ($model_class::$dashboard_columns as $column) {
                if ($column['type'] == 'image') {
                    $image = base_path() . '/public/uploads/' . $request['model'] . '/img/' . $request['id'] . '-' . $column['name'] . '.jpg';

                    if (file_exists($image) && !unlink($image)) {
                        return 'image-delete-fail';
                    }
                } else if ($column['type'] == 'file') {
                    $file = base_path() . '/public/uploads/' . $request['model'] . '/files/' . $request['id'] . '-' . $column['name'] . '.' . $column['ext'];

                    if (file_exists($file) && !unlink($file)) {
                        return 'file-delete-fail';
                    }
                }
            }

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

        $model_class = Dashboard::getModel($request['model'], 'edit');

        if ($model_class != null) {
            $image = base_path() . '/public/uploads/' . $request['model'] . '/img/' . $request['id'] . '-' . $request['name'] . '.jpg';
            $item = $model_class::find($request['id']);

            if (is_null($item)) {
                return 'record-access-fail';
            } else if (!$item->userCheck()) {
                return 'permission-fail';
            } else if (!file_exists($image)) {
                return 'image-not-exists-fail';
            } else if (!unlink($image)) {
                return 'image-delete-fail';
            }

            return 'success';
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
            'name'  => 'required',
            'ext'   => 'required'
        ]);

        $model_class = Dashboard::getModel($request['model'], 'edit');

        if ($model_class != null) {
            $file = base_path() . '/public/uploads/' . $request['model'] . '/files/' . $request['id'] . '-' . $request['name'] . '.' . $request['ext'];
            $item = $model_class::find($request['id']);

            if (is_null($item)) {
                return 'record-access-fail';
            } else if (!$item->userCheck()) {
                return 'permission-fail';
            } else if (!file_exists($file)) {
                return 'file-not-exists-fail';
            } else if (!unlink($file)) {
                return 'file-delete-fail';
            }

            return 'success';
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
