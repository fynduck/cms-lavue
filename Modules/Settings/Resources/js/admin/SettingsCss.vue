<template>
    <div>
        <p class="title_form">
            CSS
        </p>
        <transition>
            <form @submit.prevent="saveCss">
                <client-only placeholder="Loading...">
                    <codemirror ref="cssEditor"
                                :value="css"
                                :options="cmOptions"
                                @input="onCmCodeChange">
                    </codemirror>
                </client-only>
                <div class="text-center mt-3" v-if="canCreate">
                    <button :class="{'btn btn-success': true, 'btn-loading': submit}" type="submit" :title="$t('Settings.save')"
                            :disabled="submit">
                        <fa :icon="['fas', 'save']"/> {{ $t('Settings.save') }}
                    </button>
                </div>
            </form>
        </transition>
    </div>
</template>

<script>
    import axios from 'axios'
    import {mapGetters} from "vuex";

    export default {
        name: "SettingsCss",
        data() {
            return {
                css: '',
                cmOptions: {
                    tabSize: 4,
                    styleActiveLine: true,
                    lineNumbers: true,
                    line: true,
                    mode: 'text/css'
                },
                submit: false
            }
        },
        computed: {
            ...mapGetters({
                authenticated: 'auth/check',
                permissions: 'auth/checkPermission'
            }),
            source() {
                const arrayRoute = this.$route.name.split('.');
                return `/admin/${arrayRoute[0]}`;
            },
            canCreate() {
                if (this.authenticated) {
                    const arrayName = this.$router.currentRoute.name.split('.');
                    return this.permissions(arrayName[0], 'create')
                }

                return false;
            }
        },
        mounted() {
            this.getCss()
        },
        methods: {
            getCss() {
                axios.get(this.source).then(response => {
                    this.css = response.data
                }).catch(error => {
                    console.log(error)
                })
            },
            saveCss() {
                this.submit = true;
                axios.post(this.source, {css: this.css}).then(() => {
                    this.$toast.global.success(this.$t('Settings.data_save'))

                    this.submit = false;
                }).catch(() => {
                    this.$toast.global.error(this.$t('Settings.data_not_save'))
                    this.submit = false;
                })
            },
            onCmCodeChange(newCode) {
                this.css = newCode
            }
        }
    }
</script>