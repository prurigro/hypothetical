<?php namespace App\Http\Controllers;

use Log;
use Mail;
use Newsletter;
use App\Models\Blog;
use App\Models\Contact;
use App\Models\Subscriptions;
use App\Models\Meta;
use Illuminate\Http\Request;

class ApiController extends Controller {

    public function getBlogEntries()
    {
        return Blog::getBlogEntries();
    }

    public function postMeta()
    {
        return Meta::getData(request()->path);
    }

    public function postContactSubmit(Request $request)
    {
        $request->validate([
            'name'    => 'required',
            'email'   => 'required|email',
            'message' => 'required'
        ]);

        $contact = new Contact;
        $contact->name = $request['name'];
        $contact->email = $request['email'];
        $contact->message = $request['message'];
        $contact->save();

        // Send the email if the MAIL_TO_ADDRESS variable is set
        if (env('MAIL_TO_ADDRESS') != null) {
            Mail::send('email.contact', [ 'contact' => $contact ], function($mail) use ($contact) {
                $mail->to(env('MAIL_TO_ADDRESS'))
                    ->subject('Contact form submission');
            });
        }

        return 'success';
    }

    public function postSubscriptionSubmit(Request $request)
    {
        $request->validate([
            'name'    => 'required',
            'email'   => 'required|email'
        ]);

        $name    = $request['name'];
        $fname   = preg_replace('/ .*$/', '', $name);
        $lname   = preg_match('/. ./', $name) == 1 ? preg_replace('/^[^ ][^ ]* /', '', $name) : '';
        $email   = $request['email'];

        if (env('MAILCHIMP_APIKEY') != null && env('MAILCHIMP_LISTID') != null) {
            // Submit the subscription request
            Newsletter::subscribeOrUpdate($email, [
                'FNAME' => $fname,
                'LNAME' => $lname
            ]);

            if (!Newsletter::lastActionSucceeded()) {
                Log::info('Mail Chimp Error: ' . Newsletter::getLastError());
            }
        }

        // Save to the database on success
        $subscriptions = new Subscriptions;
        $subscriptions->name = $name;
        $subscriptions->email = $email;
        $subscriptions->save();

        return 'success';
    }

}
