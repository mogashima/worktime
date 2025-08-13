import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'; // Vue Router を使う場合
import axios from './plugins/axios'; // Axiosのカスタム設定（任意）
import App from './App.vue';

const app = createApp(App);

// 必要に応じてグローバルプロパティ登録（例: axios）
app.config.globalProperties.$axios = axios;

app.use(createPinia())
app.use(router);
app.mount('#app');
