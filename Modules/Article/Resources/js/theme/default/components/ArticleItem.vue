<template>
    <div class="article">
        <router-link :to="item.link">
            <figure>
                <img v-lazy.container="item.imgObj" :srcset="item.srcset" class="lazy-img"
                     lazy="loading" :alt="item.title">
            </figure>
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
    }
}
</script>