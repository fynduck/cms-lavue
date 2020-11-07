<template>
	<div v-if="loading">
		<div class="row">
			<div class="col-sm-6 col-lg-4 mb-4" v-for="n in rows">
				<div class="sk_media">
					<figure></figure>
					<div class="title">
						<div></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
    export default {
        name: "SkeletonMedia",
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

	.sk_media
		width 100%
		position relative
		margin-bottom 2rem

		figure
			width 100%
			min-height 150px
			margin-bottom 0
			animated-background(200px, 100%)

		.title
			margin .5rem 5%

			div
				animated-background(30px)

	@keyframes placeHolderShimmer
		0%
			background-position -468px 0
		100%
			background-position 468px 0

</style>
