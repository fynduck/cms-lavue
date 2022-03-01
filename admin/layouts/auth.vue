<template>
    <main class="container d-flex align-items-center justify-content-center vh-100">
        <nuxt/>
    </main>
</template>

<script>

import axios from "axios";

export default {
    async fetch() {
        try {
            const {data} = await axios.get(`/get-settings`)
            const logoIndex = data.findIndex(item => item.key === 'logo')
            if (logoIndex >= 0) {
                await this.$store.dispatch('saveLogo', data[logoIndex].value)
            }

        } catch (e) {
        }
    },
}
</script>