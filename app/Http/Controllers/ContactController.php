<?php namespace App\Http\Controllers;

use Mail;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller {

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
                $mail->from(env('MAIL_SENDFROM'), env('SITE_NAME'))
                    ->to(env('MAIL_SENDTO'))
                    ->subject('Contact form submission');
            });
        }

        return 'success';
    }

}
