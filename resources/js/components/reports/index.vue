<template>
    <ReportsHeader />
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">

        <Form ref="monthlyCounters" label-position="top">
            <Row :gutter="8">
                <Col span="4">
                    <FormItem label="* Қайси бўлимга">
                        <Select v-model="reportsStore.formData.reportType" placeholder="Бўлим танланг...">
                            <Option v-for="(report_title, index) in reportsStore.reportTypes" :value="index" :key="index">{{ report_title }}</Option>
                        </Select>
                        <Text :class="{ 'd-none': !report_type_err}" type="danger">Тўлдириш мажбурий!</Text>
                    </FormItem>
                </Col>
                <Col span="3">
                    <FormItem label="Муддат боши">
                        <DatePicker v-model="reportsStore.formData.startDate" format="yyyy-MM-dd" type="date" placeholder="Санани танланг" />
                        <Text :class="{ 'd-none': !start_date_err}" type="danger">Тўлдириш мажбурий!</Text>
                    </FormItem>
                </Col>
                <Col span="3">
                    <FormItem label="Муддат охири">
                        <DatePicker v-model="reportsStore.formData.endDate" format="yyyy-MM-dd" type="date" placeholder="Санани танланг" />
                        <Text :class="{ 'd-none': !end_date_err}" type="danger">Тўлдириш мажбурий!</Text>
                    </FormItem>
                </Col>
                <Col span="2">
                    <FormItem label="&nbsp;&nbsp;" class="text-right">
                        <Button type="primary" style="width:100%" @click="showReport">Кўриш</Button>
                    </FormItem>
                </Col>
            </Row>
        </Form>
        <Divider />

        <component :is="selectedComponent" ></component>

    </div>


</template>

<script>
import {useReportsStore} from "../../stores/ReportsStore";
import ReportsHeader from "./parts/header"

import FinishedProductsInoutReport from "./table_0_finished_products_inout"
import RawMaterialsInoutReport from "./table_1_raw_materials_inout"
import CommunalExpensesReport from "./table_2_communal_expenses"
import ExpensesReport from "./table_3_expenses"
import EmployeesSalaryReport from "./table_4_employees_salary"

export default {
    name: "Finished Products In Report",
    setup(){
        const reportsStore = useReportsStore()
        return { reportsStore}
    },
    components: {
        ReportsHeader: ReportsHeader,
        FinishedProductsInoutReport: FinishedProductsInoutReport,
        RawMaterialsInoutReport: RawMaterialsInoutReport,
        CommunalExpensesReport: CommunalExpensesReport,
        ExpensesReport: ExpensesReport,
        EmployeesSalaryReport: EmployeesSalaryReport,

    },
    data() {
        return {
            selectedComponent: "FinishedProductsInoutReport",

            report_type_err: false,
            start_date_err:  false,
            end_date_err:    false,
        }
    },
    methods: {
        async showReport(){
            this.reportsStore.tableData = []
            if(this.reportsStore.formData.startDate === '') {
                this.is_invalid = true
                this.start_date_err = true
            } else {
                this.start_date_err = false
            }

            if(this.reportsStore.formData.endDate === '') {
                this.is_invalid   = true
                this.end_date_err = true
            } else {
                this.end_date_err = false
            }

            if(this.is_invalid) {
                this.is_invalid   = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            this.reportsStore.formData.startDate = this.myDateFormat(this.reportsStore.formData.startDate, 'yyyy-mm-dd')
            this.reportsStore.formData.endDate   = this.myDateFormat(this.reportsStore.formData.endDate, 'yyyy-mm-dd')

            let report    = []
            let reportUrl = ""
            let postData = {
                formData: this.reportsStore.formData,
                productionData: this.reportsStore.productionData,
            }
            switch (this.reportsStore.formData.reportType) {
                case 0:
                    this.selectedComponent                         = "FinishedProductsInoutReport"
                    this.reportsStore.productionData.department_id = 7 //Tayyor maxsulot sehi
                    this.reportsStore.productionData.inout         = 1
                    postData.productionData                        = this.reportsStore.productionData

                    reportUrl = "/app/finished_products_inout_report"
                    break;
                case 1:
                    this.selectedComponent                         = "FinishedProductsInoutReport"
                    this.reportsStore.productionData.department_id = 7 //Tayyor maxsulot sehi
                    this.reportsStore.productionData.inout         = 2
                    postData.productionData                        = this.reportsStore.productionData

                    reportUrl = "/app/finished_products_inout_report"
                    break;
                case 2:
                    this.selectedComponent                         = "RawMaterialsInoutReport"
                    this.reportsStore.productionData.department_id = 2 //xomashyo ombori
                    this.reportsStore.productionData.inout         = 1
                    postData.productionData                        = this.reportsStore.productionData

                    reportUrl = "/app/rawmaterials_inout_report"
                    break;
                case 3:
                    this.selectedComponent                         = "RawMaterialsInoutReport"
                    this.reportsStore.productionData.department_id = 2 //xomashyo ombori
                    this.reportsStore.productionData.inout         = 2
                    postData.productionData                        = this.reportsStore.productionData

                    reportUrl = "/app/rawmaterials_inout_report"
                    break;
                case 4:
                    this.selectedComponent                   = "CommunalExpensesReport"
                    reportUrl = "/app/communal_expenses_report"

                    break;
                case 5:
                    this.selectedComponent                   = "ExpensesReport"
                    reportUrl = "/app/expenses_report"

                    break;
                case 6:
                    this.selectedComponent                   = "EmployeesSalaryReport"
                    reportUrl = "/app/employees_salary_report"

                    break;
            }

            report = await this.callApi("post", reportUrl, postData)

            // console.log("Table Data: ", report.data)

            this.reportsStore.tableData = report.data

            this.reportsStore.loading = false
        },


    },
    async created(){
        const now         = new Date();
        const year        = now.getFullYear()
        const month       = now.getMonth()
        const firstFullDate = new Date(year, month, 1)
        const lastFullDate  = new Date(year, month + 1, 0);

        this.reportsStore.formData.startDate  = this.myDateFormat(firstFullDate, 'yyyy-mm-dd')
        this.reportsStore.formData.endDate    = this.myDateFormat(lastFullDate, 'yyyy-mm-dd')

        this.showReport()

    }
}
</script>
