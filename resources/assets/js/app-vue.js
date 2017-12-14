// Determine whether to use vue.js in debug or production mode
const Vue = env.debug ? require("vue/dist/vue.js") : require("vue/dist/vue.min.js");

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

// Import page components
import HomePage from "pages/home.vue";
import ContactPage from "pages/contact.vue";
import Error404Page from "pages/error404.vue";

// Import section components
import NavSection from "sections/nav.vue";
import FooterSection from "sections/footer.vue";

// Name the nav and footer components so they can be used globally
Vue.component("nav-component", NavSection);
Vue.component("footer-component", FooterSection);

// Create a router instance
const router = new VueRouter({
    mode: "history",
    linkActiveClass: "active",
    root: "/",

    routes: [
        { path: "/", component: HomePage },
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
        firstLoad: true,
        lastPath: ""
    },

    getters: {
        getAppName: state => {
            return state.appName;
        },

        getFirstLoad: state => {
            return state.firstLoad;
        },

        getLastPath: state => {
            return state.lastPath;
        }
    },

    mutations: {
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
}).$mount("#page-content");
