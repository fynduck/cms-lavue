<template>
    <div v-if="!loading">
        <p class="title_form">
            {{ $t('Settings.settings_site') }}
        </p>
        <form @submit.prevent="onSubmit">
            <button :class="{'btn btn-success submit_absolute': true, 'btn-loading': submit}" type="submit"
                    :title="$t('Settings.save')"
                    :disabled="submit" v-if="canCreate">
                <fa :icon="['fas', 'save']"/>
            </button>
            <b-tabs>
                <b-tab :title="language.name" v-for="(language, locale_id) in locales" :key="locale_id" class="mt-4">
                    <b-card-text>
                        <div class="row">
                            <div class="form-group col-sm-6 col-md-3">
                                <label :for="`name_site_${locale_id}`">{{ $t('Settings.name_site') }}</label>
                                <input type="text" v-model="items[locale_id].name_site" :id="`name_site_${locale_id}`"
                                       :class="['form-control', errors['items.' + locale_id + '.name_site'] ? ' is-invalid' : '']">
                            </div>
                            <div class="form-group col-sm-6 col-md-3">
                                <label :for="`city_${locale_id}`">{{ $t('Settings.city') }}</label>
                                <input type="text" v-model="items[locale_id].city" :id="`city_${locale_id}`"
                                       :class="['form-control', errors['items.' + locale_id + '.city'] ? ' is-invalid' : '']">
                            </div>
                            <div class="form-group col-sm-8 col-md-4">
                                <label :for="`street_${locale_id}`">{{ $t('Settings.street') }}</label>
                                <input type="text" v-model="items[locale_id].street" :id="`street_${locale_id}`"
                                       :class="['form-control', errors['items.' + locale_id + '.street'] ? ' is-invalid' : '']">
                            </div>
                            <div class="form-group col-sm-4 col-md-2">
                                <label for="post_code">{{ $t('Settings.post_code') }}</label>
                                <input type="text" class="form-control" id="post_code" v-model="items[0].post_code">
                            </div>
                        </div>
                    </b-card-text>
                </b-tab>
            </b-tabs>
            <hr class="my-4">
            <div class="row">
                <div class="form-group col-sm-6 col-lg-3">
                    <label for="contact_phone">{{ $t('Settings.contact_phone') }}</label>
                    <input type="text" class="form-control" id="contact_phone" v-model="items[0].contact_phone">
                </div>
                <div class="form-group col-sm-6 col-lg-3">
                    <label for="contact_email">{{ $t('Settings.contact_email') }}</label>
                    <input type="text" class="form-control" id="contact_email" v-model="items[0].contact_email">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>{{ $t('Settings.logo') }}</label>
                    <upload v-model="items[0].logo"></upload>
                </div>
            </div>
            <div class="form-group">
                <label for="analytics_top">{{ $t('Settings.arbitrary_code') }}</label>
                <textarea class="form-control" rows="3" id="analytics_top" v-model="items[0].analytics_top"
                          placeholder="<head>"></textarea>
            </div>
            <div class="form-group">
                <label for="analytics">{{ $t('Settings.arbitrary_code') }}</label>
                <textarea class="form-control" rows="3" id="analytics" v-model="items[0].analytics"
                          placeholder="</body>"></textarea>
            </div>
            <p class="text-right" v-if="canCreate">
                <button :class="{'btn btn-success': true, 'btn-loading': submit}" type="submit" :title="$t('Settings.save')"
                        :disabled="submit">
                    <fa :icon="['fas', 'save']"/>
                </button>
            </p>
        </form>
    </div>
</template>

<script>
import axios from 'axios'
import {mapGetters} from 'vuex'
import Upload from "../../../../../admin/components/Upload";

export default {
    middleware: 'auth',
    name: "SettingsSite",
    components: {
        Upload
    },
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
            locales: 'lang/locales',
            authenticated: 'auth/check',
            permissions: 'auth/checkPermission'
        }),
        source() {
            const arrayRoute = this.$route.name.split('.');
            let action = `/admin/${arrayRoute[0]}`;

            action = action.split('-')
            action.pop()

            return action.join('-');
        },
        canCreate() {
            if (!this.authenticated)
                return false;

            return this.permissions('settings', 'create')
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
            axios.post(this.source, {items: this.items}).then(response => {
                let message = this.$t('Settings.data_save');
                if (typeof response.data === 'string') {
                    message = response.data;
                }

                this.$toast.global.success(message)

                if (typeof response.data !== 'string') {
                    this.items = response.data;
                }
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