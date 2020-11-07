<template>
    <div>
        <el-select v-model="value" :multiple="multiple" :placeholder="placeholder" class="w-100">
            <el-option
                    v-for="item in options"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value">
            </el-option>
        </el-select>
        <input type="hidden" v-model="value" :name="name">
    </div>
</template>

<script>
    export default {
        name: "SimpleSelect",
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
                default: 'options'
            },
            multiple: {
                type: Boolean,
                default: true
            },
            placeholder: {
                type: String,
                default: 'Select'
            }
        },
        data() {
            return {
                options: [],
                value: this.selected ? this.selected : [],
            }
        },
        mounted() {
            this.getPages(null, this.selected);
        },
        methods: {
            getPages() {
                this.loading = true;
                axios.get(this.source).then((response) => {
                    this.options = response.data;
                    this.loading = false;
                }).catch((error) => {
                    console.log(error)
                });
            }
        }
    }
</script>

<style scoped>

</style>
