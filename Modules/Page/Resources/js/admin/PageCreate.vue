<template>
    <div>
        <p class="title_form">
            {{ routeEdit ? $t('Page.edit_page') : $t('Page.add_page') }}
        </p>
        <form @submit.prevent="onSubmit" v-if="!loading">
            <router-link class="btn btn-light submit_absolute cancel" :to="{name: 'page.index'}" :title="$t('Page.cancel')">
                <fa :icon="['fas', 'reply']"/>
            </router-link>
            <button :class="{'btn btn-success submit_absolute': true, 'btn-loading': submit}" type="submit"
                    :title="$t('Page.save')"
                    :disabled="submit">
                <fa :icon="['fas', 'save']"/>
            </button>
            <b-tabs>
                <b-tab :title="language.name" v-for="(language, locale_id) in locales" :key="locale_id" class="mt-4">
                    <b-card-text>
                        <div class="row">
                            <div class="mb-3 col-md-4">
                                <label :for="`title_${locale_id}`" class="form-label">{{ $t('Page.title') }}</label>
                                <input type="text" @change="changeTitle(locale_id)"
                                       :class="['form-control', errors['items.' + locale_id + '.title'] ? ' is-invalid' : '']"
                                       v-model="item.items[locale_id].title" :id="`title_${locale_id}`"
                                       :placeholder="$t('Page.title')">
                            </div>
                            <div class="mb-3 col-md-4"
                                 v-if="!routeEdit || !['index', 'home', 'not_found'].includes(item.method)">
                                <label :for="`slug_${locale_id}`" class="form-label">{{ $t('Page.slug') }}</label>
                                <input type="text" v-model="item.items[locale_id].slug" :id="`slug_${locale_id}`"
                                       :class="['form-control', errors['items.' + locale_id + '.slug'] ? ' is-invalid' : '']"
                                       :placeholder="$t('Page.slug')">
                            </div>
                            <div class="mb-3 col-md-4 d-flex align-items-end">
                                <div class="form-check form-switch switch-success my-1 me-sm-2">
                                    <input type="checkbox" class="form-check-input"
                                           v-model="item.items[locale_id].active"
                                           :id="`active_${locale_id}`" :value="1">
                                    <label class="form-check-label" :for="`active_${locale_id}`">
                                        {{ $t('Page.on_off') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label :for="`description_${locale_id}`" class="form-label">{{ $t('Page.description') }}</label>
                            <tinymce :id="`description_${locale_id}`" :path_absolute="`${baseAPI}/admin/filemanager`"
                                     :lang="locale" :token="token" v-model="item.items[locale_id].description"></tinymce>
                        </div>
                        <div class="mb-3">
                            <label :for="`description_footer_${locale_id}`" class="form-label">{{ $t('Page.description_footer') }}</label>
                            <tinymce :id="`description_footer_${locale_id}`" :path_absolute="`${baseAPI}/admin/filemanager`"
                                     :lang="locale" :token="token" v-model="item.items[locale_id].description_footer"></tinymce>
                        </div>
                        <div class="mb-3">
                            <label :for="`meta_title_${locale_id}`" class="form-label">{{ $t('Page.meta_title') }}</label>
                            <input type="text" v-model="item.items[locale_id].meta_title" :id="`meta_title_${locale_id}`"
                                   :class="['form-control', errors['items.' + locale_id + '.meta_title'] ? ' is-invalid' : '']"
                                   :placeholder="$t('Page.meta_title')">
                        </div>
                        <div class="mb-3">
                            <label :for="`meta_description_${locale_id}`" class="form-label">{{ $t('Page.meta_description') }}</label>
                            <textarea class="form-control" v-model="item.items[locale_id].meta_description"
                                      :id="`meta_description_${locale_id}`" rows="5"></textarea>
                        </div>
                        <div class="mb-3">
                            <label :for="`meta_keywords_${locale_id}`" class="form-label">{{ $t('Page.meta_keywords') }}</label>
                            <textarea class="form-control" v-model="item.items[locale_id].meta_keywords"
                                      :id="`meta_keywords_${locale_id}`" rows="5"></textarea>
                        </div>
                    </b-card-text>
                </b-tab>
            </b-tabs>
            <hr>
            <div class="mb-3">
                <label for="sql_products" class="form-label">
                    (manufacturer_id=1 AND category_id=1 AND price=100 AND promotion_id>0 AND group_id IN(1,2,3)...)
                </label>
                <textarea class="form-control" v-model="item.sql_products" id="sql_products" rows="5"></textarea>
            </div>
            <div class="mb-3 d-flex align-items-end">
                <div class="form-check form-switch switch-success my-1 me-sm-2">
                    <input type="checkbox" class="form-check-input"
                           v-model="item.socials"
                           id="socials" :value="1">
                    <label class="form-check-label" for="socials">
                        {{ $t('Page.socials_on_off') }}
                    </label>
                </div>
            </div>
            <p class="text-end">
                <router-link class="btn btn-light" :to="{name: 'page.index'}" :title="$t('Page.cancel')">
                    <fa :icon="['fas', 'reply']"/>
                </router-link>
                <button :class="{'btn btn-success': true, 'btn-loading': submit}" type="submit" :title="$t('Page.save')"
                        :disabled="submit">
                    <fa :icon="['fas', 'save']"/>
                </button>
            </p>
        </form>
    </div>
</template>

<script>
import axios from 'axios';
import {mapGetters} from 'vuex'
import {transSlug} from "../../../../../admin/utils";
import Tinymce from "../../../../../admin/components/Tinymce";

export default {
    middleware: 'auth',
    head() {
        const title = this.routeEdit ? this.$t('Page.edit_page') : this.$t('Page.add_page');
        return {title}
    },
    components: {
        Tinymce
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
            baseAPI: 'base'
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
                this.loading = false;
            }).catch(error => {
                console.log(error)
            })
        },
        changeTitle(locale_id) {
            if (!this.routeEdit) {
                transSlug(this.item.items[locale_id].title).then(response => {
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
                this.$toast.global.success(this.$t('Page.data_saved'))

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