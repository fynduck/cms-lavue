<template>
    <div>
        <p class="title_form">
            {{ routeEdit ? $t('Menu.edit_menu') : $t('Menu.add_menu') }}
        </p>
        <form @submit.prevent="onSubmit" v-if="!loading">
            <router-link class="btn btn-light submit_absolute cancel" :to="{name: 'menu.index'}"
                         :title="$t('Menu.cancel')">
                <fa :icon="['fas', 'reply']"/>
            </router-link>
            <button :class="{'btn btn-success submit_absolute': true, 'btn-loading': submit}" type="submit"
                    :title="$t('Menu.save')"
                    :disabled="submit">
                <fa :icon="['fas', 'save']"/>
            </button>
            <b-tabs>
                <b-tab :title="language.name" v-for="(language, locale_id) in locales" :key="locale_id" class="mt-4">
                    <b-card-text>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label :for="`title_${locale_id}`">{{ $t('Menu.title') }}</label>
                                <input type="text"
                                       :class="['form-control', errors['items.' + locale_id + '.title'] ? ' is-invalid' : '']"
                                       v-model="item.items[locale_id].title" :id="`title_${locale_id}`"
                                       :placeholder="$t('Menu.title')">
                            </div>
                            <div class="form-group col-md-6">
                                <label :for="`link_${locale_id}`">{{ $t('Menu.link') }}</label>
                                <input type="text" v-model="item.items[locale_id].link" :id="`link_${locale_id}`"
                                       :class="['form-control', errors['items.' + locale_id + '.link'] ? ' is-invalid' : '']"
                                       :placeholder="$t('Menu.link')">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label :for="`additional_${locale_id}`">{{ $t('Menu.additional_title') }}</label>
                                <input type="text" :placeholder="$t('Menu.additional_title')"
                                       :class="['form-control', errors['items.' + locale_id + '.additional'] ? ' is-invalid' : '']"
                                       v-model="item.items[locale_id].additional" :id="`additional_${locale_id}`">
                            </div>
                            <div class="form-group col-md-4 d-flex align-items-end">
                                <div class="custom-control custom-switch my-1 mr-sm-2">
                                    <input type="checkbox" class="custom-control-input"
                                           v-model="item.items[locale_id].active"
                                           :id="`active_${locale_id}`" :value="1">
                                    <label class="custom-control-label" :for="`active_${locale_id}`">
                                        {{ $t('Menu.on_off') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </b-card-text>
                </b-tab>
            </b-tabs>
            <hr>
            <div class="row">
                <div class="form-group col">
                    <upload v-model="item.image"></upload>
                </div>
                <div class="form-group col">
                    <label for="position">{{ $t('Menu.position') }}</label>
                    <select id="position" :class="['form-control', errors.position ? ' is-invalid' : '']" required
                            v-model="item.position">
                        <option v-for="(title, position) in positions" :value="position">
                            {{ title }}
                        </option>
                    </select>
                </div>
                <div class="form-group col">
                    <label for="parent_id">{{ $t('Menu.menu_parent') }}</label>
                    <select id="parent_id" class="form-control" v-model="item.parent_id">
                        <option value="">----</option>
                        <option v-for="parent in parents[item.position]" :value="parent.id">
                            {{ parent.title }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4 col-lg-3">
                    <label for="icon">{{ $t('Menu.fa_icon') }}</label>
                    <input type="text" class="form-control" id="icon" placeholder="fas fa-question" v-model="item.icon">
                </div>
                <div class="form-group col-md-6 col-lg-5">
                    <custom-select v-model="item.show_page"
                                   :source="admin_search"
                                   :label="$t('Menu.show_page')"
                                   :no_result="$t('Menu.no_results')"
                    ></custom-select>
                </div>
                <div class="form-group col-md-6 col-lg-4">
                    <custom-select v-model="item.to_page"
                                   :source="admin_search"
                                   :label="$t('Menu.to_page')"
                                   :no_result="$t('Menu.no_results')"
                    ></custom-select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-lg-3 form-group">
                    <label for="target">{{ $t('Menu.target') }}</label>
                    <select name="target" id="target" class="form-control" required v-model="item.target">
                        <option v-for="target in targets" :value="target">{{ target }}</option>
                    </select>
                </div>
                <div class="col-md-3 col-lg-2 form-group">
                    <label for="priority">{{ $t('Menu.priority') }}</label>
                    <input type="number" class="form-control" min="0" v-model="item.priority" id="priority"
                           :placeholder="$t('Menu.priority')">
                </div>
                <div class="col-md-3 form-group">
                    <label for=attributes>{{ $t('Menu.custom_attributes') }}</label>
                    <input type="attributes" class="form-control" id="attributes"
                           placeholder="style=color:red,margin:10px;data-info=10" v-model="item.attributes">
                </div>
                <div class="col-md-4 col-lg-3 form-group d-flex align-items-end">
                    <div class="custom-control custom-switch my-1 mr-sm-2">
                        <input type="checkbox" class="custom-control-input" id="nofollow" v-model="item.nofollow" :value="1">
                        <label class="custom-control-label" for="nofollow">{{ $t('Menu.nofollow') }}</label>
                    </div>
                </div>
            </div>
            <p class="text-right">
                <router-link class="btn btn-light" :to="{name: 'menu.index'}" :title="$t('Menu.cancel')">
                    <fa :icon="['fas', 'reply']"/>
                </router-link>
                <button :class="{'btn btn-success': true, 'btn-loading': submit}" type="submit" :title="$t('Menu.save')"
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
import CustomSelect from "../../../../../admin/components/CustomSelect";

export default {
    middleware: 'auth',
    head() {
        const title = this.routeEdit ? this.$t('Menu.edit_menu') : this.$t('Menu.add_menu');
        return {title}
    },
    components: {
        Upload,
        CustomSelect
    },
    data() {
        return {
            admin_search: '/admin/live-select',
            positions: [],
            parents: [],
            targets: [],
            item: null,
            errors: {},
            loading: true,
            submit: false,
        }
    },
    computed: {
        ...mapGetters({
            locale: 'lang/locale',
            locales: 'lang/locales'
        }),
        routeEdit() {
            return typeof this.$route.params.id !== "undefined";
        },
        modulePath() {
            return `/admin/${this.$route.name.split('.')[0]}`
        },
        sourceActionMethod() {
            let action = this.modulePath;
            let method = 'post'

            if (typeof this.$route.params.id !== "undefined") {
                action += `/${this.$route.params.id}`
                method = 'put'
            }

            return {
                'action': action,
                'method': method
            };
        },
        source() {
            let action = this.modulePath;
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
        this.getItem()
    },
    methods: {
        getItem() {
            axios.get(this.source).then(response => {
                this.positions = response.data.positions;
                this.parents = response.data.parents;
                this.targets = response.data.targets;
                this.item = response.data.data;
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
                data: this.item,
            }).then(() => {
                this.$toast.global.success(this.$t('Menu.data_saved'))

                setTimeout(() => {
                    this.$router.push({
                        name: `${this.$route.name.split('.')[0]}.index`
                    })
                }, 2000)
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
