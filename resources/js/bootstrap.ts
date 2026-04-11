import * as _ from 'lodash-es';
import axios, { type AxiosStatic } from 'axios';

declare global {
    interface Window {
        _: typeof _;
        axios: AxiosStatic;
    }
}

if (typeof window !== 'undefined') {
    // @ts-ignore Let Laravel mix/Vite bindings bypass strict lodash typing here
    window._ = _ as any;
    window.axios = axios;
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
