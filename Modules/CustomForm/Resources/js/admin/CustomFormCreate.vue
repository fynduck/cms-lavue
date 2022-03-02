<template>
    <div class="create_custom_form">
        <p class="title_form">
            {{ routeEdit ? $t('CustomForm.edit_form') : $t('CustomForm.add_form') }}
        </p>
        <form @submit.prevent="onSubmit">
            <router-link class="btn btn-light submit_absolute cancel" :to="{name: 'custom-form.index'}"
                         :title="$t('CustomForm.cancel')">
                <fa :icon="['fas', 'reply']"/>
            </router-link>
            <button :class="{'btn btn-success submit_absolute': true, 'btn-loading': loading}" type="submit"
                    :title="$t('CustomForm.save')"
                    :disabled="loading">
                <fa :icon="['fas', 'save']"/>
            </button>
            <div class="row">
                <div class="mb-3 col-md-7">
                    <label for="title" class="form-label">{{ $t('CustomForm.title') }}</label>
                    <input type="text" :class="{'form-control': true, 'is-invalid': errors.form_name}" v-model="form.form_name"
                           name="form_name" id="title" placeholder="Feedback">
                </div>
                <div class="mb-3 col-md-5 d-flex align-items-end">
                    <div class="custom-control custom-checkbox my-1 me-sm-2">
                        <input type="checkbox" name="file" class="custom-control-input" id="file" v-model="form.file">
                        <label class="custom-control-label" for="file">{{ $t('CustomForm.upload_file') }}</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="form_class" class="form-label">{{ $t('CustomForm.form_class') }}</label>
                    <input type="text" class="form-control" v-model="form.form_class" name="form_class" id="form_class"
                           placeholder="feedback">
                </div>
                <div class="mb-3 col-md-6">
                    <label for="form_id" class="form-label">{{ $t('CustomForm.form_id') }}</label>
                    <input type="text" class="form-control" v-model="form.form_id" name="form_id" id="form_id"
                           placeholder="feedback">
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-5">
                    <label for="action" class="form-label">{{ $t('CustomForm.action') }}</label>
                    <select name="action" id="action" :class="['form-select', errors.action ? 'is-invalid' : '']"
                            v-model="form.action">
                        <option v-for="(title, action) in actions" :value="action">{{ $t(title) }}</option>
                    </select>
                </div>
                <div class="mb-3 col-md-4">
                    <label for="method" class="form-label">{{ $t('CustomForm.method') }}</label>
                    <select name="method" id="method" :class="['form-select', errors.method ? 'is-invalid': '']"
                            v-model="form.method">
                        <option v-for="(title, method) in methods" :value="method">{{ title }}</option>
                    </select>
                </div>
                <div class="mb-3 col-md-3">
                    <label for="languages" class="form-label">{{ $t('CustomForm.languages') }}</label>
                    <select name="lang" id="languages" :class="['form-select', errors.lang ? 'is-invalid': '']"
                            v-model="form.lang_id">
                        <option v-for="(lang, id) in languages" :value="id">{{ lang }}</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6">
                    <custom-select v-model="form.show_on"
                                   :source="admin_search"
                                   :label="$t('CustomForm.show_on')"
                                   :no_result="$t('CustomForm.no_results')"
                                   v-if="loaded_data"
                    ></custom-select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ $t('CustomForm.send_emails') }}</label>
                    <v-select
                        v-model="form.send_emails"
                        taggable
                        multiple
                        :label="$t('CustomForm.send_emails')"
                        :options="form.send_emails"
                    >
                        <template slot="no-options">
                            {{ $t('CustomForm.no_results') }}
                        </template>
                    </v-select>
                </div>
            </div>
            <draggable v-model="form.fields" v-bind="dragOptions">
                <div class="card mb-4" v-for="(field, key) in form.fields" :key="key">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $t('CustomForm.field') }}</strong>
                            <b-badge variant="dark">&#8470;{{ key + 1 }}</b-badge>
                        </div>
                        <em>{{ field.label || field.placeholder }}</em>
                        <fa :icon="['fas', 'trash-alt']" class="trash" @click="confirmDelete(key)"/>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="row">
                                <div class="mb-3 col-md-3">
                                    <label :for="`block_class_${key}`" class="form-label">{{
                                            $t('CustomForm.block_class')
                                        }}</label>
                                    <input type="text" class="form-control" v-model="field.block_class" name="block_class"
                                           :id="`block_class_${key}`" placeholder="block_class">
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label :for="`field_class_${key}`" class="form-label">{{
                                            $t('CustomForm.field_class')
                                        }}</label>
                                    <input type="text" class="form-control" v-model="field.field_class" name="field_class"
                                           :id="`field_class_${key}`" placeholder="field_class">
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label :for="`field_id_${key}`" class="form-label">{{ $t('CustomForm.field_id') }}</label>
                                    <input type="text" class="form-control" v-model="field.field_id" name="field_id"
                                           :id="`field_id_${key}`" placeholder="field_id">
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label :for="`field_label_${key}`" class="form-label">{{
                                            $t('CustomForm.field_label')
                                        }}</label>
                                    <input type="text" class="form-control" v-model="field.label" name="field_label"
                                           :id="`field_label_${key}`" placeholder="field_label">
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="mb-3 col-md-3">
                                    <label :for="`placeholder_${key}`" class="form-label">
                                        {{ $t('CustomForm.placeholder') }}
                                    </label>
                                    <input type="text" class="form-control" name="placeholder" :id="`placeholder_${key}`"
                                           placeholder="placeholder" v-model="field.placeholder">
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label :for="`name_${key}`" class="form-label">{{ $t('CustomForm.field_name') }}</label>
                                    <input type="text" :id="`name_${key}`" placeholder="name" v-model="field.name" name="name"
                                           :class="['form-control', errors[`fields.${key}.name`] ? 'is-invalid': '']">
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label :for="`type_${key}`" class="form-label">{{ $t('CustomForm.type_field') }}</label>
                                    <select name="type" :id="`type_${key}`"
                                            :class="['form-control', errors[`fields.${key}.type`] ? 'is-invalid': '']"
                                            v-model="field.type">
                                        <option v-for="(title, type) in types_field" :value="type">
                                            {{ $t(`${title}`) }}
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">{{ $t('CustomForm.field_validate') }}</label>
                                    <br>
                                    <v-select
                                        v-model="field.validate"
                                        multiple
                                        :options="validations"
                                        :reduce="item => item.value"
                                        label="title"
                                        :selectable="() => field.validate.length < 2"
                                    />
                                </div>
                            </div>
                        </li>
                        <draggable v-model="field.options" v-bind="dragOptions" tag="li" class="list-group-item mt-3"
                                   v-if="fieldOptions.includes(field.type)">
                            <div class="card mb-4" v-for="(option, option_key) in field.options">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $t('CustomForm.option') }}</strong>
                                        <b-badge variant="dark">&#8470;{{ option_key + 1 }}</b-badge>
                                    </div>
                                    <em>{{ option.title }}</em>
                                    <fa :icon="['fas', 'trash-alt']" class="trash" @click="confirmDelete(key, option_key)"/>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label :for="`option_title_${option_key}`" class="form-label">
                                                    {{ $t('CustomForm.text') }}
                                                </label>
                                                <input type="text"
                                                       :class="['form-control', errors[`fields.${key}.options.${option_key}.title`] ? 'is-invalid': '']"
                                                       v-model="option.title" name="title"
                                                       :id="`option_title_${option_key}`"
                                                       placeholder="custom_input">
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label :for="`value_${option_key}`" class="form-label">{{
                                                        $t('CustomForm.value')
                                                    }}</label>
                                                <input type="text"
                                                       :class="['form-control', errors[`fields.${key}.options.${option_key}.value`] ? 'is-invalid': '']"
                                                       v-model="option.value" name="value" :id="`value_${option_key}`"
                                                       placeholder="custom_input">
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label :for="`option_class_${option_key}`" class="form-label">
                                                    {{ $t('CustomForm.option_class') }}
                                                </label>
                                                <input type="text" class="form-control" v-model="option.option_class"
                                                       name="option_class"
                                                       :id="`option_class_${option_key}`" placeholder="custom_input">
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label :for="`option_id_${option_key}`" class="form-label">
                                                    {{ $t('CustomForm.option_id') }}
                                                </label>
                                                <input type="text" class="form-control" v-model="option.option_id"
                                                       name="option_id"
                                                       :id="`option_id_${option_key}`" placeholder="custom_input">
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </draggable>
                    </ul>
                    <button class="btn btn-success btn-sm" type="button" @click.prevent="addOption(key)"
                            :title="$t('CustomForm.add_option')" v-if="fieldOptions.includes(field.type)">
                        <fa :icon="['fas', 'plus']"/>
                        <span class="ms-2">{{ $t('CustomForm.add_option') }}</span>
                    </button>
                </div>
            </draggable>
            <div class="row my-4">
                <div class="col">
                    <button class="btn btn-success btn-sm" type="button" @click.prevent="addField"
                            :title="$t('CustomForm.add_field')">
                        <fa :icon="['fas', 'plus']"/>
                    </button>
                </div>
                <div class="col text-end">
                    <router-link class="btn btn-light" :to="{name: 'custom-form.index'}"
                                 :title="$t('CustomForm.cancel')">
                        <fa :icon="['fas', 'reply']"/>
                    </router-link>
                    <button :class="{'btn btn-success': true, 'btn-loading': loading}" type="submit"
                            :title="$t('CustomForm.save')"
                            :disabled="loading">
                        <fa :icon="['fas', 'save']"/>
                    </button>
                </div>
            </div>
            <confirm v-model="confirmWindow.confirm"
                     :show="confirmWindow.openConfirm"
                     :text="confirmWindow.text"
                     :cancel="$t('CustomForm.cancel')"
                     :yes="$t('CustomForm.yes')"
                     @input="deleteItem"
                     v-if="confirmWindow.openConfirm"
            ></confirm>
        </form>
    </div>
</template>

<script>
import axios from 'axios'
import {mapGetters} from 'vuex'

import CustomSelect from "../../../../../admin/components/CustomSelect";
import vSelect from 'vue-select'
import draggable from 'vuedraggable'

export default {
    middleware: 'auth',
    head() {
        return {title: this.$t('CustomForm.custom_form')}
    },
    components: {
        CustomSelect,
        vSelect,
        draggable
    },
    data() {
        return {
            admin_search: '/admin/live-select',
            form: {
                form_name: '',
                file: false,
                form_class: '',
                form_id: '',
                action: '',
                method: '',
                send_emails: [],
                show_on: [],
                lang_id: null,
                fields: [
                    {
                        type: '',
                        block_class: '',
                        field_class: '',
                        field_id: '',
                        name: '',
                        label: '',
                        placeholder: '',
                        validate: [],
                        options: []
                    }
                ]
            },
            loaded_data: false,
            actions: [],
            methods: [],
            languages: [],
            types_field: [],
            validations: [],
            errors: {},
            fieldOptions: [
                'select',
                'radio',
                'checkbox'
            ],
            loading: false,
            confirmWindow: {
                confirm: null,
                openConfirm: false,
                text: '',
                field_id: null,
                option_id: null
            }
        }
    },
    computed: {
        ...mapGetters({
            locale: 'lang/locale',
            locales: 'lang/locales',
            token: 'auth/token',
        }),
        routeEdit() {
            return typeof this.$route.params.id !== "undefined";
        },
        sourceActionMethod() {
            const arrayRoute = this.$route.name.split('.');

            let action = `/admin/${arrayRoute[0]}`;
            let method = 'post'

            if (this.routeEdit) {
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
        },
        dragOptions() {
            return {
                animation: 200,
                group: "description",
                disabled: false,
                ghostClass: "ghost"
            };
        }
    },
    mounted() {
        this.getData();
    },
    methods: {
        getData() {
            axios.get(this.source).then((response) => {
                this.actions = response.data.actions;
                this.methods = response.data.methods;
                this.languages = response.data.languages;
                this.types_field = response.data.types_field;
                response.data.validations.forEach((item, key) => {
                    this.validations.push({
                        title: this.$t(`${item.title}`),
                        value: item.value,
                    })
                })
                if (response.data.data) {
                    this.form = response.data.data;
                    this.$nextTick(() => {
                        this.loaded_data = true;
                    })
                } else {
                    this.$nextTick(() => {
                        this.form.action = Object.keys(this.actions)[0];
                        this.form.method = Object.keys(this.methods)[0];
                        this.form.fields[0].type = Object.keys(this.types_field)[0];
                        this.loading = false;
                        this.loaded_data = true;
                    });
                }
            }).catch((error) => {
                console.log(error);
            });
        },
        addField() {
            this.form.fields.push({
                type: Object.keys(this.types_field)[0],
                block_class: '',
                field_class: '',
                field_id: '',
                label: '',
                placeholder: '',
                name: '',
                validate: [],
                options: []
            });
        },
        addOption(field_id) {
            this.form.fields[field_id].options.push({
                title: '',
                value: '',
                option_class: '',
                option_id: ''
            });
        },
        onSubmit() {
            this.loading = true;
            axios({
                method: this.sourceActionMethod.method,
                url: this.sourceActionMethod.action,
                data: this.form
            }).then(() => {
                this.$toast.global.success(this.$t('CustomForm.data_saved'))
                setTimeout(() => {
                    this.$router.push({
                        name: `${this.$route.name.split('.')[0]}.index`
                    })
                }, 1000)
            }).catch((error) => {
                this.loading = false;
                this.errors = error.response.data.errors
            });
        },
        confirmDelete(field_id, option_id = null) {
            field_id = parseInt(field_id);
            option_id = parseInt(option_id)

            this.confirmWindow.confirm = 0;
            this.confirmWindow.field_id = field_id;
            this.confirmWindow.option_id = option_id;
            this.confirmWindow.confirm = option_id >= 0 ? option_id : field_id;

            let title = 'Поле ' + (field_id + 1);

            if (option_id >= 0)
                title = 'Опцию ' + (option_id + 1);

            this.confirmWindow.text = this.$t('CustomForm.you_really_delete') + ': ' + title;
            this.confirmWindow.openConfirm = true;
        },
        deleteItem() {
            this.confirmWindow.openConfirm = false;
            if (this.confirmWindow.option_id >= 0) {
                if (this.form.fields[this.confirmWindow.field_id].options[this.confirmWindow.option_id].id) {
                    const params = {
                        params: {
                            option_id: this.form.fields[this.confirmWindow.field_id].options[this.confirmWindow.option_id].id
                        }
                    }
                    axios.delete(this.source, params).then((response) => {
                        console.log(response.data.message);
                    }).catch((error) => {
                        console.log(error);
                    });
                }
                this.form.fields[this.confirmWindow.field_id].options.splice(this.confirmWindow.option_id, 1);
            } else {
                if (this.form.fields[this.confirmWindow.field_id].id) {
                    const params = {
                        params: {
                            field_id: this.form.fields[this.confirmWindow.field_id].id
                        }
                    };
                    axios.delete(this.source, params).then((response) => {
                        console.log(response.data.message);
                    }).catch((error) => {
                        console.log(error);
                    });
                }
                this.form.fields.splice(this.confirmWindow.field_id, 1);
            }
        }
    }
}
</script>

<style lang="stylus" scoped>
svg.trash
    color red
    font-size 1rem
    cursor pointer

.create_custom_form
    .card
        cursor move
</style>
