// run once the document is ready
$(document).ready(function() {
    switch (SiteVars.page) {
        case '':
            subscriptionFormInit();
            break;
        case 'contact':
            contactFormInit();
            break;
    }
});
