<template>
    <div class="row">
        <div class="col">
            <h3>Chart Search</h3>
        </div>
        <div class="col">
            <button type="button" class="btn btn-outline-info" @click.prevent="getData">Show</button>
        </div>
        <div class="col-12 mt-5">
            <pie-chart
                    v-if="chartdata.datasets.length && !loading"
                    :chartdata="chartdata"
                    :options="options"/>
        </div>
    </div>
</template>

<script>
    import PieChart from "./PieChart";

    export default {
        name: "SearchInfo",
        components: {
            PieChart
        },
        props: {
            source: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                chartdata: {
                    labels: [],
                    datasets: []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                },
                loading: false,
            }
        },
        mounted() {
            // this.getData();
        },
        methods: {
            getData() {
                axios.get(this.source).then(response => {
                    this.chartdata.datasets.push(response.data.datasets);
                    this.chartdata.labels = response.data.labels;
                    this.$nextTick(() => {
                        this.loading = false;
                    })
                }).catch(error => {
                    console.log(error)
                })
            }
        }
    }
</script>