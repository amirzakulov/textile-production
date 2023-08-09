<template>
    <Modal v-model="fpStore.fpDetailsDeleteOutModal"
           width="360"
           @on-visible-change="fpStore.modalVisibility"
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
import {useFinishedProductsStore} from "../../../../stores/FinishedProductsStore";

export default {
    name: "Delete Product Modal",
    setup(){
        const fpStore = useFinishedProductsStore()
        return { fpStore }
    },
    data(){
        return {
            isDeleting: false,
        }
    },
    methods: {
        async deleteItem() {
            this.isDeleting = true

            if(this.fpStore.inoutSetProducts.length === 1){
                this.w('Партиядаги ягона масулотни ўчираолмайсиз!')
            } else {
                const res = await this.callApi("post", "/app/delete_fp_finish_product_out", this.fpStore.departmentFinishProduct)

                if(res.status === 200) {
                    if(res.data === false){
                        this.err("Ўчириш мумкин эмас!")
                    } else {
                        this.fpStore.inoutSetProducts = res.data;

                        let fpFinishProducts          = this.callApi('get', '/app/get_fp_finish_products/'+this.fpStore.department_id)
                        this.fpStore.fpFinishProducts = fpFinishProducts.data

                        this.s('Масулот мувоффақиятли ўчирилди!')

                        if(this.fpStore.inoutSetProducts.length === 0) {
                            this.$router.push('/finished_products/finished_products_inout/')
                        }

                        this.fpStore.departmentFinishProduct    = {}
                        this.fpStore.inoutSetProductsId         = []
                        this.fpStore.inoutSetRawMaterialsId     = []
                        this.fpStore.inoutSetProducts.forEach((product, index) => {
                            this.fpStore.inoutSetProductsId.push(product.product_id)
                            this.fpStore.inoutSetRawMaterialsId.push(product.id)
                        })
                    }

                    this.fpStore.fpDetailsDeleteOutModal = false

                } else {
                    this.swr();
                }
            }

            this.isDeleting = false

        },
        cancelDelete(){
            this.fpStore.fpDetailsDeleteOutModal = false
        }
    },
}
</script>

