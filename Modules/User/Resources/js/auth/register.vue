<template>
    <div class="row justify-content-center my-5">
        <div class="col-lg-8 m-auto">
            <div class="alert alert-success" role="alert" v-if="mustVerifyEmail">
                {{ $t('User.verify_email_address') }}
            </div>
            <div class="card" v-else>
                <div class="card-body">

                    <form @submit.prevent="register" @keydown="form.onKeydown($event)">
                        <!-- Name -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">{{ $t('User.your_name') }}</label>
                            <div class="col-md-7">
                                <input v-model="form.name" :class="{ 'is-invalid': form.errors.has('name') }" type="text"
                                       name="name"
                                       class="form-control">
                                <has-error :form="form" field="name"/>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">{{ $t('User.your_email') }}</label>
                            <div class="col-md-7">
                                <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" type="email"
                                       name="email" class="form-control">
                                <has-error :form="form" field="email"/>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">{{ $t('User.password') }}</label>
                            <div class="col-md-7">
                                <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }"
                                       type="password"
                                       name="password" class="form-control">
                                <has-error :form="form" field="password"/>
                            </div>
                        </div>

                        <!-- Password Confirmation -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">{{ $t('User.confirm_password') }}</label>
                            <div class="col-md-7">
                                <input v-model="form.password_confirmation"
                                       :class="{ 'is-invalid': form.errors.has('password_confirmation') }" type="password"
                                       name="password_confirmation"
                                       class="form-control"
                                >
                                <has-error :form="form" field="password_confirmation"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-7 offset-md-3 d-flex">
                                <!-- Submit Button -->
                                <button :class="{'btn btn-success': true, 'btn-loading': form.busy}" type="submit"
                                        :disabled="form.busy">
                                    {{ $t('User.register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Form from 'vform'

export default {
    middleware: 'guest',
    head() {
        return {title: this.$t('User.register')}
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
            // Register the user.
            // const {data} = await this.form.post('/register')
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
                this.$store.dispatch('auth/saveToken', {token})

                // Update the user.
                await this.$store.dispatch('auth/updateUser', {user: data})

                // Redirect home.
                this.$router.push({name: 'home'})
            }
        }
    }
}
</script>
