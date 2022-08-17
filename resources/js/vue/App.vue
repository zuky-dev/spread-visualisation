<template>
    <div id="app" class="d-flex justify-content-center align-items-center row">
        <lineChart class="col-8" :options="chartOptions" :chartData="chartData"/>
    </div>
</template>

<style lang="scss" scoped>
    #app {
        width: 100%;
        height: 100vh;
        background: #161616;
    }
</style>

<script>
    import { mapActions, mapGetters } from 'vuex';

    import { Chart, registerables } from 'chart.js';
    import { LineChart } from "vue-chart-3";
    Chart.register(...registerables);

    export default {
        components:{
            'lineChart' : LineChart
        },
        data () {
            return {
                chartOptions: {
                    parsing: {
                        xAxisKey: 'date',
                        yAxisKey: 'price'
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: {
                                color: '#2a2a2a',
                                tickColor: '#2a2a2a'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        // Custom tooltip for showing advanced data
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return context[0].dataset.label;
                                },
                                label: function(context) {
                                    let currData = context.dataset.data[context.dataIndex];

                                    return `price: ${currData.price}, quantity: ${currData.quantity}, price per unit: ${currData.perUnit}`;
                                },
                                footer: function(context) {
                                    let currData = context[0].dataset.data[context[0].dataIndex]

                                     return `at ${currData.date}`;
                                },
                            }
                        }
                    }
                },
            }
        },
        computed: {
            ...mapGetters({
                chartData: 'chartData'
            })
        },
        methods:{
            ...mapActions({
                fetchData: 'fetchData'
            })
        },
        mounted(){
            this.fetchData(3000);
        }
    }
</script>