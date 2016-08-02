// subscription form functionality
function subscriptionFormInit() {
    const $form = $("#subscription-form"),
        $input = $form.find(":input"),
        $notify = $("#notification");

    let subscribe = {},
        submitting = false;

    const getSubscribeData = function() {
        subscribe = {
            name: $("#name").val(),
            email: $("#email").val(),
            address: $("#address").val(),
            _token: $("#token").val()
        };
    };

    $("#submit").on("click", function(e) {
        e.preventDefault();

        if (!submitting) {
            submitting = true;
            getSubscribeData();

            $.ajax({
                type: "POST",
                url: "/subscription-submit",
                data: subscribe
            }).always(function(response) {
                let responseJSON, errors, prop;

                $form.find(".error").removeClass("error");
                $notify.removeClass("visible").removeClass("error");

                if (response === "success") {
                    $form.addClass("success");

                    setTimeout(function() {
                        $notify.text("Thanks for subscribing!").addClass("success").addClass("visible");
                        $input.fadeOut(150);
                    }, 1000);
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

                    // if there are no errors with form fields then there must have been an API error
                    if (errors === 0) {
                        $notify.text("An error occurred. Are you already subscribed?").addClass("error").addClass("visible");
                    }

                    // re-enable submitting
                    submitting = false;
                }
            });
        }
    });
}
