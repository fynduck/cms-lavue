<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <button class="close" type="button" @click="$emit('close')">
                        <span>&times;</span>
                    </button>
                    <div class="modal-header">
                        <slot name="header"></slot>
                    </div>
                    <div class="modal-body">
                        <slot name="body"></slot>
                    </div>
                    <div class="modal-footer">
                        <slot name="actionBtn"></slot>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
<style lang="stylus">
    .modal-mask
        position fixed
        z-index 9998
        top 0
        left 0
        width 100%
        height 100%
        background-color rgba(0, 0, 0, .5)
        display flex
        justify-content center
        align-items center
        transition opacity .3s ease

        .close
            position absolute
            right 10px
            top 10px
            cursor pointer
            background none
            border none
            z-index 1

            &:focus
                outline none

    .modal-wrapper
        display flex
        justify-content center
        align-content center

    .modal-container
        max-width 600px
        margin 0 1rem
        background-color #fff
        border-radius 2px
        box-shadow 0 2px 8px rgba(0, 0, 0, .33)
        transition all .3s ease
        position relative
        max-height 95vh
        overflow-y auto

    .modal-header
        margin-top 0
        color #2e334c
        border none

        &:empty
            display none

    .modal-body
        font-size 20px

    .modal-footer
        border none

        &:empty
            display none

    .modal-enter,
    .modal-leave-active
        opacity 0

        &.modal-container
            -webkit-transform scale(1.1)
            transform scale(1.1)

</style>
