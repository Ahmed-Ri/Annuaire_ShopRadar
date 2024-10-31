import './bootstrap';
import Alpine from 'alpinejs';
import { createApp } from 'vue';
import vuetify from './vuetify'; 
import UserApp from './UserApp.vue';
import AdminApp from './AdminApp.vue';
import 'vue-multiselect/dist/vue-multiselect.css';
import '@fortawesome/fontawesome-free/css/all.min.css';




window.Alpine = Alpine;

Alpine.start();


if (document.getElementById('user-app')) {
    const userApp = createApp(UserApp);  
    userApp.use(vuetify); 
    userApp.mount('#user-app');
}
if (document.getElementById('admin-app')) {
    const adminApp = createApp(AdminApp);
    adminApp.use(vuetify);     
    adminApp.mount('#admin-app');
}