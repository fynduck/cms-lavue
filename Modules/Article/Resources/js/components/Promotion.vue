<template>
    <article class="col-lg-6 mb-4">
        <a :href="item.link">
            <figure class="mb-0">
                <img v-lazy.container="item.imgPromoObj" :data-srcset="item.srcset" :alt="item.title" class="lazy-img">
            </figure>
        </a>
        <div class="promo_info">
            <div class="title">{{ item.title }}</div>
            <div class="desc">{{ item.description }}</div>
            <div class="d-flex justify-content-between flex-wrap">
                <div class="promo_time">
                    <div class="title">{{ trans.before_end }}</div>
                    <time-down :end-time="item.promo_finish_date"
                               :finish-txt="trans.finished"
                               :second="false"
                               :labels="labels"
                               v-if="item.promo_finish_date"/>
                    <div class="finished" v-else-if="item.date_to">{{ trans.finished }}</div>
                    <div class="indefinitely" v-else>{{ trans.indefinitely }}</div>
                </div>
                <div class="go">
                    <a :href="item.link">{{ trans.detailed }}</a>
                </div>
            </div>
        </div>
    </article>
</template>

<script>
    import TimeDown from "../../../../../resources/js/components/TimeDown";

    export default {
        name: "Promotion",
        components: {
            TimeDown
        },
        props: {
            item: {
                type: Object,
                required: true
            },
            trans: {
                type: Object,
                required: true
            }
        },
        computed: {
            labels() {
                return {
                    days: this.trans.days,
                    hours: this.trans.hours,
                    minutes: this.trans.minutes,
                    seconds: this.trans.seconds
                };
            }
        }
    }
</script>