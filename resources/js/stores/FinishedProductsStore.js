import {defineStore} from 'pinia'

export const useFinishedProductsStore = defineStore("finishedProductsStore", {
    state: () => {
        return {
            department_id:  7,

            date: new Date(),
            currentYear: '',
            currentMonth: '',
            created_date: '',

            fpStockFinishProducts:  [], //Bulimning tayyor maxsulotlari ombori
            fpFinishProducts:       [], //Bulimning tayyor maxsulotlari

            /** ********************** Finished Products Inout **/
            fpOutgoingFinishProducts: [{raw_material_balance_id: '', count: ''}],
            inoutSets:          [],
            inoutSet:           {from_department_id: 7, to_department_id: ''},
            inoutSetProducts:   [],
            inoutSetProductsId: [],
            inoutSetRawMaterialsId: [],
            fpSet:              {},

            fpFinishedProductOutModal: false,
            fpFinishedProductDeleteSetModal: false,

            /** ********************** Finished Products Inout Details **/
            departmentFinishProduct: {},
            fpDetailsAddOutModal:   false,
            fpDetailsEditOutModal:   false,
            fpDetailsDeleteOutModal: false,

            /** ********************** Finished Products Reports **/
            finishedProductRawMaterials: [],
            fpDetailsShowModal:          false,

            rowIndex: 0,
            currencies: 0,
            departments: 0,


        }
    },

    getters: {},

    actions: {
        modalVisibility(status){
            if(!status) {
                this.inoutSet                 = {from_department_id: 7, to_department_id: ''}
                this.fpOutgoingFinishProducts = [{raw_material_balance_id: '', count: ''}]
            }
        }
    }
})
