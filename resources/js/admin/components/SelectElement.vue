<template>
    <div>
        <el-select v-model="value" placeholder="Select" class="w-100"
                   :multiple="multiple"
                   filterable
                   remote
                   :remote-method="getRemote"
                   :loading="loading"
                   clearable
        >
            <el-option-group v-for="group in options" :key="group.label" :label="group.label">
                <el-option v-for="(item, index) in group.items"
                           :key="index"
                           :label="item.label"
                           :value="item.value">
                </el-option>
            </el-option-group>
        </el-select>
        <input type="hidden" v-model="value" :name="name">
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        name: "select-element",
        props: {
            source: {
                type: String,
                required: true
            },
            selected: {
                default: ''
            },
            name: {
                type: String,
                default: 'page'
            },
            multiple: {
                type: Boolean,
                default: true
            }
        },
        data() {
            return {
                options: [],
                value: this.selected ? this.selected : [],
                loading: false,
                timeout: null
            }
        },
        watch: {
            value: {
                handler: function () {
                    this.$emit('elements', this.value);
                },
                deep: true
            }
        },
        mounted() {
            if (this.selected)
                this.getPages(null, this.selected);
        },
        methods: {
            getRemote(query) {
                if (query) {
                    clearTimeout(this.timeout);
                    this.timeout = setTimeout(() => {
                        this.getPages(query)
                    }, 800);
                }
            },
            getPages(query = null, selected = null) {
                if (query || this.selected) {
                    this.loading = true;
                    axios.get(this.source, {params: {q: query, selected: selected}}).then((response) => {
                        this.options = [];
                        for (let i = 0; i < response.data.length; i++) {
                            this.options.push(response.data[i]);
                        }
                        this.loading = false;
                    }).catch((error) => {
                        console.log(error)
                    });
                }
            }
        }
    }
</script>

<style lang="stylus">
    .el-input__inner
        border-radius 0
        height 37px
</style>
