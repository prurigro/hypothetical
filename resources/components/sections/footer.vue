<template>
    <footer class="footer-section-component">
        &copy; {{ new Date().getFullYear() }} <span v-html="$store.getters.getAppName"></span>
    </footer>
</template>
