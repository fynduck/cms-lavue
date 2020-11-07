<template>
    <ul class="nav_side sidebar-menu">
        <li class="list" v-for="n in rows">
            <span class="icon"></span>
            <span class="text"></span>
        </li>
    </ul>
</template>

<script>
    export default {
        name: "SkeletonSideBar",
        props: {
            rows: {
                type: Number,
                default: 20
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
        background linear-gradient(to right, #fff 8%, #4ae387 18%, #fff  33%)
        background-size 800px 104px
        height $h
        width $w
        position relative

    .list
        margin-bottom .4rem
        margin-left .3rem
        padding .3rem 1rem
        .icon
            display inline-block
            margin-right 10px
            animated-background(10px, 20px)
        .text
            display inline-block
            animated-background(10px, 140px)

    @keyframes placeHolderShimmer
        0%
            background-position -468px 0
        100%
            background-position 468px 0
</style>