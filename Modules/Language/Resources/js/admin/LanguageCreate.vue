<template>
    <div>
        <p class="title_form">
            {{ routeEdit ? $t('Language.edit_language') : $t('Language.add_language') }}
        </p>
        <form @submit.prevent="onSubmit" v-if="!loading">
            <router-link class="btn btn-light submit_absolute cancel" :to="{name: 'language.index'}"
                         :title="$t('Language.cancel')">
                <fa :icon="['fas', 'reply']"/>
            </router-link>
            <button :class="{'btn btn-success submit_absolute': true, 'btn-loading': submit}" type="submit"
                    :title="$t('Language.save')"
                    :disabled="submit">
                <fa :icon="['fas', 'save']"/>
            </button>
            <div class="tab-content pt-4">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="name">{{ $t('Language.title') }}</label>
                        <input type="text" :class="['form-control', errors['name'] ? ' is-invalid' : '']"
                               :placeholder="$t('Language.title')"
                               id="name" v-model="item.name">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="country_iso">{{ $t('Language.country_iso') }}</label>
                        <input type="text" :class="['form-control', errors['country_iso'] ? ' is-invalid' : '']"
                               :placeholder="$t('Language.country_iso')" id="country_iso" v-model="item.country_iso">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="slug">{{ $t('Language.slug') }}</label>
                        <input type="text" :class="['form-control', errors['slug'] ? ' is-invalid' : '']"
                               :placeholder="$t('Language.slug')" id="slug" v-model="item.slug">
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="form-group col-md-3 my-4">
                        <upload v-model="item.image"></upload>
                    </div>
                    <div class="form-group col-md-3">
                        <b-form-checkbox id="checkbox_default"
                                         switch
                                         v-model="item.default"
                                         :value="1"
                                         :unchecked-value="0">
                            {{ $t('Language.default') }}
                        </b-form-checkbox>
                    </div>
                    <div class="form-group col-md-3">
                        <b-form-checkbox id="checkbox_active"
                                         switch
                                         v-model="item.active"
                                         :value="1"
                                         :unchecked-value="0">
                            {{ $t('Language.on_off') }}
                        </b-form-checkbox>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="input-priority">{{ $t('Language.priority') }}</label>
                        <input type="number" v-model="item.priority" id="input-priority" class="form-control"
                               :placeholder="$t('Language.priority')"
                               min="0">
                    </div>
                </div>
                <p class="text-right">
                    <router-link class="btn btn-light" :to="{name: 'language.index'}" :title="$t('Language.cancel')">
                        <fa :icon="['fas', 'reply']"/>
                    </router-link>
                    <button :class="{'btn btn-success': true, 'btn-loading': submit}" type="submit" :title="$t('Language.save')"
                            :disabled="submit">
                        <fa :icon="['fas', 'save']"/>
                    </button>
                </p>
            </div>

        </form>
    </div>
</template>

<script>
import axios from 'axios';

import Upload from "../../../../../admin/components/Upload";

export default {
    middleware: 'auth',
    name: "LanguageCreate",
    metaInfo() {
        const title = this.routeEdit ? this.$t('Language.edit_language') : this.$t('Language.add_language');
        return {title}
    },

    components: {
        Upload
    },
    data() {
        return {
            item: null,
            errors: {},
            loading: true,
            submit: false
        }
    },
    computed: {
        routeEdit() {
            return typeof this.$route.params.id !== "undefined";
        },
        sourceActionMethod() {
            const arrayRoute = this.$route.name.split('.');

            let action = `/admin/${arrayRoute[0]}`;
            let method = 'post'

            if (typeof this.$route.params.id !== "undefined") {
                action += `/${this.$route.params.id}`
                method = 'put'
            }

            return {
                'action': action,
                'method': method
            };
        },
        source() {
            const arrayRoute = this.$route.name.split('.');
            let action = `/admin/${arrayRoute[0]}`;
            if (typeof this.$route.params.id !== "undefined")
                return `${action}/${this.$route.params.id}`;

            return `${action}/0`;
        }
    },
    watch: {
        'item.image'(newValue, oldValue) {
            if (!newValue && oldValue) {
                this.deleteImage(oldValue)
            }
        }
    },
    mounted() {
        this.getItem()
    },
    methods: {
        getItem() {
            axios.get(this.source).then(response => {
                this.item = response.data.data;
                this.loading = false;
            }).catch(error => {
                console.log(error)
            })
        },
        getImage(event) {
            this.item.image = event;
        },
        onSubmit() {
            this.submit = true;
            axios({
                method: this.sourceActionMethod.method,
                url: this.sourceActionMethod.action,
                data: this.item
            }).then(() => {
                this.$toast.global.success(this.$t('Language.data_saved'))

                setTimeout(() => {
                    this.$router.push({
                        name: `${this.$route.name.split('.')[0]}.index`
                    })
                }, 1000)
            }).catch(error => {
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors
                    this.submit = false;
                }
            })
        },
        deleteImage(image) {
            const paths = image.split('/');
            axios.delete(`${this.source}?image=${paths[paths.length - 1]}`)
        }
    }
}
</script>