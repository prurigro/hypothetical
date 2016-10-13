// run once the document is ready
$(document).ready(function() {
    $("footer").stickyFooter({ content: "#page-content" });
    $(window).on("load", function() { $(this).trigger("resize"); });

    switch (SiteVars.page) {
        case "":
            subscriptionFormInit();
            break;
        case "contact":
            contactFormInit();
            break;
    }
});
