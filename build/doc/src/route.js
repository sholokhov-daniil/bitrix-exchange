import {createRouter, createWebHashHistory} from 'vue-router'
import {Routes} from "@/utils/route/loader";

const routes = Routes;
routes.push(
    {
        path: '/:catchAll(.*)',
        name: 'NotFound',
        component: () => import('@/pages/NotFound.vue')
    }
)

export const route = createRouter({
    history: createWebHashHistory(process.env.BASE_URL),
    routes: routes,
});
