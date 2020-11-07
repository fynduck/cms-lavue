<template>
    <b-input-group class="mb-3">
        <b-form-input
            v-model="date_time"
            type="text"
            placeholder="YYYY-MM-DD HH:mm:ss"
        ></b-form-input>
        <b-input-group-append>
            <b-form-datepicker
                v-model="date"
                button-only
                right
                :locale="locale"
            ></b-form-datepicker>
            <b-form-timepicker
                v-model="time"
                button-only
                right
                show-seconds
                :locale="locale"
            ></b-form-timepicker>
        </b-input-group-append>
    </b-input-group>
</template>

<script>
    export default {
        name: "DateTimePicker",
        props: {
            value: String,
            locale: {
                type: String,
                default: 'ru'
            }
        },
        data() {
            return {
                date_time: '',
                date: '',
                time: '',
                formatted: '',
                selected: ''
            }
        },
        watch: {
            date(val) {
                let time = this.$moment().format('HH:mm:ss');
                if (typeof this.date_time.split(' ')[1] !== "undefined")
                    time = this.date_time.split(' ')[1];

                this.date_time = val + ' ' + time
            },
            time(val) {
                let date = this.$moment().format('YYYY-MM-DD');

                if (this.date_time.split(' ')[0])
                    date = this.date_time.split(' ')[0];

                this.date_time = date + ' ' + val
            },
            date_time() {
                this.$emit('input', this.date_time)
            }
        },
        created() {
            this.date_time = this.value;

            if (this.value) {
                this.date = this.date_time.split(' ')[0];
                this.time = this.date_time.split(' ')[1];
            }
        }
    }
</script>