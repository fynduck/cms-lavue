<template>
    <div v-if="!loading">
        <p class="title_form text-danger">
            {{ $t('Settings.config_server') }}
        </p>
        <b-alert variant="danger" show>{{ $t('Settings.alert_info') }}</b-alert>
        <form @submit.prevent="onSubmit">
            <div class="mb-3" v-for="(item, index) in items"
                 v-if="!['APP_KEY', 'CACHE_DRIVER', 'SESSION_DRIVER', 'JWT_SECRET'].includes(item.key)">
                <label :for="index" class="form-label">{{ $t(`Settings.${item.key}`) }}</label>
                <fa :icon="['fas', 'question-circle']" v-b-popover.hover.top="`http://php.net/manual/${locale}/timezones.php`"
                    title="Example" v-if="item.key === 'TIMEZONE'"/>
                <input type="text" v-model="item.value" class="form-control" :id="index">
            </div>
            <p class="text-center" v-if="canCreate">
                <button :class="{'btn btn-success': true, 'btn-loading': submit}" type="submit" :title="$t('Settings.save')"
                        :disabled="submit">
                    <fa :icon="['fas', 'save']"/>
                    {{ $t('Settings.save') }}
                </button>
            </p>
        </form>
    </div>
</template>

<script>
    import axios from 'axios';
    import {mapGetters} from 'vuex'

    export default {
        data() {
            return {
                items: [],
                loading: true,
                submit: false,
                errors: {},
            }
        },
        computed: {
            ...mapGetters({
                locale: 'lang/locale',
                authenticated: 'auth/check',
                permissions: 'auth/checkPermission'
            }),
            source() {
                const arrayRoute = this.$route.name.split('.');
                return `/admin/${arrayRoute[0]}`;
            },
            canCreate() {
                if (this.authenticated) {
                    const arrayName = this.$router.currentRoute.name.split('.');
                    return this.permissions(arrayName[0], 'create')
                }

                return false;
            }
        },
        mounted() {
            this.getItems()
        },
        methods: {
            getItems() {
                axios.get(this.source).then(response => {
                    this.items = response.data;
                    this.loading = false;
                }).catch(error => {
                    console.log(error)
                })
            },
            onSubmit() {
                this.submit = true;
                axios.post(this.source, this.items).then(() => {
                    this.$toast.global.success(this.$t('Settings.data_saved'))

                    this.$nextTick(() => {
                        this.submit = false;
                    })
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors
                        this.submit = false;
                    }
                })
            }
        }
    }
</script>