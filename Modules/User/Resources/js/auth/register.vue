<template>
    <div class="alert alert-success" role="alert" v-if="mustVerifyEmail">
        {{ $t('User.verify_email_address') }}
    </div>
    <div class="sign_up text-center" v-else>
        <form @submit.prevent="register" @keydown="form.onKeydown($event)">
            <img class="mb-4" :src="logo" alt="" height="50" v-if="isLogo">
            <h1 class="h3 mb-3 fw-normal" v-else>{{ logo }}</h1>

            <div class="form-floating mb-3">
                <input type="text" name="name" v-model="form.name" placeholder="Joon"
                       :class="{ 'is-invalid': form.errors.has('name'), 'form-control': true }" id="name">
                <label for="email">{{ $t('User.your_name') }}</label>
                <has-error :form="form" field="name"/>
            </div>

            <div class="form-floating mb-3">
                <input type="email" name="email" v-model="form.email" placeholder="name@example.com"
                       :class="{ 'is-invalid': form.errors.has('email'), 'form-control': true }" id="email">
                <label for="email">{{ $t('User.your_email') }}</label>
                <has-error :form="form" field="email"/>
            </div>
            <div class="form-floating mb-3">
                <input type="password" v-model="form.password" name="password" placeholder="Password"
                       :class="{ 'is-invalid': form.errors.has('password'), 'form-control': true }" id="password">
                <label for="password">{{ $t('User.password') }}</label>
                <has-error :form="form" field="password"/>
            </div>
            <div class="form-floating mb-3">
                <input type="password" v-model="form.password_confirmation" name="password_confirmation"
                       placeholder="Password confirmation" id="password_confirmation"
                       :class="{ 'is-invalid': form.errors.has('password_confirmation'), 'form-control': true }">
                <label for="password_confirmation">{{ $t('User.password_confirmation') }}</label>
                <has-error :form="form" field="password_confirmation"/>
            </div>
            <button :class="{'btn btn-success btn-lg w-100 mt-3': true, 'btn-loading': form.busy}" type="submit"
                    :disabled="form.busy">
                {{ $t('User.register') }}
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
        return {title: this.$t('User.register')}
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
            name: '',
            email: '',
            password: '',
            password_confirmation: ''
        }),
        mustVerifyEmail: false
    }),

    methods: {
        async register() {
            let data

            try {
                const response = await this.form.post('/register')
                data = response.data
            } catch (e) {
                return
            }

            // Must verify email fist.
            if (data.verifyEmail) {
                this.mustVerifyEmail = true
            } else {
                // Log in the user.
                const {data: {token}} = await this.form.post('/login')

                // Save the token.
                await this.$store.dispatch('auth/saveToken', {token})

                // Update the user.
                await this.$store.dispatch('auth/updateUser', {user: data})

                // Redirect home.
               await this.$router.push({name: 'home'})
            }
        }
    }
}
</script>
<style lang="stylus" scoped>
.sign_up {
    width 100%
    max-width 350px
}
</style>
