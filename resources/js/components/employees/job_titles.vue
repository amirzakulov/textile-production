<template>
    <EmployeesHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">

        <Table size="small" :columns="columns" :data="jobTitlesStore.jobTitles" :loading="loading"></Table>


    </div>

</template>

<script>
import EmployeesHeader from './parts/header.vue'
import {useJobTitlesStore} from "../../stores/JobTitlesStore";
export default {
    name: "employees",
    components: {
        EmployeesHeader: EmployeesHeader
    },
    setup() {
        const jobTitlesStore = useJobTitlesStore()

        return { jobTitlesStore }
    },
    data() {
        return {
            loading: true,
            columns: [
                {
                    title: "Номи",
                    key: 'name',
                    width: "300",
                    sortable: true
                },

            ],
        }
    },
    methods: {},
    async created(){
        const jobType = 2 //production
        const jobTitles = await this.callApi('get', '/app/get_jobtitles/')
        this.jobTitlesStore.jobTitles = jobTitles.data

        this.loading = false
    }
}
</script>



