import {createRouter, createWebHashHistory} from 'vue-router'
import {Routes} from "@/utils/route/loader";

export const route = createRouter({
    history: createWebHashHistory(process.env.BASE_URL),
    routes: Routes,
});
