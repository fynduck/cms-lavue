<template>
    <editor tinymce-script-src="/tinymce/tinymce.min.js" :init="configuration" v-model="data" v-if="loaded"></editor>
</template>

<script>
    import Editor from '@tinymce/tinymce-vue'

    export default {
        name: "tinymce",
        components: {
            Editor
        },
        props: {
            id: {
                type: String,
                required: true
            },
            height: {
                type: Number,
                default: 300
            },
            value: {default: ''},
            plugins: {
                default: function () {
                    return [
                        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                        "searchreplace wordcount visualblocks visualchars code fullscreen",
                        "insertdatetime media nonbreaking save table contextmenu directionality",
                        "emoticons template paste textcolor colorpicker textpattern imagetools codemirror"
                    ];
                }, type: Array
            },
            toolbar: {
                default: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons blockquote',
                type: String
            },
            base: {
                type: String,
                default: ''
            },
            path: {
                type: String | Boolean,
                default: false
            },
            lang: {
                type: String,
                default: 'en'
            },
            token: String,
        },
        data() {
            return {
                data: '',
                configuration: {
                    language: this.lang,
                    valid_elements: '*[*]',
                    verify_html: true,
                    codemirror: {
                        indentOnInit: true,
                        path: this.base + '/tinymce/plugins/codemirror/codemirror-5',
                        config: {
                            mode: 'text/html',
                            lineNumbers: true,
                            lineWrapping: true,
                        }
                    },
                },
                loaded: false
            }
        },
        created() {
            this.data = this.value;

            this.configuration.plugins = this.plugins;
            this.configuration.toolbar = this.toolbar;

            if (this.height)
                this.configuration.height = this.height;

            if (this.path) {
                const file_manager_path = this.base + this.path;
                const lang = this.lang;
                const token = this.token;

                const file_manager = {
                    file_picker_callback(callback, value, meta, t) {
                        console.log(callback, value, meta, t)
                        const w = window,
                            d = document,
                            e = d.documentElement,
                            g = d.getElementsByTagName('body')[0],
                            x = w.innerWidth || e.clientWidth || g.clientWidth,
                            y = w.innerHeight || e.clientHeight || g.clientHeight;

                        let cmsURL = `${file_manager_path}?editor=${meta.fieldname}&lang=${lang}`;

                        if (meta.filetype === 'image')
                            cmsURL = cmsURL + "&type=Images";
                        else
                            cmsURL = cmsURL + "&type=Files";

                        if (token)
                            cmsURL = cmsURL + `&token=${token}`;

                        tinymce.activeEditor.windowManager.openUrl({
                            url: cmsURL,
                            title: 'Filemanager',
                            width: x * 0.8,
                            height: y * 0.8,
                            resizable: "yes",
                            close_previous: "no",
                            onMessage: (api, message) => {
                                callback(message.content);
                            }
                        });
                    }
                }
                this.configuration = Object.assign(this.configuration, file_manager, this.configuration);
            }

            this.$nextTick(() => {
                this.loaded = true;
            })
        },
        watch: {
            data(val) {
                this.$emit('input', val);
            }
        }
    }
</script>