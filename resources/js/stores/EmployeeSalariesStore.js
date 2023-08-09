import {defineStore} from 'pinia'

export const useEmployeeSalariesStore = defineStore("employeeSalariesStore", {
    state: () => {
        return {
            addModal: false,
            editModal: false,
            deleteModal: false,

            paidEmployees: [],
            allEmployees: [],
            employee: {},
            rowIndex: 0,


            years: [
                {id: 2022, label: '2022'},
                {id: 2023, label: '2023'},
                {id: 2024, label: '2024'},
                {id: 2025, label: '2025'},
            ],
            monthes: [
                {id: 1, label: 'Январь'},
                {id: 2, label: 'Февраль'},
                {id: 3, label: 'Март'},
                {id: 4, label: 'Апрель'},
                {id: 5, label: 'Май'},
                {id: 6, label: 'Июнь'},
                {id: 7, label: 'Июль'},
                {id: 8, label: 'Август'},
                {id: 9, label: 'Сентябрь'},
                {id: 10, label: 'Октябрь'},
                {id: 11, label: 'Ноябрь'},
                {id: 12, label: 'Декабрь'},
            ],
        }
    },

    getters: {

    },

    actions: {},


})
