<template>
    <ProductionHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <Table size="small" :columns="columns" :data="productionStore.rawMaterials" :loading="loading">
            <template #count="{row}">
                {{row.count == null ? 0:row.count}}
            </template>
            <template #price="{row}">
                {{row.price.toLocaleString()}}
            </template>
        </Table>
    </div>

</template>

<script>
import ProductionHeader from './parts/header.vue'
import { useProductionStore} from "../../stores/ProductionStore";

export default {
    name: "Production Raw Materials Stock",
    setup(){
        const productionStore = useProductionStore()

        return { productionStore }
    },
    components: {
        ProductionHeader: ProductionHeader
    },
    data() {
        return {
            loading: true,
            columns: [
                {
                    title: "Коди",
                    key: 'code',
                    width: "180",
                    sortable: true
                }, {
                    title: "Махсулот Номи",
                    key: 'name',
                    sortable: true
                }, {
                    title: "Ранги",
                    key: 'color'
                }, {
                    title: "Давлат",
                    key: 'country'
                }, {
                    title: "Бирлиги",
                    key: 'measurement'
                }, {
                    title: "Миқдор",
                    slot: 'count'
                }, {
                    title: "Нарх",
                    slot: 'price'
                },
            ],
        }
    },
    methods: {},
    async created(){
        document.title = "Ишлаб чиқариш | Хомашё Омбори"

        this.productionStore.department_id  = this.$route.params.department_id
        const products                      = await this.callApi('get', '/app/get_production_rm_stock/'+this.productionStore.department_id)
        this.productionStore.rawMaterials   = products.data

        this.loading = false
    },
}
</script>



