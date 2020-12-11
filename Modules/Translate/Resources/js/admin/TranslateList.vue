<template>
    <div>
        <b-card class="mb-2">
            <b-button :variant="selectedLocale === locale.slug ? 'outline-primary' : 'primary'" class="mr-2"
                      @click="selectedLocale = locale.slug" v-for="(locale, locale_id) in locales" :key="locale_id">
                {{ locale.name }}
            </b-button>
        </b-card>
        <b-tabs content-class="mt-3" v-model="tabIndex" v-if="files.length">
            <b-tab :title="$t(`file_${file}`)" v-for="file in files" :key="file">
                <form @submit.prevent="onSave">
                    <b-row>
                        <b-col>
                            <b-form-group
                                label-cols-sm="3"
                                label-align-sm="right"
                                label-size="sm"
                                label-for="filterInput"
                                class="mb-0"
                            >
                                <b-input-group size="sm">
                                    <b-form-input
                                        v-model="filter"
                                        type="search"
                                        id="filterInput"
                                        placeholder="Type to Search"
                                    ></b-form-input>
                                    <b-input-group-append>
                                        <b-button :disabled="!filter" @click="filter = ''" variant="outline-info">Clear</b-button>
                                    </b-input-group-append>
                                </b-input-group>
                            </b-form-group>
                        </b-col>
                        <b-col>
                            <button :class="{'btn btn-primary float-right': true, 'btn-loading': submit}" type="submit" :title="$t('save')"
                                    :disabled="submit">
                                <i class="fas fa-save"></i>
                            </button>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col>
                            <b-table show-empty
                                     striped
                                     hover
                                     v-loading="loading"
                                     stacked="sm"
                                     :items="items"
                                     :fields="fields"
                                     :current-page="currentPage"
                                     :per-page="perPage"
                                     :filter="filter"
                                     @filtered="onFiltered"
                            >
                                <template v-slot:cell(trans)="data">
                                        <span v-if="typeof data.value == 'object'">
                                            <div v-for="(item, index) in data.value">
                                                <span v-if="typeof item == 'object'">
                                                    <div v-for="(item2, key) in item">
                                                        <b-form-group
                                                            horizontal
                                                            :label-cols="2"
                                                            label-size="sm"
                                                            :label="index"
                                                            :label-for="'input_' + key">

                                                            <b-form-input :id="'input_' + key"
                                                                          :name="data.item.slug + '[' + index + '][' + key + ']'"
                                                                          v-model="data.item.trans[index][key]"
                                                                          size="sm">
                                                            </b-form-input>
                                                        </b-form-group>
                                                    </div>
                                                </span>
                                                <b-form-group v-else
                                                              horizontal
                                                              :label-cols="2"
                                                              label-size="sm"
                                                              :label="index"
                                                              :label-for="'input_' + index">

                                                    <b-form-input :id="'input_' + index"
                                                                  :name="data.item.slug + '[' + index + ']'"
                                                                  v-model="data.item.trans[index]"
                                                                  size="sm">
                                                    </b-form-input>
                                                </b-form-group>
                                            </div>
                                        </span>
                                    <b-form-input v-else type="text" :name="data.item.slug"
                                                  v-model="data.item.trans"></b-form-input>
                                </template>
                            </b-table>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col>
                            <b-pagination
                                v-model="currentPage"
                                :total-rows="totalRows"
                                :per-page="perPage"
                                align="fill"
                                size="sm"
                                class="my-0"
                            ></b-pagination>
                        </b-col>
                        <b-col>
                            <button :class="{'btn btn-primary float-right': true, 'btn-loading': submit}" type="submit" :title="$t('save')"
                                    :disabled="submit">
                                <i class="fas fa-save"></i>
                            </button>
                        </b-col>
                    </b-row>
                </form>
            </b-tab>
        </b-tabs>
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
                search: '',
                locales: [],
                selectedLocale: null,
                files: [],
                items: [],
                tabIndex: 0,
                totalRows: 0,
                currentPage: 1,
                perPage: 10,
                filter: null,
                loading: false,
                submit: false
            };
        },
        computed: {
            routeName() {
                return this.$route.name.split('.')[0];
            },
            source() {
                return `/admin/${this.$router.currentRoute.name.split('.')[0]}`
            },
            fields() {
                return [
                    {key: 'slug', label: this.$t('Translate.slug')},
                    {key: 'trans', label: this.$t('Translate.current_lang')}
                ]
            }
        },
        mounted() {
            this.getItems();
        },
        watch: {
            tabIndex() {
                this.items = [];
                this.getItems();
            },
            selectedLocale() {
                this.items = [];
                this.getItems();
            }
        },
        methods: {
            getItems() {
                this.loading = true;
                let data = {
                    params: {
                        file_name: this.files[this.tabIndex],
                        locale: this.selectedLocale
                    }
                };
                axios.get(this.source, data).then((response) => {
                    this.items = response.data.items;
                    this.locales = response.data.locales
                    this.selectedLocale = response.data.locale
                    this.files = response.data.files
                    this.loading = false;
                    this.totalRows = this.items.length
                }).catch((error) => {
                    console.log(error);
                });
            },
            onFiltered(filteredItems) {
                this.totalRows = filteredItems.length
                this.currentPage = 1
            },
            onSave() {
                this.submit = true;
                axios.post(this.source, {
                    items: this.items,
                    locale: this.selectedLocale,
                    file_name: this.files[this.tabIndex]
                }).then(response => {
                    this.submit = false;
                    this.$bvToast.toast(this.$t('Translate.data_save'), {
                        title: this.$t('Translate.status'),
                        variant: 'info',
                        solid: true
                    })
                }).catch(error => {
                    console.log(error)
                })
            }
        }
    };
</script>
