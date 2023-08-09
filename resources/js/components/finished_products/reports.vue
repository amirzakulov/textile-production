<template>
    <StockHeader />

    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">

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
        </Row>

        <Table size="small" :columns="columns" :data="filteredProducts" :loading="loading" border height="500">
            <template #prevMonthTotal="{row}">
                <strong>{{row.prev_month_count > 0 ? formatPrice(row.prev_month_count * row.prev_month_price).toLocaleString() : ''}}</strong>
            </template>
            <template #inPriceTotal="{row}">
                <strong>{{row.inCount > 0 ? formatPrice(row.inCount * row.inPrice).toLocaleString() : ''}}</strong>
            </template>
            <template #outPriceTotal="{row}">
                <strong>{{row.outCount != 0 ? formatPrice(row.outCount * row.outPrice).toLocaleString() : ''}}</strong>
            </template>
            <template #thisMonthTotal="{row}">
                <strong>{{row.count > 0 ? formatPrice(row.count * row.price).toLocaleString() : ''}}</strong>
            </template>
        </Table>
    </div>

</template>

<script>
import StockHeader from './parts/header.vue'
import {useFinishedProductsStore} from "../../stores/FinishedProductsStore";

export default {
    name: "reports",
    components: {
        StockHeader:StockHeader
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

            // if (this.keywordCode != '' && this.keywordCode) {
            //     productsTmp = productsTmp.filter((product) => {
            //         return product.code.toLowerCase().includes(this.keywordCode.toLowerCase())
            //     })
            // }

            return productsTmp

        },
    },
    data(){
        return {
            keywordName: '',
            // keywordCode: '',
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

            additionalPrice: 0,

            loading: true,
            columns: [
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
                    title: "Ой боши қолдиқ",
                    align: 'center',
                    children: [
                        {
                            title: "Миқдор",
                            key: 'prev_month_count',
                            align: 'center',
                        },
                        {
                            title: "Нарх",
                            key: 'prev_month_price',
                            align: 'center',
                        },
                        {
                            title: "Жами",
                            slot: 'prevMonthTotal',
                            align: 'center',
                        },
                    ]
                },
                {
                    title: "Кирим",
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
                            title: "Жами",
                            key: 'inPrice',
                            slot: 'inPriceTotal',
                            align: 'center',
                        },
                    ]
                },
                {
                    title: "Чиқим",
                    align: 'center',
                    children: [
                        {
                            title: "Миқдор",
                            key: 'outCount',
                            align: 'center',
                        },
                        {
                            title: "Нарх",
                            key: 'outPrice',
                            align: 'center',
                        },
                        {
                            title: "Жами",
                            slot: 'outPriceTotal',
                            align: 'center',
                        },
                    ]
                },
                {
                    title: "Ой охири қолдиқ",
                    align: 'center',
                    children: [
                        {
                            title: "Миқдор",
                            key: 'count',
                            align: 'center',
                        },
                        {
                            title: "Нарх",
                            key: 'price',
                            align: 'center',
                        },
                        {
                            title: "Жами",
                            slot: 'thisMonthTotal',
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
            console.log(salaryTotal.data)

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
            this.products = Object.values( res.data )
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
                this.additionalPrice = totalExpenses / this.inTotalCount
            }

            this.loading = false

        } else {
            this.swr()
        }

    }
}
</script>
