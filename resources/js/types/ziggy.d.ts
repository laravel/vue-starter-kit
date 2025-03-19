import { Router as ZiggyRouter, RouteParams } from 'ziggy-js';

type AppRouter = {
    (): ZiggyRouter;
    (name: string, params?: RouteParams<typeof name> | undefined, absolute?: boolean): string;
};

declare global {
    // eslint-disable-next-line no-var
    var route: AppRouter;
}
