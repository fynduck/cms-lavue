import axios from 'axios'

/**
 * Get cookie from request.
 *
 * @param  {Object} req
 * @param  {String} key
 * @return {String|undefined}
 */
export function cookieFromRequest(req, key) {
    if (!req.headers.cookie) {
        return
    }

    const cookie = req.headers.cookie.split(';').find(
        c => c.trim().startsWith(`${key}=`)
    )

    if (cookie) {
        return cookie.split('=')[1]
    }
}

/**
 * https://router.vuejs.org/en/advanced/scroll-behavior.html
 */
export function scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
        return savedPosition
    }

    let position = {}

    if (to.matched.length < 2) {
        position = {x: 0, y: 0}
    } else if (to.matched.some(r => r.components.default.options.scrollToTop)) {
        position = {x: 0, y: 0}
    }
    if (to.hash) {
        position = {selector: to.hash}
    }

    return position
}

export function moduleIconsSort(name, key) {
    const data = {
        'dashboard': {
            'iconClass': ['fas', 'tachometer-alt'],
            'sort': 1
        },
        'menu': {
            'iconClass': ['fas', 'route'],
            'sort': 3
        },
        'article': {
            'iconClass': ['far', 'newspaper'],
            'sort': 5
        },
        'page': {
            'iconClass': ['fas', 'scroll'],
            'sort': 7
        },
        'custom_form': {
            'iconClass': ['far', 'list-alt'],
            'sort': 9
        },
        'language': {
            'iconClass': ['fas', 'globe'],
            'sort': 11
        },
        'user_group': {
            'iconClass': ['fas', 'user-tag'],
            'sort': 13
        },
        'user': {
            'iconClass': ['fas', 'users'],
            'sort': 14
        },
        'translate': {
            'iconClass': ['fas', 'language'],
            'sort': 15
        },
        'redirect': {
            'iconClass': ['fas', 'exchange-alt'],
            'sort': 17
        },
        'module': {
            'iconClass': ['fas', 'puzzle-piece'],
            'sort': 17
        },
        'settings': {
            'iconClass': ['fas', 'cogs'],
            'sort': 50
        }
    }

    return typeof data[name] !== "undefined" && typeof data[name][key] !== "undefined" ? data[name][key] : null
}

export function transSlug(title) {
    return axios.options('/admin/trans-slug', {params: {txt: title}});
}

export function checkPermission(routeName, permissions) {
    if (routeName === 'settings-site')
        routeName = 'settings'

    const arrayName = routeName.split('.');
    const right = arrayName[arrayName.length - 1];
    arrayName.pop()

    return !!(typeof permissions[arrayName.join('.')] !== "undefined" && permissions[arrayName.join('.')].includes(right));
}