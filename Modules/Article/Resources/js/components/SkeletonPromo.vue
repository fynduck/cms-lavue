<template>
    <div v-if="loading" class="row">
        <div class="col-lg-6" v-for="item in rows">
            <article class="article_sk">
                <figure></figure>
                <div class="title_sk"></div>
                <div class="desc_sk"></div>
                <div class="date_sk">
                    <div></div>
                    <div></div>
                </div>
            </article>
        </div>
    </div>
</template>

<script>
    export default {
        name: "SkeletonPromo",
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
    animated-background($h = 100px, $w = 100%)
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
        animated-background(300px, 100%)
        margin-bottom .5rem

    .title_sk
        animated-background(50px, 100%)
        margin-bottom 1rem

    .desc_sk
        animated-background(70px, 100%)
        margin-bottom 1rem

    .date_sk
        display flex
        justify-content space-between

        div
            animated-background(50px, 48%)
            margin-bottom 2rem

            &:last-child
                margin-left 1rem

    .article_sk
        width 100%
        position relative
        margin-bottom 1, 5rem
        min-height auto !important

    @keyframes placeHolderShimmer
        0%
            background-position -468px 0
        100%
            background-position 468px 0

</style>
