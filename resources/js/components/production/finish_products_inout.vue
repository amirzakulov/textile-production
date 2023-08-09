<template>
    <ProductionHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <Row :gutter="8">
            <Col span="6">
                <ButtonGroup>
                    <Button type="primary" @click="openFinishProductInModal"><Icon type="md-add" /> Кирим</Button>
                    <Button type="error" @click="openFinishProductOutModal"><Icon type="md-remove" /> Чиқим</Button>
                </ButtonGroup>
            </Col>
            <Col span="4">
                <Select v-model="productionStore.currentYear" placeholder="Йил...">
                    <Option v-for="year in getYears()" :value="year.value" :key="year.id">{{ year.label }}</Option>
                </Select>
            </Col>
            <Col span="4">
                <Select v-model="productionStore.currentMonth" placeholder="Ой...">
                    <Option v-for="month in getMonthes()" :value="month.value" :key="month.id">{{ month.label }}</Option>
                </Select>
            </Col>
            <Col span="2">
                <Button type="primary" style="width:100%" @click="showReport">Кўриш</Button>
            </Col>

            <Col span="8" class="text-right">
                <RadioGroup v-model="tableType" @on-change="showReport" type="button" button-style="solid" class="ivu-mb">
                    <Radio label="byProduct"><Icon type="ios-menu" />Махсулот</Radio>
                    <Radio label="bySet"><Icon type="ios-remove" />Партия</Radio>
                </RadioGroup>
            </Col>

        </Row>

        <div v-if="tableType == 'byProduct'"><IncomeByProducts /></div>
        <div v-if="tableType == 'bySet'"><IncomeByInoutSet /></div>

    </div>

    <FinishProductInModal />
    <FinishProductOutModal />

    <FinishProductEditSetModal />
    <FinishProductDeleteSetModal />
</template>

<script>
import ProductionHeader from './parts/header.vue'
import {useProductionStore} from "../../stores/ProductionStore";

import IncomeByProducts from './parts/finish_products_inout/table_by_products.vue'
import IncomeByInoutSet from './parts/finish_products_inout/table_by_inout_set.vue'

import FinishProductInModal from "./parts/finish_products_inout/inModal.vue"
import FinishProductOutModal from "./parts/finish_products_inout/outModal"

import FinishProductEditSetModal from "./parts/finish_products_inout/editSetModal"
import FinishProductDeleteSetModal from "./parts/finish_products_inout/deleteSetModal.vue"
export default {
    name: "productionIncome",
    setup(){
        const productionStore = useProductionStore()
        return { productionStore }
    },
    components: {
        ProductionHeader: ProductionHeader,
        IncomeByProducts: IncomeByProducts,
        IncomeByInoutSet: IncomeByInoutSet,

        FinishProductInModal: FinishProductInModal,
        FinishProductOutModal: FinishProductOutModal,

        FinishProductEditSetModal: FinishProductEditSetModal,
        FinishProductDeleteSetModal: FinishProductDeleteSetModal,
    },
    data(){
        return {
            tableType: 'bySet',
            inout: 1,
        }
    },

    methods: {
        async showReport(){
            let postData = {
                year:           this.productionStore.currentYear,
                month:          this.productionStore.currentMonth,
                department_id:  this.productionStore.department_id
            }
            if(this.tableType === 'bySet') {
                const inoutSets = await this.callApi('get', '/app/get_production_finish_products_sets/'+this.productionStore.department_id)
                this.productionStore.inoutSets = inoutSets.data
            } else if (this.tableType === 'byProduct'){
                const departmentInOutRawMaterials = await this.callApi('post', '/app/get_production_income_products', postData)

                this.productionStore.departmentInOutRawMaterials = departmentInOutRawMaterials.data

            }
        },
        openFinishProductInModal(){
            this.productionStore.created_date = this.myDateFormat(null, "yyyy-mm-dd")
            this.productionStore.finishProductInModal = true
        },
        openFinishProductOutModal(){
            this.productionStore.created_date = this.myDateFormat(null, "yyyy-mm-dd")
            this.productionStore.finishProductOutModal = true
        }
    },

    async created(){
        document.title = "Ишлаб чиқариш | Кирим"
        this.productionStore.department_id  = this.$route.params.department_id

        this.productionStore.currentYear = this.productionStore.date.getFullYear().toString()
        this.productionStore.currentMonth = this.productionStore.date.toLocaleDateString("en-GB", {month: "2-digit"})
        let department_types = {types: [2, 3]}

        const [departmentRawMaterials, departmentProducts, departmentFinishProducts, departments] = await Promise.all([
            this.callApi('get', '/app/get_production_department_rawmaterials/'+this.productionStore.department_id),
            this.callApi("get", "/app/get_production_department_products/"+this.productionStore.department_id),
            this.callApi("get", "/app/get_production_department_finish_products/"+this.productionStore.department_id),
            this.callApi('post', '/app/get_departments_types', department_types),
        ])

        this.productionStore.departmentRawMaterials     = departmentRawMaterials.data  //Bulimning xomashyolari
        this.productionStore.departmentProducts         = departmentProducts.data //Bulim maxsulotlari
        this.productionStore.departmentFinishProducts   = departmentFinishProducts.data
        this.productionStore.departments                = departments.data

        this.loading = false

    }
}
</script>

