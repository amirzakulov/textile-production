<template>
    <Modal
        v-model="employeesStore.deleteModal"
        width="360"
        @on-visible-change="employeesStore.modalVisibility"
    >
        <template #header>
            <p style="color:#f60;text-align:center">
                <Icon type="ios-close-circle" size="20" />
                <span>Ўчиришни тасдиқлаш?</span>
            </p>
        </template>
        <div style="text-align:center">
            <p>Хақиқатдан хам ўчирасизми?</p>
        </div>
        <template #footer>
<!--            <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteEmployee">ХА</Button>-->
            <Row :gutter="8">
                <Col span="12">
                    <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteItem">Хa</Button>
                </Col>
                <Col span="12">
                    <Button size="large" long @click="cancelDelete">ЙЎҚ</Button>
                </Col>
            </Row>
        </template>
    </Modal>

</template>

<script>
    import {useEmployeesStore} from "../../../stores/EmployeesStore";

    export default {
        name: "addModal",
        setup() {
            const employeesStore = useEmployeesStore()

            return { employeesStore }
        },
        data(){
            return {
                isDeleting: false,
            }
        },
        methods: {
            async deleteItem() {
                this.isDeleting = true
                const res = await this.callApi("post", "/app/delete_employee", this.employeesStore.employee)
                if(res.status === 200) {
                    this.employeesStore.employees.splice(this.employeesStore.rowIndex, 1)
                    this.s('Ходим мувоффақиятли ўчирилди!')
                } else {
                    this.swr();
                }

                this.isDeleting = false
                this.employeesStore.deleteModal = false
            },

            cancelDelete(){
                this.employeesStore.deleteModal = false
            }
        }
    }
</script>
