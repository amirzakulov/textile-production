<template>
    <ul class="nav nav-tabs ml-4">
        <li class="nav-item">
            <router-link :to="{ name: 'otherExpenses'}" class="nav-link"><Icon type="md-speedometer" size="16" color="#000000" /> Харажатлар</router-link>
        </li>
    </ul>
</template>

<script>
export default {
    name: "Other Expenses Header",
    data:{
        types: [],
        expense_type_id: 0,


    },
    methods: {

    },
    async created() {
        const types =  await this.callApi('get', '/app/get_expense_types')
        this.types  = types.data

    }
}
</script>

<style scoped>

</style>
