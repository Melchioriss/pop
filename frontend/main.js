import Vue from 'vue';

import router from './router';
import store from './store/index';

import App from './App.vue';

import VueCookie from 'vue-cookie';

Vue.use(VueCookie);

import './plugins/index'


const app = document.createElement('div');
app.id = 'app';

document.body.appendChild(app);

new Vue({
    el: '#app',
    render: h => h(App),
    router,
    store,
    components: {
        App
    },
    created () {
        this.$store.dispatch('loadProfile');
        this.$store.dispatch('loadGroups');
    }
});
