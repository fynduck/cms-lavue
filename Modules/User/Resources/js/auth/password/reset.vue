<template>
    <div class="row justify-content-center my-5">
        <div class="col-lg-8 m-auto">
            <div class="card">
                <div class="card-body">
                    <form @submit.prevent="reset" @keydown="form.onKeydown($event)">
                        <alert-success :form="form" :message="status"/>

                        <!-- Email -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">{{ $t('User.email') }}</label>
                            <div class="col-md-7">
                                <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" type="email"
                                       name="email" class="form-control" readonly>
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
                                       name="password_confirmation" class="form-control">
                                <has-error :form="form" field="password_confirmation"/>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group row">
                            <div class="col-md-9 ml-md-auto">
                                <button :class="{'btn btn-success': true, 'btn-loading': form.busy}" type="submit"
                                        :disabled="form.busy">
                                    {{ $t('User.reset_password') }}
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
            const {data} = await this.form.post('/password/reset')

            this.status = data.status

            this.form.reset()
        }
    }
}
</script>
