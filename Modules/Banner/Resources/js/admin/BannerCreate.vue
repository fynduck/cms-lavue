<template>
    <div>
        <p class="title_form">
            {{ routeEdit ? $t('Banner.edit_banner') : $t('Banner.add_banner') }}
        </p>
        <form @submit.prevent="onSubmit" v-if="!loading">
            <router-link class="btn btn-light submit_absolute cancel" :to="{name: 'banner.index'}" :title="$t('Banner.cancel')">
                <fa :icon="['fas', 'reply']"/>
            </router-link>
            <button :class="{'btn btn-success submit_absolute': true, 'btn-loading': submit}" type="submit"
                    :title="$t('Banner.save')"
                    :disabled="submit">
                <fa :icon="['fas', 'save']"/>
            </button>
            <b-tabs>
                <b-tab :title="language.name" v-for="(language, locale_id) in locales" :key="locale_id" class="mt-4">
                    <b-card-text>
                        <div class="row">
                            <div class="mb-3 col-md-8">
                                <label :for="`title_${locale_id}`" class="form-label">{{ $t('Banner.title') }}</label>
                                <input :class="['form-control', errors['items.' + locale_id + '.title'] ? ' is-invalid' : '']"
                                       type="text" v-model="item.items[locale_id].title" :id="`title_${locale_id}`">
                            </div>
                            <div class="mb-3 col-md-4 d-flex align-items-end">
                                <div class="form-check form-switch switch-success my-1 me-sm-2">
                                    <input type="checkbox" class="form-check-input"
                                           v-model="item.items[locale_id].active"
                                           :id="`active_${locale_id}`" :value="1">
                                    <label class="form-check-label" :for="`active_${locale_id}`">
                                        {{ $t('Banner.on_off') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label :for="`description_${locale_id}`" class="form-label">{{ $t('Banner.description') }}</label>
                            <tinymce :id="`description_${locale_id}`" :path_absolute="`${baseAPI}/admin/filemanager`"
                                     :lang="locale"
                                     :token="token" v-model="item.items[locale_id].description"></tinymce>
                        </div>
                    </b-card-text>
                </b-tab>
            </b-tabs>
            <hr>
            <div class="row">
                <div class="col-md-7 col-lg-6 col-xl-4 d-flex align-items-center my-2 mb-3">
                    <upload v-model="item.image"></upload>
                    <b-alert variant="danger" :show="!!errors['image']">
                        <strong v-for="error in errors['image']">{{ error }}</strong>
                    </b-alert>
                </div>
                <div class="col-md-7 col-lg-6 col-xl-4 d-flex align-items-center my-2 mb-3">
                    <upload v-model="item.mobile_image"></upload>
                    <b-alert variant="danger" :show="!!errors['mobile_image']">
                        <strong v-for="error in errors['mobile_image']">{{ error }}</strong>
                    </b-alert>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6 col-lg-3">
                    <label for="position" class="form-label">{{ $t('Banner.position') }}</label>
                    <select id="position" :class="['form-select', errors.position ? ' is-invalid' : '']" required
                            v-model="item.position">
                        <option v-for="(title, position) in positions" :value="position">
                            {{ $t(title) }}
                        </option>
                    </select>
                </div>
                <div class="mb-3 col-md-6 col-lg-9 col-xl-4" v-if="!loading">
                    <custom-select v-model="item.pagesShow"
                                   :source="admin_search"
                                   :label="$t('Menu.show_page')"
                                   :no_result="$t('Menu.no_results')"
                    ></custom-select>
                </div>
                <div class="mb-3 col-md-6 col-lg-4 col-xl-2">
                    <label for="link" class="form-label">{{ $t('Banner.link') }}</label>
                    <input :class="['form-control', errors['link'] ? ' is-invalid' : '']" type="text" v-model="item.link"
                           id="link">
                </div>
                <div class="mb-3 col-md-6 col-lg-4 col-xl-3" v-if="!loading">
                    <custom-select v-model="item.toPage"
                                   :source="admin_search"
                                   :label="$t('Menu.to_page')"
                                   :no_result="$t('Menu.no_results')"
                    ></custom-select>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-2 mb-3">
                    <label for="target" class="form-label">{{ $t('Menu.target') }}</label>
                    <select name="target" id="target" class="form-control" required v-model="item.target">
                        <option v-for="target in targets" :value="target">{{ target }}</option>
                    </select>
                </div>
                <div class="col-md-6 col-lg-2 mb-3 d-flex justify-content-center flex-column">
                    <label for="priority" class="form-label">{{ $t('Banner.priority') }}</label>
                    <input type="number" min="0" class="form-control" id="priority" v-model="item.priority">
                </div>
                <div class="col-md-6 col-lg-4 mb-3 d-flex justify-content-center flex-column">
                    <label class="form-label">{{ $t('Banner.date_from') }}</label>
                    <date-time-picker v-model="item.date_from" :locale="locale"></date-time-picker>
                </div>
                <div class="col-md-6 col-lg-4 mb-3 d-flex justify-content-center flex-column">
                    <label class="form-label">{{ $t('Banner.date_to') }}</label>
                    <date-time-picker v-model="item.date_to" :locale="locale"></date-time-picker>
                </div>

            </div>
            <p class="text-end">
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
import Tinymce from "../../../../../admin/components/Tinymce";
import CustomSelect from "../../../../../admin/components/CustomSelect";
import swal from "sweetalert2";

export default {
    middleware: 'auth',
    head() {
        const title = this.routeEdit ? this.$t('Banner.edit_banner') : this.$t('Banner.add_banner');
        return {title}
    },
    components: {
        Upload,
        DateTimePicker,
        Tinymce,
        CustomSelect
    },
    data() {
        return {
            admin_search: '/admin/live-select',
            item: null,
            positions: [],
            targets: [],
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
            delete this.errors.image
            if (!newValue && oldValue) {
                this.deleteImage(oldValue)
            }
        },
        'item.mobile_image'(newValue, oldValue) {
            delete this.errors.mobile_image
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
                this.positions = response.data.positions;
                this.targets = response.data.targets;
                this.loading = false;
            }).catch(error => {
                console.log(error)
            })
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
                } else {
                    swal.fire({
                        type: 'error',
                        title: this.$t('error_alert_title'),
                        text: this.$t('error_alert_text'),
                        reverseButtons: true,
                        confirmButtonText: 'ok',
                    })
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