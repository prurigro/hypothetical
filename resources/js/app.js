// Initialize Vue
const Vue = require("vue/dist/vue.js");

// Import plugins
import VueRouter from "vue-router";
import VueResource from "vue-resource";
import Vuex from "vuex";
import { sync } from "vuex-router-sync";

// Load plugins
Vue.use(VueRouter);
Vue.use(VueResource);
Vue.use(Vuex);

// CSRF prevention header
Vue.http.headers.common["X-CSRF-TOKEN"] = env.csrfToken;

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
const router = new VueRouter({
    mode: "history",
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
const store = new Vuex.Store({
    state: {
        appName: env.appName,
        appLang: env.appLang,
        appDefaultLang: env.appDefaultLang,
        firstLoad: true,
        lastPath: ""
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
        }
    },

    actions: {

    }
});

// Sync vue-router-sync with vuex store
sync(store, router);

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
            Vue.nextTick(() => {
                TweenMax.to("#router-view", 0.25, { opacity: 1 });
            });
        }
    }
});

const App = new Vue({
    router,
    store
}).$mount("#vue-container");
