export default {
    data() {
        return {
            metaTitle: "",
            metaDescription: "",
            metaKeywords: "",

            metaTags: {
                "title": [ "name", "title" ],
                "description": [ "name", "description" ],
                "keywords": [ "name", "keywords" ],
                "dc:title": [ "name", "title" ],
                "dc:description": [ "name", "description" ],
                "og:title": [ "property", "title" ],
                "og:description": [ "property", "description" ],
                "og:url": [ "property", "url" ],
                "twitter:title": [ "name", "title" ],
                "twitter:description": [ "name", "description" ]
            }
        };
    },

    computed: {
        pageTitle() {
            return this.metaTitle === "" ? env.appName : `${this.metaTitle} | ${env.appName}`;
        },

        pageDescription() {
            return this.metaDescription === "" ? env.appDesc : this.metaDescription;
        },

        fullPath() {
            return document.location.origin + this.$route.path;
        }
    },

    methods: {
        updateMetaTag(name, attribute, content) {
            const $tag = $("meta[" + name + "=" + attribute.replace(/:/, "\\:") + "]");

            if ($tag.length) {
                $tag.attr("content", content);
            }
        },

        updateMetaData() {
            let metaContent;

            document.title = this.pageTitle;
            $("link[rel=canonical]").attr("href", this.fullPath);

            Object.keys(this.metaTags).forEach((name) => {
                switch (this.metaTags[name][1]) {
                    case "title":
                        metaContent = this.pageTitle;
                        break;
                    case "description":
                        metaContent = this.pageDescription;
                        break;
                    case "keywords":
                        metaContent = this.metaKeywords;
                        break;
                    case "url":
                        metaContent = this.fullPath;
                        break;
                    default:
                        metaContent = "";
                }

                this.updateMetaTag(this.metaTags[name][0], name, metaContent);
            });
        }
    },

    created() {
        this.updateMetaData();
    }
};
