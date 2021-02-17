<template>
    <div class="container">
        <div v-if="error.statusCode === 404">
            <div v-if="error.page">
                <h1 class="title_page my-4">{{ title }}</h1>
                <div v-if="description" v-html="description"></div>
            </div>
            <div v-else-if="error.message" class="text-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="90" height="90" fill="#DBE1EC" viewBox="0 0 48 48">
                    <path
                        d="M22 30h4v4h-4zm0-16h4v12h-4zm1.99-10C12.94 4 4 12.95 4 24s8.94 20 19.99 20S44 35.05 44 24 35.04 4 23.99 4zM24 40c-8.84 0-16-7.16-16-16S15.16 8 24 8s16 7.16 16 16-7.16 16-16 16z"/>
                </svg>
                {{ error.message }}
            </div>
            <div v-else class="text-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="90" height="90" fill="#DBE1EC" viewBox="0 0 48 48">
                    <path
                        d="M22 30h4v4h-4zm0-16h4v12h-4zm1.99-10C12.94 4 4 12.95 4 24s8.94 20 19.99 20S44 35.05 44 24 35.04 4 23.99 4zM24 40c-8.84 0-16-7.16-16-16S15.16 8 24 8s16 7.16 16 16-7.16 16-16 16z"/>
                </svg>
                {{ $t('page_not_found')}}
            </div>
            <div class="text-center">
                <NuxtLink to="/">{{ $t('go_home') }}</NuxtLink>
            </div>
        </div>
        <div v-else>
            <h1 v-if="error.message">{{ error.message }}</h1>
            <h1 class="my-5" v-else>{{ title }}</h1>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        error: {
            type: Object,
            default: null
        }
    },
    layout: 'error',
    head() {
        return {
            title: this.title || 'a',
        }
    },
    computed: {
        statusCode() {
            return (this.error && this.error.statusCode) || 500
        },
        message() {
            return this.error.message || '<%= messages.client_error %>'
        },
        description() {
            return this.error.page.description.replace(/<p>\s*<\/p>/gi, "");
        },
        title() {
            return this.error.page.title || (this.error.statusCode === 404 ? 'Not found' : 'An error occurred');
        }
    }
}
</script>