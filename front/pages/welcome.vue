<template>
  <div>
    <h1 class="text-center my-4 title_page">Забронировать с нами легко</h1>
    <div class="row">
      <div class="col-lg-4 mb-3 mb-lg-0 text-center">
        <div class="px-0 px-lg-3">
          <div class="icon-rounded mb-3">
            <i data-fa-symbol="map" class="fas fa-map fa-fw"></i>
            <fa :icon="['fas', 'map']"/>
          </div>
          <h3 class="h5">Найти идеальный прокат</h3>
          <p class="text-muted">Однажды утром, когда Грегор Самса проснулся от беспокойных снов, он обнаружил, что
            превратился в своей постели в</p>
        </div>
      </div>
      <div class="col-lg-4 mb-3 mb-lg-0 text-center">
        <div class="px-0 px-lg-3">
          <div class="icon-rounded mb-3">
            <fa :icon="['far', 'credit-card']"/>
          </div>
          <h3 class="h5">Бронируйте с уверенностью</h3>
          <p class="text-muted">Постельные принадлежности едва могли прикрыть его и, казалось, были готовы соскользнуть
            в любой момент. Его много ног, яма</p>
        </div>
      </div>
      <div class="col-lg-4 mb-3 mb-lg-0 text-center">
        <div class="px-0 px-lg-3">
          <div class="icon-rounded mb-3">
            <fa :icon="['fas', 'heart']"/>
          </div>
          <h3 class="h5">Хороших каникул</h3>
          <p class="text-muted">Его комната, настоящая человеческая, хотя и слишком маленькая, мирно лежала между
            четырьмя знакомыми</p>
        </div>
      </div>
    </div>
  </div>
<!--  <div>-->
<!--    <div class="top-right links">-->
<!--      <template v-if="authenticated">-->
<!--        <router-link :to="{ name: 'home' }">-->
<!--          {{ $t('home') }}-->
<!--        </router-link>-->
<!--      </template>-->
<!--      <template v-else>-->
<!--        <router-link :to="{ name: 'login' }">-->
<!--          {{ $t('login') }}-->
<!--        </router-link>-->
<!--        <router-link :to="{ name: 'register' }">-->
<!--          {{ $t('register') }}-->
<!--        </router-link>-->
<!--      </template>-->
<!--    </div>-->

<!--    <div class="text-center">-->
<!--      <div class="title mb-4">-->
<!--        <span class="laravel">Laravel</span><span class="nuxt">Nuxt</span>-->
<!--        &lt;!&ndash; {{ title }} &ndash;&gt;-->
<!--      </div>-->

<!--      <div class="links">-->
<!--        <a href="https://github.com/cretueusebiu/laravel-nuxt">github.com/cretueusebiu/laravel-nuxt</a>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
</template>

<script>
import { mapGetters } from 'vuex'

export default {

  head () {
    return { title: this.$t('home') }
  },

  data: () => ({
    title: process.env.appName
  }),

  computed: mapGetters({
    authenticated: 'auth/check'
  })
}
</script>

<style scoped>
.top-right {
  position: absolute;
  right: 10px;
  top: 18px;
}

.title {
  font-size: 85px;
}

.laravel {
  color: #2e495e;
}

.nuxt {
  color: #00c48d;
}
</style>
