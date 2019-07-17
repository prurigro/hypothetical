// global variables
const fadeTime = 250;

// declare a reverse function for jquery
jQuery.fn.reverse = [].reverse;

// show or hide the loading modal
function loadingModal(action) {
    const $loadingModal = $("#loading-modal");

    if (action === "show") {
        $loadingModal.css({
            visibility: "visible",
            opacity: 1
        });
    } else if (action === "hide") {
        $loadingModal.css({ opacity: 0 });

        setTimeout(function() {
            $loadingModal.css({ visibility: "hidden" });
        }, fadeTime);
    }
}

// show the confirmation modal and run the supplied command if confirm is pressed
function askConfirmation(message, command, cancelCommand) {
    const $confirmationModal = $("#confirmation-modal"),
        $heading = $confirmationModal.find(".card-header"),
        $cancelButton = $confirmationModal.find(".btn.cancel-button"),
        $confirmButton = $confirmationModal.find(".btn.confirm-button");

    // close the confirmation modal and unbind its events
    const closeConfirmationModal = function() {
        // unbind events
        $(document).off("keydown", escapeModal);
        $cancelButton.off("click", closeConfirmationModal);
        $confirmButton.off("click", confirmModal);

        // hide the modal
        $confirmationModal.css({ opacity: 0 });

        setTimeout(function() {
            // set visibility to hidden
            $confirmationModal.css({ visibility: "hidden" });

            // clear the heading
            $heading.empty();
        }, fadeTime);
    };

    // close the modal if the escape button is pressed
    const escapeModal = function(e) {
        if (e.keyCode === 27) {
            closeConfirmationModal();
        } else {
            e.preventDefault();
        }
    };

    // functionality to run when clicking the confirm button
    const confirmModal = function() {
        command();
        closeConfirmationModal();
    };

    // functionality to run when clicking the cancel button
    const cancelModal = function() {
        if (cancelCommand !== undefined) {
            cancelCommand();
        }

        closeConfirmationModal();
    };

    // hide the modal when the cancel button is pressed
    $cancelButton.on("click", cancelModal);

    // hide the modal when the escape key is pressed
    $(document).on("keydown", escapeModal);

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
        $acceptButton = $alertModal.find(".btn.accept-button");

    // close the alert modal and unbind its events
    const closeAlertModal = function() {
        // unbind events
        $(document).off("keydown", escapeModal);
        $acceptButton.off("click", closeAlertModal);

        // clear the message
        $message.empty();

        // hide the modal
        $alertModal.css({ opacity: 0 });

        setTimeout(function() {
            $alertModal.css({ visibility: "hidden" });
        }, fadeTime);

        // if a command was passed run it now
        if (command !== undefined) {
            command();
        }
    };

    // close the modal if the escape button is pressed
    const escapeModal = function(e) {
        if (e.keyCode === 27) {
            closeAlertModal();
        } else {
            e.preventDefault();
        }
    };

    // hide the modal when the escape key is pressed
    $(document).on("keydown", escapeModal);

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
        $token = $("#token"),
        model = $editList.data("model");

    // initialize new button functionality
    const newButtonInit = function() {
        const $newButton = $(".btn.new-button");

        $newButton.on("click", function() {
            window.location.href = "/dashboard/edit/" + model + "/new";
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
            window.location.href = "/dashboard/edit/" + model + "/" + itemId;
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
                        _token: $token.val()
                    }
                }).always(function(response) {
                    if (response === "success") {
                        $listItem.slideUp(150, function() { $listItem.remove(); });
                    } else {
                        showAlert("Failed to delete record");
                    }
                });
            });
        });
    };

    // initialize action button functionality
    const actionButtonInit = function() {
        const $actionButtons = $(".btn.action-button");

        $actionButtons.on("click", function() {
            const $this = $(this),
                $listItem = $this.closest(".list-group-item"),
                itemId = $listItem.data("id"),
                confirmationMessage = $this.data("confirmation"),
                successMessage = $this.data("success"),
                errorMessage = $this.data("error"),
                postUrl = $this.data("url");

            askConfirmation(confirmationMessage, function() {
                $.ajax({
                    type: "POST",
                    url: postUrl,
                    data: {
                        id: itemId,
                        _token: $token.val()
                    }
                }).always(function(response) {
                    if (response === "success") {
                        showAlert(successMessage);
                    } else {
                        showAlert("ERROR: " + errorMessage);
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
                            _token: $token.val()
                        }
                    }).always(function(response) {
                        if (response !== "success") {
                            showAlert("Sorting failed", function() {
                                document.location.reload(true);
                            });
                        }
                    });
                }
            });
        }
    };

    // initialize filter functionality if the filter-input element exists
    const filterInputInit = function() {
        const $filter = $("#filter-input");

        if ($filter.length) {
            // empty the filter
            $filter.val("");

            // initialize the filter list functionality
            const filterList = new List("edit-list-wrapper", {
                valueNames: [ "title-column" ]
            });

            // add/remove a filtered class to identify when the list is filtered
            $filter.on("input", function() {
                if ($filter.val() === "") {
                    $editList.removeClass("filtered");
                } else {
                    $editList.addClass("filtered");
                }
            });
        }
    };

    newButtonInit();
    editButtonInit();
    deleteButtonInit();
    actionButtonInit();
    sortRowInit();
    filterInputInit();
}

// initialize edit item functionality
function editItemInit() {
    const $editItem = $("#edit-item"),
        $submit = $("#submit"),
        $backButton = $("#back"),
        $textInputs = $(".text-input"),
        $datePickers = $(".date-picker"),
        $mkdEditors = $(".mkd-editor"),
        $fileUploads = $(".file-upload"),
        $imgUploads = $(".image-upload"),
        $token = $("#token"),
        model = $editItem.data("model"),
        id = $editItem.data("id"),
        operation = id === "new" ? "create" : "update";

    let allowTimes = [],
        simplemde = [],
        formData = {},
        submitting = false,
        hours,
        minutes,
        changes = false;

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

        // add values from the contents of date-picker class elements
        $datePickers.each(function() {
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

    const uploadFile = function(row_id, currentFile) {
        let file, fileUpload;

        // functionality to run on success
        const returnSuccess = function() {
            loadingModal("hide");
            window.location.href = `/dashboard/edit/${model}/${row_id}`;
        };

        // add the file from the file upload box for file-upload class elements
        if ($fileUploads.length >= currentFile + 1) {
            fileUpload = $fileUploads[currentFile];

            if ($(fileUpload).val() !== "") {
                file = new FormData();

                // add the file, id and model to the formData variable
                file.append("file", fileUpload.files[0]);
                file.append("name", $(fileUpload).attr("name"));
                file.append("id", row_id);
                file.append("model", model);
                file.append("ext", $(fileUpload).data("ext"));

                $.ajax({
                    type: "POST",
                    url: "/dashboard/file-upload",
                    data: file,
                    processData: false,
                    contentType: false,
                    beforeSend: function(xhr) { xhr.setRequestHeader("X-CSRF-TOKEN", $token.val()); }
                }).always(function(response) {
                    if (response === "success") {
                        uploadFile(row_id, currentFile + 1);
                    } else {
                        loadingModal("hide");

                        showAlert("Failed to upload file", function() {
                            submitting = false;
                        });
                    }
                });
            } else {
                uploadFile(row_id, currentFile + 1);
            }
        } else {
            returnSuccess();
        }
    };

    const uploadImage = function(row_id, currentImage) {
        let file, imgUpload;

        // functionality to run on success
        const returnSuccess = function() {
            uploadFile(row_id, 0);
        };

        // add the image from the image upload box for image-upload class elements
        if ($imgUploads.length >= currentImage + 1) {
            imgUpload = $imgUploads[currentImage];

            if ($(imgUpload).val() !== "") {
                file = new FormData();

                // add the file, id and model to the formData variable
                file.append("file", imgUpload.files[0]);
                file.append("name", $(imgUpload).attr("name"));
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
                        uploadImage(row_id, currentImage + 1);
                    } else {
                        loadingModal("hide");

                        showAlert("Failed to upload image", function() {
                            submitting = false;
                        });
                    }
                });
            } else {
                uploadImage(row_id, currentImage + 1);
            }
        } else {
            returnSuccess();
        }
    };

    const contentChanged = function() {
        changes = true;
        $submit.removeClass("no-input");
    };

    $(".edit-button.delete.image").on("click", function(e) {
        const $this = $(this),
            name = $this.data("name");

        if (!submitting) {
            submitting = true;

            askConfirmation("Are you sure you want to delete this image?", function() {
                // delete the image
                $.ajax({
                    type: "DELETE",
                    url: "/dashboard/image-delete",
                    data: {
                        id: id,
                        model: model,
                        name: name,
                        _token: $token.val()
                    }
                }).always(function(response) {
                    if (response === "success") {
                        $(`#current-image-${name}`).slideUp(200);
                        submitting = false;
                    } else {
                        showAlert("Failed to delete image: " + response, function() {
                            submitting = false;
                        });
                    }
                });
            }, function() {
                submitting = false;
            });
        }
    });

    $(".edit-button.delete.file").on("click", function(e) {
        const $this = $(this),
            name = $this.data("name"),
            ext = $this.data("ext");

        if (!submitting) {
            submitting = true;

            askConfirmation("Are you sure you want to delete this file?", function() {
                // delete the file
                $.ajax({
                    type: "DELETE",
                    url: "/dashboard/file-delete",
                    data: {
                        id: id,
                        model: model,
                        name: name,
                        ext: ext,
                        _token: $token.val()
                    }
                }).always(function(response) {
                    if (response === "success") {
                        $(`#current-file-${name}`).slideUp(200);
                        submitting = false;
                    } else {
                        showAlert("Failed to delete file: " + response, function() {
                            submitting = false;
                        });
                    }
                });
            }, function() {
                submitting = false;
            });
        }
    });

    // allow start time selection to start on the hour and every 15 minutes after
    for (hours = 0; hours <= 23; hours++) {
        for (minutes = 0; minutes <= 3; minutes++) {
            allowTimes.push(hours + ":" + (minutes === 0 ? "00" : minutes * 15));
        }
    }

    // enable the datepicker for each element with the date-picker class
    $datePickers.each(function() {
        $(this).flatpickr({
            enableTime: true
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
            // load the initial value into the editor
            simplemde[column].value($this.attr("value"));
            simplemde[column].codemirror.refresh();

            // watch for changes to simplemde editor contents
            simplemde[column].codemirror.on("change", contentChanged);
        }, 500);
    });

    // watch for changes to input and select element contents
    $editItem.find("input, select").on("input change", contentChanged);

    // initialize back button
    $backButton.on("click", function() {
        if (!submitting) {
            if (changes) {
                askConfirmation("Cancel changes and return to the list?", function() {
                    window.location.href = "/dashboard/edit/" + model;
                });
            } else {
                window.location.href = "/dashboard/edit/" + model;
            }
        }
    });

    // initialize submit button
    $submit.on("click", function() {
        if (!submitting && changes) {
            submitting = true;

            // show the loading modal
            loadingModal("show");

            // populate the formData object
            getFormData();

            // submit the update
            if (Object.keys(formData.columns).length) {
                $.ajax({
                    type: "POST",
                    url: "/dashboard/update",
                    data: formData
                }).always(function(response) {
                    if ((/^id:[0-9][0-9]*$/).test(response)) {
                        uploadImage(response.replace(/^id:/, ""), 0);
                    } else {
                        loadingModal("hide");

                        showAlert("Failed to " + operation + " record", function() {
                            submitting = false;
                        });
                    }
                });
            } else {
                uploadImage(formData.id, 0);
            }
        }
    });
}

// initialize the user profile image functionality
function userProfileImageInit() {
    const $form = $("#user-profile-image"),
        $upload = $("#profile-image-upload"),
        $delete = $("#profile-image-delete"),
        $token = $("#token"),
        $display = $form.find(".image-display").first(),
        defaultImage = $display.data("default");

    let file,
        submitting = false;

    $upload.on("change", function() {
        if ($upload.val() !== "" && !submitting) {
            submitting = true;

            askConfirmation("Update your user profile image?", function() {
                // show the loading modal
                loadingModal("show");

                // add the image to the form data
                file = new FormData();
                file.append("file", $upload[0].files[0]);

                // submit the form data
                $.ajax({
                    type: "POST",
                    url: "/dashboard/user/profile-image-upload",
                    data: file,
                    processData: false,
                    contentType: false,
                    beforeSend: function(xhr) { xhr.setRequestHeader("X-CSRF-TOKEN", $token.val()); }
                }).always(function(response) {
                    loadingModal("hide");

                    if ((/\.png\?version=/).test(response)) {
                        $display.css({ backgroundImage: `url(${response})` });
                        $delete.removeClass("inactive");
                        submitting = false;
                    } else {
                        showAlert("Failed to upload image", function() {
                            submitting = false;
                        });
                    }
                });
            }, function() {
                $upload.val("");
                submitting = false;
            });
        }
    });

    $delete.on("click", function() {
        if (!submitting) {
            submitting = true;

            askConfirmation("Delete your profile image?", function() {
                // delete the profile image
                $.ajax({
                    type: "DELETE",
                    url: "/dashboard/user/profile-image-delete",
                    data: {
                        _token: $token.val()
                    }
                }).always(function(response) {
                    if (response === "success") {
                        $display.css({ backgroundImage: `url(${defaultImage})` });
                        $delete.addClass("inactive");
                        submitting = false;
                    } else {
                        showAlert("Failed to delete profile image", function() {
                            submitting = false;
                        });
                    }
                });
            }, function() {
                submitting = false;
            });
        }
    });
}

// initialize the user profile update functionality
function userProfileUpdateInit() {
    const $form = $("#user-profile-update"),
        $submit = $form.find(".submit-button"),
        $inputs = $form.find("input"),
        $name = $("#name"),
        $website = $("#website"),
        $facebook = $("#facebook"),
        $soundcloud = $("#soundcloud"),
        $instagram = $("#instagram"),
        $twitter = $("#twitter"),
        $token = $("#token");

    let formData = {},
        submitting = false;

    const getFormData = function() {
        formData = {
            name: $name.val(),
            website: $website.val(),
            facebook: $facebook.val(),
            soundcloud: $soundcloud.val(),
            instagram: $instagram.val(),
            twitter: $twitter.val(),
            _token: $token.val()
        };
    };

    // remove the error class from an input and enable submit when its value changes
    $inputs.on("input change", function() {
        $submit.removeClass("no-input");
        $(this).removeClass("error");
    });

    // initialize submit button
    $submit.on("click", function() {
        if (!submitting) {
            submitting = true;

            // remove the error class from inputs
            $inputs.removeClass("error");

            // show the loading modal
            loadingModal("show");

            // populate the formData object
            getFormData();

            // submit the update
            $.ajax({
                type: "POST",
                url: "/dashboard/user/profile-update",
                data: formData
            }).always(function(response) {
                loadingModal("hide");

                if (response === "success") {
                    $submit.addClass("no-input");

                    showAlert("User profile updated successfully", function() {
                        submitting = false;
                    });
                } else {
                    // add the error class to fields that haven't been filled correctly
                    for (let errorName in response.responseJSON.errors) {
                        if ($form.find(`[name='${errorName}']`).length) {
                            $form.find(`[name='${errorName}']`).addClass("error");
                        }
                    }

                    showAlert("Error updating user profile", function() {
                        submitting = false;
                    });
                }
            });
        }
    });
}

// initialize the user password update functionality
function userPasswordUpdateInit() {
    const $form = $("#user-password-update"),
        $submit = $form.find(".submit-button"),
        $inputs = $form.find("input"),
        $oldpass = $("#oldpass"),
        $newpass = $("#newpass"),
        $newpassConfirmation = $("#newpass_confirmation"),
        $token = $("#token");

    let formData = {},
        submitting = false;

    const getFormData = function() {
        formData = {
            oldpass: $oldpass.val(),
            newpass: $newpass.val(),
            newpass_confirmation: $newpassConfirmation.val(),
            _token: $token.val()
        };
    };

    // remove the error class from inputs and enable submit if all inputs have data when changes are made
    $inputs.on("input change", function() {
        let enableSubmit = true;

        for (let i = 0; i < $inputs.length; i++) {
            if ($inputs[i].value === "") {
                enableSubmit = false;
                break;
            }
        }

        if (enableSubmit) {
            $submit.removeClass("no-input");
        } else {
            $submit.addClass("no-input");
        }

        $inputs.removeClass("error");
    });

    // initialize submit button
    $submit.on("click", function() {
        if (!submitting) {
            submitting = true;

            // remove the error class from inputs
            $inputs.removeClass("error");

            // show the loading modal
            loadingModal("show");

            // populate the formData object
            getFormData();

            if (formData.newpass !== formData.newpass_confirmation) {
                // fail with an error if the newpass and newpass_confirmation don't match
                $newpassConfirmation.val("");
                $newpass.addClass("error");
                $newpassConfirmation.addClass("error");

                showAlert("New passwords do not match", function() {
                    submitting = false;
                });
            } else {
                // submit the update
                $.ajax({
                    type: "POST",
                    url: "/dashboard/user/password-update",
                    data: formData
                }).always(function(response) {
                    loadingModal("hide");

                    if (response === "success") {
                        $inputs.val("").trigger("change");

                        showAlert("Password updated successfully", function() {
                            submitting = false;
                        });
                    } else if (response === "old-password-fail") {
                        $oldpass.addClass("error");

                        showAlert("Old password is not correct", function() {
                            submitting = false;
                        });
                    } else {
                        $newpass.addClass("error");
                        $newpassConfirmation.val("");

                        showAlert("New password must be at least 6 characters", function() {
                            submitting = false;
                        });
                    }
                });
            }
        }
    });
}

// run the respective initialization functions for each form on the current page
$(document).ready(function() {
    if ($("#edit-list").length) {
        editListInit();
    }

    if ($("#edit-item").length) {
        editItemInit();
    }

    if ($("#user-profile-image").length) {
        userProfileImageInit();
    }

    if ($("#user-profile-update").length) {
        userProfileUpdateInit();
    }

    if ($("#user-password-update").length) {
        userPasswordUpdateInit();
    }
});
