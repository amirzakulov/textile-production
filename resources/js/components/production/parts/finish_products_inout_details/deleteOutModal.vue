<template>
    <Modal v-model="productionStore.fpDetailsDeleteOutModal" width="360"
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
<!--            <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteProductionfpOut">ХА</Button>-->
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
            this.isDeleting = true

            if(this.productionStore.inoutSetProducts.length === 1){
                this.w('Партиядаги ягона масулотни ўчираолмайсиз!')
            } else {
                const res = await this.callApi("post", "/app/delete_production_finish_product_out", this.productionStore.departmentFinishProduct)

                if(res.status === 200) {
                    if(res.data === false){
                        this.err("Ўчириш мумкин эмас!")
                    } else {
                        this.productionStore.inoutSetProducts = res.data;

                        let departmentFinishProducts = await this.callApi('get', '/app/get_production_department_finish_products/'+this.productionStore.department_id)
                        this.productionStore.departmentFinishProducts = departmentFinishProducts.data

                        this.s('Масулот мувоффақиятли ўчирилди!')

                        this.productionStore.inoutSetProductsId = []
                        this.productionStore.inoutSetRawMaterialsId = []
                        this.productionStore.inoutSetProducts.forEach((product, index) => {
                            this.productionStore.inoutSetProductsId.push(product.product_id)
                            this.productionStore.inoutSetRawMaterialsId.push(product.id)
                        })
                    }

                    this.productionStore.fpDetailsDeleteOutModal = false
                    this.productionStore.departmentFinishProduct   = {}
                } else {
                    this.swr();
                }
            }

            this.isDeleting                 = false

        },
        cancelDelete(){
            this.productionStore.fpDetailsDeleteOutModal = false
        }
    },
}
</script>

