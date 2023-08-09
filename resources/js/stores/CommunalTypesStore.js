import {defineStore} from 'pinia'

export const useCommunalTypesStore = defineStore("communalTypesStore", {
    state: () => {
        return {

            //Communal Devices
            deviceAddModal:     false,
            deviceEditModal:    false,
            deviceDeleteModal:  false,
            device:             {},
            rowIndex:           0,

            //Communal Types
            paymentEditModal:   false,
            paymentDeleteModal: false,
            communalTypeId:     0,
            lastPayment:        {},
            restrictDaysOption: {},
            devices:            [],
            payment:            {},
            payments:           [],
            communalTypeData:   {},

        }
    },

    getters: {},

    actions: {}
})
