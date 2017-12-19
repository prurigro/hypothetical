// nav functionality
function navInit() {
    $(".nav-section-component-mobile-header-toggle").on("click", function() {
        $(".nav-section-component-mobile-header-toggle, .nav-section-component-links").toggleClass("open");
    });
}
