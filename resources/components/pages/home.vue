<template>
    <div class="home-page-component">
        <subscription-form />
    </div>
</template>

<script>
    import BasePageMixin from "mixins/base-page.js";
    import SubscriptionFormSection from "sections/subscription-form.vue";

    export default {
        mixins: [
            BasePageMixin
        ],

        components: {
            "subscription-form": SubscriptionFormSection
        }
    };
</script>
