<template>
    <button class="scroll-top" :class="{ right: right }" v-html="word" @click.prevent="scrollUp" v-show="visible"></button>
</template>

<script>
    export default {
        name: "ScrollTop",
        props: {
            word: {
                type: String,
                default: 'Top'
            },
            behavior: {
                type: String,
                default: 'smooth'
            },
            position: {
                type: String,
                default: 'right'
            },
            right: {
                type: Boolean,
                default: false
            },
            top: {
                type: Number,
                default: 0
            },
            visibleOffset: {
                type: Number,
                default: 200
            }
        },
        data() {
            return {
                visible: false
            }
        },
        mounted() {
            window.addEventListener('scroll', this.catchScroll)
        },
        methods: {
            catchScroll() {
                this.visible = window.pageYOffset > parseInt(this.visibleOffset);
            },

            scrollUp() {
                window.scrollTo({
                    'behavior': 'smooth',
                    'top': this.top
                });
            }
        }
    }
</script>

<style lang="stylus" scoped>
    .scroll-top
        position fixed
        bottom 2rem
        cursor pointer
        padding 1rem
        outline none
        width 50px
        height 50px
        display flex
        justify-content center
        align-items center
        z-index 10

        &.right
            right 2rem
</style>
