<template>
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">

        <Table size="small" :columns="columns" :data="reportsStore.tableData" :loading="reportsStore.loading">
            <template #price="{row}">
                {{formatPrice(row.price)}}
            </template>
            <template #total="{row}">
                {{formatPrice(row.price * row.count)}}
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
                    width: "180",
                    sortable: true
                }, {
                    title: "Коди",
                    key: 'code',
                    sortable: true
                }, {
                    title: "Бирлиги",
                    key: 'measurement'
                }, {
                    title: "Миқдор",
                    key: 'count'
                }, {
                    title: "Нарх (сўм)",
                    slot: 'price'
                }, {
                    title: "Жами (сўм)",
                    slot: 'total'
                },
            ],
        }
    },
    methods: {},
    async created(){}
}
</script>
