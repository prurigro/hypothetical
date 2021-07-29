export default {
    methods: {
        imageType(image) {
            if (this.$store.getters.getSupportsWebP === true) {
                return image.replace(/\.(png|jpg)/, ".webp");
            } else if (this.$store.getters.getSupportsWebP === false) {
                return image;
            } else {
                return "";
            }
        }
    }
};
