<template>
    <div>
        <b-card>
            <b-row>
                <b-col sm="6" class="my-1">
                    <b-input-group>
                        <b-form-input v-model="filter" :placeholder="$t('Redirect.insert_query')"/>
                        <b-input-group-append>
                            <b-btn variant="outline-info" :disabled="!filter" @click="filter = ''">{{ $t('Redirect.clear') }}</b-btn>
                        </b-input-group-append>
                    </b-input-group>
                </b-col>
                <b-col sm="3" class="my-1 d-flex align-items-center">
                    <b-form-checkbox id="checkbox_status"
                                     v-model="active"
                                     :value="1"
                                     :unchecked-value="0">
                        {{ active ? $t('Redirect.inactive') : $t('Redirect.active') }}
                    </b-form-checkbox>
                </b-col>
                <b-col sm="3" class="text-right" v-if="canCreate">
                    <router-link class="btn btn-primary" :to="{name: `${routeName}.create`}"
                                 :title="$t('Redirect.add_redirect')">
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
                 responsive
                 :items="items"
                 :fields="fields"
                 :sort-by.sync="sortBy"
                 :sort-desc.sync="sortDesc"
                 @sort-changed="changeSort">

            <template v-slot:cell(active)="row">
                <fa :icon="['far', 'check-circle']" class="text-success" v-if="row.item.active"/>
                <fa :icon="['far', 'times-circle']" class="text-danger" v-else/>
            </template>
            <template v-slot:cell(actions)="row">
                <b-button-group>
                    <router-link class="btn btn-primary" v-if="row.item.permissions.edit"
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
import axios from 'axios'
import {mapGetters} from 'vuex'
    export default {
        middleware: 'auth',
        head() {
            const title = this.routeEdit ? this.$t('Redirect.edit_redirect') : this.$t('Redirect.add_redirect');
            return {title}
        },
        name: "RedirectList",
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
                    {key: 'from', label: this.$t('Redirect.from'), sortable: false},
                    {key: 'to', label: this.$t('Redirect.to'), sortable: true},
                    {key: 'active', label: this.$t('Redirect.active'), sortable: true, 'class': 'text-center'},
                    {key: 'actions', label: this.$t('Redirect.action'), 'class': 'text-right'}
                ]
            }
        },
        watch: {
            current_page() {
                this.getItems();
            },
            active() {
                this.current_page = 1;
                this.getItems();
            },
            filter() {
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    this.current_page = 1;
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
                        active: this.active,
                        sortBy: this.sortBy,
                        sortDesc: this.sortDesc,
                        q: this.filter
                    }
                };
                axios.get(this.source, data).then((response) => {
                    this.per_page = response.data.per_page;
                    this.total = response.data.total;
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
                this.confirmWindow.text = this.$t('Redirect.you_really_delete') + ': ' + item.from + '->' + item.to;
                this.confirmWindow.openConfirm = true;
            },
            deleteItem(id) {
                this.confirmWindow.openConfirm = false;
                if (id) {
                    this.loading = true;
                    axios.delete(`${this.source}/${id}`).then((response) => {
                        this.$bvToast.toast(this.$t('Redirect.data_delete'), {
                            title: this.$t('Redirect.status'),
                            variant: 'info',
                            solid: true
                        })
                        this.getItems();
                        this.loading = false;
                    }).catch((error) => {
                        this.$bvToast.toast(error, {
                            title: this.$t('Redirect.status'),
                            variant: 'info',
                            solid: true
                        })
                    });
                }
            }
        }
    }
</script>
