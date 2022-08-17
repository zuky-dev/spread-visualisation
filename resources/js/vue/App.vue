<template>
    <div>
        <lineChart :options="chartOptions" :chartData="chartData"/>
    </div>
</template>

<style lang="scss">
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
                        }
                    },
                    plugins: {
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