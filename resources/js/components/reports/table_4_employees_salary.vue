<template>
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">

        <div><strong>Маош:</strong> {{reportsStore.tableData}}</div>

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

        }
    },
    methods: {},
    async created(){}
}
</script>
