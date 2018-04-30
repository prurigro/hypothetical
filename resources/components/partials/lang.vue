<template>
    <span
        class="lang-component"
        v-html="getLanguageString">
    </span>
</template>

<script>
    export default {
        props: {
            cStrings: {
                type: Object
            }
        },

        computed: {
            getLanguageString() {
                if (this.cStrings.hasOwnProperty(this.$store.getters.getAppLang)) {
                    return this.cStrings[this.$store.getters.getAppLang];
                } else if (this.cStrings.hasOwnProperty(this.$store.getters.getAppDefaultLang)) {
                    return this.cStrings[this.$store.getters.getAppDefaultLang];
                } else {
                    return "";
                }
            }
        }
    };
</script>
