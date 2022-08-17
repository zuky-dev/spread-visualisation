<template>
    <div id="app" class="m-0 d-flex flex-column justify-content-center align-items-center">
        <span class="col-8 py-1 px-3 mb-4" id="buy">Bidding</span>
        <lineChart class="col-8" :options="chartOptions" :chartData="chartData"/>
        <span class="col-8 py-1 px-3 mt-4" id="sell">Selling</span>
        <h1 id="currencies" class="p-3">{{ currency1 }} : {{ currency2 }}</h1>
    </div>
</template>

<style lang="scss" scoped>
    #app {
        width: 100%;
        height: 100vh;
        background: #161616;
        position: relative;

        #currencies {
            position: absolute;
            width: initial;
            bottom: 0;
            right: 0;
        }

        #buy{
            background: linear-gradient(-90deg, #20B2AB, #1AA3A6, #1395A1, #0D869D, #067898);
            text-transform: uppercase;
            font-weight: 800;
            font-size: 1.3em;
        }

        #sell{
            background: linear-gradient(90deg, #FF8B01, #FA6F01, #F55301, #F03801, #EB1C01);
            text-align: right;
            text-transform: uppercase;
            font-weight: 800;
            font-size: 1.3em;
        }
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
                currency1: import.meta.env.VITE_CEXIO_CURRENCY_1,
                currency2: import.meta.env.VITE_CEXIO_CURRENCY_2,
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