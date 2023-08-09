<template>
    <FinishedProductsHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <Row :gutter="8">
            <Col span="6">
                <ButtonGroup>
                    <Button type="error" @click="openFpFinishedProductOutModal"><Icon type="md-remove" /> Чиқим</Button>
                </ButtonGroup>
            </Col>
            <Col span="4">
                <Select v-model="fpStore.currentYear" placeholder="Йил...">
                    <Option v-for="year in getYears()" :value="year.value" :key="year.id">{{ year.label }}</Option>
                </Select>
            </Col>
            <Col span="4">
                <Select v-model="fpStore.currentMonth" placeholder="Ой...">
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

        <div v-if="tableType == 'bySet'"><IncomeByInoutSet /></div>

    </div>

    <FinishProductOutModal />
    <FinishProductDeleteOutModal />
</template>

<script>
import FinishedProductsHeader from './parts/header.vue'
import {useFinishedProductsStore} from "../../stores/FinishedProductsStore";

import IncomeByInoutSet from './parts/finish_products_inout/table_by_inout_set.vue'

import FinishProductOutModal from './parts/finish_products_inout/outModal.vue'
import FinishProductDeleteOutModal from './parts/finish_products_inout/deleteSetModal'

export default {
    name: "Finished Products Inout",
    setup(){
        const fpStore = useFinishedProductsStore()
        return { fpStore }
    },
    components: {
        FinishedProductsHeader: FinishedProductsHeader,

        IncomeByInoutSet: IncomeByInoutSet,
        FinishProductOutModal: FinishProductOutModal,
        FinishProductDeleteOutModal: FinishProductDeleteOutModal,
    },
    data(){
        return {
            tableType: 'bySet',
        }
    },
    methods: {
        async showReport(){
            let postData = {
                year:           this.fpStore.currentYear,
                month:          this.fpStore.currentMonth,
                department_id:  this.fpStore.department_id
            }

            if(this.tableType == 'bySet') {
                const inoutSets = await this.callApi('get', '/app/get_fp_finish_products_sets/'+this.fpStore.department_id)
                this.fpStore.inoutSets = inoutSets.data

            } else if (this.tableType == 'byProduct'){
                // const departmentInOutRawMaterials = await this.callApi('post', '/app/get_production_income_products', postData)
                //
                // this.fpStore.departmentInOutRawMaterials = departmentInOutRawMaterials.data

            }

        },
        openFpFinishedProductOutModal(){
            this.fpStore.created_date = this.myDateFormat(null, "yyyy-mm-dd")
            this.fpStore.fpFinishedProductOutModal  = true
        }
    },

    async created(){
        document.title = "Тайёр махсулот Кирим-чиқим"
        this.fpStore.currentYear = this.fpStore.date.getFullYear().toString()
        this.fpStore.currentMonth = this.fpStore.date.toLocaleDateString("en-GB", {month: "2-digit"})

        let department_types = {types: [4]}
        const [departments, fpFinishProducts] = await Promise.all([
            this.callApi('post', '/app/get_departments_types', department_types),
            this.callApi('get', '/app/get_fp_finish_products/'+this.fpStore.department_id),
        ])

        this.fpStore.departments      = departments.data
        this.fpStore.fpFinishProducts = fpFinishProducts.data

    }
}
</script>

