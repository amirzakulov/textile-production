import {defineStore} from 'pinia'

export const useFashionsStore = defineStore("fashionsStore", {
    state: () => {
        return {
            addModal: false,
            editModal: false,
            deleteModal: false,

            fashions: [],
            fashion: {},
            rowIndex: 0,

            categories: [],
        }
    },

    getters: {},

    actions: {}
})
