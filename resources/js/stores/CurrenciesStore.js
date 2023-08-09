import {defineStore} from 'pinia'

export const useCurrenciesStore = defineStore("currenciesStore", {
    state: () => {
        return {
            currency_id:   '',

            addCurrencyRateModal:       false,
            editCurrencyRateModal:      false,
            deleteCurrencyRateModal:    false,

            currencyRates: [],
            currencyRate:  {},
            currencies:    [],
            rowIndex:      0,
            dollarRate:    {},


        }
    },

    getters: {},

    actions: {
        modalVisibility(status){
            if(!status) {
                this.currencyRate  = {}
            }
        }
    }
})
