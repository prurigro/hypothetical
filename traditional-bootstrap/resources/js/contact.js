// contact form functionality
function contactFormInit() {
    const $form = $("#contact-form"),
        $input = $form.find(":input"),
        $name = $form.find("[name='name']"),
        $email = $form.find("[name='email']"),
        $message = $form.find("[name='message']"),
        $token = $form.find("[name='_token']"),
        $submit = $form.find("[name='submit']"),
        $notify = $form.find(".notification");

    let contact = {},
        submitting = false;

    const getContactData = function() {
        contact = {
            name: $name.val(),
            email: $email.val(),
            message: $message.val(),
            _token: $token.val()
        };
    };

    $submit.on("click", function(e) {
        e.preventDefault();

        if (!submitting) {
            submitting = true;
            getContactData();

            $.ajax({
                type: "POST",
                url: "/api/contact-submit",
                data: contact
            }).always(function(response) {
                let errors;

                $form.find(".error").removeClass("error");
                $notify.removeClass("visible");

                if (response === "success") {
                    $input.attr("disabled", true);
                    $submit.addClass("disabled");
                    $notify.text("Thanks for your message!").addClass("success").addClass("visible");
                } else {
                    errors = 0;

                    // add the error class to fields that haven't been filled correctly
                    for (let errorName in response.responseJSON.errors) {
                        if ($form.find(`[name='${errorName}']`).length) {
                            $form.find(`[name='${errorName}']`).addClass("error");
                            errors++;
                        }
                    }

                    if (errors > 0) {
                        $notify.find("span").text(errors);
                        $notify.addClass("visible");
                    }

                    // re-enable submitting
                    submitting = false;
                }
            });
        }
    });
}
