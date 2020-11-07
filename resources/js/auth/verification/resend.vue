<template>
    <div class="row">
        <div class="col-lg-8 m-auto">
            <div class="card">
                <div class="card-header">
                    {{ $t('verify_email') }}
                </div>
                <div class="card-body">
                    <form @submit.prevent="send" @keydown="form.onKeydown($event)">
                        <alert-success :form="form" :message="status"/>

                        <!-- Email -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">{{ $t('email') }}</label>
                            <div class="col-md-7">
                                <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }"
                                       class="form-control" type="email" name="email">
                                <has-error :form="form" field="email"/>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group text-center">
                            <button type="submit" :class="['btn btn-primary', form.busy ? 'btn-loading' : '']">
                                {{ $t('send_verification_link') }}
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
            return {title: this.$t('verify_email')}
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
        created() {
            if (this.$route.query.email) {
                this.form.email = this.$route.query.email
            }
        },

        methods: {
            send() {
                this.form.post('/api/email/resend').then(response => {
                    this.status = response.data.status
                    this.form.reset()
                }).catch(e => {
                    console.log(e.response.data)
                })
            }
        }
    }
</script>
