<template>
    <div class="reset text-center">
        <form @submit.prevent="reset" @keydown="form.onKeydown($event)">
            <alert-success :form="form" :message="status"/>
            <div class="form-floating mb-3">
                <input type="email" name="email" v-model="form.email" placeholder="name@example.com"
                       :class="{ 'is-invalid': form.errors.has('email'), 'form-control': true }" id="email" readonly>
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
                {{ $t('User.reset_password') }}
            </button>
            <p class="mt-5 mb-3 text-muted">© {{ $moment().format('YYYY') }}</p>
        </form>
    </div>
</template>

<script>
import Form from 'vform'

export default {
    layout: 'auth',
    head() {
        return {title: this.$t('User.reset_password')}
    },

    data: () => ({
        status: '',
        form: new Form({
            token: '',
            email: '',
            password: '',
            password_confirmation: ''
        })
    }),

    created() {
        this.form.email = this.$route.query.email
        this.form.token = this.$route.params.token
    },

    methods: {
        async reset() {
            try {
                const {data} = await this.form.post('/password/reset')
                this.status = data.status
            } catch (e) {
                return
            }

            this.form.reset()

            setTimeout(() => {
                this.$router.push({name: 'login'})
            }, 3000)
        }
    }
}
</script>
<style lang="stylus" scoped>
.reset {
    width 100%
    max-width 350px
}
</style>