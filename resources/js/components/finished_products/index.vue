<template>
    <FinishedProductsHeader />

    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">

        <Table size="small" :columns="columns" :data="finishedProductsStore.fpStockFinishProducts" :loading="loading">
            <template #price="{row}">
                {{row.price.toLocaleString()}}
            </template>
        </Table>
    </div>

</template>

<script>
import FinishedProductsHeader from './parts/header.vue'
import {useFinishedProductsStore} from "../../stores/FinishedProductsStore";
export default {
    name: "Finished Products",
    setup(){
        const finishedProductsStore = useFinishedProductsStore()

        return { finishedProductsStore }
    },
    components: {
        FinishedProductsHeader: FinishedProductsHeader
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
        document.title = "Тайёр Махсулот Омбори"
        const [fpStockFinishProducts] = await Promise.all([
            this.callApi('get', '/app/get_fp_products_stock/'+ this.finishedProductsStore.department_id),
        ])

        this.finishedProductsStore.fpStockFinishProducts  = fpStockFinishProducts.data //Bulimning tayyor maxsulotlari jami

        this.loading = false

    },
}
</script>
