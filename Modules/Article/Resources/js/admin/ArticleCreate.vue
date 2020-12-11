<template>
    <div>
        <p class="title_form">
            {{ routeEdit ? $t('Article.edit_article') : $t('Article.add_article') }}
        </p>
        <form @submit.prevent="onSubmit" v-if="!loading">
            <router-link class="btn btn-light submit_absolute cancel" :to="{name: 'article.index'}" :title="$t('Article.cancel')">
                <fa :icon="['fas', 'reply']"/>
            </router-link>
            <button :class="{'btn btn-primary submit_absolute': true, 'btn-loading': submit}" type="submit"
                    :title="$t('Article.save')"
                    :disabled="submit">
                <fa :icon="['fas', 'save']"/>
            </button>
            <b-tabs>
                <b-tab :title="language.name" v-for="(language, locale_id) in locales" :key="locale_id" class="mt-4">
                    <b-card-text>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label :for="`title_${locale_id}`">{{ $t('Article.title') }}</label>
                                <input type="text" @change="changeTitle(locale_id)"
                                       :class="['form-control', errors['items.' + locale_id + '.title'] ? ' is-invalid' : '']"
                                       v-model="item.items[locale_id].title" :id="`title_${locale_id}`"
                                       :placeholder="$t('Article.title')">
                            </div>
                            <div class="form-group col-md-4">
                                <label :for="`slug_${locale_id}`">{{ $t('Article.slug') }}</label>
                                <input type="text"
                                       :class="['form-control', errors['items.' + locale_id + '.slug'] ? ' is-invalid' : '']"
                                       v-model="item.items[locale_id].slug" :id="`slug_${locale_id}`"
                                       :placeholder="$t('Article.slug')">
                            </div>
                            <div class="form-group col-md-4 d-flex align-items-end">
                                <div class="custom-control custom-checkbox my-1 mr-sm-2">
                                    <input type="checkbox" class="custom-control-input" v-model="item.items[locale_id].active"
                                           :id="`active_${locale_id}`" :value="1">
                                    <label class="custom-control-label" :for="`active_${locale_id}`">
                                        {{ $t('Article.on_off') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label :for="`description_${locale_id}`">{{ $t('Article.description')}}</label>
                            <tinymce :id="`description_${locale_id}`" path_absolute="/api/admin/filemanager" :lang="locale"
                                     :token="token" v-model="item.items[locale_id].description"></tinymce>
                        </div>
                        <div class="form-group">
                            <label :for="`short_desc_${locale_id}`">{{ $t('Article.short_desc')}}</label>
                            <textarea class="form-control" v-model="item.items[locale_id].short_desc"
                                      :id="`short_desc_${locale_id}`" rows="5"></textarea>
                        </div>
                        <div class="form-group">
                            <label :for="`meta_title_${locale_id}`">{{ $t('Article.meta_title') }}</label>
                            <input type="text"
                                   :class="['form-control', errors['items.' + locale_id + '.meta_title'] ? ' is-invalid' : '']"
                                   v-model="item.items[locale_id].meta_title" :id="`meta_title_${locale_id}`"
                                   :placeholder="$t('Article.meta_title')">
                        </div>
                        <div class="form-group">
                            <label :for="`meta_description_${locale_id}`">{{ $t('Article.meta_description')}}</label>
                            <textarea class="form-control" v-model="item.items[locale_id].meta_description"
                                      :id="`meta_description_${locale_id}`" rows="5"></textarea>
                        </div>
                        <div class="form-group">
                            <label :for="`meta_keywords_${locale_id}`">{{ $t('Article.meta_keywords')}}</label>
                            <textarea class="form-control" v-model="item.items[locale_id].meta_keywords"
                                      :id="`meta_keywords_${locale_id}`" rows="5"></textarea>
                        </div>
                    </b-card-text>
                </b-tab>
            </b-tabs>
            <hr>
            <div class="form-row">
                <div class="col-md-6 col-lg-4 col-xl-3 form-group">
                    <label for="type">{{ $t('Article.type_article') }}</label>
                    <b-select v-model="item.type" :options="types" id="type"
                              :class="[errors['type'] ? ' is-invalid' : '']"></b-select>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3 form-group" v-if="item.type === 'promotions'">
                    <label for="discount">{{ $t('Article.discount')}}</label>
                    <input type="number" class="form-control" id="discount" v-model="item.discount">
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3 form-group" v-if="item.type === 'promotions'">
                    <label>{{ $t('Article.date_from') }}</label>
                    <date-time-picker v-model="item.date_from" :locale="locale"></date-time-picker>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3 form-group" v-if="item.type === 'promotions'">
                    <label>{{ $t('Article.date_to') }}</label>
                    <date-time-picker v-model="item.date_to" :locale="locale"></date-time-picker>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3 form-group">
                    <label>{{ $t('Article.date') }}</label>
                    <date-time-picker v-model="item.date" :locale="locale"></date-time-picker>
                </div>
                <div class="col-md-4 col-xl-3 form-group">
                    <label for="sort">{{ $t('Article.sort') }}</label>
                    <input type="number" min="0" class="form-control" id="sort" v-model="item.sort"
                           :placeholder="$t('Article.sort')">
                </div>
                <div class="col-md-4 col-xl-3 form-group d-flex align-items-center">
                    <div class="custom-control custom-checkbox my-1 mr-sm-2">
                        <input type="checkbox" class="custom-control-input" v-model="item.no_show_home" id="no_show_home"
                               :value="1">
                        <label class="custom-control-label" for="no_show_home">{{ $t('Article.no_show_home') }}</label>
                    </div>
                </div>
                <div class="col-md-4 col-xl-3 form-group d-flex align-items-center">
                    <div class="custom-control custom-checkbox my-1 mr-sm-2">
                        <input type="checkbox" class="custom-control-input" v-model="item.socials" id="socials" :value="1">
                        <label class="custom-control-label" for="socials">{{ $t('Article.socials_on_off') }}</label>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <upload v-model="item.image"></upload>
                <b-alert variant="danger" :show="!!errors['image']">
                    <strong v-for="error in errors['image']">{{ error}}</strong>
                </b-alert>
            </div>
            <p class="text-right">
                <router-link class="btn btn-light" :to="{name: 'article.index'}" :title="$t('Article.cancel')">
                    <fa :icon="['fas', 'reply']"/>
                </router-link>
                <button :class="{'btn btn-primary': true, 'btn-loading': submit}" type="submit" :title="$t('Article.save')"
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
    import DateTimePicker from "../../../../../admin/components/DateTimePicker";
    import {transSlug} from "../../../../../admin/utils";
    import Tinymce from "../../../../../admin/components/Tinymce";

    export default {
        middleware: 'auth',
        head() {
            const title = this.routeEdit ? this.$t('Article.edit_article') : this.$t('Article.add_article');
            return {title}
        },
        components: {
            Upload,
            DateTimePicker,
            Tinymce
        },
        data() {
            return {
                item: null,
                types: [],
                errors: {},
                loading: true,
                submit: false,
                value: 'YYYY-MM-DD',
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
        watch: {
            'item.image'(newValue, oldValue) {
                if (!newValue && oldValue) {
                    this.deleteImage(oldValue)
                }
            }
        },
        mounted() {
            this.getItem();
        },
        methods: {
            getItem() {
                axios.get(this.source).then(response => {
                    this.item = response.data.data;
                    this.types = response.data.types;
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
                }).then(response => {
                    this.$bvToast.toast(this.$t('Article.data_save'), {
                        title: this.$t('Article.status'),
                        variant: 'info',
                        solid: true
                    })

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
            },
            deleteImage(image) {
                const paths = image.split('/');
                axios.delete(`${this.source}?image=${paths[paths.length - 1]}`)
            }
        }
    }
</script>