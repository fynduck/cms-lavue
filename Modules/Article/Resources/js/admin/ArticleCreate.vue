<template>
    <div>
        <p class="title_form">
            {{ routeEdit ? $t('Article.edit_article') : $t('Article.add_article') }}
        </p>
        <form @submit.prevent="onSubmit" v-if="!loading">
            <router-link class="btn btn-light submit_absolute cancel" :to="{name: 'article.index'}" :title="$t('Article.cancel')">
                <fa :icon="['fas', 'reply']"/>
            </router-link>
            <button :class="{'btn btn-success submit_absolute': true, 'btn-loading': submit}" type="submit"
                    :title="$t('Article.save')"
                    :disabled="submit">
                <fa :icon="['fas', 'save']"/>
            </button>
            <b-tabs>
                <b-tab :title="language.name" v-for="(language, locale_id) in locales" :key="locale_id" class="mt-4">
                    <b-card-text>
                        <div class="row">
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
                                <b-form-checkbox :id="`active_${locale_id}`"
                                                 class="switch-success"
                                                 switch
                                                 v-model="item.items[locale_id].active"
                                                 :value="1"
                                                 :unchecked-value="0">
                                    {{ $t('Article.on_off') }}
                                </b-form-checkbox>
                            </div>
                        </div>
                        <div class="form-group">
                            <label :for="`description_${locale_id}`">{{ $t('Article.description') }}</label>
                            <tinymce :id="`description_${locale_id}`" :path_absolute="`${baseAPI}/admin/filemanager`" :lang="locale"
                                     :token="token" v-model="item.items[locale_id].description"></tinymce>
                        </div>
                        <div class="form-group">
                            <label :for="`short_desc_${locale_id}`">{{ $t('Article.short_desc') }}</label>
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
                            <label :for="`meta_description_${locale_id}`">{{ $t('Article.meta_description') }}</label>
                            <textarea class="form-control" v-model="item.items[locale_id].meta_description"
                                      :id="`meta_description_${locale_id}`" rows="5"></textarea>
                        </div>
                        <div class="form-group">
                            <label :for="`meta_keywords_${locale_id}`">{{ $t('Article.meta_keywords') }}</label>
                            <textarea class="form-control" v-model="item.items[locale_id].meta_keywords"
                                      :id="`meta_keywords_${locale_id}`" rows="5"></textarea>
                        </div>
                    </b-card-text>
                </b-tab>
            </b-tabs>
            <hr>
            <div class="row">
                <div class="col-md-7 col-lg-6 col-xl-4 d-flex align-items-center my-2">
                    <upload v-model="item.image"></upload>
                    <b-alert variant="danger" :show="!!errors['image']">
                        <strong v-for="error in errors['image']">{{ error }}</strong>
                    </b-alert>
                </div>
                <div class="col-md-5 col-lg-3 form-group d-flex justify-content-center flex-column">
                    <label for="type">{{ $t('Article.type_article') }}</label>
                    <b-select v-model="item.type" :options="types" id="type"
                              :class="[errors['type'] ? ' is-invalid' : '']"></b-select>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-2 form-group d-flex justify-content-center flex-column" v-if="item.type === 'promotions'">
                    <label for="discount">{{ $t('Article.discount') }}</label>
                    <input type="number" class="form-control" id="discount" v-model="item.discount">
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3 form-group d-flex justify-content-center flex-column" v-if="item.type === 'promotions'">
                    <label>{{ $t('Article.date_from') }}</label>
                    <date-time-picker v-model="item.date_from" :locale="locale"></date-time-picker>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3 form-group d-flex justify-content-center flex-column" v-if="item.type === 'promotions'">
                    <label>{{ $t('Article.date_to') }}</label>
                    <date-time-picker v-model="item.date_to" :locale="locale"></date-time-picker>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3 form-group d-flex justify-content-center flex-column">
                    <label>{{ $t('Article.date') }}</label>
                    <date-time-picker v-model="item.date" :locale="locale"></date-time-picker>
                </div>
                <div class="col-md-4 col-xl-2 form-group d-flex justify-content-center flex-column">
                    <label for="priority">{{ $t('Article.priority') }}</label>
                    <input type="number" min="0" class="form-control" id="priority" v-model="item.priority"
                           :placeholder="$t('Article.priority')">
                </div>
                <div class="col-md-4 col-xl-3 form-group d-flex align-items-center">
                    <b-form-checkbox id="no_show_home"
                                     class="switch-success"
                                     switch
                                     v-model="item.no_show_home"
                                     :value="1"
                                     :unchecked-value="0">
                        {{ $t('Article.no_show_home') }}
                    </b-form-checkbox>
                </div>
                <div class="col-md-4 col-xl-3 form-group d-flex align-items-center">
                    <b-form-checkbox id="socials"
                                     class="switch-success"
                                     switch
                                     v-model="item.socials"
                                     :value="1"
                                     :unchecked-value="0">
                        {{ $t('Article.socials_on_off') }}
                    </b-form-checkbox>
                </div>
            </div>
            <p class="text-right">
                <router-link class="btn btn-light" :to="{name: 'article.index'}" :title="$t('Article.cancel')">
                    <fa :icon="['fas', 'reply']"/>
                </router-link>
                <button :class="{'btn btn-success': true, 'btn-loading': submit}" type="submit" :title="$t('Article.save')"
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
            }).then(() => {
                this.$toast.global.success(this.$t('Article.data_saved'))
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