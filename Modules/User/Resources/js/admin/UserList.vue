<template>
    <div>
        <b-card class="mb-4">
            <b-row>
                <b-col md="5" lg="6" class="my-1">
                    <b-input-group>
                        <b-form-input v-model="filter" :placeholder="$t('User.insert_query')"/>
                        <b-input-group-append>
                            <b-btn variant="outline-info" :disabled="!filter" @click="filter = ''">
                                {{ $t('User.clear') }}
                            </b-btn>
                        </b-input-group-append>
                    </b-input-group>
                </b-col>
                <b-col class="my-1" md="5" lg="4">
                    <b-form-group class="mb-0">
                        <b-form-select class="form-select" :options="groups" v-model="group">
                            <template slot="first">
                                <option :value="null">{{ $t('User.default_option') }}</option>
                            </template>
                        </b-form-select>
                    </b-form-group>
                </b-col>
                <b-col md="2" class="text-end" v-if="canCreate">
                    <router-link class="btn btn-success" :to="{name: `${routeName}.create`}"
                                 :title="$t('User.add_user')">
                        <fa :icon="['fas', 'plus']"/>
                    </router-link>
                </b-col>
            </b-row>
        </b-card>
        <b-table show-empty
                 :busy="loading"
                 striped
                 hover
                 stacked="sm"
                 :items="items"
                 :fields="fields">
            <template #table-busy>
                <div class="text-center text-success my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Loading...</strong>
                </div>
            </template>
            <template v-slot:cell(actions)="row">
                <b-button-group>
                    <router-link class="btn btn-success" v-if="row.item.permissions.edit"
                                 :to="{name: `${routeName}.edit`, params: {id: row.item.id}}">
                        <fa :icon="['fas', 'pencil-alt']"/>
                    </router-link>
                    <b-button variant="danger" v-if="row.item.permissions.destroy"
                              @click.prevent="confirmDelete(row.item)">
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
        return {title: this.$t('User.user')}
    },
    data() {
        return {
            items: [],
            loading: false,
            group: null,
            groups: [],
            filter: '',
            current_page: 1,
            per_page: 20,
            total: null,
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
                {key: 'name', label: this.$t('User.name')},
                {key: 'email', label: this.$t('User.email')},
                {key: 'group', label: this.$t('User.group')},
                {key: 'actions', label: this.$t('User.action'), 'class': 'text-end'}
            ]
        }
    },
    mounted() {
        this.getItems();
    },
    watch: {
        group() {
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
    methods: {
        getItems() {
            this.loading = true;
            axios.get(this.source, {
                params: {
                    q: this.filter,
                    group: this.group
                }
            }).then((response) => {
                this.items = response.data.data;
                this.groups = response.data.groups;
                this.per_page = response.data.meta.per_page;
                this.total = response.data.meta.total;
                this.$nextTick(() => {
                    this.loading = false;
                })
            }).catch((error) => {
                console.log(error);
            });
        },
        confirmDelete(item) {
            this.confirmWindow.confirm = item.id;
            this.confirmWindow.text = this.$t('User.you_really_delete') + ': ' + item.name;
            this.confirmWindow.openConfirm = true;
        },
        deleteItem(id) {
            this.confirmWindow.openConfirm = false;
            if (id) {
                this.loading = true;
                axios.delete(`${this.source}/${id}`).then(() => {
                    this.$toast.global.success(this.$t('User.data_delete'))
                    this.getItems();
                    this.loading = false;
                }).catch(() => {
                    this.$toast.global.error(this.$t('User.data_not_delete'))
                });
            }
        }
    }
}
</script>
