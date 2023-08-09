<template>
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">

        <Table size="small" :columns="columns" :data="reportsStore.tableData" :loading="reportsStore.loading">
            <template #payment="{row}">
                {{formatPrice(row.payment)}}
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
                    title: "Бирлиги",
                    key: 'measurement'
                }, {
                    title: "Миқдор",
                    key: 'active_amount'
                }, {
                    title: "Тўлов (сўм)",
                    slot: 'payment',
                }
            ],
        }
    },
    methods: {},
    async created(){}
}
</script>
