<template>
    <div>
        <b-table show-empty
                 :busy="loading"
                 striped
                 stacked="lg"
                 hover
                 :items="items"
                 :fields="fields"
        >
            <template v-slot:cell(show_details)="row">
                <b-button size="sm" @click="row.toggleDetails" variant="outline-dark">
                    {{ $t('Search.select_model') }}
                </b-button>
            </template>
            <template #row-details="row">
                <b-row class="mb-2">
                    <b-col v-for="(model, index) in row.item.models" :key="index">
                        <div class="form-check form-switch switch-success">
                            <input type="checkbox" class="form-check-input"
                                   v-model="model.active"
                                   :id="`active_${index}`" :value="1">
                            <label class="form-check-label" :for="`active_${index}`">
                                {{ model.name }}
                            </label>
                        </div>
                    </b-col>
                </b-row>
            </template>
        </b-table>
        <p class="text-end" v-if="canEdit">
            <button :class="{'btn btn-success': true, 'btn-loading': submit}" type="button" :title="$t('Search.save')"
                    :disabled="submit" @click="save()">
                <fa :icon="['fas', 'save']"/>
            </button>
        </p>
    </div>
</template>

<script>
import {mapGetters} from "vuex";
import axios from "axios";

export default {
    middleware: 'auth',
    computed: {
        ...mapGetters({
            authenticated: 'auth/check',
            permissions: 'auth/checkPermission'
        }),
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
                {
                    key: 'name', label: this.$t('Search.title'), sortable: true
                },
                {
                    key: 'show_details', label: this.$t('Search.activate'), sortable: true, 'class': 'text-center'
                },
            ]
        }
    },
    data() {
        return {
            items: [],
            loading: false,
            timeout: null,
            submit: false
        }
    },
    mounted() {
        this.getItems()
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
        save() {
            this.submit = true;
            axios.post(this.source, this.items).then(response => {
                this.$toast.global.success(this.$t('Search.data_saved'))
                this.submit = false;
            }).catch(error => {
                location.reload()
            })
        }
    }
}
</script>