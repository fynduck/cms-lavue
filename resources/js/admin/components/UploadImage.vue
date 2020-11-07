<template>
    <el-upload
            :ref="`upload_${name}`"
            :name="name"
            :headers="{ 'X-CSRF-TOKEN': csr}"
            class="upload-image"
            :action="action"
            :on-change="handleChange"
            :before-upload="handleUploaded"
            :on-remove="handleRemove"
            :before-remove="beforeRemove"
            :on-exceed="handleExcept"
            :file-list="fileList"
            :list-type="list_type"
            :auto-upload="false"
            :multiple="multiple"
            :limit="limit">
        <el-button size="small" type="primary" class="mr-3">{{ button_text }}</el-button>
    </el-upload>
</template>

<script>
    export default {
        name: "UploadImage",
        props: {
            action: {
                type: String,
                default: ''
            },
            delete_action: {
                type: String,
                default: ''
            },
            name: {
                type: String,
                default: 'image'
            },
            button_text: {
                type: String,
                required: true
            },
            old_img: {
                type: Array,
                default() {
                    return [];
                }
            },
            csr: {
                type: String,
                default: ''
            },
            custom_class: {
                type: String
            },
            limit: {
                type: Number,
                default: 1
            },
            list_type: {
                type: String,
                default: 'picture'
            },
            multiple: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                fileList: []
            };
        },
        mounted() {
            for (let i in this.old_img)
                this.fileList.push(this.old_img[i]);
        },
        methods: {
            handleChange(fileData, fileList) {
                const file = fileData.raw;
                if (!file.type.includes('image/')) {
                    alert('Please select an image file');
                    return;
                }

                this.convertToBase64(fileList)
            },
            handleUploaded(response) {
                document.getElementById('image_name').value += response + ',';
            },
            handleRemove(file, fileList) {
                axios.delete(this.delete_action, {params: {image: file.name}}).then((response) => {
                    this.$message({
                        type: 'success',
                        message: response.data.message
                    });
                    this.convertToBase64(fileList)
                }).catch((error) => {
                    console.log(error)
                })
            },
            beforeRemove(file, fileList) {
                return this.$confirm('Remove ' + file.name, 'Warning', {
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Cancel',
                    type: 'warning'
                });
            },
            handleExcept() {
                alert(`Limit ${this.limit} is exceeded`)
            },
            convertToBase64(fileList) {
                let images = [];
                if (typeof FileReader === 'function') {

                    for (let i = 0; i < fileList.length; i++) {
                        if (Object.keys(fileList[i]).includes('raw')) {
                            const reader = new FileReader();
                            reader.onload = (event) => {
                                images.push(event.target.result)
                            };
                            reader.readAsDataURL(fileList[i].raw);
                        }
                    }
                } else {
                    alert('Sorry, FileReader API not supported');
                }

                this.$emit('images', images);
            }
        }
    }
</script>

<style lang="stylus">
    .el-upload-list
        display flex
        flex-wrap wrap

        li
            margin-right 10px !important
            max-width 260px
</style>
