<template>
    <ul class="nav nav-tabs ml-4">
        <li class="nav-item">
            <router-link :to="{ name: 'communal'}" class="nav-link"><Icon type="md-speedometer" size="16" color="#000000" /> Хисобот</router-link>
        </li>
        <template v-for="(ctype, i) in types" :key="i">
            <li class="nav-item">
                <router-link :to="{ name: 'communalType', params: {communal_type_id: ctype.id}}" class="nav-link"><Icon :type="ctype.icon" size="16" color="#000000" /> {{ctype.name}}</router-link>
            </li>
        </template>
    </ul>
</template>

<script>
export default {
    name: "Communal Header",
    data:{
        types: [],
        communal_type_id: 0,


    },
    methods: {

    },
    async created() {
        const types =  await this.callApi('get', '/app/get_communal_types')
        this.types  = types.data

    }
}
</script>

<style scoped>

</style>
