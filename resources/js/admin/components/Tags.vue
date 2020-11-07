<template>
    <div>
        <label v-if="label">{{ label }}</label>
        <br>
        <el-tag
                type="success"
                :key="tag"
                v-for="tag in dynamicTags"
                closable
                :disable-transitions="false"
                @close="handleClose(tag)">
            {{tag}}
        </el-tag>
        <el-input
                class="input-new-tag"
                v-if="inputVisible"
                v-model="inputValue"
                ref="saveTagInput"
                size="mini"
                @keypress.enter.native="handleInputConfirm"
                @blur="handleInputConfirm"
        >
        </el-input>
        <el-button v-else class="button-new-tag" size="small" @click="showInput">+ {{ nameAdd }}</el-button>
        <el-input type="hidden" :name="name" v-model="selected"></el-input>
    </div>
</template>

<script>
    export default {
        name: "Tags",
        props: {
            name: {
                type: String,
                default: 'tags'
            },
            label: String,
            nameAdd: {
                type: String,
                default: 'Add'
            },
            tags: String,
        },
        data() {
            return {
                dynamicTags: [],
                inputVisible: false,
                inputValue: '',
                selected: ''
            };
        },
        watch: {
            selected: {
                handler: function () {
                    this.$emit('tags', this.selected);
                },
                deep: true
            }
        },
        mounted() {
            if (this.tags) {
                this.selected = this.tags;
                this.dynamicTags = this.tags.split(',');
            }
        },
        methods: {
            handleClose(tag) {
                this.dynamicTags.splice(this.dynamicTags.indexOf(tag), 1);
                this.selected = this.dynamicTags.join(',');
            },

            showInput() {
                this.inputVisible = true;
                this.$nextTick(() => {
                    this.$refs.saveTagInput.$refs.input.focus();
                });
            },

            handleInputConfirm() {
                let inputValue = this.inputValue;
                if (inputValue) {
                    this.dynamicTags.push(inputValue);
                    this.selected = this.dynamicTags.join(',');
                }
                this.inputVisible = false;
                this.inputValue = '';
            }
        }
    }
</script>

<style scoped lang="stylus">
    .el-tag + .el-tag
        margin-left 10px

    .button-new-tag
        margin-left 10px
        height 32px
        line-height 30px
        padding-top 0
        padding-bottom 0

    .input-new-tag
        width 200px
        margin-left 10px
        vertical-align bottom
</style>
