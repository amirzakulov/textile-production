import {defineStore} from 'pinia'

export const useFashionsStore = defineStore("fashionsStore", {
    state: () => {
        return {
            addModal: false,
            editModal: false,
            deleteModal: false,

            addFashionDetailsModal: false,
            editFashionDetailsModal: false,
            deleteFashionDetailsModal: false,

            fashions: [],
            fashion: {},

            fashionDetails: [],
            fashionDetail:  {},

            rowIndex: 0,

            categories: [],
        }
    },

    getters: {},

    actions: {
        modalVisibility(status){
            if(!status) {
                this.fashion        = {}
                this.fashionDetail  = {}
            }
        },
    }
})
