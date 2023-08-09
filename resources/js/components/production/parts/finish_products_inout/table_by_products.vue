<template>
    <Table size="small" :columns="columns" :data="productionStore.departmentInOutRawMaterials" :loading="loading" ></Table>
</template>

<script>

import {useProductionStore} from "../../../../stores/ProductionStore";

export default {
    name: "Production Income Products",
    setup(){
        const productionStore = useProductionStore()
        return { productionStore }
    },
    data(){
        return {

            loading: true,
            columns: [
                {
                    title: "Номи",
                    key: 'name',
                },
                {
                    title: "Код",
                    key: 'code',
                },
                {
                    title: "Сони",
                    key: 'count'
                },
            ],
        }
    },
    methods: {},

    async created(){
        let postData = {
            year:           this.productionStore.currentYear,
            month:          this.productionStore.currentMonth,
            department_id:  this.productionStore.department_id
        }
        const departmentInOutRawMaterials = await this.callApi('post', '/app/get_production_income_products', postData)

        this.productionStore.departmentInOutRawMaterials = departmentInOutRawMaterials.data

        this.loading = false

    }
}
</script>
