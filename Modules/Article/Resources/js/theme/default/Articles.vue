<template>
    <section v-if="item" class="container">
        <div class="row">
            <div class="col-lg-9">
                <h1 class="my-4 title_page">{{ item.title }}</h1>
                <time :datetime="$moment(item.date).format()" class="time_views">
                    <i class="far fa-clock"></i> {{ item.show_date }}
                </time>
                <span class="time_views">
                    <i class="far fa-eye"></i> <span>{{ item.views }}</span>
                </span>
                <figure class="mt-2">
                    <img v-lazy.container="item.imgObj" :data-srcset="item.srcset" :alt="item.title" class="lazy-img">
                </figure>
                <v-runtime-template :template="description" v-if="item.description"/>
            </div>
            <div class="col-lg-3 relates mt-4" v-if="relates.length">
                <h2 class="relate_news">{{ $t('Article.latest_news') }}</h2>
                <div class="row">
                    <div class="col-sm-6 col-md-4 col-lg-12 mb-4" v-for="relate in relates">
                        <article-item :item="relate"/>
                    </div>
                </div>
            </div>
        </div>
        <page-inner-slider class="my-5" :page-id="item.id" :page-type="page.module"></page-inner-slider>
    </section>
</template>

<script>
import axios from "axios";
import {mapGetters} from "vuex";
import VRuntimeTemplate from "v-runtime-template";
import ArticleItem from './components/ArticleItem';

export default {
    name: "Articles",
    components: {
        VRuntimeTemplate,
        ArticleItem,
        PageInnerSlider: () => import(`../../../../../Banner/Resources/js/theme/${process.env.appTheme}/PageInnerSlider`),
    },
    head() {
        return {
            title: this.meta.title,
            meta: [
                {hid: 'description', name: 'description', content: this.meta.description},
                {hid: 'keywords', name: 'keywords', content: this.meta.keywords}
            ]
        }
    },
    data() {
        return {
            meta: {
                title: '',
                description: '',
                keywords: ''
            },
            relates: []
        }
    },
    async fetch() {
        if (this.$route.params.category) {
            await this.$store.dispatch('page/setItem', null)
            await this.$store.dispatch('lang/setPageLang', {})
            const {data} = await axios.get(this.route('article', this.$route.params.category))
            if (data.data.method === 'not_found') {
                return this.$nuxt.error({statusCode: 404, message: data.data.title, page: data.data})
            }

            await this.$store.dispatch('page/setItem', data.data)

            if (typeof data.meta !== "undefined") {
                await this.$store.dispatch('page/setMeta', data.meta)
            }
            if (typeof data.meta !== "undefined") {
                this.meta.title = data.meta.meta_title
                this.meta.description = data.meta.meta_description
                this.meta.keywords = data.meta.meta_keywords
            }

            if (typeof data.relates !== "undefined") {
                this.relates = data.relates;
            }

            if (typeof data.page_lang !== "undefined") {
                await this.$store.dispatch('lang/setPageLang', data.page_lang)
            }
        }
    },
    computed: {
        ...mapGetters({
            item: 'page/item',
            page: 'page/page'
        }),
        description() {
            return this.item.description.replace(/<p>\s*<\/p>/gi, "");
        }
    }
}
</script>