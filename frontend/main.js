import Vue from 'vue';
import VueCookie from 'vue-cookie';

import router from './router';
import store from './store/index';
import App from './App.vue';

Vue.use(VueCookie);

import './plugins/index'

const app = document.createElement('div');
app.id = 'app';

document.body.appendChild(app);

export default new Vue({
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
