<template>
    <div class="position-relative">
        <b-card class="mb-4">
            <b-row>
                <b-col sm="6" lg="4" class="my-1">
                    <b-input-group>
                        <b-form-input v-model="filter" :placeholder="$t('Page.insert_query')"/>
                        <b-input-group-append>
                            <b-btn variant="outline-info" :disabled="!filter" @click="filter = ''">{{ $t('Page.clear') }}</b-btn>
                        </b-input-group-append>
                    </b-input-group>
                </b-col>
                <b-col sm="6" lg="3" class="my-1">
                    <b-form-select v-model="lang_id" :options="langOptions"></b-form-select>
                </b-col>
                <b-col sm="6" lg="3" class="my-1 d-flex align-items-center">
                    <b-form-checkbox id="checkbox_status"
                                     switch
                                     v-model="active"
                                     :value="1"
                                     :unchecked-value="0">
                        {{ $t('Page.active_s') }}
                    </b-form-checkbox>
                </b-col>
                <b-col sm="6" lg="2" class="text-right" v-if="canCreate">
                    <router-link class="btn btn-success" :to="{name: `${routeName}.create`}" :title="$t('Page.add_page')">
                        <fa :icon="['fas', 'plus']"/>
                    </router-link>
                </b-col>
            </b-row>
        </b-card>
        <confirm v-model="confirmWindow.confirm"
                 :show="confirmWindow.openConfirm"
                 :text="confirmWindow.text"
                 :cancel="$t('cancel')"
                 :yes="$t('yes')"
                 @input="deleteItem"
                 v-if="confirmWindow.openConfirm"
        ></confirm>
        <!-- Main table element -->
        <b-table show-empty
                 :busy="loading"
                 striped
                 hover
                 stacked="lg"
                 :items="items"
                 :fields="fields"
                 :sort-by.sync="sortBy"
                 :sort-desc.sync="sortDesc"
                 @sort-changed="changeSort"
        >
            <template #table-busy>
                <div class="text-center text-success my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Loading...</strong>
                </div>
            </template>
            <template v-slot:cell(slug)="row">
                <a :href="row.item.link" target="_blank">{{ row.item.slug }}</a>
            </template>
            <template v-slot:cell(seo_complete)="row">
                <b-badge variant="danger" v-if="row.item.seo_complete <= 50">
                    {{row.item.seo_complete}}%
                </b-badge>
                <b-badge variant="warning" v-else-if="row.item.seo_complete > 50 && row.item.seo_complete <= 70">
                    {{row.item.seo_complete}}%
                </b-badge>
                <b-badge variant="success" v-else>
                    {{row.item.seo_complete}}%
                </b-badge>
            </template>
            <template v-slot:cell(active)="row">
                <fa :icon="['far', 'check-circle']" class="text-success" v-if="row.item.active"/>
                <fa :icon="['far', 'times-circle']" class="text-danger" v-else/>
            </template>
            <template v-slot:cell(socials)="row">
                <fa :icon="['fas', 'share-alt']" class="text-success" v-if="row.item.socials"/>
                <fa :icon="['fas', 'share-alt']" class="text-danger" v-else/>
            </template>
            <template v-slot:cell(actions)="row">
                <b-button-group>
                    <router-link class="btn btn-success" v-if="row.item.permissions.edit"
                                 :to="{name: `${routeName}.edit`, params: {id: row.item.id}}">
                        <fa :icon="['fas', 'pencil-alt']"/>
                    </router-link>
                    <b-button variant="danger" v-if="row.item.permissions.destroy" @click.prevent="confirmDelete(row.item)">
                        <fa :icon="['fas', 'trash-alt']"/>
                    </b-button>
                </b-button-group>
            </template>
        </b-table>
        <b-pagination align="center" v-if="total > per_page" size="md" :total-rows="total" v-model="current_page"
                      :per-page="per_page"/>
    </div>
</template>

<script>
    import axios from "axios";
    import {mapGetters} from "vuex";

    export default {
        middleware: 'auth',
        head() {
            const title = this.$t('Page.page');
            return {title}
        },
        data() {
            return {
                items: [],
                active: 1,
                lang_id: null,
                current_page: 1,
                per_page: 20,
                total: null,
                sortBy: null,
                sortDesc: false,
                filter: null,
                loading: false,
                timeout: null,
                confirmWindow: {
                    confirm: null,
                    openConfirm: false,
                    text: ''
                },
                languages: {}
            }
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

            canCreate() {
                if (this.authenticated) {
                    const arrayName = this.$router.currentRoute.name.split('.');
                    return this.permissions(arrayName[0], 'create')
                }

                return false;
            },
            fields() {
                return [
                    {key: 'title', label: this.$t('Page.title'), sortable: true},
                    {key: 'slug', label: this.$t('Page.slug')},
                    {key: 'seo_complete', label: this.$t('Page.seo')},
                    {key: 'active', label: this.$t('Page.status'), sortable: true, 'class': 'text-center active'},
                    {key: 'lang', label: this.$t('Page.lang'), sortable: true, 'class': 'text-center'},
                    {key: 'socials', label: this.$t('Page.socials_on_off'), 'class': 'text-center active'},
                    {key: 'actions', label: this.$t('Page.action'), 'class': 'text-right'}
                ]
            },
            langOptions() {
                let options = [{
                    value: null,
                    text: this.$t('Page.all_lang')
                }];
                for (let key of Object.keys(this.languages)) {
                    options.push({
                        value: key,
                        text: this.languages[key]
                    })
                }

                return options;
            }
        },
        watch: {
            current_page() {
                this.getItems();
            },
            active() {
                this.current_page = 1
                this.getItems();
            },
            lang_id() {
                this.current_page = 1
                this.getItems();
            },
            filter() {
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    this.current_page = 1
                    this.getItems();
                }, 500);
            }
        },
        mounted() {
            this.getItems();
        },
        methods: {
            getItems() {
                this.loading = true;
                let data = {
                    params: {
                        page: this.current_page,
                        lang_id: this.lang_id,
                        active: this.active,
                        sortBy: this.sortBy,
                        sortDesc: this.sortDesc,
                        q: this.filter
                    }
                };
                axios.get(this.source, data).then((response) => {
                    this.per_page = response.data.meta.per_page;
                    this.total = response.data.meta.total;
                    this.items = response.data.data;
                    this.languages = response.data.languages;
                    this.loading = false;
                }).catch((error) => {
                    console.log(error);
                });
            },
            changeSort() {
                this.loading = true;
                this.getItems();
            },
            confirmDelete(item) {
                this.confirmWindow.confirm = item.id;
                this.confirmWindow.text = this.$t('Menu.you_really_delete') + ': ' + item.title;
                this.confirmWindow.openConfirm = true;
            },
            deleteItem(id) {
                this.confirmWindow.openConfirm = false;
                if (id) {
                    this.loading = true;
                    axios.delete(`${this.source}/${id}`).then((response) => {
                        this.$toast.global.success(this.$t('Page.data_delete'))
                        this.getItems();
                        this.loading = false;
                    }).catch((error) => {
                    });
                }
            }
        }
    }
</script>
