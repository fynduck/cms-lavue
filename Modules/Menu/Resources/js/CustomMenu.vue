<template>
    <div class="d-flex flex-column flex-sm-row flex-ml-column justify-content-between align-items-center">
        <div v-for="item in items" v-if="items.length" :style="generateStyle(item)" class="custom_menu_item">
            <a :href="item.link" :target="item.target">
                {{ item.title }}
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "CustomMenu",
        props: {
            source: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                items: []
            }
        },
        mounted() {
            this.getItems();
        },
        methods: {
            getItems() {
                axios.get(this.source).then((response) => {
                    this.items = response.data.data;
                }).catch((error) => {
                    console.log(error);
                });
            },
            generateStyle(item) {
                let styles = '';
                if (item.image)
                    styles = `background-image: url(${item.image});`;

                if (item.color)
                    styles += `background-color: ${item.color}`;

                return styles;
            }
        }
    }
</script>