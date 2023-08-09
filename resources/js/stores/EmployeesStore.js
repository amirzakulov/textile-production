import {defineStore} from 'pinia'

export const useEmployeesStore = defineStore("employeesStore", {
    state: () => {
        return {
            addModal: false,
            editModal: false,
            deleteModal: false,
            employmentStatusModal: false,

            employees: [],
            employee: {},
            rowIndex: 0,

            departments: [],
            jobTitles: [],

        }
    },

    getters: {},

    actions: {
        modalVisibility(status){
            if(!status) {
                this.employee = {}
            }
        },
        getToday(){
            const now   = new Date();
            const year  = now.getFullYear()
            const month = now.getMonth()
            const date  = now.getDate()
            const today = new Date(year, month, date)

            return today
        }
    },


})
