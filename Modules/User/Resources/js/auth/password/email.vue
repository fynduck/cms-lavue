<template>
    <div class="reset_email text-center">
        <form @submit.prevent="send" @keydown="form.onKeydown($event)">
            <alert-success :form="form" :message="status"/>

            <div class="form-floating mb-3">
                <input type="email" name="email" v-model="form.email" placeholder="name@example.com"
                       :class="{ 'is-invalid': form.errors.has('email'), 'form-control': true }" id="email">
                <label for="email">{{ $t('User.your_email') }}</label>
                <has-error :form="form" field="email"/>
            </div>
            <button :class="{'btn btn-success w-100 mt-3': true, 'btn-loading': form.busy}" type="submit"
                    :disabled="form.busy">
                {{ $t('User.send_password_reset_link') }}
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
            email: ''
        })
    }),

    methods: {
        async send() {
            try {
                const {data} = await this.form.post('/password/email')
                this.status = data.status
            } catch (e) {
                return
            }

            this.form.reset()
        }
    }
}
</script>
<style lang="stylus" scoped>
.reset_email {
    width 100%
    max-width 350px
}
</style>
