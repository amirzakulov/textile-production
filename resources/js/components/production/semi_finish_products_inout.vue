<template>
    <ProductionHeader />

    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <InoutByInoutSets />
    </div>

</template>

<script>
import ProductionHeader from './parts/header.vue'
import {useProductionStore} from "../../stores/ProductionStore";

import InoutByInoutSets from './parts/semi_finish_products_inout/table_by_inout_set.vue'
// import SemiFinishProductOutModal from "./parts/semi_finish_products_inout/outModal"

export default {
    name: "Production Raw Materials Inout",
    setup(){
        const productionStore = useProductionStore()

        return { productionStore }
    },
    components: {
        ProductionHeader: ProductionHeader,

        InoutByInoutSets: InoutByInoutSets,

        // SemiFinishProductOutModal:SemiFinishProductOutModal,

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
        this.productionStore.department_id  = this.$route.params.department_id
        let department_types = {types: [3, 2]}
        const [departments, departmentSemiFinishProducts] = await Promise.all([
            this.callApi('post', '/app/get_departments_types', department_types),
            this.callApi('get', '/app/get_production_semi_finish_products/'+this.productionStore.department_id),
        ])

        this.productionStore.departments                  = departments.data
        this.productionStore.departmentSemiFinishProducts = departmentSemiFinishProducts.data

        this.loading = false
    },
}
</script>



