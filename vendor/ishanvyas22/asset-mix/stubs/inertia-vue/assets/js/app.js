import Vue from 'vue';
import VueMeta from 'vue-meta';
import PortalVue from 'portal-vue';
import { InertiaApp } from '@inertiajs/inertia-vue';

Vue.config.productionTip = false;

Vue.use(InertiaApp);
Vue.use(PortalVue);
Vue.use(VueMeta);

let app = document.getElementById('app');

new Vue({
    metaInfo: {
        titleTemplate: (title) => title ? `${title} - AppName` : 'AppName'
    },
    render: h => h(InertiaApp, {
        props: {
            initialPage: JSON.parse(app.dataset.page),
            resolveComponent: name => import(`@/Pages/${name}`).then(module => module.default),
        },
    }),
}).$mount(app);
