<?php namespace App\Http\Controllers;

use Newsletter;
use App\Models\Subscriptions;
use Illuminate\Http\Request;

class SubscriptionController extends Controller {

    public function postSubscriptionSubmit(Request $request)
    {
        $this->validate($request, [
            'name'    => 'required',
            'email'   => 'required|email'
        ]);

        $name    = $request['name'];
        $fname   = preg_replace('/ .*$/', '', $name);
        $lname   = preg_match('/. ./', $name) === 1 ? preg_replace('/^[^ ][^ ]* /', '', $name) : '';
        $email   = $request['email'];

        if (env('MAILCHIMP_APIKEY', '') != '' && env('MAILCHIMP_LISTID', '') != '') {
            // Submit the subscription request
            Newsletter::subscribe($email, [
                'FNAME'   => $fname,
                'LNAME'   => $lname
            ]);
        }

        // Save to the database on success
        $subscriptions = new Subscriptions;
        $subscriptions->name = $name;
        $subscriptions->email = $email;
        $subscriptions->save();

        return 'success';
    }

}
