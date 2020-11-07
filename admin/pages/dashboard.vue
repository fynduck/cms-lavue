<template>
  <section id="content" class="">
    <div class="row">
      <div class="col-xs-12 col-md-10">
        <div class="pl15 pr50">
          <h4> {{ $t('welcome') }} </h4>
          <hr class="alt short">
          <p class="text-muted">
            Lorem ipsum dolor sit amet, is nisi ut aliquip ex ea commodo consectetur adipi sicing elit, sed do eiusmod
            tempor incididu ut labore et is nisi ut aliquip ex ea commodo dolore magna aliqua. Ut enim ad minim veniam, quis
            nostrud exetation ullamco laboris nisi ut aliquip ex ea commodo consequat.
          </p>
        </div>
      </div>
    </div>
    <!--    @if(auth()->user()->isAdmin())-->
    <!--    <div class="row">-->
    <!--      <div class="col-xs-12 col-md-10">-->
    <!--        <statistics source="{{ route('product-info') }}"></statistics>-->
    <!--      </div>-->
    <!--    </div>-->
    <!--    <hr class="my-5">-->
    <!--    <div class="row">-->
    <!--      <div class="col-xs-12 col-md-10">-->
    <!--        <search-info source="{{ route('search-info') }}"></search-info>-->
    <!--      </div>-->
    <!--    </div>-->
    <!--    @endif-->
  </section>
</template>

<script>
  export default {
    middleware: 'auth',
  }
</script>