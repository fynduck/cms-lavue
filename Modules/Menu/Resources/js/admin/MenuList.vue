<template>
    <div>
        <b-card class="mb-4">
            <b-row>
                <b-col md="5" lg="4" class="my-1">
                    <b-input-group>
                        <b-form-input v-model="filter" :placeholder="$t('Menu.insert_query')"/>
                        <b-input-group-append>
                            <b-btn variant="outline-info" :disabled="!filter" @click="filter = ''">{{ $t('Menu.clear') }}</b-btn>
                        </b-input-group-append>
                    </b-input-group>
                </b-col>
                <b-col sm="6" md="3" class="my-1 d-flex align-items-center">
                    <b-form-select v-model="lang_id" :options="langOptions"></b-form-select>
                </b-col>
                <b-col sm="6" md="3" class="my-1 d-flex align-items-center">
                    <b-form-checkbox id="checkbox_status"
                                     class="switch-success"
                                     switch
                                     v-model="active"
                                     :value="1"
                                     :unchecked-value="0">
                        {{ $t('Menu.active_s') }}
                    </b-form-checkbox>
                </b-col>
                <b-col sm="6" md="3" lg="2" class="text-right" v-if="canCreate">
                    <b-button v-b-modal.menu-settings variant="info">{{ $t('Menu.settings') }}</b-button>
                    <router-link class="btn btn-success" :to="{name: `${routeName}.create`}"
                                 :title="$t('Menu.add_menu')">
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
                 stacked="lg"
                 :responsive="true"
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
            <template v-slot:cell(image)="row">
                <img :src="row.item.show_img" alt="" width="40">
            </template>
            <template v-slot:cell(show_page)="row">
                <a :href="row.item.show_page" target="_blank">{{ row.item.show_page }}</a>
            </template>
            <template v-slot:cell(position)="row">
                {{ $t(row.item.position) }}
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
        <b-modal id="menu-settings" hide-footer centered>
            <size-settings :settings="settings" :source="source"></size-settings>
        </b-modal>
        <b-pagination align="center" v-if="total > per_page" size="md" :total-rows="total" v-model="current_page"
                      :per-page="per_page"/>
    </div>
</template>

<script>
import axios from 'axios'
import {mapGetters} from 'vuex'
import SizeSettings from "./components/SizeSettings";

export default {
    middleware: 'auth',
    head() {
        return {title: this.$t('Menu.menu')}
    },
    components: {
        SizeSettings
    },
    data() {
        return {
            items: [],
            active: 0,
            lang_id: null,
            current_page: 1,
            per_page: 25,
            position: null,
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
            languages: {},
            settings: {
                ratios: {
                    width: 0,
                    height: 0,
                },
                ratio: false,
                action: 'resize-crop',
                optimize: null,
                greyscale: null,
                blur: null,
                brightness: null,
                background: null,
                sizes: []
            }
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
                this.getItems()
            }, 500);
        },
        position() {
            this.getItems();
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
                {key: 'image', label: this.$t('Menu.image')},
                {key: 'title', label: this.$t('Menu.title'), sortable: true},
                {key: 'show_page', label: this.$t('Menu.to_page'), sortable: false},
                {key: 'position', label: this.$t('Menu.position'), sortable: true},
                {key: 'lang', label: this.$t('Menu.lang')},
                {key: 'priority', label: this.$t('Menu.priority'), sortable: true, 'class': 'text-center'},
                {key: 'active', label: this.$t('Menu.status'), sortable: true, 'class': 'text-center status'},
                {key: 'actions', label: this.$t('Menu.action'), 'class': 'text-center'}
            ]
        },
        langOptions() {
            let options = [{
                value: null,
                text: this.$t('Menu.all_lang')
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
                    lang_id: this.lang_id,
                    sortBy: this.sortBy,
                    sortDesc: this.sortDesc,
                    position: this.position,
                    q: this.filter
                }
            };
            axios.get(this.source, data).then((response) => {
                this.per_page = response.data.meta.per_page;
                this.total = response.data.meta.total;
                this.items = response.data.data;
                this.languages = response.data.languages;
                if (response.data.settings.sizes) {
                    this.settings = response.data.settings;
                }
                this.loading = false;
            }).catch((error) => {
            });
        },
        changeSort() {
            this.loading = true;
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
                    this.$toast.global.success(this.$t('Menu.data_delete'))
                    this.getItems();
                    this.loading = false;
                }).catch((error) => {
                    this.$toast.global.error(error.response.data.message)
                });
            }
        }
    }
}
</script>