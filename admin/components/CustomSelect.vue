<template>
    <div>
        <label v-if="label" class="form-label">{{ label }}</label>
        <v-select v-model="selected"
                  :multiple="multiple"
                  :filterable="false"
                  :options="options"
                  @input="changed"
                  @search="onSearch">
            <template slot="no-options">
                {{ no_result }}
            </template>
            <template slot="option" slot-scope="option">
                <div><strong>{{ option.label }}</strong></div>
                <em>{{ option.module_name }}</em>
            </template>
        </v-select>
    </div>
</template>

<script>
    import axios from 'axios'
    import vSelect from 'vue-select'

    export default {
        name: "CustomSelect",
        props: {
            value: {
                type: String | Array,
                default: null
            },
            label: {
                type: String,
                default: 'label'
            },
            no_result: {
                type: String,
                default: 'No result'
            },
            source: {
                type: String,
                required: true
            }
        },
        components: {
            vSelect
        },
        created() {
            this.selected = Array.isArray(this.value) ? [] : null;
            this.multiple = Array.isArray(this.value)
            if (this.value) {
                this.runQuery(null, this.value).then(res => {
                    this.options = res.data
                    if (Array.isArray(this.value)) {
                        this.value.forEach(value => {
                            this.selected.push(this.options.find(item => item.value === value))
                        })
                    } else {
                        this.selected = this.options.find(item => item.value === this.value)
                    }
                })
            }
        },
        data() {
            return {
                selected: null,
                multiple: false,
                timeout: null,
                options: []
            }
        },
        methods: {
            onSearch(search, loading) {
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    if (search) {
                        loading(true);
                        this.runQuery(search, loading).then(res => {
                            this.options = res.data
                            loading(false);
                        })
                    }
                }, 500);
            },
            async runQuery(search, selected = null) {
                return await axios.get(this.source, {params: {q: search, selected: selected}})
            },
            changed() {
                let values = Array.isArray(this.value) ? [] : null;
                if (Array.isArray(this.value)) {
                    this.selected.forEach(item => {
                        values.push(item.value)
                    })
                } else {
                    values = !this.selected || this.selected.value
                }
                this.$nextTick(() => [
                    this.$emit('input', values)
                ])
            }
        },
    }
</script>