<?php namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use File;
use Image;
use Excel;

use App\Models\Contact;
use App\Models\Subscriptions;

class DashboardController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        return view('dashboard.home', [
            'heading' => 'Dashboard Home'
        ]);
    }

    /**
     * Dashboard View
     */
    public function getContact()
    {
        return view('dashboard.view', [
            'heading' => 'Contact Form Submissions',
            'model'   => 'contact',
            'rows'    => Contact::getContactSubmissions(),
            'cols'    => Contact::$dashboard_columns
        ]);
    }

    public function getSubscriptions()
    {
        return view('dashboard.view', [
            'heading' => 'Subscriptions',
            'model'   => 'subscriptions',
            'rows'    => Subscriptions::getSubscriptions(),
            'cols'    => Subscriptions::$dashboard_columns
        ]);
    }

    /**
     * Dashboard Edit
     */

    /**
     * Dashboard Export: Export data as a spreadsheet
     */
    public function getExport($model)
    {
        // set the name of the spreadsheet
        $sheet_name = ucfirst($model);

        // set the model using the 'model' request argument
        switch ($model) {
            case 'contact':
                $headings = [ 'Date', 'Name', 'Email', 'Message' ];
                $items = Contact::select('created_at', 'name', 'email', 'message')->get();
                break;
            case 'subscriptions':
                $headings = [ 'Date', 'Email', 'Name' ];
                $items = Subscriptions::select('created_at', 'email', 'name')->get();
                break;
            default:
                abort(404);
        }

        Excel::create($sheet_name, function($excel) use($sheet_name, $headings, $items) {
            $excel->sheet($sheet_name, function($sheet) use($sheet_name, $headings, $items) {
                $sheet->fromArray($items);
                $sheet->row(1, $headings);
            });
        })->store('xls')->export('xls');
    }

    /**
     * Dashboard Image Upload: Upload images
     */
    public function postImageUpload(Request $request)
    {
        if ($request->hasFile('file')) {
            $directory = base_path() . '/public/uploads/' . $request['model'] . '/img/';
            file::makeDirectory($directory, 0755, true, true);
            $image = Image::make($request->file('file'));
            $image->save($directory . $request['id'] . "-" . $request['name'] . '.jpg');
        } else {
            return 'file-upload-fail';
        }

        return 'success';
    }

    /**
     * Dashboard Edit: Create and edit rows
     */
    public function postEdit(Request $request)
    {
        $this->validate($request, [
            'id'      => 'required',
            'model'   => 'required',
            'columns' => 'required'
        ]);

        // store the id request variable for easy access
        $id = $request['id'];

        // set the model using the 'model' request argument
        switch ($request['model']) {
            default:
                return 'model-access-fail';
        }

        // populate the eloquent object with the remaining items in $request
        foreach ($request['columns'] as $column) {
            $item->$column = $request[$column];
        }

        // save the new or updated item
        $item->save();

        // return the id number in the format '^id:[0-9][0-9]*$' on success
        return 'id:' . $item->id;
    }

    /**
     * Dashboard Reorder: Reorder rows
     */
    public function postReorder(Request $request)
    {
        $this->validate($request, [
            'order'  => 'required',
            'column' => 'required',
            'model'  => 'required'
        ]);

        $order = $request['order'];
        $column = $request['column'];

        // set the model using the 'model' request argument
        switch ($request['model']) {
            default:
                return 'model-access-fail';
        }

        // update each row with the new order
        foreach (array_keys($order) as $order_id) {
            $item = $items::find($order_id);
            $item->$column = $order[$order_id];
            $item->save();
        }

        return 'success';
    }

    /**
     * Dashboard Delete: Delete rows
     */
    public function deleteDelete(Request $request)
    {
        $this->validate($request, [
            'id'    => 'required',
            'model' => 'required'
        ]);

        // set the model using the 'model' request argument
        switch ($request['model']) {
            default:
                return 'model-access-fail';
        }

        // delete the row with the id using the 'id' request argument
        if ($items::where('id', $request['id'])->exists()) {
            $items::where('id', $request['id'])->delete();
        } else {
            return 'row-delete-fail';
        }

        // delete associated files if they exist
        foreach ($items::$dashboard_columns as $column) {
            if ($column['type'] == 'image') {
                $image_file = base_path() . '/public/uploads/' . $request['model'] . '/img/' . $request['id'] . "-" . $column['name'] . '.jpg';

                if (file_exists($image_file) && !unlink($image_file)) {
                    return 'image-delete-fail';
                }
            }
        }

        // Return a success
        return 'success';
    }

}
