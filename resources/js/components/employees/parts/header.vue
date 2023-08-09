<template>
    <ul class="nav nav-tabs ml-4">
        <li class="nav-item">
            <router-link :to="{ name: 'employees' }" class="nav-link">Ходимлар</router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'payroll' }" class="nav-link">Маош</router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'fired' }" class="nav-link">Ишдан Бўшаганлар</router-link>
        </li>
        <li class="nav-item">
            <router-link :to="{ name: 'jobTitles' }" class="nav-link">Лавозимлар</router-link>
        </li>
    </ul>
</template>

<script>
export default {
    name: "Employee Header"
}
</script>

