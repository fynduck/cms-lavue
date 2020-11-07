<template>
    <div>
        <el-select v-model="value" :placeholder="placeholder" class="w-100"
                   :multiple="multiple"
                   filterable
                   required
                   :remote="remote"
                   :remote-method="getRemote"
                   :loading="loading">
            <el-option v-for="(title, key) in options"
                       :key="key"
                       :label="title"
                       :value="key">
            </el-option>
        </el-select>
        <input type="hidden" v-model="value" :name="name">
    </div>
</template>

<script>
    export default {
        name: 'AdvancedSelect',
        props: {
            source: {
                type: String,
                required: true
            },
            remote: {
                type: Boolean,
                default: true
            },
            selected: {
                type: String,
                default: ''
            },
            name: {
                type: String,
                default: 'page'
            },
            multiple: {
                type: Boolean,
                default: true
            },
            placeholder: {
                type: String,
                default: 'Select'
            },
            parent: Boolean
        },
        data() {
            return {
                options: [],
                value: this.selected ? this.selected : [],
                loading: false
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
                this.getResponse(null, this.selected);

            if (window.innerWidth <= 2024) {
                setTimeout(() => {
                    const input = document.querySelector('input.el-input__inner');
                    input.addEventListener('input', this.getRemote);
                }, 1000)
            }
        },
        methods: {
            getRemote(query) {
                if (typeof query.data !== 'undefined')
                    query = query.data;

                if (query)
                    _.debounce(this.getResponse(query), 500);
            },
            getResponse(query = null, selected = null) {
                if (query || this.selected) {
                    this.loading = true;
                    const data = {
                        params: {
                            q: query,
                            parent: this.parent && !selected ? document.getElementsByName('country_id')[0].value : null,
                            selected: selected
                        }
                    };
                    axios.get(this.source, data).then((response) => {
                        this.options = response.data;

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
    .el-select
        .el-input
            &.is-focus
                .el-input__inner
                    border-color #949494

    .is-invalid
        .el-input__inner
            border-color #e3342f

    .el-input__inner
        border-color #949494
        border-radius 0
        height 37px
        background-color transparent
        color #fff

        &:focus
            border-color #949494 !important
</style>
