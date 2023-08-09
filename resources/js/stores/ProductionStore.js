import {defineStore} from 'pinia'

export const useProductionStore = defineStore("productionStore", {
    state: () => {
        return {
            department_id:  0,
            inoutSets:      [],
            inoutSet:       {from_department_id: '', to_department_id: ''},
            departmentInoutSet: {},

            date: new Date(),
            currentYear: '',
            currentMonth: '',
            created_date: '',

            departmentProducts:             [], //Bulimning maxsulotlar
            departmentProduct:              {name:'', code:'', measurement:'', fashion_id:'', },
            departmentStockRawMaterials:    [], //Bulimning xomashyolari ombori
            departmentRawMaterials:         [], //Bulimning xomashyolari
            departmentRawMaterial:          {}, //Bulimning xomashyolari
            departmentStockFinishProducts:  [], //Bulimning tayyor maxsulotlari ombori
            departmentFinishProducts:       [], //Bulimning tayyor maxsulotlari
            departmentFinishProduct:        {raw_material_balance_id: '', count: ''}, //Bulimning tayyor maxsuloti
            departmentStockSemiFinishProducts:  [], //Bulimning boshqa bulimlardan kelgan yarnim tayyor maxsulotlari ombori
            departmentSemiFinishProducts:  [], //Bulimning boshqa bulimlardan kelgan yarnim tayyor maxsulotlari ombori

            departmentInOutRawMaterials:    [], //Bulimga kirgan xomashyo maxsulotlari

            outgoingRawMaterials: [{raw_material_balance_id: '', count: ''}], //Bulimning tayyor maxsulotlar chiqimi
            outgoingFinishProducts: [{raw_material_balance_id: '', count: ''}], //Bulimning tayyor maxsulotlar chiqimi
            rowIndex: 0,

            /** *********************** Department Products ************************ **/
            productAddModal:    false,
            productEditModal:   false,
            productDeleteModal: false,
            fashions:           [],
            measurements:       [],
            departments:        [],
            currencies:         [],

            /** *********************** Finish Products ************************ **/
            finishProductRawMaterials: [],

            finishProductInModal: false,
            finishProductOutModal: false,

            /** *********************** Raw Materials Inout Sets ************************ **/
            fpSetEditModal: false,
            fpSetDeleteModal: false,

            inoutSetProducts: [],
            inoutSetProductsId: [],
            inoutSetRawMaterialsId: [],

            rmDetailsAddOutModal: false,
            rmDetailsEditOutModal: false,
            rmDetailsDeleteOutModal: false,

            /** *********************** Finished Products Details ************************ **/
            fpDetailsAddOutModal: false,
            fpDetailsEditInModal: false,
            fpDetailsEditOutModal: false,
            fpDetailsDeleteOutModal: false,

        }
    },

    getters: {},

    actions: {
        modalVisibility(status){
            if(!status) {
                this.departmentProduct       = {name:'', code:'', measurement:'', fashion_id:'', }
                this.departmentFinishProduct = {raw_material_balance_id: '', count: '',}
                this.outgoingRawMaterials    = [{raw_material_balance_id: '', count: ''}]
                this.departmentRawMaterial   = {}
                this.outgoingFinishProducts  = [{raw_material_balance_id: '', count: ''}]
                this.inoutSet.to_department_id = ''
            }
        }
    }
})
