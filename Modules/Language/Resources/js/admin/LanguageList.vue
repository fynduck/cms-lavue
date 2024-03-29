<template>
    <div>
        <b-card class="mb-4">
            <b-row>
                <b-col md="6" class="my-1">
                    <b-input-group>
                        <b-form-input v-model="filter" :placeholder="$t('Language.insert_query')"/>
                        <b-input-group-append>
                            <b-btn variant="outline-info" :disabled="!filter" @click="filter = ''">{{ $t('Language.clear') }}</b-btn>
                        </b-input-group-append>
                    </b-input-group>
                </b-col>
                <b-col sm="6" md="3" class="my-1 d-flex align-items-center">
                    <div class="form-check form-switch switch-success">
                        <input type="checkbox" class="form-check-input"
                               v-model="active"
                               id="checkbox_status" :value="1">
                        <label class="form-check-label" for="checkbox_status">
                            {{ $t('Language.active_s') }}
                        </label>
                    </div>
                </b-col>
                <b-col sm="6" md="3" class="text-end" v-if="canCreate">
                    <router-link class="btn btn-success" :to="{name: `${routeName}.create`}"
                                 :title="$t('Language.add_language')">
                        <fa :icon="['fas', 'plus']"/>
                    </router-link>
                </b-col>
            </b-row>
        </b-card>

        <!-- Main table element -->
        <b-table show-empty
                 :busy="loading"
                 striped
                 hover
                 stacked="md"
                 :items="items"
                 :fields="fields"
                 :sort-by.sync="sortBy"
                 :sort-desc.sync="sortDesc"
                 @sort-changed="changeSort"
        >
            <template v-slot:cell(image)="row">
                <b-img width="25" :src="row.item.show_img" alt=""/>
            </template>
            <template v-slot:cell(default)="row">
                <fa :icon="['fas', 'globe']" class="text-success" v-if="row.item.default"/>
            </template>
            <template v-slot:cell(active)="row">
                <fa :icon="['far', 'check-circle']" class="text-success" v-if="row.item.active"/>
                <fa :icon="['far', 'times-circle']" class="text-danger" v-else/>
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
        <confirm v-model="confirmWindow.confirm"
                 :show="confirmWindow.openConfirm"
                 :text="confirmWindow.text"
                 :cancel="$t('cancel')"
                 :yes="$t('yes')"
                 @input="deleteItem"
                 v-if="confirmWindow.openConfirm"
        ></confirm>
        <b-pagination align="center" v-if="total > per_page" size="md" :total-rows="total" v-model="current_page"
                      :per-page="per_page"/>
    </div>
</template>

<script>
    import axios from 'axios';
    import {mapGetters} from "vuex";

    export default {
        middleware: 'auth',
        name: "LanguageList",
        head() {
            const title = this.$t('Language.language');
            return {title}
        },
        data() {
            return {
                items: [],
                active: 1,
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
                }
            }
        },
        watch: {
            current_page() {
                this.getItems();
            },
            active() {
                this.getItems();
            },
            filter() {
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    this.getItems();
                }, 500);
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
                return `/admin/${this.$route.name.split('.')[0]}`
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
                    {key: 'image', label: this.$t('Language.image'), sortable: false},
                    {key: 'name', label: this.$t('Language.title'), sortable: true},
                    {key: 'url', label: this.$t('Language.url')},
                    {key: 'default', label: this.$t('Language.default')},
                    {key: 'active', label: this.$t('Language.status'), sortable: true, 'class': 'text-center status'},
                    {key: 'priority', label: this.$t('Language.priority'), sortable: true, 'class': 'text-center'},
                    {key: 'actions', label: this.$t('Language.action'), 'class': 'text-end'}
                ]
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
                    this.loading = false;
                }).catch((error) => {
                    console.log(error);
                });
            },
            changeSort() {
                this.loading = true;
                setTimeout(() => {
                    this.getItems();
                }, 200);
            },
            confirmDelete(item) {
                this.confirmWindow.confirm = item.id;
                this.confirmWindow.text = this.$t('Language.you_really_delete') + ': ' + item.name;
                this.confirmWindow.openConfirm = true;
            },
            deleteItem(id) {
                this.confirmWindow.openConfirm = false;
                if (id) {
                    this.loading = true;
                    axios.delete(`${this.source}/${id}`).then(() => {
                        this.$toast.global.success(this.$t('Language.data_delete'))
                        this.getItems();
                    }).catch(() => {
                    });
                }
            }
        }
    }
</script>

<style lang="stylus">
    .w-20
        width 20% !important
</style>
