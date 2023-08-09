<template>
    <ul class="nav nav-tabs ml-4">
        <li class="nav-item">
            <router-link :to="{ name: 'fashions' }" class="nav-link" :class="{ 'router-link-exact-active': subIsActive('/fashions') }">Моделлар</router-link>
        </li>
    </ul>
</template>

<script>
export default {
    name: "Fashions header",
    data() {},
    methods: {
        subIsActive(input) {
            const paths = Array.isArray(input) ? input : [input]
            return paths.some(path => {
                return this.$route.path.indexOf(path) === 0 // current path starts with this path string
            })
        }
    },
    async created() {}
}
</script>

