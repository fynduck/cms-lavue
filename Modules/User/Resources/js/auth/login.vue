<template>
    <div class="sign_in text-center">
        <form @submit.prevent="login" @keydown="form.onKeydown($event)">
            <img class="mb-4" :src="logo" alt="" height="50" v-if="isLogo">
            <h1 class="h3 mb-3 fw-normal" v-else>{{ logo }}</h1>

            <div class="form-floating mb-3">
                <input type="email" name="email" v-model="form.email" placeholder="name@example.com"
                       :class="{ 'is-invalid': form.errors.has('email'), 'form-control': true }" id="email">
                <label for="email">{{ $t('User.your_email') }}</label>
                <div class="invalid-feedback" v-if="form.errors.has('email')" v-html="form.errors.get('email')"></div>
            </div>
            <div class="form-floating mb-3">
                <input type="password" v-model="form.password" name="password" placeholder="Password"
                       :class="{ 'is-invalid': form.errors.has('password'), 'form-control': true }" id="password">
                <label for="password">{{ $t('User.password') }}</label>
            </div>

            <div class="form-check form-switch switch-success d-flex justify-content-center">
                <input type="checkbox" class="form-check-input"
                       v-model="remember"
                       id="remember_me" :value="1">
                <label class="form-check-label ms-2" for="remember_me">
                    {{ $t('User.remember_me') }}
                </label>
            </div>
            <router-link :to="{ name: 'password.request' }" class="small ml-auto my-auto text-success">
                {{ $t('User.forgot_password') }}
            </router-link>
            <button :class="{'btn btn-success btn-lg w-100 mt-3': true, 'btn-loading': form.busy}" type="submit"
                    :disabled="form.busy">
                {{ $t('User.login') }}
            </button>
            <p class="mt-5 mb-3 text-muted">© {{ $moment().format('YYYY') }}</p>
        </form>
    </div>
</template>

<script>
import Form from 'vform'
import {mapGetters} from "vuex";

export default {
    middleware: 'guest',
    layout: 'auth',
    head() {
        return {title: this.$t('User.login')}
    },

    computed: {
        ...mapGetters({
            logo: 'logo'
        }),
        isLogo() {
            return (/(jpg|gif|png|JPG|GIF|PNG|JPEG|jpeg)$/.test(this.logo))
        }
    },

    data: () => ({
        form: new Form({
            email: '',
            password: ''
        }),
        remember: false,
    }),

    methods: {
        async login() {
            let data

            // Submit the form.
            try {
                const response = await this.form.post('/login')
                data = response.data
            } catch (e) {
                return
            }

            // Save the token.
            this.$store.dispatch('auth/saveToken', {
                token: data.access_token,
                remember: this.remember
            })

            // Fetch the user.
            await this.$store.dispatch('auth/fetchUser')

            // Redirect home.
            this.$router.push({name: 'dashboard.index'})
        }
    }
}
</script>
<style lang="stylus" scoped>
.sign_in {
    width 100%
    max-width 350px
}
</style>
