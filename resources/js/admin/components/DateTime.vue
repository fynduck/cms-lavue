<template>
    <div class="form-group">
        <label v-if="trans.title">{{ trans.title }}</label>
        <el-date-picker class="d-block w-100"
                        :name="name"
                        v-model="selected"
                        type="datetime"
                        value-format="yyyy-MM-dd HH:mm:ss"
                        :placeholder="trans.placeholder"
                        @change="emitData"
        >
        </el-date-picker>
    </div>
</template>

<script>
    export default {
        name: "DateTime",
        props: {
            trans: {
                type: Object
            },
            old_date: String,

            name: {
                type: String,
                default: 'date'
            }
        },
        data() {
            return {
                selected: this.old_date,
            };
        },
        methods: {
            emitData() {
                this.$emit('date', this.selected);
            }
        }
    }
</script>

<style lang="stylus">
    .el-input__inner
        -webkit-border-radius: 0
        -moz-border-radius: 0
        border-radius: 0
</style>