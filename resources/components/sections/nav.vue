<template>
    <nav class="nav-section-component navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" id="navbar-toggle" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <router-link class="navbar-logo" to="/"></router-link>
            </div>

            <div id="navbar-collapse" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li class="navlink"><router-link to="/" title="Home">Home</router-link></li>
                    <li class="navlink"><router-link to="/contact" title="Contact">Contact</router-link></li>
                </ul>
            </div>
        </div>
    </nav>
</template>
