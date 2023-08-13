<template>
    <Modal v-model="usersStore.deleteModal" width="360">
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
import {useUsersStore} from "../../../stores/UsersStore";

export default {
    name: "Add Fashion Modal",
    setup() {
        const usersStore = useUsersStore()

        return { usersStore }
    },
    data(){
        return {
            isDeleting: false,
        }
    },
    methods: {
        async deleteItem() {
            this.isDeleting = true
            const res = await this.callApi("post", "/app/delete_user", this.usersStore.user)
            if(res.status === 200) {
                this.usersStore.users.splice(this.usersStore.rowIndex,1)
                this.usersStore.user = {}
                this.s('Мувоффақиятли ўчирилди!')
            } else {
                this.swr();
            }

            this.isDeleting = false
            this.usersStore.deleteModal = false
        },
        cancelDelete(){
            this.usersStore.deleteModal = false
        }
    },
}
</script>

