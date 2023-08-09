<template>
    <Modal v-model="productionStore.productDeleteModal"
           width="360"
           @on-visible-change="productionStore.modalVisibility"
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
<!--            <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteDepartmentProduct">ХА</Button>-->
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
import {useProductionStore} from "../../../../stores/ProductionStore";

export default {
    name: "Delete Product Modal",
    setup() {
        const productionStore = useProductionStore()

        return { productionStore }
    },
    data(){
        return {
            isDeleting: false,
        }
    },
    methods: {
        async deleteItem() {

            const res = await this.callApi("post", "/app/delete_production_product", this.productionStore.departmentProduct)
            if(res.status === 200) {
                this.productionStore.departmentProducts.splice(this.productionStore.rowIndex,1)
                this.s('Мувоффақиятли ўчирилди!')
            } else {
                this.swr();
            }

            this.isDeleting                         = false
            this.productionStore.productDeleteModal = false
        },
        cancelDelete(){
            this.productionStore.productDeleteModal = false
        }
    },
}
</script>

