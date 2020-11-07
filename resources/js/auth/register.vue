<template>
    <div class="row justify-content-center my-5">
        <div class="col-lg-10 col-ml-9 m-auto">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-success" role="alert" v-if="mustVerifyEmail">
                        {{ $t('verify_email_address') }}
                    </div>
                    <div v-if="form.successful" class="alert alert-success alert-dismissible" role="alert">
                        <button v-if="dismissible" type="button" class="close" aria-label="Close" @click="dismiss">
                            <span aria-hidden="true">&times;</span>
                        </button>

                        <slot>
                            <div v-html="message"/>
                        </slot>
                    </div>
                    <form @submit.prevent="register" @keydown="form.onKeydown($event)" v-else>
                        <!-- Name -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">{{ $t('name') }}</label>
                            <div class="col-md-7">
                                <input v-model="form.name" :class="{ 'is-invalid': form.errors.has('name') }" type="text"
                                       name="name"
                                       class="form-control">
                                <has-error :form="form" field="name"/>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">{{ $t('email') }}</label>
                            <div class="col-md-7">
                                <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" type="email"
                                       name="email" class="form-control">
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

                        <!-- Password Confirmation -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">{{ $t('confirm_password') }}</label>
                            <div class="col-md-7">
                                <input v-model="form.password_confirmation"
                                       :class="{ 'is-invalid': form.errors.has('password_confirmation') }" type="password"
                                       name="password_confirmation"
                                       class="form-control"
                                >
                                <has-error :form="form" field="password_confirmation"/>
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" :class="['btn btn-primary', form.busy ? 'btn-loading' : '']">
                                {{ $t('register') }}
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
            return {title: this.$t('register')}
        },
        components: {
            Form,
            HasError
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
            register() {
                // Register the user.
                this.form.post('/api/register').then(response => {
                    // Must verify email fist.
                    if (response.data.verifyEmail) {
                        this.mustVerifyEmail = true
                    } else {
                        // Update the user.
                        this.$store.dispatch('users/updateUser', {user: response.data})

                        // Log in the user.
                        this.form.post('/api/login').then(response => {
                            // Save the token.
                            this.$store.dispatch('users/saveToken', {
                                token: response.data.access_token
                            })

                            // Redirect home.
                            this.$router.push({name: 'dashboard'})
                        })
                    }
                }).catch(e => {
                    console.log(e.response.data.message)
                })
            }
        }
    }
</script>
