<template>
    <ul class="nav nav-tabs ml-4">
        <li class="nav-item">
            <router-link :to="{ name: 'rawMaterials', params: {category_id: 1} }" class="nav-link" :class="{ 'router-link-exact-active': subIsActive('/raw-materials/'+this.$route.params.category_id) }"><Icon type="md-menu" size="16" color="#000000" /> Хомашё омбор</router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'rmInOut'}" class="nav-link" :class="{ 'router-link-exact-active': subIsActive('/raw-materials/inout') }">
                <Icon type="md-arrow-down" size="12" color="#000000" />
                <Icon type="md-arrow-up" size="12" color="#ff0000" />
                Кирим-чиқим</router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'rmSemiFinishProducts' }" class="nav-link"><Icon type="md-menu" size="12" color="#000000" /> ЯТ махсулот омбор</router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'rmSemiFinishProductsInOut' }" class="nav-link" :class="{ 'router-link-exact-active': subIsActive('/raw-materials/semi-finish-products-inout/details') }">
                <Icon type="md-arrow-down" size="12" color="#000000" />
                <Icon type="md-arrow-up" size="12" color="#ff0000" />
                ЯТ кирим-чиқим
            </router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'rmProducts' }" class="nav-link"><Icon type="md-shirt" size="16" color="#2d8cf0" /> Махсулотлар</router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'rmReports' }" class="nav-link"><Icon type="md-trending-up" size="16" color="#2d8cf0" /> Хисоботлар</router-link>
        </li>
    </ul>
</template>

<script>
export default {
    name: "Stock header",
    methods: {
        subIsActive(input) {
            const paths = Array.isArray(input) ? input : [input]
            return paths.some(path => {
                return this.$route.path.indexOf(path) === 0 // current path starts with this path string
            })
        },

    },
    created() {}
}
</script>

