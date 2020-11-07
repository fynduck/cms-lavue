<template>
	<div class="form-row" v-if="loading">
		<div class="col-md-5 col-lg-4 mb-3">
			<figure></figure>
			<div class="sk_time_on_site">
				<span></span>
				<span></span>
			</div>
		</div>
		<div class="col-md-7 col-lg-8">
			<div class="form-group ml-md-3 ml-lg-0 sk_item">
				<div></div>
			</div>
			<div class="form-group ml-md-3 ml-lg-0 sk_item">
				<div></div>
				<div></div>
			</div>
			<div class="form-group ml-md-3 ml-lg-0 sk_item">
				<div></div>
				<div></div>
			</div>
			<div class="form-group ml-md-3 ml-lg-0 sk_item">
				<div></div>
				<div></div>
				<div></div>
			</div>
		</div>
	</div>
</template>

<script>
    export default {
        name: 'SkeletonSettings',
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

	.sk_item
		animated-background(30px, 100%)
		margin-bottom 2rem

	.sk_time_on_site
		animated-background(20px, 100%)
		margin-bottom 2rem

	figure
		width 100%
		position relative
		animated-background(30px, 100%)
		margin-bottom 2rem
		min-height 200px

	@keyframes placeHolderShimmer
		0%
			background-position -468px 0
		100%
			background-position 468px 0

</style>
