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

        $name    = $request['name'];
        $email   = $request['email'];
        $message = $request['message'];

        $contact = new Contact;
        $contact->name = $name;
        $contact->email = $email;
        $contact->message = $message;
        $contact->save();

        // Send the email if the MAIL_SENDTO variable is set
        if (env('MAIL_SENDTO') != null) {
            Mail::send('email.contact', [ 'contact' => $contact ], function ($mail) use ($contact) {
                $mail->from(env('MAIL_ADDRESS'), env('SITE_NAME'))
                    ->to(env('MAIL_SENDTO'))
                    ->subject('Contact form submission');
            });
        }

        return 'success';
    }

}
