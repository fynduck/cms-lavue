<template>
    <div>
        <label v-if="trans.range_title">{{ trans.range_title }}</label>
        <el-date-picker class="w-100"
                        name="ft"
                        v-model="date_selected"
                        type="datetimerange"
                        range-separator="До"
                        @change="emitData"
                        :start-placeholder="trans.from"
                        :end-placeholder=trans.to>
        </el-date-picker>
    </div>
</template>

<script>
    export default {
        name: "DateTimeFromTo",
        props: {
            trans: {
                type: Object
            },
            old_date: Object
        },
        data() {
            return {
                date_selected: [
                    this.old_date.from,
                    this.old_date.to
                ],
            };
        },
        methods: {
            emitData() {
                this.$emit('date_range', this.date_selected);
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
