<template>
    <div v-if="!loading">
        <p class="title_form">
            {{ $t('Settings.socials_on_off') }}
        </p>
        <form @submit.prevent="onSubmit">
            <div class="row" v-for="(item, index) in items">
                <div class="col-sm-6 col-lg-3 mb-3">
                    <label for="name" class="form-label">{{ $t('Settings.title') }}</label>
                    <input type="text" v-model="item.name" :class="['form-control', errors[index + '.name'] ? ' is-invalid' : '']"
                           id="name">
                </div>
                <div class="col-sm-6 col-lg-4 mb-3">
                    <label for="url" class="form-label">{{ $t('Settings.url') }}</label>
                    <input type="text" v-model="item.url" :class="['form-control', errors[index + '.url'] ? ' is-invalid' : '']"
                           id="url">
                    <div class="invalid-feedback" v-if="errors[index + '.url']">
                        <strong v-for="error in errors[index + '.url']">{{ error }}</strong>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <label for="class_icon" class="form-label">{{ $t('Settings.class_icon') }}</label>
                    <input type="text" v-model="item.class_icon"
                           :class="['form-control', errors[index + '.class_icon'] ? ' is-invalid' : '']" id="class_icon">
                </div>
                <div class="col-sm-5 col-lg-1 mb-3">
                    <label for="priority" class="form-label">{{ $t('Settings.priority') }}</label>
                    <input type="text" v-model="item.priority" :class="['form-control', errors[index + '.priority'] ? ' is-invalid' : '']"
                           id="priority">
                </div>
                <div class="col d-flex align-items-center justify-content-center" :title="$t('Settings.delete')"
                     @click.prevent="deleteSocial(index)" v-if="canCreate">
                    <button type="button" class="btn btn-danger">
                        <fa :icon="['fas', 'trash-alt']"/>
                    </button>
                </div>
            </div>
            <p class="mt-4 d-flex justify-content-between" v-if="canCreate">
                <button type="button" class="btn btn-success" :title="$t('Settings.add')" @click.prevent="addSocial()">
                    <fa :icon="['fas', 'plus']"/>
                </button>
                <button :class="{'btn btn-success': true, 'btn-loading': submit}" type="submit" :title="$t('Settings.save')"
                        :disabled="submit">
                    <fa :icon="['fas', 'save']"/>
                </button>
            </p>
        </form>
    </div>
</template>

<script>
    import axios from 'axios';
    import {mapGetters} from "vuex";

    export default {
        data() {
            return {
                items: [],
                loading: true,
                submit: false,
                errors: {},
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
            this.getItems()
        },
        methods: {
            getItems() {
                axios.get(this.source).then(response => {
                    this.items = response.data.data;
                    this.loading = false;
                }).catch(error => {
                    console.log(error)
                })
            },
            addSocial() {
                this.items.push({
                    name: '',
                    url: '',
                    class_icon: '',
                    priority: '',
                })
            },
            deleteSocial(index) {
                this.items.splice(index, 1)
            },
            onSubmit() {
                this.submit = true;
                axios.post(this.source, this.items).then(response => {
                    this.items = response.data.data;
                    this.$toast.global.success(this.$t('Settings.data_saved'))

                    this.$nextTick(() => {
                        this.submit = false;
                    })
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors
                        this.submit = false;
                    }
                })
            }
        }
    }
</script>