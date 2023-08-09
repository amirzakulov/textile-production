<template>
    <StockHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">

        <Row :gutter="8" class="ivu-mb">
            <Col span="5" class="">
                <div class="bg-light _1adminOverveiw_card _box_shadow1 _border_radious _1adminOverveiw_bg_two">
                    <div class="_1adminOverveiw_card_left">
                        <p class="_1adminOverveiw_card_left_num">{{salaryTotal.toLocaleString()}} сўм</p>

                        <p class="_1adminOverveiw_card_left_title">Маош</p>
                    </div>
                    <div class="_1adminOverveiw_card_right">
                        <Icon type="md-trending-up" />
                    </div>
                </div>
            </Col>
            <Col span="5">
                <div class="bg-light _1adminOverveiw_card _box_shadow1 _border_radious _1adminOverveiw_bg_two">
                    <div class="_1adminOverveiw_card_left">
                        <p class="_1adminOverveiw_card_left_num">{{communalExpensesTotal.toLocaleString()}} сўм</p>

                        <p class="_1adminOverveiw_card_left_title">Коммунал тўловлар</p>
                    </div>
                    <div class="_1adminOverveiw_card_right">
                        <Icon type="md-trending-up" />
                    </div>
                </div>
            </Col>
            <Col span="5">
                <div class="bg-light _1adminOverveiw_card _box_shadow1 _border_radious _1adminOverveiw_bg_two">
                    <div class="_1adminOverveiw_card_left">
                        <p class="_1adminOverveiw_card_left_num">{{otherExpensesTotal.toLocaleString()}} сўм</p>

                        <p class="_1adminOverveiw_card_left_title">Қўшимча харажатлар</p>
                    </div>
                    <div class="_1adminOverveiw_card_right">
                        <Icon type="md-trending-up" />
                    </div>
                </div>
            </Col>
            <Col span="5">
                <div class="bg-light _1adminOverveiw_card _box_shadow1 _border_radious _1adminOverveiw_bg_two">
                    <div class="_1adminOverveiw_card_left">
                        <p class="_1adminOverveiw_card_left_num">{{this.inTotalCount}} дона</p>

                        <p class="_1adminOverveiw_card_left_title">Тайёр махсулотлар</p>
                    </div>
                    <div class="_1adminOverveiw_card_right">
                        <Icon type="md-trending-up" />
                    </div>
                </div>
            </Col>
            <Col span="4">
                <div class="bg-danger _1adminOverveiw_card _border_radious _1adminOverveiw_bg_two">
                    <div class="_1adminOverveiw_card_left">
                        <p class="_1adminOverveiw_card_left_num text-white">{{variableCost.toLocaleString()}} сўм</p>
                        <p class="_1adminOverveiw_card_left_title text-white">Қўшимча қиймат</p>
                    </div>
                    <div class="_1adminOverveiw_card_right">
                        <Icon type="md-trending-up" />
                    </div>
                </div>
            </Col>
        </Row>

        <Row :gutter="8" class="ivu-mb">
            <Col span="4">
                <Select v-model="currentYear" placeholder="Йил...">
                    <Option v-for="year in getYears()" :value="year.value" :key="year.id">{{ year.label }}</Option>
                </Select>
            </Col>
            <Col span="4">
                <Select v-model="currentMonth" placeholder="Ой...">
                    <Option v-for="month in getMonthes()" :value="month.value" :key="month.id">{{ month.label }}</Option>
                </Select>
            </Col>
            <Col span="2">
                <Button type="primary" style="width:100%" @click="showReport">Кўриш</Button>
            </Col>

            <Col span="2" offset="4" class="text-right pt-2">
                <span>қидирув:</span>
            </Col>
            <Col span="8">
                <Input v-model="keywordName" clearable placeholder="Модел номи..." />
            </Col>
<!--            <Col span="4">-->
<!--                <Input v-model="keywordCode" clearable placeholder="Махсулот коди..." />-->
<!--            </Col>-->
        </Row>

        <Table size="small" :columns="columns" :data="filteredProducts" :loading="loading" border height="500"
               highlight-row ref="currentRowTable"
        >
            <template #inVariableCost="{row}">
                <strong>{{row.inCount > 0 ? formatPrice(variableCost, 2).toLocaleString() : ''}}</strong>
            </template>
            <template #perItemCost="{row}">
                <strong>{{row.inCount > 0 ? formatPrice(variableCost + row.inPrice, 2).toLocaleString() : ''}}</strong>
            </template>
            <template #inPriceTotal="{row}">
                <strong>{{row.inCount > 0 ? formatPrice(row.inCount * (row.inPrice + variableCost), 2).toLocaleString() : ''}}</strong>
            </template>
        </Table>
    </div>

    <FpDetailsShowModal />
</template>

<script>
import StockHeader from './parts/header.vue'
import {useFinishedProductsStore} from "../../stores/FinishedProductsStore";
import {resolveComponent} from "vue";

import FpDetailsShowModal from "./parts/report2/finishedProductDetailsModal"

export default {
    name: "reports",
    components: {
        StockHeader:StockHeader,
        FpDetailsShowModal:FpDetailsShowModal,
    },
    setup(){
        const fpStore = useFinishedProductsStore()
        return { fpStore }
    },
    computed: {
        filteredProducts(){
            let productsTmp = this.products

            if (this.keywordName != '' && this.keywordName) {
                productsTmp = productsTmp.filter((product) => {
                    return product.name.toLowerCase().includes(this.keywordName.toLowerCase())
                })
            }

            return productsTmp
        },
    },
    data(){
        return {
            keywordName: '',
            date: new Date(),
            currentYear: '',
            currentMonth: '',

            products: [],
            prevMonthTotal: 0,
            inTotal: 0,
            inTotalCount: 0,
            outTotal: 0,
            lastMonthTotal:0,

            salaryTotal: 0,
            communalExpensesTotal: 0,
            otherExpensesTotal: 0,

            variableCost: 0,

            loading: true,
            columns: [
                {
                    title: " ",
                    key: 'action',
                    width: 50,
                    align: 'right',
                    fixed: "left",
                    render: (h, params) => {

                        return h('div', {class: 'ivu-btn-group ivu-btn-group-default'}, [

                            h(resolveComponent('Button'), {
                                title:"Кўриш",
                                type: 'primary',
                                size: 'small',

                                onClick: () => {
                                    this.showFinishedProductDetailsModal(params.row, params.index)
                                }

                            }, {
                                default() {
                                    return h(resolveComponent('Icon'), {
                                        type: 'md-menu',
                                        color: "#fff",
                                    })
                                }
                            }),

                        ])
                    }
                },

                {
                    title: "Модел",
                    key: 'fashion_name',
                    width: "100",
                    sortable: true,
                    fixed: "left",
                    align: 'center',
                },
                {
                    title: "Бирлик",
                    key: 'measurement',
                    width: "100",
                    fixed: "left",
                    align: 'center',
                },
                {
                    title: "Жорий ой кирим",
                    align: 'center',
                    children: [
                        {
                            title: "Миқдор",
                            key: 'inCount',
                            align: 'center',
                        },
                        {
                            title: "Нарх",
                            key: 'inPrice',
                            align: 'center',
                        },
                        {
                            title: "Қ.Қиймат",
                            slot: 'inVariableCost',
                            align: 'center',
                        },
                        {
                            title: "Тан нарх",
                            slot: 'perItemCost',
                            align: 'center',
                        },
                        {
                            title: "Жами",
                            key: 'inPrice',
                            slot: 'inPriceTotal',
                            align: 'center',
                        },
                    ]
                },


            ],

        }
    },

    methods : {
        async showReport(){
            const yearMonthPostData = {
                year:           this.currentYear,
                month:          this.currentMonth,
            }

            //Ойлик маош
            const salaryTotal = await this.callApi('post', '/app/get_monthly_salary_total', yearMonthPostData)
            this.salaryTotal = salaryTotal.data

            //Коммунал тўловлар
            const communalExpensesTotal = await this.callApi('post', '/app/get_monthly_communal_expenses_total', yearMonthPostData)
            this.communalExpensesTotal = communalExpensesTotal.data

            //Бошқа харажатлар
            const otherExpensesTotal = await this.callApi('post', '/app/get_monthly_other_expenses_total', yearMonthPostData)
            this.otherExpensesTotal = otherExpensesTotal.data

            let postData = {
                year:           this.currentYear,
                month:          this.currentMonth,
                department_id:  this.fpStore.department_id,
                isRawMaterial:  0,
                product_department_id:  6,
            }

            const res = await this.callApi("post", "/app/department_finished_products_report", postData)
            if(res.status == 200) {
                this.products = Object.values( res.data )

                let prevMonthTotal  = 0;
                let lastMonthTotal  = 0;
                let inTotal         = 0;
                let inTotalCount    = 0;
                let outTotal        = 0;

                this.products.forEach(function (product, index) {

                    if(product.prev_month_count && product.prev_month_price) {
                        prevMonthTotal += (product.prev_month_count * product.prev_month_price)
                    }

                    if(product.count && product.price) {
                        lastMonthTotal += product.count * product.price
                    }

                    if(product.inCount && product.inPrice) {
                        inTotal += product.inCount * product.inPrice
                        inTotalCount += product.inCount
                    }

                    if(product.outCount && product.outPrice) {
                        outTotal += product.outCount * product.outPrice
                    }
                })

                this.prevMonthTotal = prevMonthTotal
                this.lastMonthTotal = lastMonthTotal
                this.inTotal = inTotal
                this.inTotalCount = inTotalCount
                this.outTotal = outTotal

                let totalExpenses = (this.salaryTotal + this.communalExpensesTotal + this.otherExpensesTotal)
                if(totalExpenses) {
                    this.variableCost = totalExpenses / this.inTotalCount
                }

                this.loading = false

            } else {
                this.swr()
            }
        },
        async showFinishedProductDetailsModal(row, index){

            const productReportData = {
                year:           this.currentYear,
                month:          this.currentMonth,
                product_id:     row.id,
                department_id:  this.fpStore.department_id,
            }
            const res = await this.callApi('post', '/app/fp_finish_product_rawmaterials', productReportData)
            this.fpStore.finishedProductRawMaterials = res.data
            this.fpStore.departmentFinishProduct     = row

            this.fpStore.fpDetailsShowModal = true

        }
    },

    async created(){
        document.title = "Тайёр махсулот | Хисоботлар"

        this.currentYear    = this.date.getFullYear().toString()
        this.currentMonth   = this.date.toLocaleDateString("en-GB", {month: "2-digit"})

        const yearMonthPostData = {
            year:           this.currentYear,
            month:          this.currentMonth,
        }

        //Ойлик маош
        const salaryTotal = await this.callApi('post', '/app/get_monthly_salary_total', yearMonthPostData)
        this.salaryTotal = salaryTotal.data

        //Коммунал тўловлар
        const communalExpensesTotal = await this.callApi('post', '/app/get_monthly_communal_expenses_total', yearMonthPostData)
        this.communalExpensesTotal = communalExpensesTotal.data

        //Бошқа харажатлар
        const otherExpensesTotal = await this.callApi('post', '/app/get_monthly_other_expenses_total', yearMonthPostData)
        this.otherExpensesTotal = otherExpensesTotal.data

        //Тайёр махсулотлар
        let postData = {
            year:           this.currentYear,
            month:          this.currentMonth,
            department_id:  this.fpStore.department_id,
            isRawMaterial:  0,
            product_department_id:  6,
        }

        const res = await this.callApi('post', '/app/department_finished_products_report', postData)

        if(res.status == 200) {
            this.products = Object.values( res.data )

            let prevMonthTotal = 0;
            let lastMonthTotal = 0;
            let inTotal = 0;
            let inTotalCount = 0;
            let outTotal = 0;

            this.products.forEach(function (product, index) {

                if(product.prev_month_count && product.prev_month_price) {
                    prevMonthTotal += (product.prev_month_count * product.prev_month_price)
                }

                if(product.count && product.price) {
                    lastMonthTotal += product.count * product.price
                }

                if(product.inCount && product.inPrice) {
                    inTotal += product.inCount * product.inPrice
                    inTotalCount += product.inCount
                }

                if(product.outCount && product.outPrice) {
                    outTotal += product.outCount * product.outPrice
                }
            })

            this.prevMonthTotal = prevMonthTotal
            this.lastMonthTotal = lastMonthTotal
            this.inTotal = inTotal
            this.inTotalCount = inTotalCount
            this.outTotal = outTotal

            let totalExpenses = (this.salaryTotal + this.communalExpensesTotal + this.otherExpensesTotal)
            if(totalExpenses) {
                this.variableCost = totalExpenses / this.inTotalCount
            }

            this.loading = false

        } else {
            this.swr()
        }

    }
}
</script>
