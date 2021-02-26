<template>
    <div>
        <b-card class="mb-4">
            <b-row>
                <b-col sm="6" lg="4" class="my-1">
                    <b-input-group>
                        <b-form-input v-model="filter" :placeholder="$t('Article.insert_query')"/>
                        <b-input-group-append>
                            <b-btn variant="outline-info" :disabled="!filter" @click="filter = ''">
                                {{ $t('Article.clear') }}
                            </b-btn>
                        </b-input-group-append>
                    </b-input-group>
                </b-col>
                <b-col sm="6" lg="3" class="my-1">
                    <b-form-select v-model="lang_id" :options="langOptions"></b-form-select>
                </b-col>
                <b-col sm="6" lg="3" class="my-1 d-flex align-items-center">
                    <div class="custom-control custom-switch custom-">
                        <input type="checkbox" class="custom-control-input" id="checkbox_status"
                               v-model="active"
                               :value="1">
                        <label class="custom-control-label" for="checkbox_status">
                            {{ active ? $t('Article.inactive') : $t('Article.active') }}
                        </label>
                    </div>
<!--                    <b-form-checkbox id="checkbox_status"-->
<!--                                     switch-->
<!--                                     v-model="active"-->
<!--                                     :value="1"-->
<!--                                     :unchecked-value="0">-->

<!--                    </b-form-checkbox>-->
                </b-col>
                <b-col sm="6" lg="2" class="text-right" v-if="canCreate">
                    <b-button v-b-modal.article-settings variant="info">{{ $t('Article.settings') }}</b-button>
                    <router-link class="btn btn-success" :to="{name: `${routeName}.create`}"
                                 :title="$t('Article.add_article')">
                        <fa :icon="['fas', 'plus']"/>
                    </router-link>
                </b-col>
            </b-row>
        </b-card>

        <!-- Main table element -->
        <b-table show-empty
                 :busy="loading"
                 striped
                 stacked="lg"
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
            <template v-slot:cell(image)="row"><img :src="row.item.show_img" alt="" width="40"></template>
            <template v-slot:cell(type)="row">{{ row.item.show_type }}</template>
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
        <b-modal id="article-settings" hide-footer centered>
            <b-row class="mb-1 size" v-for="(size, key) in settings.sizes" :key="key">
                <b-col class="mb-3">
                    <label :for="`name_${key}`">{{ $t('Article.size_name') }}</label>
                    <b-form-input :id="`name_${key}`" v-model="size.name"></b-form-input>
                </b-col>
                <b-col class="mb-3">
                    <label :for="`width_${key}`">{{ $t('Article.width') }}</label>
                    <b-form-input :id="`width_${key}`" v-model.number="size.width" type="number"></b-form-input>
                </b-col>
                <b-col class="mb-3">
                    <label :for="`height_${key}`">{{ $t('Article.height') }}</label>
                    <b-form-input :id="`height_${key}`" v-model.number="size.height" type="number"></b-form-input>
                </b-col>
                <fa :icon="['fas', 'trash-alt']" class="text-danger remove" @click="deleteSize(key)"/>
            </b-row>
            <b-row class="align-items-center">
                <b-col sm="6" class="mb-3">
                    <b-form-select v-model="settings.action" :options="resizes" size="sm" class="my-3"></b-form-select>
                </b-col>
                <b-col sm="6" class="mb-3">
                    <b-form-checkbox v-model="settings.greyscale" switch>
                        {{ $t('Article.greyscale') }}
                    </b-form-checkbox>
                </b-col>
            </b-row>
            <b-row>
                <b-col md="4" class="mb-3">
                    <label for="blur">{{ $t('Article.blur') }}</label>
                    <b-form-input v-model.number="settings.blur" id="blur" type="number" min="0" max="100"></b-form-input>
                </b-col>
                <b-col md="4" class="mb-3">
                    <label for="brightness">{{ $t('Article.brightness') }}</label>
                    <b-form-input v-model.number="settings.brightness" id="brightness" type="number" min="-100"
                                  max="100"></b-form-input>
                </b-col>
                <b-col md="4" class="mb-3">
                    <label for="background">{{ $t('Article.background') }}</label>
                    <div class="d-flex">
                        <b-form-input v-model="settings.background" id="background" type="color"></b-form-input>
                        <b-button @click="removeBg">
                            <fa :icon="['fas', 'trash-alt']"/>
                        </b-button>
                    </div>
                </b-col>
            </b-row>
            <b-row class="justify-content-between">
                <b-col>
                    <b-button variant="info" @click.prevent="addSize" :title="$t('Article.add_size')">
                        <fa :icon="['fas', 'plus']"/>
                    </b-button>
                </b-col>
                <b-col class="text-right">
                    <b-button variant="primary" :class="{'btn-loading': loading_setting}" :title="$t('Article.save')"
                              :disabled="loading_setting"
                              @click="saveSettings">
                        <fa :icon="['fas', 'save']"/>
                    </b-button>
                </b-col>
            </b-row>
        </b-modal>
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
        return {title: this.$t('Article.article')}
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
            languages: {},
            resizes: [
                {
                    value: 'resize',
                    text: this.$t('Menu.resize')
                },
                {
                    value: 'crop',
                    text: this.$t('Menu.crop')
                }
            ],
            settings: {
                action: 'resize',
                greyscale: null,
                blur: null,
                brightness: null,
                background: null,
                sizes: []
            },
            loading_setting: false,
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
                {key: 'image', label: this.$t('Article.image'), sortable: false},
                {key: 'title', label: this.$t('Article.title'), sortable: true},
                {key: 'type', label: this.$t('Article.type'), sortable: true},
                {key: 'lang', label: this.$t('Article.lang'), sortable: false, 'class': 'text-center'},
                {key: 'priority', label: this.$t('Article.priority'), sortable: true, 'class': 'text-center'},
                {key: 'active', label: this.$t('Article.status'), sortable: true, 'class': 'text-center status'},
                {key: 'actions', label: this.$t('Article.action'), 'class': 'text-center'}
            ]
        },
        langOptions() {
            let options = [{
                value: null,
                text: this.$t('Article.all_lang')
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
            this.current_page = 1;
            this.getItems();
        },
        lang_id() {
            this.current_page = 1;
            this.getItems();
        },
        filter() {
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                this.current_page = 1
                this.getItems()
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
                    lang_id: this.lang_id,
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
                if (response.data.settings.sizes) {
                    this.settings = response.data.settings;
                }
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
            this.confirmWindow.text = this.$t('Article.you_really_delete') + ': ' + item.title;
            this.confirmWindow.openConfirm = true;
        },
        deleteItem(id) {
            this.confirmWindow.openConfirm = false;
            if (id) {
                this.loading = true;
                axios.delete(`${this.source}/${id}`).then(() => {
                    this.$toast.global.success(this.$t('Article.data_delete'))
                    this.getItems();
                    this.loading = false;
                }).catch(() => {
                });
            }
        },
        emptySize() {
            return {
                name: '',
                width: 0,
                height: 0,
            }
        },
        addSize() {
            this.settings.sizes.push(this.emptySize())
        },
        deleteSize(index) {
            this.settings.sizes.splice(index, 1)
        },
        removeBg() {
            this.settings.background = null
        },
        saveSettings() {
            this.loading_setting = true;
            axios.post(`${this.source}-settings`, this.settings).then(() => {
                this.$bvModal.hide('article-settings')
                this.$toast.global.success(this.$t('Article.settings_saved'))

                this.loading_setting = false;
            }).catch(() => {
            })
        }
    }
}
</script>
