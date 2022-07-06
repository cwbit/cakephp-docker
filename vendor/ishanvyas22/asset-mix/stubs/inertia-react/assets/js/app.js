import axios from 'axios';
import React from 'react';
import { InertiaApp } from '@inertiajs/inertia-react';
import { render } from 'react-dom';

// Setup CSRF tokens.
axios.defaults.xsrfCookieName = 'csrfToken';
axios.defaults.xsrfHeaderName = 'X-Csrf-Token';

const el = document.getElementById('app');
if (!el) {
    throw new Error('Could not find application root element');
}

render(
    <InertiaApp
        initialPage={JSON.parse(el.dataset.page || '')}
        resolveComponent={(name) =>
            import(`./Pages/${name}`).then((module) => module.default)
        }
    />,
    el
);
