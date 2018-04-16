<template>
    <div class="error-page-component">
        <h1>Page Not Found</h1>
    </div>
</template>

<script>
    import BasePageMixin from "mixins/base-page.js";

    export default {
        mixins: [
            BasePageMixin
        ],

        data() {
            return {
                metaTitle: "Page Not Found",
                metaDescription: "The requested page cannot be found"
            };
        }
    };
</script>
