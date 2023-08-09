<template>
    <ul class="nav nav-tabs ml-4">
        <li class="nav-item">
            <router-link :to="{ name: 'fgFinishedProductsStock' }" class="nav-link"><Icon type="ios-reorder" size="16" color="#000000" /> Тайёр Махсулот Омбори</router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'fgFinishedProductsInout'}" class="nav-link" :class="{ 'router-link-exact-active': subIsActive('/finished_products/finished_products_inout') }">
                <Icon type="md-arrow-down" size="12" color="#000000" />
                <Icon type="md-arrow-up" size="12" color="#ff0000" />
                Тайёр махсулот Кирим-чиқим
            </router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'fgReports'}" class="nav-link" :class="{ 'router-link-active': subIsActive('/finished_products/reports') }"><Icon type="md-trending-up" size="16" color="#2d8cf0" /> Хисобот 1</router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'fgReports2'}" class="nav-link" :class="{ 'router-link-active': subIsActive('/finished_products/reports2') }"><Icon type="md-trending-up" size="16" color="#2d8cf0" /> Хисобот 2</router-link>
        </li>
    </ul>
</template>

<script>
    export default {
        name: "Finished Products Header",
        data() {
            return {}
        },
        methods: {
            subIsActive(input) {
                const paths = Array.isArray(input) ? input : [input]
                return paths.some(path => {
                    return this.$route.path.indexOf(path) === 0 // current path starts with this path string
                })
            }
        },

    }
</script>
