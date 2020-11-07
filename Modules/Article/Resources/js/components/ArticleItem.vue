<template>
    <div class="article">
        <a :href="item.link">
            <figure>
                <img v-lazy.container="item.imgObj" :data-srcset="item.srcset" :alt="item.title" class="lazy-img">
            </figure>
        </a>
        <div class="info">
            <a :href="item.link" class="title">{{ item.title }}</a>
<!--            <div class="desc">{{ item.description }}</div>-->
        </div>
    </div>
</template>

<script>
    export default {
        name: "Article",
        props: {
            item: {
                type: Object,
                required: true
            },
            trans: {}
        }
    }
</script>