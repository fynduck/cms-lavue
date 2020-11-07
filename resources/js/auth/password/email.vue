<template>
    <div class="row">
        <div class="col-lg-8 m-auto">
            <div class="card">
                <div class="card-header">
                    {{ $t('reset_password') }}
                </div>
                <div class="card-body">
                    <form @submit.prevent="send" @keydown="form.onKeydown($event)">
                        <alert-success :form="form" :message="status"/>

                        <!-- Email -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">{{ $t('email') }}</label>
                            <div class="col-md-7">
                                <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" type="email"
                                       name="email" class="form-control">
                                <has-error :form="form" field="email"/>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group text-center">
                            <button type="submit" :class="['btn btn-primary', form.busy ? 'btn-loading' : '']">
                                {{ $t('send_password_reset_link') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {Form, HasError, AlertSuccess} from 'vform'

    export default {
        metaInfo() {
            return {title: this.$t('reset_password')}
        },
        components: {
            Form,
            HasError,
            AlertSuccess
        },
        data: () => ({
            status: '',
            form: new Form({
                email: ''
            })
        }),

        methods: {
            send() {
                this.form.post('/api/password/email').then(response => {
                    this.status = response.data.status
                    this.form.reset()
                }).catch(e => {
                    console.log(e)
                })
            }
        }
    }
</script>
