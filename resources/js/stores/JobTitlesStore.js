import {defineStore} from 'pinia'

export const useJobTitlesStore = defineStore("jobTitlesStore", {
    state: () => {
        return {
            addModal: false,
            editModal: false,
            deleteModal: false,

            jobTitles: [],
            jobTitle: {},
            rowIndex: 0,

        }
    },

    getters: {},

    actions: {

    },


})
