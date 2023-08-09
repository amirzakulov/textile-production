<template>
    <CommunalExpenseHeader />

        <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">

            <Form ref="monthlyCounters" label-position="top">
                <Row :gutter="8">
                    <Col span="4">
                        <FormItem label="Муддат боши">
                            <DatePicker v-model="dateForm.startDate" format="yyyy-MM-dd" type="date" placeholder="Санани танланг" />
                        </FormItem>
                    </Col>
                    <Col span="4">
                        <FormItem label="Муддат охири">
                            <DatePicker v-model="dateForm.endDate" format="yyyy-MM-dd" type="date" placeholder="Санани танланг" />
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
            <Row :gutter="8">
                <Col span="4">Тури</Col>
                <Col span="4">Ишлатилди</Col>
                <Col span="4">Тўлов (Сўм)</Col>
            </Row>
            <Row :gutter="8" v-for="(communalType, i) in communalReport" :key="i">
                <Col span="4"><strong>{{communalType.name}}:</strong></Col>
                <Col span="4">{{communalType.amount }} {{communalType.measurement}}</Col>
                <Col span="4">{{ communalType.payment.toLocaleString() }}</Col>
            </Row>
            <Row :gutter="8">
                <Col span="4"></Col>
                <Col span="4"></Col>
                <Col span="4"><strong>{{ getTotalExpense.toLocaleString() }}</strong></Col>
            </Row>
        </div>

    </template>

    <script>
    import CommunalExpenseHeader from './parts/header_communal.vue'
    export default {
        name: "Communal Expenses",
        components: {
            CommunalExpenseHeader: CommunalExpenseHeader,
        },
        computed: {
            getTotalExpense(){
                let total = 0
                this.communalReport.forEach((communalType, index) => {
                    total += parseInt(communalType.payment)
                })

                return total
            }
        },
        data() {
            return {
                dateForm: {
                    startDate:  '',
                    endDate:    '',
                },
                currentDay:   '',
                currentMonth: '',
                currentYear:  '',
                communalReport: [],
            }
        },
        methods: {
            async showReport(){
                const report = await this.callApi("post", "/app/communal_report", this.dateForm)
                this.communalReport = report.data
            },


        },
        async created(){
            const now         = new Date();
            const year        = now.getFullYear()
            const month       = now.getMonth()
            const firstFullDate = new Date(year, month, 1)
            const lastFullDate  = new Date(year, month + 1, 0);
            this.dateForm.startDate = this.myDateFormat(firstFullDate, 'yyyy-mm-dd')
            this.dateForm.endDate   = this.myDateFormat(lastFullDate, 'yyyy-mm-dd')

            const report = await this.callApi("post", "/app/communal_report", this.dateForm)
            this.communalReport = report.data

        }
    }
    </script>
