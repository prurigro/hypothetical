<?php namespace App\Http\Controllers;

use Log;
use Mail;
use Newsletter;
use App\Models\Blog;
use App\Models\Contact;
use App\Models\Subscriptions;
use Illuminate\Http\Request;

class ApiController extends Controller {

    public function getBlogEntries()
    {
        return Blog::getBlogEntries();
    }

    public function postContactSubmit(Request $request)
    {
        $this->validate($request, [
            'name'    => 'required',
            'email'   => 'required|email',
            'message' => 'required'
        ]);

        $contact = new Contact;
        $contact->name = $request['name'];
        $contact->email = $request['email'];
        $contact->message = $request['message'];
        $contact->save();

        // Send the email if the MAIL_SENDTO variable is set
        if (env('MAIL_SENDTO') != null) {
            Mail::send('email.contact', [ 'contact' => $contact ], function($mail) use ($contact) {
                $mail->from(env('MAIL_SENDFROM'), env('APP_NAME'))
                    ->to(env('MAIL_SENDTO'))
                    ->subject('Contact form submission');
            });
        }

        return 'success';
    }

    public function postSubscriptionSubmit(Request $request)
    {
        $this->validate($request, [
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
