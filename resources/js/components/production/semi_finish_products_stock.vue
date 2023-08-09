<template>
    <ProductionHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <Table size="small" :columns="columns" :data="productionStore.departmentStockSemiFinishProducts" :loading="loading">
            <template #price="{row}">
                {{row.price.toLocaleString()}}
            </template>
        </Table>
    </div>

    <FinishProductInModal />
    <FinishProductOutModal />
</template>

<script>
import ProductionHeader from './parts/header.vue'
import { useProductionStore} from "../../stores/ProductionStore";

import FinishProductInModal from "./parts/finish_products_inout/inModal.vue"
import FinishProductOutModal from "./parts/finish_products_inout/outModal"

export default {
    name: "Production Raw Materials Stock",
    setup(){
        const productionStore = useProductionStore()

        return { productionStore }
    },
    components: {
        ProductionHeader: ProductionHeader,
        FinishProductInModal: FinishProductInModal,
        FinishProductOutModal: FinishProductOutModal,
    },
    data() {
        return {
            loading: true,
            columns: [
                {
                    title: "Махсулот Номи",
                    key: 'name',
                    width: "180",
                    sortable: true
                }, {
                    title: "Коди",
                    key: 'code',
                    sortable: true
                }, {
                    title: "Модел",
                    key: 'fashion_name'
                }, {
                    title: "Бирлиги",
                    key: 'measurement'
                }, {
                    title: "Миқдор",
                    key: 'count'
                }, {
                    title: "Нарх",
                    slot: 'price'
                },
            ],
        }
    },
    methods: {},
    async created(){

        this.productionStore.department_id = this.$route.params.department_id
        // const [departmentStockSemiFinishProducts, departmentRawMaterials, departmentProducts, departmentFinishProducts, departments] = await Promise.all([
        const [departmentStockSemiFinishProducts] = await Promise.all([
            this.callApi('get', '/app/get_production_semi_finish_products_stock/'+this.productionStore.department_id),
            // this.callApi('get', '/app/get_production_department_rawmaterials/'+this.productionStore.department_id),
            // this.callApi("get", "/app/get_production_department_products/"+this.productionStore.department_id),
            // this.callApi("get", "/app/get_production_department_finish_products/"+this.productionStore.department_id),
            // this.callApi('get', '/app/get_departments'),
        ])

        this.productionStore.departmentStockSemiFinishProducts  = departmentStockSemiFinishProducts.data //Bulimning tayyor maxsulotlari jami
        // this.productionStore.departmentRawMaterials     = departmentRawMaterials.data  //Bulimning xomashyolari
        // this.productionStore.departmentProducts         = departmentProducts.data //Bulim maxsulotlari
        // this.productionStore.departmentFinishProducts   = departmentFinishProducts.data
        // this.productionStore.departments                = departments.data

        this.loading = false
    },
}
</script>



