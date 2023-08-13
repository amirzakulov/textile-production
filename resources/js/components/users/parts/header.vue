<template>

    <ul class="nav nav-tabs ml-4">
        <li class="nav-item">
            <router-link :to="{ name: 'users' }" class="nav-link">Foydalanuvchilar</router-link>
        </li>
        <!-- <li class="nav-item">
           <router-link :to="{ name: 'roles'}" class="nav-link">Foydalanuvchilar turi</router-link>
        </li>
        <li class="nav-item">
           <router-link :to="{ name: 'assignRole'}" class="nav-link">Huquqlar</router-link>
        </li> -->
    </ul>
</template>

<script>
export default {
    name: "userHeader",
    data() {
        return {
            activeCategory: 0,
        }
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

