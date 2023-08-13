<template>
    <Modal v-model="fashionsStore.deleteFashionDetailsModal" width="360"
           @on-visible-change="fashionsStore.modalVisibility"
    >
        <template #header>
            <p style="color:#f60;text-align:center">
                <Icon type="ios-close-circle" size="20" />
                <span>Ўчиришни тасдиқлаш?</span>
            </p>
        </template>
        <div class="text-center">
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
import {useFashionsStore} from "../../../../stores/FashionsStore";

export default {
    name: "Delete Product Modal",
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
            this.isDeleting = true
            const res = await this.callApi("post", "/app/delete_fashion_detail", this.fashionsStore.fashionDetail)
            if(res.status === 200) {
                this.fashionsStore.fashionDetails.splice(this.fashionsStore.rowIndex,1)
                this.s('Мувоффақиятли ўчирилди!')
            } else {
                this.swr();
            }

            this.isDeleting                              = false
            this.fashionsStore.deleteFashionDetailsModal = false
        },
        cancelDelete(){
            this.fashionsStore.deleteFashionDetailsModal = false
        }
    },
}
</script>

