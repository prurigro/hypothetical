<template>
    <div class="blog-page-component">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-10 offset-lg-1 col-xl-8 offset-xl-2 col-xxl-6 offset-xxl-3">
                    <h1>Blog</h1>

                    <div
                        v-for="entry in blogEntries"
                        class="blog-entry">

                        <div
                            v-if="entry.headerimage !== ''"
                            class="blog-entry-header-image"
                            :style="{ backgroundImage: 'url(' + imageType(entry.headerimage) + ')' }">
                        </div>

                        <div class="blog-entry-content">
                            <h2 class="blog-entry-content-title">{{ entry.title }}</h2>

                            <div class="blog-entry-content-info">
                                <span class="blog-entry-content-info-name">{{ entry.username }}</span> |
                                <span class="blog-entry-content-info-date">{{ entry.date }}</span>
                            </div>

                            <div
                                class="blog-entry-content-body"
                                v-html="entry.body">
                            </div>

                            <div class="blog-entry-content-taglist">
                                <span
                                    v-for="tag in entry.tags"
                                    class="blog-entry-content-taglist-item">

                                    {{ tag }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                blogEntries: []
            };
        },

        methods: {
            populateBlogEntries() {
                this.$http.get("/api/blog-entries" + env.apiToken).then((response) => {
                    this.blogEntries = response.body;
                }, (response) => {
                    console.log("Failed to retrieve blog entries");
                });
            }
        },

        created() {
            this.populateBlogEntries();
        }
    };
</script>
