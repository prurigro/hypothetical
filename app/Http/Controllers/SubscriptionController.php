<?php namespace App\Http\Controllers;

use Newsletter;
use App\Models\Subscriptions;
use Illuminate\Http\Request;

class SubscriptionController extends Controller {

    public function postSubscriptionSubmit(Request $request)
    {
        $this->validate($request, [
            'name'    => 'required',
            'email'   => 'required|email',
            'address' => array('required', 'regex:/^([A-Za-z][0-9][A-Za-z] *[0-9][A-Za-z][0-9]|[0-9][0-9][0-9][0-9][0-9])$/')
        ]);

        $name    = $request['name'];
        $fname   = preg_replace('/ .*$/', '', $name);
        $lname   = preg_match('/. ./', $name) === 1 ? preg_replace('/^[^ ][^ ]* /', '', $name) : '';
        $email   = $request['email'];
        $address = $request['address'];

        if (env('MAILCHIMP_APIKEY', '') != '' && env('MAILCHIMP_LISTID', '') != '') {
            // Submit the subscription request
            Newsletter::subscribe($email, [
                'FNAME'   => $fname,
                'LNAME'   => $lname,
                'ADDRESS' => $address
            ]);
        }

        // Save to the database on success
        $subscriptions = new Subscriptions;
        $subscriptions->name = $name;
        $subscriptions->email = $email;
        $subscriptions->location = $address;
        $subscriptions->save();

        return 'success';
    }

}
