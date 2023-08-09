<template>
    <ProductionHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <Row :gutter="8">
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

            <Col span="14" class="text-right">
                <RadioGroup v-model="tableType" @on-change="showReport" type="button" button-style="solid" class="ivu-mb">
                    <Radio label="byProduct"><Icon type="ios-menu" />Махсулот</Radio>
                    <Radio label="bySet"><Icon type="ios-remove" />Партия</Radio>
                </RadioGroup>
            </Col>

        </Row>

        <div v-if="tableType == 'byProduct'"><IncomeByProducts /></div>
        <div v-if="tableType == 'bySet'"><IncomeByInoutSet /></div>
    </div>

</template>

<script>
import ProductionHeader from './parts/header.vue'
import {useProductionStore} from "../../stores/ProductionStore";

import IncomeByProducts from './parts/raw_materials_inout/table_by_products.vue'
import IncomeByInoutSet from './parts/raw_materials_inout/table_by_inout_set.vue'

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
            if(this.tableType == 'bySet') {
                const inoutSets = await this.callApi('get', '/app/get_production_rawmaterial_sets/'+this.productionStore.department_id)
                this.productionStore.inoutSets = inoutSets.data
            } else if (this.tableType == 'byProduct'){
                const departmentInOutRawMaterials = await this.callApi('post', '/app/get_production_income_products', postData)

                this.productionStore.departmentInOutRawMaterials = departmentInOutRawMaterials.data

            }

        }
    },

    async created(){
        document.title = "Ишлаб чиқариш | Кирим"
        this.productionStore.department_id  = this.$route.params.department_id
        this.productionStore.currentYear = this.productionStore.date.getFullYear().toString()
        this.productionStore.currentMonth = this.productionStore.date.toLocaleDateString("en-GB", {month: "2-digit"})

    }
}
</script>

