<template>
    <div v-if="items.length">
        <div class="title_category_menu">{{ title }}</div>
        <div class="item_category_menu">
            <a :href="item.link" :target="item.target" v-for="item in items" class="link_menu">
                {{ item.title }}
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "CategoryMenu",
        props: {
            source: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                title: '',
                items: []
            }
        },
        mounted() {
            this.getItems()
        },
        methods: {
            getItems() {
                axios.get(this.source).then(response => {
                    this.items = response.data.data;
                    this.title = response.data.trans.often_asked;
                }).catch((error) => {
                    console.log(error)
                })
            }
        }
    }
</script>