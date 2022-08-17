import { createStore } from "vuex";

const MAX_GRAPH_POINTS = 15;
const API_URL = '/api';

const store = createStore({
    state: {
        dates: [],
        buys: [],
        sells: [],
    },
    mutations: {
        dates(state, value){
            state.dates.push(value);

            if (state.dates.length > MAX_GRAPH_POINTS) {
                state.dates.shift();
            }
        },
        buys(state, value){
            for (let i = 0; i < value[1].length; i++) {
                if (state.buys[i] == undefined) {
                    state.buys[i] = [];
                }

                state.buys[i].push({
                    price: value[1][i].price,
                    quantity: value[1][i].quantity,
                    perUnit: value[1][i].perUnit,
                    date: value[0],
                });

                if (state.buys[i].length > MAX_GRAPH_POINTS) {
                    state.buys[i].shift();
                }
            }
        },
        sells(state, value){
            for (let i = 0; i < value[1].length; i++) {
                if (state.sells[i] == undefined) {
                    state.sells[i] = [];
                }

                state.sells[i].push({
                    price: value[1][i].price,
                    quantity: value[1][i].quantity,
                    perUnit: value[1][i].perUnit,
                    date: value[0],
                });

                if (state.sells[i].length > MAX_GRAPH_POINTS) {
                    state.sells[i].shift();
                }
            }
        }
    },
    actions: {
        fetchData: (state, interval = 5000) => {
            let inter = setInterval(() => {updateData(state)}, interval);
        }
    },
    getters: {
        lastDate: state => {
            let since = null;

            if (state.dates.length > 0) {
                since = state.dates[state.dates.length - 1];
            }

            return since;
        },
        chartData: state => {
            let sellsColors = ['#FF8B01', '#FA6F01', '#F55301', '#F03801', '#EB1C01'];
            let buysColors = ['#20B2AB', '#1AA3A6', '#1395A1', '#0D869D', '#067898'];

            let obj = {
                labels: [],
                datasets: []
            };

            for (let i = 0; i < state.dates.length; i++) {
                obj.labels.push(state.dates[i]);
            }

            for (let i = 0; i < state.sells.length; i++) {
                let sells = [];
                state.sells[i].forEach(element => {
                    sells.push({
                        price: element.price,
                        date: element.date,
                        quantity: element.quantity,
                        perUnit: element.perUnit,
                    });
                });

                obj.datasets.push({
                    label: 'Sells TOP' + (i + 1),
                    data: sells,
                    fill: false,
                    tension: 0.2,
                    borderColor: sellsColors[i]
                });
            }

            for (let i = 0; i < state.buys.length; i++) {
                let buys = [];
                state.buys[i].forEach(element => {
                    buys.push({
                        price: element.price,
                        date: element.date,
                        quantity: element.quantity,
                        perUnit: element.perUnit,
                    });
                });

                obj.datasets.push({
                    label: 'Buys TOP' + (i + 1),
                    data: buys,
                    fill: false,
                    tension: 0.2,
                    borderColor: buysColors[i]
                });
            }

            return obj;
        }
    }
});

function updateData (state) {
    let since = state.getters.lastDate;

    axios.get(API_URL + '/orderbook', {
        params: {
            since: since
        }
    }).then((response) => {
        let data = response.data;

        if (!Array.isArray(data)) {
            for (const [date, value] of Object.entries(data)) {
                state.commit('dates', date);

                if (value.BUY != undefined) {
                    state.commit('buys', [date, value.BUY]);
                }

                if (value.SELL != undefined) {
                    state.commit('sells', [date, value.SELL]);
                }
            }
        }
    });
}

export default store;