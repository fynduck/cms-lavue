<template>
    <div class="time_ends">
        <div class="item months" v-if="month && diff > 0">
            <div class="time">{{ timeData[0].current | twoDigits }}</div>
            <div class="label">{{ timeData[0].label }}</div>
        </div>
        <div class="item days" v-if="day && diff > 0">
            <div class="time">{{ timeData[1].current | twoDigits }}</div>
            <div class="label">{{ timeData[1].label }}</div>
        </div>
        <div class="item minutes" v-if="minute && diff > 0">
            <div class="time">{{ timeData[2].current | twoDigits }}</div>
            <div class="label">{{ timeData[2].label }}</div>
        </div>
        <div class="item seconds" v-if="second && diff > 0">
            <div class="time">{{ timeData[3].current | twoDigits }}</div>
            <div class="label">{{ timeData[3].label }}</div>
        </div>
        <div v-if="finishTxt && diff === 0" class="finished_txt">{{ finishTxt }}</div>
    </div>
</template>

<script>
    export default {
        name: "TimeDown",
        props: {
            month: {
                type: Boolean,
                default: true
            },
            day: {
                type: Boolean,
                default: true
            },
            minute: {
                type: Boolean,
                default: true
            },
            second: {
                type: Boolean,
                default: true
            },
            finishTxt: String,
            endTime: {
                type: Number,
                required: true
            },
            labels: {
                type: Object,
                required: false,
                default: function () {
                    return {
                        days: "Days",
                        hours: "Hours",
                        minutes: "Minutes",
                        seconds: "Seconds"
                    }
                }
            }
        },
        data() {
            return {
                now: Math.trunc(new Date().getTime() / 1000),
                date: null,
                interval: null,
                diff: 0,
                show: false,
                timeData: [
                    {
                        current: 0,
                        previous: 0,
                        label: this.labels.days
                    },
                    {
                        current: 0,
                        previous: 0,
                        label: this.labels.hours
                    },
                    {
                        current: 0,
                        previous: 0,
                        label: this.labels.minutes
                    },
                    {
                        current: 0,
                        previous: 0,
                        label: this.labels.seconds
                    }
                ]
            }
        },
        created() {
            this.date = Math.trunc((this.endTime * 1000) / 1000);
            if (!this.date)
                throw new Error("Invalid props value, correct the 'end-time'");

            this.interval = setInterval(() => {
                this.now = Math.trunc(new Date().getTime() / 1000)
            }, 1000)
        },
        computed: {
            seconds() {
                return Math.trunc(this.diff) % 60
            },
            minutes() {
                return Math.trunc(this.diff / 60) % 60
            },
            hours() {
                return Math.trunc(this.diff / 60 / 60) % 24
            },
            days() {
                return Math.trunc(this.diff / 60 / 60 / 24)
            }
        },
        watch: {
            now(value) {
                this.diff = this.date - this.now;
                if (this.diff <= 0) {
                    this.diff = 0;
                } else {
                    this.updateTime(0, this.days);
                    this.updateTime(1, this.hours);
                    this.updateTime(2, this.minutes);
                    this.updateTime(3, this.seconds);
                }
            }
        },
        filters: {
            twoDigits(value) {
                if (value.toString().length <= 1)
                    return "0" + value.toString();

                return value.toString()
            }
        },
        methods: {
            updateTime(idx, newValue) {
                if (idx >= this.timeData.length || newValue === undefined)
                    return;

                const d = this.timeData[idx];
                const val = newValue < 0 ? 0 : newValue;

                if (val !== d.current) {
                    d.previous = d.current;
                    d.current = val
                }
            }
        }
    }
</script>