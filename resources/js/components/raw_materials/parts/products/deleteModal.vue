<template>
    <Modal v-model="rmStore.productDeleteModal" width="360"
           @on-visible-change="rmStore.modalVisibility"
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
<!--            <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteRmProduct">ХА</Button>-->
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
import {useRawMaterialsStore} from "../../../../stores/RawMaterialsStore";

export default {
    name: "Delete Product Modal",
    setup() {
        const rmStore = useRawMaterialsStore()

        return { rmStore }
    },
    data(){
        return {
            isDeleting: false,
        }
    },
    methods: {
        async deleteItem() {
            this.isDeleting = true
            const res = await this.callApi("post", "/app/delete_product", this.rmStore.product)
            if(res.status === 200) {
                this.rmStore.filteredProducts.splice(this.rmStore.rowIndex,1)
                this.s('Масулот мувоффақиятли ўчирилди!')
            } else {
                this.swr();
            }

            this.isDeleting                 = false
            this.rmStore.productDeleteModal = false
        },
        cancelDelete(){
            this.rmStore.productDeleteModal = false
        }
    },
}
</script>

