<template>
    <div>
        <p class="title_form">
            {{ routeEdit ? $t('Redirect.edit_redirect') : $t('Redirect.add_redirect') }}
        </p>
        <form @submit.prevent="onSubmit" v-if="!loading">
            <router-link class="btn btn-light submit_absolute cancel" :to="{name: 'redirect.index'}"
                         :title="$t('Redirect.cancel')">
                <fa :icon="['fas', 'reply']"/>
            </router-link>
            <button :class="{'btn btn-success submit_absolute': true, 'btn-loading': submit}" type="submit"
                    :title="$t('Redirect.save')"
                    :disabled="submit">
                <fa :icon="['fas', 'save']"/>
            </button>
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="from">{{ $t('Redirect.from') }}</label>
                    <input type="text" :class="['form-control', errors['from'] ? ' is-invalid' : '']"
                           v-model="item.from"
                           id="from">
                </div>
                <div class="mb-3 col-md-6">
                    <label for="to">{{ $t('Redirect.to') }}</label>
                    <input type="text" :class="['form-control', errors['to'] ? ' is-invalid' : '']" v-model="item.to"
                           id="to">
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="status_code">{{ $t('Redirect.status_code') }}</label>
                    <input type="number" min="0" :class="['form-control', errors['status_code'] ? ' is-invalid' : '']"
                           v-model="item.status_code"
                           id="status_code">
                </div>
                <div class="mb-3 d-flex align-items-end col-md-6">
                    <div class="form-check form-switch switch-success">
                        <input type="checkbox" class="form-check-input"
                               v-model="item.active"
                               id="active" :value="1">
                        <label class="form-check-label" for="active">
                            {{ $t('Redirect.active') }}
                        </label>
                    </div>
                </div>
            </div>
            <p class="text-end">
                <router-link class="btn btn-light" :to="{name: 'redirect.index'}" :title="$t('Redirect.cancel')">
                    <fa :icon="['fas', 'reply']"/>
                </router-link>
                <button :class="{'btn btn-success': true, 'btn-loading': submit}" type="submit"
                        :title="$t('Redirect.save')"
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

export default {
    metaInfo() {
        const title = this.routeEdit ? this.$t('edit_page') : this.$t('add_page');
        return {title}
    },
    data() {
        return {
            item: null,
            errors: {},
            loading: true,
            submit: false
        }
    },
    computed: {
        ...mapGetters({
            locale: 'lang/locale',
            locales: 'lang/locales',
            token: 'auth/token',
        }),
        routeEdit() {
            return typeof this.$route.params.id !== "undefined";
        },
        sourceActionMethod() {
            const arrayRoute = this.$route.name.split('.');

            let action = `/admin/${arrayRoute[0]}`;
            let method = 'post'

            if (this.routeEdit) {
                action += `/${this.$route.params.id}`
                method = 'put'
            }

            return {
                'action': action,
                'method': method
            };
        },
        source() {
            const arrayRoute = this.$route.name.split('.');
            let action = `/admin/${arrayRoute[0]}`;
            if (typeof this.$route.params.id !== "undefined")
                return `${action}/${this.$route.params.id}`;

            return `${action}/0`;
        }
    },
    mounted() {
        this.getItem();
    },
    methods: {
        getItem() {
            axios.get(this.source).then(response => {
                this.item = response.data.data;
                this.$nextTick(() => {
                    this.loading = false;
                });
            }).catch(error => {
                console.log(error)
            })
        },
        changeTitle(locale_id) {
            if (!this.routeEdit) {
                this.$root.$children[0].transSlug(this.item.items[locale_id].title).then(response => {
                    this.item.items[locale_id].slug = response.data
                })
            }
        },
        onSubmit() {
            this.submit = true;
            axios({
                method: this.sourceActionMethod.method,
                url: this.sourceActionMethod.action,
                data: this.item
            }).then(() => {
                this.$toast.global.success(this.$t('Redirect.data_saved'))

                setTimeout(() => {
                    this.$router.push({
                        name: `${this.$route.name.split('.')[0]}.index`
                    })
                }, 1000)
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
