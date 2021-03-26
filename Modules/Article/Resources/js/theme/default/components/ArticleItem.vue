<template>
    <div class="article">
        <router-link :to="item.link">
            <picture>
                <source :srcset="linkToImg(srcset)" :media="mediaWidth(srcset, srcKey)" v-for="(srcset, srcKey) in item.srcset">
                <img v-lazy.container="item.imgObj.src" class="lazy-img" lazy="loading" :alt="item.title">
            </picture>
        </router-link>
        <div class="info">
            <router-link :to="item.link">
                <div class="title">
                    {{ item.title }}
                </div>
                <time :datetime="$moment(item.date).format()" class="time_views">
                    <i class="far fa-clock"></i> {{ item.show_date }}
                </time>
                <span class="time_views">
                <i class="far fa-eye"></i> <span>{{ item.views }}</span>
            </span>
                <div v-if="item.desc" class="desc mt-2">{{ item.desc }}</div>
            </router-link>
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
    },
    methods: {
        linkToImg(item) {
            return item.split(' ')[0]
        },
        mediaWidth(item, index) {

            if (index === 0) {
                return null
            }
            const width = item.split(' ')[1].replace('w', '');

            return `(max-width: ${width}px)`
        }
    }
}
</script>