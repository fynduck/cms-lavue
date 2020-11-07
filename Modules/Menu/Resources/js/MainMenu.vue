<template>
    <b-dropdown id="main_menu" :text="title" class="custom_menu" variant="left_menu">
        <b-dropdown-item v-for="(item, key) in items" :href="item.link" :target="item.target"
                         :rel="item.nofollow ? 'nofollow' : ''" :key="key">
            {{ item.title }}
        </b-dropdown-item>
    </b-dropdown>
</template>

<script>
    export default {
        name: "MainMenu",
        props: {
            source: {
                type: String,
                required: true,
            },
            page_id: {
                type: String | Number,
                required: true
            },
            type: {
                type: String,
                default: 'page'
            },
            position: String,
            img: String,
            title: String
        },
        data() {
            return {
                toggle_menu: 'show',
                items: []
            }
        },
        mounted() {
            this.getItems();
        },
        methods: {
            getItems() {
                let params = {
                    params: {
                        page_id: this.page_id,
                        type: this.type,
                        position: this.position
                    }
                };

                axios.get(this.source, params).then((response) => {
                    this.items = response.data.data;
                }).catch((error) => {
                    console.log(error);
                })
            }
        }
    }
</script>
