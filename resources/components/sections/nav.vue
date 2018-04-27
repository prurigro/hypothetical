<template>
    <nav class="nav-section-component">
        <div class="nav-section-component-mobile-header">
            <button
                class="nav-section-component-mobile-header-toggle"
                :class="{ open: openNav }"
                @click="openNav = !openNav">

                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="sr-only">Toggle navigation</span>
            </button>
        </div>

        <div class="nav-section-component-links" :class="{ open: openNav }">
            <div class="nav-section-component-links-wrapper">
                <router-link
                    v-for="link in navLinks"
                    class="navlink"
                    :to="link.path"
                    exact>

                    <span>{{ link.title }}</span>
                </router-link>
            </div>
        </div>

        <router-link class="nav-section-component-logo" to="/" exact>
            <img src="/img/logo.png" />
        </router-link>
    </nav>
</template>

<script>
    export default {
        data() {
            return {
                openNav: false,

                navLinks: [
                    { path: "/", title: "Home" },
                    { path: "/blog", title: "Blog" },
                    { path: "/contact", title: "Contact" }
                ]
            };
        },

        watch: {
            "$route"(to, from) {
                this.openNav = false;
            }
        }
    };
</script>
