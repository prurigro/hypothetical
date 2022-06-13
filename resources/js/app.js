// Import features from Vue
import { createApp, nextTick } from "vue";

// Initialize Vue
const Vue = createApp({});

// Import and configure axios
window.axios = require("axios");

window.axios.defaults.headers.common = {
    "X-Requested-With": "XMLHttpRequest",
    "X-CSRF-TOKEN": env.csrfToken
};

Vue.config.globalProperties.$http = window.axios;

// Import plugins
import { createRouter, createWebHistory } from "vue-router";
import { createStore } from "vuex";

// Import local javascript
import SupportsWebP from "imports/supports-webp.js";

// Import global mixins
import ImageType from "mixins/image-type.js";

// Register global mixins
Vue.mixin(ImageType);

// Import global components
import NavSection from "sections/nav.vue";
import FooterSection from "sections/footer.vue";
import Lang from "partials/lang.vue";

// Name the global components
Vue.component("nav-component", NavSection);
Vue.component("footer-component", FooterSection);
Vue.component("lang", Lang);

// Import page components
import HomePage from "pages/home.vue";
import BlogPage from "pages/blog.vue";
import ContactPage from "pages/contact.vue";
import Error404Page from "pages/error404.vue";

// Create a router instance
const router = new createRouter({
    history: createWebHistory(),
    linkActiveClass: "active",

    routes: [
        { path: "/", component: HomePage },
        { path: "/blog", component: BlogPage },
        { path: "/contact", component: ContactPage },
        { path: "/*", component: Error404Page }
    ],

    scrollBehavior(to, from, savedPosition) {
        if (to.hash) {
            return {
                selector: `[id='${to.hash.slice(1)}']`
            };
        } else {
            return { x: 0, y: 0 };
        }
    }
});

// Create a vuex store instance
const store = createStore({
    state: {
        appName: env.appName,
        appLang: env.appLang,
        appDefaultLang: env.appDefaultLang,
        firstLoad: true,
        lastPath: "",
        supportsWebP: null
    },

    getters: {
        getAppName: state => {
            return state.appName;
        },

        getAppLang: state => {
            return state.appLang;
        },

        getAppDefaultLang: state => {
            return state.appDefaultLang;
        },

        getFirstLoad: state => {
            return state.firstLoad;
        },

        getLastPath: state => {
            return state.lastPath;
        },

        getSupportsWebP: state => {
            return state.supportsWebP;
        }
    },

    mutations: {
        setAppLang(state, value) {
            state.appLang = value;
            Vue.http.get(`/language/${value}`);
        },

        setFirstLoad(state, value) {
            state.firstLoad = value;
        },

        setLastPath(state, value) {
            state.lastPath = value;
        },

        setSupportsWebP(state, value) {
            state.supportsWebP = value;
        }
    },

    actions: {

    }
});

// Detect webp support
SupportsWebP.detect(store);

// Functionality to run before page load and change
router.beforeEach((to, from, next) => {
    if (to.path !== store.getters.getLastPath) {
        if (store.getters.getFirstLoad) {
            next();
        } else {
            // Unfocused any focused elements
            if ("activeElement" in document) {
                document.activeElement.blur();
            }

            // Fade the page out and scroll when moving from one page to another
            TweenMax.to("#router-view", 0.25, {
                opacity: 0,
                onComplete: next
            });
        }
    }
});

// Functionality to run on page load and change
router.afterEach((to, from) => {
    if (to.path !== store.getters.getLastPath) {
        store.commit("setLastPath", to.path);

        if (store.getters.getFirstLoad) {
            // Set Page.firstLoad to false so we know the initial load has completed
            store.commit("setFirstLoad", false);
        } else {
            nextTick(() => {
                TweenMax.to("#router-view", 0.25, { opacity: 1 });
            });
        }
    }
});

Vue.use(router).use(store).mount("#vue-container");
