<template>
    <StockHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
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

            <Col span="2" class="text-right pt-2">
                <span>қидирув:</span>
            </Col>
            <Col span="8">
                <Input v-model="keywordName" clearable placeholder="Махсулот номи..." />
            </Col>
            <Col span="4">
                <Input v-model="keywordCode" clearable placeholder="Махсулот коди..." />
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
import {useProductionStore} from "../../stores/ProductionStore";

export default {
    name: "reports",
    components: {
        StockHeader:StockHeader
    },
    setup() {
        const productionStore = useProductionStore()

        return { productionStore }
    },
    computed: {
        filteredProducts(){
            let productsTmp = this.products

            if (this.keywordName != '' && this.keywordName) {
                productsTmp = productsTmp.filter((product) => {
                    return product.name.toLowerCase().includes(this.keywordName.toLowerCase())
                })
            }

            if (this.keywordCode != '' && this.keywordCode) {
                productsTmp = productsTmp.filter((product) => {
                    return product.code.toLowerCase().includes(this.keywordCode.toLowerCase())
                })
            }

            return productsTmp

        },
    },
    data(){
        return {
            keywordName: '',
            keywordCode: '',
            date: new Date(),
            currentYear: '',
            currentMonth: '',

            products: [],
            prevMonthTotal: 0,
            inTotal: 0,
            outTotal: 0,
            lastMonthTotal:0,

            loading: true,
            columns: [
                {
                    title: "Номи",
                    key: 'name',
                    width: "180",
                    sortable: true,
                    sortType: 'asc',
                    fixed: "left",
                },
                {
                    title: "Код",
                    key: 'code',
                    width: "100",
                    sortable: true,
                    fixed: "left",
                },
                {
                    title: "Бирлик",
                    key: 'measurement',
                    width: "100",
                    fixed: "left",
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
            let postData = {
                year:           this.currentYear,
                month:          this.currentMonth,
                department_id:  this.productionStore.department_id,
                isRawMaterial:  1,
            }

            const res = await this.callApi("post", "/app/department_rawmaterial_report", postData)
            this.products = Object.values( res.data )
        }
    },

    async created(){
        document.title = "Ишлаб чиқариш | Хисоботлар"
        this.productionStore.department_id = this.$route.params.department_id

        this.currentYear    = this.date.getFullYear().toString()
        this.currentMonth   = this.date.toLocaleDateString("en-GB", {month: "2-digit"})

        let postData = {
            year:           this.currentYear,
            month:          this.currentMonth,
            department_id:  this.productionStore.department_id,
            isRawMaterial:  1,
        }

        // const res = await this.callApi('post', '/app/get_rawmaterial_stock_balance', postData)
        const res = await this.callApi('post', '/app/department_rawmaterial_report', postData)

        if(res.status == 200) {
            this.products = Object.values( res.data )

            let prevMonthTotal = 0;
            let lastMonthTotal = 0;
            let inTotal = 0;
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
                }

                if(product.outCount && product.outPrice) {
                    outTotal += product.outCount * product.outPrice
                }
            })


            this.prevMonthTotal = prevMonthTotal
            this.lastMonthTotal = lastMonthTotal
            this.inTotal = inTotal
            this.outTotal = outTotal

            this.loading = false

        } else {
            this.swr()
        }

    }
}
</script>
