<template>
    <div class="article">
        <router-link :to="item.link">
            <figure>
                <img v-lazy.container="item.imgObj" :data-srcset="item.srcset" :alt="item.title" class="lazy-img">
            </figure>
        </router-link>
        <div class="info">
            <router-link :to="item.link" class="title">
                {{ item.title }}1
            </router-link>
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