// declare a reverse function for jquery
jQuery.fn.reverse = [].reverse;

// show the confirmation modal and run the supplied command if confirm is pressed
function askConfirmation(message, command) {
    const $confirmationModal = $("#confirmation-modal"),
        $heading = $confirmationModal.find(".panel-heading"),
        $cancelButton = $confirmationModal.find(".btn.cancel-button"),
        $confirmButton = $confirmationModal.find(".btn.confirm-button"),
        fadeTime = 250;

    // close the confirmation modal and unbind its events
    const closeConfirmationModal = function() {
        // unbind events
        $(document).off("keyup", escapeModal);
        $cancelButton.off("click", closeConfirmationModal);
        $confirmButton.off("click", confirmModal);

        // clear the heading
        $heading.empty();

        // hide the modal
        $confirmationModal.css({ opacity: 0 });
        setTimeout(function() { $confirmationModal.css({ visibility: "hidden" }); }, fadeTime);
    };

    // close the modal if the escape button is pressed
    const escapeModal = function(e) {
        if (e.keyCode === 27) { closeConfirmationModal(); }
    };

    // functionality to run when clicking the confirm button
    const confirmModal = function() {
        command();
        closeConfirmationModal();
    };

    // hide the modal when the cancel button is pressed
    $cancelButton.on("click", closeConfirmationModal);

    // hide the modal when the escape key is pressed
    $(document).on("keyup", escapeModal);

    // run the command and hide the modal when the confirm button is pressed
    $confirmButton.on("click", confirmModal);

    // set the heading with the supplied message
    $heading.text(message);

    // show the confirmation modal
    $confirmationModal.css({
        visibility: "visible",
        opacity: 1
    });
}

// show the alert modal and display the provided message until accept is pressed
function showAlert(message, command) {
    const $alertModal = $("#alert-modal"),
        $message = $alertModal.find(".message"),
        $acceptButton = $alertModal.find(".btn.accept-button"),
        fadeTime = 250;

    // close the alert modal and unbind its events
    const closeAlertModal = function() {
        // unbind events
        $(document).off("keyup", escapeModal);
        $acceptButton.off("click", closeAlertModal);

        // clear the message
        $message.empty();

        // hide the modal
        $alertModal.css({ opacity: 0 });
        setTimeout(function() { $alertModal.css({ visibility: "hidden" }); }, fadeTime);

        // if a command was passed run it now
        if (command !== undefined) { command(); }
    };

    // close the modal if the escape button is pressed
    const escapeModal = function(e) {
        if (e.keyCode === 27) { closeAlertModal(); }
    };

    // hide the modal when the escape key is pressed
    $(document).on("keyup", escapeModal);

    // hide the modal when the accept button is pressed
    $acceptButton.on("click", closeAlertModal);

    // set the message with the supplied message
    $message.text(message);

    // show the alert modal
    $alertModal.css({
        visibility: "visible",
        opacity: 1
    });
}

// initialize edit list functionality
function editListInit() {
    const editList = document.getElementById("edit-list"),
        $editList = $(editList),
        model = $editList.data("model");

    // initialize new button functionality
    const newButtonInit = function() {
        const $newButton = $(".btn.new-button");

        $newButton.on("click", function() {
            window.location.href = "/dashboard/" + model + "-edit/new";
        });
    };

    // initialize edit button functionality
    const editButtonInit = function() {
        const $editButtons = $(".btn.edit-button");

        $editButtons.on("click", function() {
            const $this = $(this),
                $listItem = $this.closest(".list-group-item"),
                itemId = $listItem.data("id");

            // go to the edit page
            window.location.href = "/dashboard/" + model + "-edit/" + itemId;
        });
    };

    // initialize delete button functionality
    const deleteButtonInit = function() {
        const $deleteButtons = $(".btn.delete-button");

        $deleteButtons.on("click", function() {
            const $this = $(this),
                $listItem = $this.closest(".list-group-item"),
                itemId = $listItem.data("id");

            askConfirmation("Are you sure you want to delete this?", function() {
                $.ajax({
                    type: "DELETE",
                    url: "/dashboard/delete",
                    data: {
                        model: model,
                        id: itemId,
                        _token: $("#token").val()
                    }
                }).always(function(response) {
                    if (response === "success") {
                        $listItem.slideUp(150, function() { $listItem.remove(); });
                    } else {
                        showAlert("ERROR: Failed to delete record");
                    }
                });
            });
        });
    };

    // initialize sort functionality if data-sort is set
    const sortRowInit = function() {
        let sortOrder = {}, sortCol, sortable;

        if ($editList.attr("data-sort")) {
            sortCol = $editList.data("sort");

            sortable = Sortable.create(editList, {
                handle: ".sort-icon",
                onUpdate: function() {
                    // update the sortOrder object based on the updated order
                    $editList.find(".list-group-item").reverse().each(function(index) {
                        sortOrder[$(this).data("id")] = index;
                    });

                    $.ajax({
                        type: "POST",
                        url: "/dashboard/reorder",
                        data: {
                            model: model,
                            order: sortOrder,
                            column: sortCol,
                            _token: $("#token").val()
                        }
                    }).always(function(response) {
                        if (response !== "success") {
                            showAlert("ERROR: Sorting failed", function() {
                                document.location.reload(true);
                            });
                        }
                    });
                }
            });
        }
    };

    newButtonInit();
    editButtonInit();
    deleteButtonInit();
    sortRowInit();
}

function editItemInit() {
    const $editItem = $("#edit-item"),
        $submit = $("#submit"),
        $backButton = $("#back"),
        $textInputs = $(".text-input"),
        $dateTimePickers = $(".date-time-picker"),
        $mkdEditors = $(".mkd-editor"),
        $imgUpload = $("#image-upload"),
        $token = $("#_token"),
        $spinner = $("#loading-modal"),
        fadeTime = 250,
        model = $editItem.data("model"),
        id = $editItem.data("id");

    let allowTimes = [],
        simplemde = [],
        formData = {},
        submitting = false,
        hours,
        minutes;

    // show the loading modal
    const showLoadingModal = function() {
        $spinner.css({
            visibility: "visible",
            opacity: 1
        });
    };

    // hide the loading modal
    const hideLoadingModal = function() {
        $spinner.css({ opacity: 0 });
        setTimeout(function() { $spinner.css({ visibility: "hidden" }); }, fadeTime);
    };

    // fill the formData object with data from all the form fields
    const getFormData = function() {
        // function to add a column and value to the formData object
        const addFormData = function(column, value) {
            // add the value to a key with the column name
            formData[column] = value;

            // add the column to the array of columns
            formData.columns.push(column);
        };

        // reset the formData object
        formData = {};

        // add the database model row id and _token
        formData.model = model;
        formData.id = id;
        formData._token = $token.val();

        // create an empty array to contain the list of columns
        formData.columns = [];

        // add values from the contents of text-input class elements
        $textInputs.each(function() {
            const $this = $(this),
                column = $this.attr("id"),
                value = $this.val();

            addFormData(column, value);
        });

        // add values from the contents of date-time-picker class elements
        $dateTimePickers.each(function() {
            const $this = $(this),
                column = $this.attr("id"),
                value = $this.val() + ":00";

            addFormData(column, value);
        });

        // add values from the contents of the markdown editor for mkd-editor class elements
        $mkdEditors.each(function() {
            const $this = $(this),
                column = $this.attr("id"),
                value = simplemde[column].value();

            addFormData(column, value);
        });
    };

    const uploadImage = function(row_id) {
        let file;

        // functionality to run on success
        const returnSuccess = function() {
            hideLoadingModal();
            window.location.href = "/dashboard/" + model;
        };

        // add the image from the image upload box for image-upload class elements
        if ($imgUpload.length && $imgUpload.val() !== "") {
            file = new FormData();

            // add the file, id and model to the formData variable
            file.append("file", $imgUpload[0].files[0]);
            file.append("id", row_id);
            file.append("model", model);

            $.ajax({
                type: "POST",
                url: "/dashboard/image-upload",
                data: file,
                processData: false,
                contentType: false,
                beforeSend: function(xhr) { xhr.setRequestHeader("X-CSRF-TOKEN", $token.val()); }
            }).always(function(response) {
                if (response === "success") {
                    returnSuccess();
                } else {
                    hideLoadingModal();
                    showAlert("ERROR: Failed to upload image");
                    submitting = false;
                }
            });
        } else {
            returnSuccess();
        }
    };

    // allow start time selection to start on the hour and every 15 minutes after
    for (hours = 0; hours <= 23; hours++) {
        for (minutes = 0; minutes <= 3; minutes++) {
            allowTimes.push(hours + ":" + (minutes === 0 ? "00" : minutes * 15));
        }
    }

    // enable the datetimepicker for each element with the date-time-picker class
    $dateTimePickers.each(function() {
        $(this).datetimepicker({
            format: "Y-m-d H:i",
            allowTimes: allowTimes,
            step: 15
        });
    });

    // enable the markdown editor for each element with the mkd-editor class
    $mkdEditors.each(function() {
        const $this = $(this),
            column = $this.attr("id");

        simplemde[column] = new SimpleMDE({
            element: this,
            toolbar: [
                "bold",
                "italic",
                "|",
                "heading-1",
                "heading-2",
                "heading-3",
                "|",
                "quote",
                "unordered-list",
                "ordered-list",
                "link"
            ],
            blockStyles: { italic: "_" },
            autoDownloadFontAwesome: false,
            tabSize: 4,
            spellChecker: false
        });

        setTimeout(function() {
            simplemde[column].value($this.attr("value"));
        }, 100);
    });

    // initialize back button
    $backButton.on("click", function() {
        if (!submitting) {
            askConfirmation("Cancel and return to the " + model + " list?", function() {
                window.location.href = "/dashboard/" + model;
            });
        }
    });

    // initialize submit button
    $submit.on("click", function() {
        if (!submitting) {
            submitting = true;

            // show the loading modal
            showLoadingModal();

            // populate the formData object
            getFormData();

            // submit the update
            $.ajax({
                type: "POST",
                url: "/dashboard/edit",
                data: formData
            }).always(function(response) {
                if (/^id:[0-9][0-9]*$/.test(response)) {
                    uploadImage(response.replace(/^id:/, ""));
                } else {
                    hideLoadingModal();
                    showAlert("ERROR: Failed to update record");
                    submitting = false;
                }
            });
        }
    });
}

// run once the document is ready
$(document).ready(function() {
    if ($("#edit-list").length) {
        editListInit();
    } else if ($("#edit-item").length) {
        editItemInit();
    }
});
