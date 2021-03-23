require('dotenv').config()
const {join} = require('path')
const {copySync, removeSync} = require('fs-extra')

module.exports = {
    server: {
        port: 9000
    },
    // mode: 'spa',

    srcDir: __dirname,
    buildDir: '.nuxt/front',

    env: {
        apiUrl: process.env.API_URL || process.env.APP_URL + '/api',
        appName: process.env.APP_NAME || 'CMS-lava',
        appLocale: process.env.APP_LOCALE || 'en',
        appTheme: process.env.THEME || 'default'
    },

    head: {
        title: process.env.APP_NAME,
        titleTemplate: '%s - ' + process.env.APP_NAME,
        meta: [
            {charset: 'utf-8'},
            {name: 'viewport', content: 'width=device-width, initial-scale=1, shrink-to-fit=no'},
            {hid: 'description', name: 'description', content: ''},
            {hid: 'keywords', name: 'keywords', content: ''}
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
        {src: '~assets/stylus/theme/' + process.env.THEME + '/styles.styl', lang: 'stylus', mode: 'client'},
        {src: '~static/fontawesome/css/all.min.css', mode: 'client'}
    ],

    purgeCSS: {
        enabled: true
    },
    plugins: [
        '~components/global',
        '~plugins/i18n',
        '~plugins/axios',
        {src: '~plugins/bootstrap', mode: 'client'},
        {src: '~plugins/lazyImage', mode: 'client'}
    ],

    modules: [
        '@nuxtjs/router',
        'nuxt-moment',
        '@nuxtjs/style-resources',
        'nuxt-font-loader',
    ],

    styleResources: {
        stylus: [
            '~assets/stylus/theme/' + process.env.THEME + '/_variables.styl',
            '~assets/stylus/theme/' + process.env.THEME + '/_breakpoints.styl',
            '~assets/stylus/_keyframes.styl',
            '~assets/stylus/_blanks.styl',
            '../Modules/*/Resources/stylus/theme/' + process.env.THEME + '/*.styl'
        ]
    },

    fontLoader: {
        url: {
            google: 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap',
        },
        prefetch: true,
        preconnect: true
    },

    build: {
        extractCSS: true,
        cssSourceMap: false,
        optimization: {
            splitChunks: {
                cacheGroups: {
                    styles: {
                        name: 'styles',
                        test: /\.(css|vue|styl|scss)$/,
                        chunks: 'all',
                        enforce: true
                    }
                }
            }
        },
        extend(config, ctx) {
            config.resolve.alias["vue"] = "vue/dist/vue.common";
        },
        babel: { compact: true }
    },

    render: {
        bundleRenderer: {
            shouldPreload: (file, type) => {
                return ['font'].includes(type)
            }
        }

    },

    hooks: {
        generate: {
            done(generator) {
                // Copy dist files to public/front/_nuxt
                if (generator.nuxt.options.dev === false && generator.nuxt.options.mode === 'spa') {
                    const publicDir = join(generator.nuxt.options.rootDir, 'public/front', '_nuxt')
                    removeSync(publicDir)
                    copySync(join(generator.nuxt.options.generate.dir, '_nuxt'), publicDir)
                    copySync(join(generator.nuxt.options.generate.dir, '200.html'), join(publicDir, 'index.html'))
                    removeSync(generator.nuxt.options.generate.dir)
                }
            }
        }
    }
}
