<template>
    <div>
        <p class="title_form">
            {{ routeEdit ? $t('UserGroup.edit_group') : $t('UserGroup.add_group') }}
        </p>
        <form @submit.prevent="onSubmit" v-if="!loading">
            <router-link class="btn btn-light submit_absolute cancel" :to="{name: 'user-group.index'}" :title="$t('UserGroup.cancel')">
                <i class="fas fa-reply"></i>
            </router-link>
            <button :class="{'btn btn-success submit_absolute': true, 'btn-loading': submit}" type="submit" :title="$t('UserGroup.save')"
                    :disabled="submit">
                <i class="fas fa-save"></i>
            </button>
            <div class="mb-3">
                <label for="name" class="form-label">{{ $t('UserGroup.title') }}</label>
                <input type="text" :class="['form-control', errors['name'] ? ' is-invalid' : '']" v-model="form.name" id="name">
                <div class="invalid-feedback" v-if="errors['title']">
                    <strong v-for="error in errors['title']">{{ error }}</strong>
                </div>
            </div>
            <div class="mb-3" v-for="(item, route_name) in routes">
                <label :for="route_name"><strong>{{ $t(`UserGroup.${route_name}`) }}</strong></label>
                <div class="row">
                    <div class="mb-3 col" v-for="(rights, right) in item">
                        <div class="custom-control custom-checkbox my-1 me-sm-2">
                            <input type="checkbox" class="custom-control-input" :id="`param-value-${ route_name }-${ right }`"
                                   v-model="routes[route_name][right]">
                            <label class="custom-control-label" :for="`param-value-${ route_name }-${ right }`">
                                {{ $t(`UserGroup.${right}`) }}
                            </label>
                        </div>
                    </div>
                </div>

            </div>
            <p class="text-end">
                <router-link class="btn btn-light" :to="{name: 'user-group.index'}" :title="$t('UserGroup.cancel')">
                    <i class="fas fa-reply"></i>
                </router-link>
                <button :class="{'btn btn-success': true, 'btn-loading': submit}" type="submit" :title="$t('UserGroup.save')"
                        :disabled="submit">
                    <i class="fas fa-save"></i>
                </button>
            </p>
        </form>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        middleware: 'auth',
        head() {
            const title = this.routeEdit ? this.$t('UserGroup.edit_group') : this.$t('UserGroup.add_group');
            return {title}
        },
        data() {
            return {
                form: {},
                routes: {},
                errors: {},
                loading: true,
                submit: false
            }
        },
        computed: {
            routeEdit() {
                return typeof this.$route.params.id !== "undefined";
            },
            sourceActionMethod() {
                const arrayRoute = this.$route.name.split('.');

                let action = `/admin/${arrayRoute[0]}`;
                let method = 'post'

                if (this.routeEdit) {
                    action += `/${this.$route.params.id}`
                    method = 'put'
                }

                return {
                    'action': action,
                    'method': method
                };
            },
            source() {
                const arrayRoute = this.$route.name.split('.');
                let action = `/admin/${arrayRoute[0]}`;
                if (typeof this.$route.params.id !== "undefined")
                    return `${action}/${this.$route.params.id}`;

                return `${action}/0`;
            }
        },
        mounted() {
            this.getItem()
        },
        methods: {
            getItem() {
                axios.get(this.source).then(response => {
                    this.form = response.data.data;
                    this.routes = response.data.routes;
                    this.loading = false;
                }).catch(error => {
                    console.log(error)
                })
            },
            onSubmit() {
                this.submit = true;
                axios({
                    method: this.sourceActionMethod.method,
                    url: this.sourceActionMethod.action,
                    data: {form: this.form, items: this.routes}
                }).then(() => {
                    this.$toast.global.success(this.$t('UserGroup.data_saved'))

                    setTimeout(() => {
                        this.$router.push({
                            name: `${this.$route.name.split('.')[0]}.index`
                        })
                    }, 1000)
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