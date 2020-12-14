import Vue from 'vue';
import draggable from 'vuedraggable';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import MultiSelect from 'vue-multiselect'

window.axios = require('axios');

Vue.use(BootstrapVue);
Vue.use(IconsPlugin);

Vue.component('multiselect', MultiSelect);
Vue.component('draggable', draggable);
Vue.component('media-selector', require('./components/media-selector.vue').default);
Vue.component('nested', require('./components/nested.vue').default);
Vue.component('list', require('./components/list.vue').default);
Vue.component('aqua-park', require('./components/aqua-park.vue').default);
Vue.component('legend-info', require('./components/legend-info.vue').default);
Vue.component('gallery', require('./components/gallery.vue').default);
Vue.component('map-gallery', require('./components/map-gallery.vue').default);
Vue.component('price', require('./components/price.vue').default);
Vue.component('icons', require('./components/icons.vue').default);
Vue.component('pass', require('./components/pass.vue').default);

const app = new Vue({
    el: '#app'
});

