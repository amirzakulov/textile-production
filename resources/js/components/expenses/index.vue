<template>
    <!--    <ProductionHeader />-->
        <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
            <Form ref="monthlyCounters" label-position="top" class="ivu-mb">
                <Row :gutter="8">
                    <Col span="4">
                        <FormItem label="">
                            <Select v-model="selectedYear" placeholder="Йилни танланг...">
                                <Option v-for="year in years" :key="year" :value="year">{{ year }}</Option>
                            </Select>
                        </FormItem>
                    </Col>
                    <Col span="2">
                        <FormItem label="" class="text-right">
                            <Button type="primary" style="width:100%" @click="showReport">Кўриш</Button>
                        </FormItem>
                    </Col>
                </Row>
            </Form>
            <div class="_overflow _table_div">
            <table class="_table">
                <thead>
                <tr class="blue_thead bordered">
                    <th class="_text_center align-top">Номи</th>
                    <th class="_text_center">Январь</th>
                    <th class="_text_center">Февраль</th>
                    <th class="_text_center">Март</th>
                    <th class="_text_center">Апрель</th>
                    <th class="_text_center">Май</th>
                    <th class="_text_center">Июнь</th>
                    <th class="_text_center">Июль</th>
                    <th class="_text_center">Август</th>
                    <th class="_text_center">Сентябрь</th>
                    <th class="_text_center">Октябрь</th>
                    <th class="_text_center">Ноябрь</th>
                    <th class="_text_center">Декабрь</th>

                </tr>
                </thead>
                <tbody>
                    <tr v-for="(expenses, i) in expensesTotal" :key="i">
                        <td width="20px">{{expenses[0]}}</td>
                        <template v-for="(expense, e) in expenses" :key="e">
                        <td class="_text_center" v-if="e != 0">{{expense == 0 ? '':expense.toLocaleString()}}</td>
                        </template>
                    </tr>
                </tbody>
            </table>
        </div>

            <div style="width: 80%; margin-top: 100px;">
                <img src="/public/images/chart.JPG" />
            </div>

        </div>

    </template>

<script>
// import ProductionHeader from './header.vue'
export default {
    name: "Expense Dashboard",
    components: {
        // ProductionHeader: ProductionHeader
    },
    data() {
        return {
            addModal: true,
            expensesTotal: [],
            years: [2022, 2023, 2024, 2025, 2026, 2027, 2028, 2029, 2030],
            selectedYear: new Date().getFullYear(),
        }
    },
    methods: {
        async showReport(){
                const expensesTotal = await this.callApi("post", "/app/expense_total", {selectedYear: this.selectedYear})
                this.expensesTotal = expensesTotal.data
            },
        },
        async created(){
            document.title = "Харажатлар"

            const expensesTotal = await this.callApi("post", "/app/expense_total", {selectedYear: this.selectedYear})
            this.expensesTotal = expensesTotal.data

        }
}
</script>
