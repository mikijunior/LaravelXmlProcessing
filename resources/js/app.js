window.Vue = require('vue');
require('./bootstrap');

import { BootstrapVue } from 'bootstrap-vue'

Vue.use(BootstrapVue)


import UploadFile from "./components/UploadFile";

const app = new Vue({
    el: '#app',
    components: {
        UploadFile,
    }
});

