// contact form functionality
function contactFormInit() {
    const $form = $("#contact-form"),
        $input = $form.find(":input"),
        $notify = $("#notification");

    let contact = {},
        submitting = false;

    const getContactData = function() {
        contact = {
            name: $("#name").val(),
            email: $("#email").val(),
            message: $("#message").val(),
            _token: $("#token").val()
        };
    };

    $("#submit").on("click", function(e) {
        const $submit = $(this);

        e.preventDefault();

        if (!submitting) {
            submitting = true;
            getContactData();

            $.ajax({
                type: "POST",
                url: "/contact-submit",
                data: contact
            }).always(function(response) {
                let responseJSON, errors, prop;

                $form.find(".error").removeClass("error");
                $notify.removeClass("visible");

                if (response === "success") {
                    $input.attr("disabled", true);
                    $submit.addClass("disabled");
                    $notify.text("Thanks for your message!").addClass("success").addClass("visible");
                } else {
                    responseJSON = response.responseJSON;
                    errors = 0;

                    // add the error class to fields that haven't been filled out
                    for (prop in responseJSON) {
                        if (responseJSON.hasOwnProperty(prop)) {
                            $("#" + prop).addClass("error");
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
