import {route} from 'ziggy-js';
import { inject } from 'vue';

type Router = typeof route;

export function useRoute(): Router {
    const route = inject<Router>('route');

    if (!route) {
        throw new Error('Route is not provided. Make sure that you are using the ZiggyVue plugin correctly.');
    }

    return route;
}
