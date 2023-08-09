<template>
    <ul class="nav nav-tabs ml-4">
        <li class="nav-item">
            <router-link :to="{ name: 'reports'}" class="nav-link" :class="{ 'router-link-active': subIsActive('/reports') }"><Icon type="md-trending-up" size="16" color="#2d8cf0" /> Хисоботлар</router-link>
        </li>
    </ul>
</template>

<script>
    export default {
        name: "Reports Header",
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
