<template>
    <div>
        <b-card class="mb-4">
            <b-row>
                <b-col sm="7" class="my-1">
                    <b-input-group>
                        <b-form-input v-model="filter" :placeholder="$t('CustomForm.insert_query')"/>
                        <b-input-group-append>
                            <b-btn :disabled="!filter" @click="filter = ''" variant="outline-info">{{
                                    $t('CustomForm.clear')
                                }}
                            </b-btn>
                        </b-input-group-append>
                    </b-input-group>
                </b-col>
                <b-col sm="5" class="text-right" v-if="canCreate">
                    <router-link class="btn btn-success" :to="{name: `${routeName}.create`}" :title="$t('CustomForm.add_form')">
                        <fa :icon="['fas', 'plus']"/>
                    </router-link>
                </b-col>
            </b-row>
        </b-card>

        <b-table show-empty
                 :busy="loading"
                 striped
                 responsive
                 hover
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
            <template v-slot:cell(index)="row">
                {{ row.index + 1 }}
            </template>
            <template v-slot:cell(send_emails)="row">
                <b-badge pill variant="info" v-for="(email, key) in row.item.send_emails" :key="key">{{ email }}</b-badge>
            </template>
            <template v-slot:cell(actions)="row">
                <b-button-group>
                    <router-link class="btn btn-success" v-if="row.item.permissions.edit"
                                 :to="{name: `${routeName}.edit`, params: {id: row.item.id}}">
                        <fa :icon="['fas', 'pencil-alt']"/>
                    </router-link>
                    <b-button @click="openCopy(row.index)" variant="secondary" :title="$t('CustomForm.make_copy')"
                              v-if="row.item.permissions.edit">
                        <fa :icon="['fas', 'clone']"/>
                    </b-button>
                    <b-button @click="openCompleted(row.index)" variant="info" :class="{'btn-loading': load_sends}"
                              :title="$t('CustomForm.form_completed')"
                              v-if="row.item.permissions.edit">
                        <fa :icon="['fas', 'paper-plane']"/>
                    </b-button>
                    <b-button variant="danger" v-if="row.item.permissions.destroy" @click.prevent="confirmDelete(row.item)">
                        <fa :icon="['fas', 'trash-alt']"/>
                    </b-button>
                </b-button-group>
            </template>
        </b-table>
        <b-modal v-model="show_completed" size="xl" hide-footer :title="items[form_key].form_name"
                 v-if="form_key !== null && completedItems.length">
            <template v-for="(item, index) in completedItems">
                <b-row class="mb-2">
                    <b-col sm="2">
                        <b-badge variant="dark">{{ $t('CustomForm.form_data') }}</b-badge>
                    </b-col>
                    <b-col sm="10">
                        <b-list-group v-if="Object.keys(item.form_data.fields).length">
                            <b-list-group-item v-for="(value, field) in item.form_data.fields" :key="field">
                                <b-badge variant="light" pill>{{ field }}</b-badge>
                                {{ value }}
                            </b-list-group-item>
                        </b-list-group>
                    </b-col>
                </b-row>
                <hr>
            </template>
            <b-pagination
                v-model="completeCurrentPage"
                :total-rows="completeTotal"
                :per-page="completePerPage"
                size="sm"
                class="my-0"
            ></b-pagination>
        </b-modal>
        <b-modal v-model="show_copy" hide-footer :title="$t('CustomForm.make_copy')">
            <b-form-input v-model="copy_form.form_name"></b-form-input>
            <b-form-select v-model="copy_form.lang_id" :options="languages" class="mt-3"></b-form-select>
            <b-button class="mt-3" block @click="saveCopy" variant="primary">{{ $t('CustomForm.save') }}</b-button>
        </b-modal>
        <confirm v-model="confirmWindow.confirm"
                 :show="confirmWindow.openConfirm"
                 :text="confirmWindow.text"
                 :cancel="$t('CustomForm.cancel')"
                 :yes="$t('CustomForm.yes')"
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
        return {title: this.$t('CustomForm.custom_form')}
    },
    data() {
        return {
            items: [],
            completedItems: [],
            status: 1,
            show_completed: false,
            form_key: null,
            current_page: 1,
            per_page: 20,
            total: null,
            sortBy: null,
            sortDesc: false,
            filter: null,
            loading: false,
            timeout: null,
            completeCurrentPage: 1,
            completePerPage: 2,
            completeTotal: 0,
            show_copy: false,
            load_sends: false,
            copy_form: {
                id: null,
                form_name: null,
                lang_id: null
            },
            languages: [],
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
        completeCurrentPage() {
            this.getCompleted();
        },
        form_key() {
            this.completeCurrentPage = 1
            this.completePerPage = 0
            this.completeTotal = 0
            this.completedItems = []
        },
        filter() {
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                this.current_page = 1;
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
                {key: 'index', label: '#'},
                {key: 'form_name', label: this.$t('CustomForm.title'), sortable: true},
                {key: 'method', label: this.$t('CustomForm.method'), sortable: true},
                {key: 'action', label: this.$t('CustomForm.action')},
                {key: 'send_emails', label: this.$t('CustomForm.send_emails')},
                {key: 'lang', label: this.$t('CustomForm.language')},
                {key: 'actions', label: this.$t('CustomForm.action'), 'class': 'text-center'}
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
                    sortBy: this.sortBy,
                    sortDesc: this.sortDesc,
                    q: this.filter
                }
            };
            axios.get(this.source, data).then((response) => {
                this.per_page = response.data.meta.per_page;
                this.total = response.data.meta.total;
                this.items = response.data.data;

                if (parseInt(this.current_page) === 1) {
                    this.languages = response.data.languages
                }
                this.loading = false;
            }).catch((error) => {
                console.log(error);
            });
        },
        openCompleted(index) {
            this.show_completed = true;
            this.form_key = index;
            this.load_sends = true;
            this.getCompleted()
        },
        getCompleted() {
            const id = this.items[this.form_key].id
            let data = {
                params: {
                    page: this.completeCurrentPage
                }
            };
            axios.get(`${this.source}-list/${id}`, data).then((response) => {
                this.completePerPage = response.data.meta.per_page;
                this.completeTotal = response.data.meta.total;
                this.completedItems = response.data.data;
                this.load_sends = false
            }).catch((error) => {
                this.load_sends = false
                console.log(error);
            });
        },
        openCopy(key) {
            this.copy_form.form_name = this.items[key].form_name
            this.copy_form.id = this.items[key].id
            this.show_copy = true;
        },
        saveCopy() {
            axios.post(`${this.source}-clone/${this.copy_form.id}`, this.copy_form).then(() => {
                this.$toast.global.success(this.$t('CustomForm.data_save'))
                this.show_copy = false;
                this.copy_form.form_name = null
                this.copy_form.id = null
                this.copy_form.lang_id = null
                this.current_page = 1;
                this.getItems();
            }).catch(() => {
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
            this.confirmWindow.text = this.$t('CustomForm.you_really_delete') + ': ' + item.form_name;
            this.confirmWindow.openConfirm = true;
        },
        deleteItem(id) {
            this.confirmWindow.openConfirm = false;
            if (id) {
                this.loading = true;
                axios.delete(`${this.source}/${id}`).then(() => {
                    this.$toast.global.success(this.$t('CustomForm.data_delete'))
                    this.getItems();
                    this.loading = false;
                }).catch(() => {
                });
            }
        }
    }
}
</script>
