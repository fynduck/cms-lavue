<template>
    <div id="top_carouse" class="carousel carousel-dark slide" data-bs-ride="carousel"
         :data-bs-interval="carouselSettings.interval" v-if="items.length">
        <div class="carousel-indicators" v-if="carouselSettings.indicators">
            <button type="button" data-bs-target="#top_carouse" :data-bs-slide-to="index" class="active"
                    v-for="(item, index) in items"></button>
        </div>
        <div class="carousel-inner">
            <div :class="{'carousel-item': true, 'active': index === 0}" v-for="(item, index) in items">
                <img class="d-block lazy-img"
                     v-lazy="item.slide"
                     :src="item.slide.loading"
                     :srcset="showSrcset(item)"
                     :alt="item.title"
                     lazy="loading">
                <div class="carousel-caption d-none d-md-block" v-if="item.description" v-html="item.description"></div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#top_carouse" data-bs-slide="prev"
                v-if="carouselSettings.nav">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#top_carouse" data-bs-slide="next"
                v-if="carouselSettings.nav">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "SliderTop",
    props: {
        pageId: {
            type: Number,
            required: true
        },
        page_type: {
            type: String,
            default: 'page'
        }
    },
    computed: {
        showIndicators() {
            return this.items.length > 1
        },
        source() {
            return '/get-slides'
        },
    },
    data() {
        return {
            items: [],
            carouselSettings: []
        }
    },
    async fetch() {
        let data = {
            params: {
                page_id: this.pageId,
                type: this.page_type,
                position: 'top'
            }
        };
        await axios.get(this.source, data).then(response => {
            this.items = response.data.data;
            if (typeof response.data.carousel_settings !== "undefined") {
                this.carouselSettings = response.data.carousel_settings
            }
        }).catch(() => {
        });
    },

    methods: {
        toLink(link) {
            if (link)
                location.href = link
        },
        showSrcset(item) {
            let srcset = item.srcset;
            if (!process.server && item.mobile_srcset.length) {
                let mobileSizes = [];
                item.mobile_srcset.forEach(item => {
                    let itemSplit = item.split(' ')
                    if (itemSplit.length > 1) {
                        mobileSizes.push(parseInt(itemSplit[1]))
                    }
                })

                mobileSizes.sort(function (a, b) {
                    return a - b;
                });

                if (window.innerWidth <= mobileSizes[mobileSizes.length - 1]) {
                    srcset = item.mobile_srcset
                }
            }

            return srcset
        }
    }
}
</script>