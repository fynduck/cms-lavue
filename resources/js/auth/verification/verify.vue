<template>
    <div class="row" v-if="!loading">
        <div class="col-lg-8 m-auto">
            <div class="card">
                <div class="card-body">
                    <template v-if="success">
                        <div class="alert alert-success" role="alert">
                            {{ status }}
                        </div>

                        <router-link :to="{ name: 'login' }" class="btn btn-primary">
                            {{ $t('login') }}
                        </router-link>
                    </template>
                    <template v-else>
                        <div class="alert alert-danger" role="alert">
                            {{ status || $t('failed_to_verify_email') }}
                        </div>

                        <router-link :to="{ name: 'verification.resend' }" class="small float-right">
                            {{ $t('resend_verification_link') }}
                        </router-link>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        metaInfo() {
            return {title: this.$t('verify_email')}
        },
        data() {
            return {
                success: false,
                status: false,
                loading: true
            }
        },
        mounted() {
            this.asyncData()
        },
        methods: {
            asyncData() {
                axios.post(`/api${location.pathname}${location.search}`).then(response => {
                    this.success = true
                    this.status = response.data.status
                    this.loading = false
                }).catch(e => {
                    this.status = e.response.data.status
                    this.loading = false
                })
            }
        }
    }
</script>