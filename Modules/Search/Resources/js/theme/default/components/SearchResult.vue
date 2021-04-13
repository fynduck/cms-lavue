<template>
    <section class="container">
        <div v-for="item in items" class="mb-4 search_item">
            <router-link :to="item.link">
                {{ item.title }}
            </router-link>
            <div class="mt-1" v-if="item.description">
                {{ item.description }}
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: "SearchResult",
    props: {
        items: {
            type: Object | Array,
            required: true
        }
    }
}
</script>