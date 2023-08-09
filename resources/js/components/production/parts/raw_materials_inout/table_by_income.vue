<template>
    <Table size="small" :columns="columns" :data="productionStore.inoutSets" :loading="loading" >
        <template #name="{row}">
            <Icon type="md-arrow-down" color="#0099ff" />
            {{row.name}}
        </template>
        <template #created_at="{row}">
            {{ new Date(row.created_at).toLocaleString() }}
        </template>
    </Table>
</template>

<script>

import {useProductionStore} from "../../../../stores/ProductionStore";

export default {
    name: "Production Income By Set",
    setup(){
        const productionStore = useProductionStore()
        return { productionStore }
    },
    data(){
        return {
            inout: 1,
            loading: true,
            columns: [
                {
                    title: "Партия №",
                    slot: 'name',
                    sortable: true
                },
                {
                    title: "Қабул қилди (ФИШ)",
                    key: 'fullName',
                    width: "180",
                },
                {
                    title: "Қаердан",
                    key: 'department_from'
                },
                {
                    title: "Қаерга",
                    key: 'department_to'
                },
                {
                    title: "Сана",
                    slot: 'created_at'
                },
            ],
        }
    },
    methods: {},

    async created(){
        const inoutSets = await this.callApi('get', '/app/get_production_sets/'+this.inout+'/'+this.productionStore.department_id)

        this.productionStore.inoutSets = inoutSets.data
        this.loading = false

    }
}
</script>
