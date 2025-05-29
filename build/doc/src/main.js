import {createApp} from 'vue'
import App from './App.vue';
import {route} from '@/route';
import { createVCodeBlock } from '@wdns/vue-code-block';

import './App.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'bootstrap/dist/css/bootstrap.min.css'

import 'prismjs';
import 'prismjs/components/prism-typescript';

const codeBlock = createVCodeBlock({});

const vue = createApp(App);
vue.use(route);
vue.use(codeBlock);

vue.mount('#bitrix-exchange-doc-app');