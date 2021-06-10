<template>
    <div class="text-center">
        <template v-if="success">
            <div :class="`alert alert-${success ? 'success' : 'danger'}`" role="alert">
                {{ status || $t('User.failed_to_verify_email') }}
            </div>

            <router-link :to="{ name: 'login' }" class="btn btn-success" v-if="success">
                {{ $t('User.login') }}
            </router-link>
            <router-link :to="{ name: 'verification.resend' }" class="small float-right" v-else>
                {{ $t('User.resend_verification_link') }}
            </router-link>
        </template>
    </div>
</template>

<script>
import axios from 'axios'

const qs = params => Object.keys(params).map(key => `${key}=${params[key]}`).join('&')

export default {
    middleware: 'guest',
    layout: 'auth',

    metaInfo() {
        return {title: this.$t('User.verify_email')}
    },

    async asyncData({params, query}) {
        try {
            const {data} = await axios.post(`/email/verify/${params.id}?${qs(query)}`)

            return {success: true, status: data.status}
        } catch (e) {
            return {success: false, status: e.response.data.status}
        }
    }
}
</script>
