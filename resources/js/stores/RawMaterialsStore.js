import {defineStore} from 'pinia'

export const useRawMaterialsStore = defineStore("rawMaterialsStore", {
    state: () => {
        return {
            department_id: 2,

            /* Xomashyo maxsulotlari */
            rmStockProducts: [],

            /* Bulim maxsulotlari */
            productAddModal:    false,
            productEditModal:   false,
            productDeleteModal: false,
            products:       [],
            product:        {},

            /* Partiya kirim-chiqim */
            incomeProducts: [{product_id: '', currency_id: '', price_value: '', count:''}],
            outgoingProducts: [{raw_material_balance_id: '', count: ''}],
            rmSets: [],
            rmSet:  {},

            //Kirim
            productInModal: false,
            productOutModal: false,
            rmSetEditModal: false,
            rmSetDeleteModal: false,

            created_date: '',

            //Chiqim
            departmentRawMaterials: [],
            inoutSet: {from_department_id: 2, to_department_id: ''},

            /* Partiya maxsulotlari */
            rmSetProducts:  [],
            rawMaterial:    {},
            rmSetProductsId:[],
            rmSetRawMaterialsId:[],
            stockCount:     0,
            rmAddModal:     false,
            rmEditModal:    false,
            rmDeleteModal:  false,

            rmAddOutModal:     false,
            rmEditOutModal:    false,
            rmDeleteOutModal:  false,

            /** ************************** Semi Finished Products **/
            rmSemiFinishedStockProducts: [],
            rmSemiFinishProducts: [],
            rmSemiFinishProduct:  {},
            rmOutgoingSemiFinishProducts: [{raw_material_balance_id: '', count: ''}],

            rmSemiFinishProductsOutModal: false,
            rmSemiFinishProductDeleteSetModal: false,

            rmEditSemiFinishProductOutModal: false,
            rmDeleteSemiFinishProductOutModal: false,

            /* Genaral variables */
            rowIndex:       0,
            departments:    [],
            categories:     [],
            measurements:   [],
            currencies:     [],
            keywordName:    '',
            keywordCode:    '',

            productsTmp:    [],
        }
    },

    getters: {
        filteredProducts(){
            this.productsTmp = this.products

            if (this.keywordName !== '' && this.keywordName) {
                this.productsTmp = this.productsTmp.filter((product) => {
                    return product.name.toLowerCase().includes(this.keywordName.toLowerCase())
                })
            }

            if (this.keywordCode !== '' && this.keywordCode) {
                this.productsTmp = this.productsTmp.filter((product) => {
                    return product.code.toLowerCase().includes(this.keywordCode.toLowerCase())
                })
            }

            return this.productsTmp
        },
    },

    actions: {
        modalVisibility(status){
            if(!status) {
                this.incomeProducts     = [{product_id: '', currency_id: '', price_value: '', count:''}]
                this.outgoingProducts   = [{raw_material_balance_id: '', count: ''}]
                this.inoutSet           = {from_department_id: 2, to_department_id: ''}
                this.rawMaterial        = {}
                this.rmOutgoingSemiFinishProducts = [{raw_material_balance_id: '', count: ''}]
                this.product            = {}
            }
        },
    }
})
