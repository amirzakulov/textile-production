<template>
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">

        <Table size="small" :columns="columns" :data="reportsStore.tableData" :loading="reportsStore.loading">
            <template #amount="{row}">
                {{ formatPrice(row.amount) }}
            </template>
        </Table>

    </div>
</template>

<script>
import {useReportsStore} from "../../stores/ReportsStore";

export default {
    name: "index",
    setup(){
        const reportsStore = useReportsStore()
        return { reportsStore }
    },
    data() {
        return {
            columns: [
                {
                    title: "Номи",
                    key: 'name',
                    sortable: true
                }, {
                    title: "Тўлов (сўм)",
                    slot: 'amount',
                }
            ],
        }
    },
    methods: {},
    async created(){}
}
</script>
