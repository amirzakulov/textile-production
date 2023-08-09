<template>
    <ProductionHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <Table size="small" :columns="columns" :data="rmStore.rmSemiFinishedStockProducts" :loading="loading">
            <template #price="{row}">
                {{row.price.toLocaleString()}}
            </template>
        </Table>
    </div>
</template>

<script>
import ProductionHeader from './parts/header.vue'
import { useRawMaterialsStore} from "../../stores/RawMaterialsStore";

export default {
    name: "Production Raw Materials Inout",
    setup(){
        const rmStore = useRawMaterialsStore()

        return { rmStore }
    },
    components: {
        ProductionHeader: ProductionHeader,
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

        const [rmSemiFinishedStockProducts] = await Promise.all([
            this.callApi('get', '/app/get_rm_semi_finish_products_stock/'+this.rmStore.department_id),
        ])

        this.rmStore.rmSemiFinishedStockProducts  = rmSemiFinishedStockProducts.data //Bulimning yarim tayyor maxsulotlari jami

        this.loading = false
    },
}
</script>



