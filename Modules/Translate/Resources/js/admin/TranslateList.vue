<template>
    <div>
        <b-card no-body class="translate">
            <div class="d-flex justify-content-center my-4" v-if="module_loading">
                <b-spinner type="grow" variant="success" label="Loading..."></b-spinner>
            </div>
            <b-tabs
                pills
                card
                vertical
                lazy
                @activate-tab="loadModuleFiles"
                active-nav-item-class="bg-success"
                v-else>
                <b-tab :title="module" v-for="(module, index) in modules" :key="index"
                       :title-link-class="moduleIndex != index ? 'text-success' : ''">
                    <button :class="{'btn btn-success submit_absolute': true, 'btn-loading': submit}" type="button"
                            :title="$t('Translate.save')"
                            :disabled="submit"
                            @click="onSave(index)"
                            v-if="!files_loading && canCreate">
                        <fa :icon="['fas', 'save']"/>
                    </button>
                    <div class="d-flex justify-content-center my-4" v-if="files_loading">
                        <b-spinner type="grow" variant="success" label="Loading..."></b-spinner>
                    </div>
                    <b-tabs card lazy active-nav-item-class="bg-success" v-else>
                        <b-tab :title="lang" v-for="(files, lang) in loadLangFiles(index)" :key="lang"
                               title-link-class="text-success">
                            <b-form-input
                                id="filter-input"
                                v-model="filter"
                                type="search"
                                :placeholder="$t('Translate.search')"
                            ></b-form-input>
                            <b-table show-empty
                                     striped
                                     hover
                                     stacked="sm"
                                     :items="files"
                                     :fields="fields"
                                     :filter="filter"
                            >
                                <template v-slot:cell(value)="data">
                                    <b-form-input type="text" :name="data.item.slug" v-model="data.item.value"></b-form-input>
                                </template>
                            </b-table>
                        </b-tab>
                    </b-tabs>
                    <div class="text-end" v-if="!files_loading && canCreate">
                        <button :class="{'btn btn-success': true, 'btn-loading': submit}" type="button"
                                :title="$t('Translate.save')"
                                :disabled="submit"
                                @click="onSave(index)">
                            <fa :icon="['fas', 'save']"/>
                        </button>
                    </div>
                </b-tab>
            </b-tabs>
        </b-card>
    </div>
</template>

<script>
import axios from 'axios'
import {mapGetters} from 'vuex'

export default {
    middleware: 'auth',
    head() {
        return {title: this.$t('Translate.translate')}
    },
    data() {
        return {
            modules: [],
            module_loading: true,
            files_loading: true,
            filter: null,
            search: '',
            files: [],
            items: [],
            moduleIndex: 0,
            submit: false
        };
    },
    computed: {
        ...mapGetters({
            authenticated: 'auth/check',
            permissions: 'auth/checkPermission'
        }),
        routeName() {
            return this.$route.name.split('.')[0];
        },
        source() {
            return `/admin/${this.$router.currentRoute.name.split('.')[0]}`
        },
        fields() {
            return [
                {key: 'slug', label: this.$t('Translate.slug')},
                {key: 'value', label: this.$t('Translate.current_lang')}
            ]
        },
        canCreate() {
            if (this.authenticated) {
                const arrayName = this.$router.currentRoute.name.split('.');
                return this.permissions(arrayName[0], 'create')
            }

            return false;
        },
    },
    mounted() {
        this.getModules();
    },
    watch: {
        selectedLocale() {
            this.items = [];
            this.getModules();
        }
    },
    methods: {
        getModules() {
            this.loading = true;
            let data = {
                params: {
                    module: this.files[this.moduleIndex],
                    locale: this.selectedLocale
                }
            };
            axios.get(this.source).then(response => {
                this.modules = response.data.modules;

                this.$nextTick(() => {
                    this.module_loading = false;
                })

                this.loadModuleFiles(0)
            }).catch(error => {
                this.$toast.global.error(error.response.data.message)
            });
        },
        loadModuleFiles(current) {
            this.moduleIndex = current;
            this.files_loading = true;
            const moduleName = this.modules[current]
            axios.get(this.source + '/' + moduleName).then(response => {
                this.items.push({
                    module_key: current,
                    files: response.data.files
                });

                this.$nextTick(() => {
                    this.files_loading = false
                })

            }).catch(error => {
                this.$toast.global.error(error.response.data.message)
            });
        },
        loadLangFiles(module_key) {
            let langFiles = [];
            const moduleFiles = this.items.find(item => item.module_key == module_key);
            if (moduleFiles) {
                langFiles = moduleFiles.files
            }

            return langFiles;
        },
        onSave(module) {
            this.submit = true;
            axios.post(this.source, {
                files: this.loadLangFiles(module),
                module: this.modules[module]
            }).then(response => {
                this.submit = false;
                this.$toast.global.success(this.$t('Translate.data_saved'))
            }).catch(error => {
                this.submit = false;
                let message = error.response.data.message;
                if (typeof message === "undefined")
                    message = error.response.data;
                this.$toast.global.error(message)
            })
        }
    }
};
</script>
<style lang="stylus">
.translate
    .nav-item
        .active
            color #fff !important
</style>
