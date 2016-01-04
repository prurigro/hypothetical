// contact page functionality
function contactFormInit() {
    var $form = $('#contact-form'),
        $notify = $('#notification'),
        contact = {},
        submitting = false;

    var getContactData = function() {
        contact = {
            name:    $('#name').val(),
            email:   $('#email').val(),
            message: $('#message').val(),
            _token:  $('#token').val()
        };
    };

    $('#submit').on('click', function(e) {
        e.preventDefault();

        if (!submitting) {
            submitting = true;
            getContactData();

            $.ajax({
                type: 'POST',
                url:  '/contact-submit',
                data: contact
            }).always(function(response) {
                $form.find('.error').removeClass('error');
                $notify.removeClass('visible');

                if (response === 'success') {
                    $notify.text('Thanks for your message!').addClass('success').addClass('visible');
                } else {
                    var responseJSON = response.responseJSON,
                        errors = 0;

                    // add the error class to fields that haven't been filled out
                    for (var prop in responseJSON) {
                        if (responseJSON.hasOwnProperty(prop)) {
                            $('#' + prop).addClass('error');
                            errors++;
                        }
                    }

                    if (errors > 0) {
                        $notify.find('span').text(errors);
                        $notify.addClass('visible');
                    }

                    // re-enable submitting
                    submitting = false;
                }
            });
        }
    });
}
