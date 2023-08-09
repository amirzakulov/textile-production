<template>
    <Modal v-model="fashionsStore.deleteModal" width="360">
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
<!--            <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteFashion">ХА</Button>-->
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
import {useFashionsStore} from "../../../stores/FashionsStore";

export default {
    name: "Add Fashion Modal",
    setup() {
        const fashionsStore = useFashionsStore()

        return { fashionsStore }
    },
    data(){
        return {
            isDeleting: false,
        }
    },
    methods: {
        async deleteItem() {

            const res = await this.callApi("post", "/app/delete_fashion", this.fashionsStore.fashion)
            if(res.status === 200) {
                this.fashionsStore.fashions.splice(this.fashionsStore.rowIndex,1)
                this.s('Андоза мувоффақиятли ўчирилди!')
            } else {
                this.swr();
            }

            this.isDeleting = false
            this.fashionsStore.deleteModal = false
        },
        cancelDelete(){
            this.fashionsStore.deleteModal = false
        }
    },
}
</script>

