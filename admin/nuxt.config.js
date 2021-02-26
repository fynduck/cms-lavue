require('dotenv').config()
const {join} = require('path')
const {copySync, removeSync} = require('fs-extra')

module.exports = {
    server: {
        port: 9001
    },
    // mode: 'spa',

    srcDir: __dirname,
    buildDir: '.nuxt/admin',

    env: {
        apiUrl: process.env.API_URL || process.env.APP_URL + '/api',
        appName: process.env.APP_NAME || 'Admin',
        appLocale: process.env.APP_LOCALE || 'en'
    },

    head: {
        title: process.env.APP_NAME,
        titleTemplate: '%s - ' + process.env.APP_NAME,
        meta: [
            {charset: 'utf-8'},
            {name: 'viewport', content: 'width=device-width, initial-scale=1, shrink-to-fit=no'},
        ],
        link: [
            {rel: 'icon', type: 'image/x-icon', href: '/favicon.png'}
        ]
    },

    loading: {color: '#4ae387'},

    router: {
        middleware: ['locale', 'check-auth']
    },

    css: [
        {src: '~assets/sass/app.scss', lang: 'scss', mode: 'client'},
        {src: '~assets/stylus/admin.styl', lang: 'stylus', mode: 'client'},
        {src: '~static/fontawesome/css/all.min.css', mode: 'client'}
    ],

    plugins: [
        {src: '~components/global', mode: 'client'},
        '~plugins/i18n',
        '~plugins/vform',
        '~plugins/axios',
        '~plugins/fontawesome',
        {src: '~plugins/codemirror', mode: 'client'}
    ],

    modules: [
        '@nuxtjs/router',
        'bootstrap-vue/nuxt',
        'nuxt-moment',
        '@nuxtjs/toast'
    ],
    bootstrapVue: {
        bootstrapVueCSS: false,
        bootstrapCSS: false
    },
    toast: {
        position: 'top-right',
        register: [
            {
                name: 'success',
                message: message => message,
                options: {
                    theme: 'outline',
                    className: 'rounded py-3 px-4',
                    iconPack: 'fontawesome',
                    type: 'success',
                    duration: 2000,
                    icon: {
                        name: 'check-circle'
                    }
                }
            },
            {
                name: 'error',
                message: message => message,
                options: {
                    theme: 'outline',
                    className: 'rounded py-3 px-4',
                    iconPack: 'fontawesome',
                    type: 'error',
                    duration: 4000,
                    icon: {
                        name: 'exclamation'
                    }
                }
            }
        ]
    },
    purgeCSS: {
        enabled: true
    },
    build: {
        extractCSS: true,
        cssSourceMap: false,
        optimization: {
            splitChunks: {
                cacheGroups: {
                    styles: {
                        name: 'styles',
                        test: /\.(css|vue|styl)$/,
                        chunks: 'all',
                        enforce: true
                    }
                }
            }
        },
        babel: {compact: true}
    },

    hooks: {
        generate: {
            done(generator) {
                // Copy dist files to public/admin/_nuxt
                if (generator.nuxt.options.dev === false && generator.nuxt.options.mode === 'spa') {
                    const publicDir = join(generator.nuxt.options.rootDir, 'public/admin', '_nuxt')
                    removeSync(publicDir)
                    copySync(join(generator.nuxt.options.generate.dir, '_nuxt'), publicDir)
                    copySync(join(generator.nuxt.options.generate.dir, '200.html'), join(publicDir, 'index.html'))
                    removeSync(generator.nuxt.options.generate.dir)
                }
            }
        }
    }
}
