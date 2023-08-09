import {defineStore} from 'pinia'

export const useReportsStore = defineStore("reportsStore", {
    state: () => {
        return {
            reportTypes: [
                "Тайёр махсулот кирим",
                "Тайёр махсулот чиқим",
                "Хомашё кирим",
                "Хомашё чиқим",
                "Коммунал харажатлар",
                "Қўшимча харажатлар",
                "Ойлик маош",
            ],
            formData: {
                reportType: 0,
                startDate:  '',
                endDate:    '',
            },
            productionData: {
                department_id: '',
                inout: '',
            },
            tableData: [],
            loading: true,


        }
    },

    getters: {},

    actions: {

    }
})
