<template>
    <div class="row" v-if="loading">
        <div class="col-md-6 col-lg-4" v-for="item in rows">
            <article class="article_sk">
                <figure></figure>
                <h2 class="title_sk"></h2>
                <div class="date_sk"></div>
            </article>
        </div>
    </div>
</template>

<script>
export default {
    name: "SkeletonArticle",
    props: {
        loading: {
            type: Boolean,
            default: true
        },
        rows: {
            type: Number,
            default: 3
        }
    }
}
</script>

<style scoped lang="stylus">
animatedBackground($h = 100px, $w = 100%)
    animation-duration 1s
    animation-fill-mode forwards
    animation-iteration-count infinite
    animation-name placeHolderShimmer
    animation-timing-function linear
    background #f6f7f8
    background linear-gradient(to right, #eeeeee 8%, #dddddd 18%, #eeeeee 33%)
    background-size 800px 104px
    height $h
    width $w
    position relative

figure
    animatedBackground(200px, 100%)
    margin-bottom .5rem

.title_sk
    animatedBackground(90px, 100%)
    margin-bottom 1rem

.date_sk
    animatedBackground(60px, 100%)
    margin-bottom 2rem

.article_sk
    width 100%
    position relative
    margin-bottom 7.5rem
    min-height auto !important

@keyframes placeHolderShimmer
    0%
        background-position -468px 0
    100%
        background-position 468px 0

</style>
