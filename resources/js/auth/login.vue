<template>
    <div class="row justify-content-center my-5">
        <div class="col-lg-8 m-auto">
            <div class="card">
                <div class="card-body p-5">
                    <form @submit.prevent="login" @keydown="form.onKeydown($event)">
                        <!-- Email -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">{{ $t('email') }}</label>
                            <div class="col-md-7">
                                <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" type="email"
                                       name="email"
                                       class="form-control">
                                <has-error :form="form" field="email"/>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">{{ $t('password') }}</label>
                            <div class="col-md-7">
                                <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }"
                                       type="password"
                                       name="password" class="form-control">
                                <has-error :form="form" field="password"/>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="form-group row justify-content-center">
                            <div class="col-md-7 d-flex">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" name="remember" v-model="remember"
                                           id="remember">
                                    <label class="custom-control-label" for="remember">{{ $t('remember_me') }}</label>
                                </div>

                                <router-link :to="{ name: 'password.request' }" class="small ml-auto my-auto">
                                    {{ $t('forgot_password') }}
                                </router-link>
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" :class="['btn btn-primary', form.busy ? 'btn-loading' : '']">
                                {{ $t('login') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {Form, HasError} from 'vform'

    export default {
        metaInfo() {
            const title = this.$t('login');
            return {title}
        },
        components: {
            Form,
            HasError
        },
        data: () => ({
            form: new Form({
                email: '',
                password: ''
            }),
            remember: false
        }),
        mounted() {
            if (this.$store.getters['users/token'])
                this.$router.push({name: 'dashboard'})
        },
        methods: {
            async login() {
                // Submit the form.
                this.form.post('/api/login').then(response => {
                    // Save the token.
                    this.$store.dispatch('users/saveToken', {
                        token: response.data.access_token,
                        remember: this.remember
                    })

                    this.$router.push({name: 'dashboard'})
                }).catch(e => {
                    console.log(e.response.data.message)
                })
            }
        }
    }
</script>
