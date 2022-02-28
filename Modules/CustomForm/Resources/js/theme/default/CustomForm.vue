<template>
    <div v-if="Object.keys(form).length" :class="form.form_class">
        <h2 class="title_form">{{ form.form_name }}</h2>
        <form :action="form.action" :method="form.method" @submit.prevent="onSubmit"
              :enctype="form.file ? 'multipart/form-data' : 'application/x-www-form-urlencoded'">
            <div class="row">
                <div :class="['mb-3', field.block_class || 'col-12']" v-for="(field, field_key) in form.fields">
                    <template v-if="fieldInput.includes(field.type)">
                        <label :for="`field_${field.field_id || field.id}`" class="form-label" v-if="field.label">
                            {{ field.label }}
                        </label>
                        <input :type="field.type" :name="field.name" :pattern="isValidate(field, true)"
                               :class="[field.type ==='range' ? 'form-range' : 'form-control', field.field_class, has(field.name) ? 'is-invalid' : '']"
                               v-model="field.value" :placeholder="field.placeholder" :required="isValidate(field)"
                               :id="`field_${field.field_id || field.id}`">
                        <div class="invalid-feedback" v-if="has(field.name)">
                            <div v-for="error in get(field.name)">{{ error }}</div>
                        </div>
                    </template>
                    <template v-else-if="field.type === 'file'">
                        <label :for="`file_${field.field_id || field.id}`" class="form-label" v-if="field.label">
                            {{ field.label }}
                        </label>
                        <input :type="field.type" :name="field.name" @change="addFile($event, field_key)"
                               :class="['form-control', field.field_class, has(field.name) ? 'is-invalid' : '']"
                               :required="isValidate(field)" :id="`file_${field.field_id || field.id}`">
                        <div class="invalid-feedback" v-if="has(field.name)">
                            <div v-for="error in get(field.name)">{{ error }}</div>
                        </div>
                    </template>
                    <template v-else-if="field.type === 'textarea'">
                        <label :for="`textarea_${field.field_id || field.id}`" v-if="field.label" class="form-label">
                            {{ field.label }}
                        </label>
                        <textarea :class="['form-control', field.field_class, has(field.name) ? 'is-invalid' : '']"
                                  v-model="field.value" :placeholder="field.placeholder" :required="isValidate(field)"
                                  :pattern="isValidate(field, true)" rows="3"
                                  :id="`textarea_${field.field_id || field.id}`"
                        ></textarea>
                        <div class="invalid-feedback" v-if="has(field.name)">
                            <div v-for="error in get(field.name)">{{ error }}</div>
                        </div>
                    </template>
                    <template v-else-if="fieldRadioCheck.includes(field.type)">
                        <label v-if="field.label" class="form-label">{{ field.label }}</label>
                        <br>
                        <div :class="['form-check form-check-inline', `custom-${field.type}`]"
                             v-for="option in field.options">
                            <input :type="field.type" :id="`key_${option.option_id || option.id}`" :value="option.value"
                                   :class="['form-check-input', option.option_class, has(field.name) ? 'is-invalid' : '']"
                                   :name="field.name" v-model="field.value" :required="isValidate(field)">
                            <label class="form-check-label" :for="`key_${option.option_id || option.id}`"
                                   v-html="option.title"></label>
                            <div class="invalid-feedback" v-if="has(field.name)">
                                <div v-for="error in get(field.name)">{{ error }}</div>
                            </div>
                        </div>
                    </template>
                    <template v-else-if="field.type === 'select'">
                        <label :for="`select_${field.field_id || field.id}`" v-if="field.label" class="form-label">
                            {{ field.label }}
                        </label>
                        <select :class="['form-select', field.field_class, has(field.name) ? 'is-invalid' : '']"
                                :id="`select_${field.field_id || field.id}`" v-model="field.value"
                                :required="isValidate(field)">
                            <option v-for="option in field.options" :value="option.value"
                                    :class="[option.option_class ? option.option_class : null]"
                                    :id="[option.option_id ? option.option_id : null]">
                                {{ option.title }}
                            </option>
                        </select>
                        <div class="invalid-feedback" v-if="has(field.name)">
                            <div v-for="error in get(field.name)">{{ error }}</div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="alert alert-success" role="alert" v-if="success">
                {{ success }}
            </div>
            <div class="text-center my-3">
                <button :class="{'btn-custom': true, 'btn-loading': loading}" type="submit">
                    {{ $t('CustomForm.send') }}
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "CustomForm",
    components: {},
    props: {
        source: {
            type: String,
            default: null
        }
    },
    data() {
        return {
            form: [],
            fieldInput: [
                'text',
                'number',
                'tel',
                'email',
                'range'
            ],
            fieldRadioCheck: [
                'radio',
                'checkbox',
            ],
            trans: [],
            errorsValidate: {},
            success: '',
            color: '#e68129',
            size: '10px',
            loading: false
        }
    },
    computed: {
        sendFields() {
            let fields = {};
            this.form.fields.forEach(item => {
                fields[item.name] = item.value;
            })

            return fields;
        }
    },
    async fetch() {
        if (this.source) {
            const {data} = await axios.get(this.source)
            if (typeof data.data !== "undefined") {
                this.form = data.data;
            }

            this.trans = data.trans
        }
    },
    methods: {
        addFile(event, field_key) {
            let input = event.target;
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = (e) => {
                    this.form.fields[field_key].value = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
                input.nextElementSibling.innerHTML = input.files[0].name;
                this.form.fields[field_key].file_name = input.files[0].name;
            }
        },
        isValidate(field, pattern = false) {
            let needValidate = false;
            field.validate.forEach(item => {
                if (!pattern) {
                    if (item === 'required') {
                        needValidate = true;
                    }
                } else {
                    if (item !== 'required') {
                        needValidate = item;
                    }
                }
            })
            return needValidate;
        },
        onSubmit() {
            this.loading = true;
            this.errorsValidate = {};
            axios({
                method: this.form.method,
                url: this.form.action,
                data: {id: this.form.id, fields: this.sendFields}
            }).then(() => {
                this.success = this.$t('CustomForm.message_sends');
                for (let objectField of this.form.fields) {
                    if (Array.isArray(objectField.value))
                        objectField.value = [];
                    else
                        objectField.value = ''
                }
                this.$nextTick(() => {
                    this.loading = false;
                })
            }).catch((error) => {
                this.errorsValidate = error.response.data;
                this.$nextTick(() => {
                    this.loading = false;
                })
            });
        },
        has(field) {
            return this.errorsValidate.hasOwnProperty(field)
        },
        get(field) {
            if (this.errorsValidate[field]) {
                return this.errorsValidate[field]
            }
            return [];
        }
    }
}
</script>

<style lang="stylus" scoped>
.star
    label
        &:after
            content '*'
            color #f00
            margin-left 5px
</style>
