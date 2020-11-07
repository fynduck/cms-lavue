<template>
	<div class="user_sk" v-if="loading">
		<figure></figure>
		<div class="info"></div>
		<div class="info"></div>
		<div class="info"></div>
		<div class="info"></div>
		<div class="btn_sk"></div>
		<div class="btn_sk"></div>
	</div>
</template>

<script>
    export default {
        name: "SkeletonUserCard",
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

	.user_sk
		figure
			animated-background(180px, 100%)

		.info
			animated-background(30px, 60%)
			margin-bottom 1rem

	@keyframes placeHolderShimmer
		0%
			background-position -468px 0
		100%
			background-position 468px 0

</style>
