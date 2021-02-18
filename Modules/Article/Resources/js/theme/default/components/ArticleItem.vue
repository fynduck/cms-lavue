<template>
    <div class="card article">
        <router-link :to="item.link">
            <figure>
                <img v-lazy.container="item.imgObj" :data-srcset="item.srcset" :alt="item.title" class="lazy-img">
            </figure>
        </router-link>
        <div class="card-body">
            <div class="title">
                <router-link :to="item.link">
                    {{ item.title }}
                </router-link>
            </div>
            <div v-if="item.desc">{{ item.desc }}</div>
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
        }
    }
}
</script>