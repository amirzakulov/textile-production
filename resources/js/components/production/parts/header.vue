<template>

    <ul class="nav nav-tabs ml-4">
        <li class="nav-item">
            <router-link :to="{ name: 'productionRawMaterialsStock', params: {department_id: productionStore.department_id}}" class="nav-link"><Icon type="ios-reorder" size="16" color="#000000" /> ХМ омбори</router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'productionRawMaterialsInout', params: {department_id: productionStore.department_id}}" class="nav-link" :class="{ 'router-link-exact-active': subIsActive('/production/'+$route.params.department_id+'/raw_materials_inout') }">
                <Icon type="md-arrow-down" size="12" color="#000000" />
                <Icon type="md-arrow-up" size="12" color="#ff0000" />
                ХМ Кирим-чиқим
                 </router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'productionFinishProductsStock', params: {department_id: productionStore.department_id}}" class="nav-link"><Icon type="md-menu" size="16" color="#000000" /> ТМ омбори</router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'productionFinishProductsInout', params: {department_id: productionStore.department_id}}" class="nav-link" :class="{ 'router-link-exact-active': subIsActive('/production/'+$route.params.department_id+'/finish_products_inout') }">
                <Icon type="md-arrow-down" size="12" color="#000000" />
                <Icon type="md-arrow-up" size="12" color="#ff0000" />
                ТМ Кирим-чиқим
            </router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'productionSemiFinishProductsStock', params: {department_id: productionStore.department_id}}" class="nav-link"><Icon type="ios-reorder" size="16" color="#000000" /> ЯТ махсулот омбори</router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'productionSemiFinishProductsInout', params: {department_id: productionStore.department_id} }" class="nav-link" :class="{ 'router-link-exact-active': subIsActive('/production/'+$route.params.department_id+'/semi_finish_products_inout') }"><Icon type="md-arrow-down" size="12" color="#000000" />ЯТ кирим</router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'productionDepartmentProducts', params: {department_id: productionStore.department_id}}" class="nav-link" :class="{ 'router-link-exact-active': subIsActive('/production/'+$route.params.department_id+'/department_products') }"><Icon type="md-shirt" size="16" color="#2d8cf0" /> Махсулотлар</router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'productionReports', params: {department_id: productionStore.department_id}}" class="nav-link" :class="{ 'router-link-exact-active': subIsActive('/production/'+$route.params.department_id+'/reports') }"><Icon type="md-trending-up" size="16" color="#2d8cf0" /> Хисоботлар</router-link>
        </li>
    </ul>
</template>

<script>
import {useProductionStore} from "../../../stores/ProductionStore";

export default {
    name: "production_header",
    setup(){
        const productionStore = useProductionStore()

        return { productionStore }
    },
    data:{
        // department_id: '',
        section_id: '',
    },
    methods: {
        subIsActive(input) {
            const paths = Array.isArray(input) ? input : [input]
            return paths.some(path => {
                return this.$route.path.indexOf(path) === 0 // current path starts with this path string
            })
        }
    },
    created() {
        this.productionStore.department_id  = this.$route.params.department_id
        this.section_id                     = this.$route.params.section_id
    }
}
</script>
