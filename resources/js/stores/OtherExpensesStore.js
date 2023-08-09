import {defineStore} from 'pinia'

export const useOtherExpensesStore = defineStore("otherExpensesStore", {
    state: () => {
        return {

            //Expense Types
            expenseTypeAddModal:    false,
            expenseTypeEditModal:   false,
            expenseTypeDeleteModal: false,
            expenseTypes:       [],
            expenseType:        {},
            rowIndex:           0,

            //Expenses
            expenseEditModal:   false,
            expenseDeleteModal: false,
            expenseTypeId:      0,
            expenses:           [],
            expense:            {},
            expenseTypeData:    {},

        }
    },

    getters: {},

    actions: {}
})
