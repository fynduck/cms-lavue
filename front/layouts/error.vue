<template>
    <div v-if="error.statusCode === 404">
        <div v-if="error.page">
            <h1 class="title_page my-4">{{ error.page.title }}</h1>
            <div v-if="description" v-html="description"></div>
        </div>
        <div v-else-if="error.message">{{ error.message }}</div>
        <div v-else>Page not found</div>
    </div>
    <div v-else>
        <h1 v-if="error.message">{{ error.message }}</h1>
        <h1 class="my-5" v-else>An error occurred</h1>
    </div>
</template>

<script>
export default {
    props: ['error'],
    layout: 'error',
    computed: {
        description() {
            return this.error.page.description.replace(/<p>\s*<\/p>/gi, "");
        }
    }
}
</script>