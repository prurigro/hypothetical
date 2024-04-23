export default {
    data() {
        return {
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

        updateMetadata(meta) {
            let metaContent;

            document.title = meta.title;
            $("link[rel=canonical]").attr("href", this.fullPath);

            Object.keys(this.metaTags).forEach((name) => {
                switch (this.metaTags[name][1]) {
                    case "title":
                        metaContent = meta.title;
                        break;
                    case "description":
                        metaContent = meta.description;
                        break;
                    case "keywords":
                        metaContent = meta.keywords;
                        break;
                    case "url":
                        metaContent = this.fullPath;
                        break;
                    default:
                        metaContent = "";
                }

                this.updateMetaTag(this.metaTags[name][0], name, metaContent);
            });
        },

        fetchMetadata() {
            this.$http.post(`/api/meta${env.apiToken}`, { path: this.$route.path }).then((response) => {
                this.updateMetadata(response.data);
            }).catch((error) => {
                console.log("error fetching metadata");
                this.updateMetadata({ title: appName, description: "", keywords: "" });
            });
        }
    },

    created() {
        // Don't fetch metadata on the first page load as this is handled by the page render
        if (this.$store.getters.getFirstPage) {
            this.$store.commit("setFirstPage", false);
        } else {
            this.fetchMetadata();
        }
    }
};
