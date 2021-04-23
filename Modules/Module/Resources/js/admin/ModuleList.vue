<template>
    <div>
        <!-- Main table element -->
        <b-table show-empty
                 :busy="loading"
                 striped
                 stacked="lg"
                 hover
                 :items="items"
                 :filter="filter"
                 :fields="fields"
                 :sort-by.sync="sortBy"
                 :sort-desc.sync="sortDesc"
        >
            <template v-slot:cell(active)="row">
                <b-form-checkbox :id="`checkbox_module_status_${row.item.name}`"
                                 class="switch-success"
                                 switch
                                 v-model="row.item.active"
                                 :value="1"
                                 :unchecked-value="0"
                                 v-if="canEdit && !exceptDisable.includes(row.item.name)"
                                 @change="changeStatus(row.item)">
                    {{ active ? $t('Module.inactive') : $t('Module.active') }}
                </b-form-checkbox>
                <div v-else>
                    <fa :icon="['far', 'check-circle']" class="text-success" v-if="row.item.active"/>
                    <fa :icon="['far', 'times-circle']" class="text-danger" v-else/>
                </div>
            </template>
            <template v-slot:cell(actions)="row">
                <b-button variant="danger" v-if="row.item.permissions.destroy" @click.prevent="confirmDelete(row.item)">
                    <fa :icon="['fas', 'trash-alt']"/>
                </b-button>
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
    </div>
</template>

<script>
import {mapGetters} from "vuex";
import axios from "axios";

export default {
    middleware: 'auth',
    data() {
        return {
            sortBy: null,
            sortDesc: false,
            items: [],
            active: 0,
            filter: null,
            loading: false,
            timeout: null,
            confirmWindow: {
                confirm: null,
                openConfirm: false,
                text: ''
            },
            exceptDisable: [
                'Page',
                'Language',
                'Module',
                'Redirect',
                'UserGroup',
                'User',
                'Translate',
                'Settings',
            ]
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
        canEdit() {
            if (this.authenticated) {
                const arrayName = this.$router.currentRoute.name.split('.');
                return this.permissions(arrayName[0], 'edit')
            }

            return false;
        },
        fields() {
            return [
                {key: 'name', label: this.$t('Module.title'), sortable: true},
                {key: 'active', label: this.$t('Module.status'), sortable: true, 'class': 'text-center status'},
                // {key: 'actions', label: this.$t('Module.action'), 'class': 'text-center'}
            ]
        }
    },
    mounted() {
        this.getItems();
    },
    methods: {
        getItems() {
            this.loading = true;
            axios.get(this.source).then((response) => {
                this.items = response.data;

                this.loading = false;

            }).catch((error) => {
                console.log(error);
            });
        },
        changeStatus(item) {
            this.$nextTick(() => {
                axios.put(this.source + '/' + item.name, {status: item.active}).then(response => {
                    this.$toast.global.success(this.$t('Module.status_saved'))
                }).catch(error => {
                    console.log(error)
                })
            })
        },
        confirmDelete(item) {
            this.confirmWindow.confirm = item.name;
            this.confirmWindow.text = this.$t('Module.you_really_delete') + ': ' + item.name;
            this.confirmWindow.openConfirm = true;
        },
        deleteItem(name) {
            this.confirmWindow.openConfirm = false;
            if (name) {
                this.loading = true;
                axios.delete(`${this.source}/${name}`).then(() => {
                    this.$toast.global.success(this.$t('Module.data_delete'))
                    this.getItems();
                    this.loading = false;
                }).catch(() => {
                });
            }
        }
    }
}
</script>