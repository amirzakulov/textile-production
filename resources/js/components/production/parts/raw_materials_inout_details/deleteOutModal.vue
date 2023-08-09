<template>
    <Modal v-model="productionStore.rmDetailsDeleteOutModal"
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
<!--            <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteProductionRmProductOut">ХА</Button>-->
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
                const res = await this.callApi("post", "/app/delete_production_rawmaterial_out", this.productionStore.departmentRawMaterial)

                if(res.status === 200) {
                    if(res.data === false){
                        this.err("Ўчириш мумкин эмас!")
                    } else {
                        this.productionStore.inoutSetProducts        = res.data;

                        let departmentRawMaterials = await this.callApi('get', '/app/get_production_department_rawmaterials/'+this.productionStore.department_id)
                        this.productionStore.departmentRawMaterials = departmentRawMaterials.data

                        this.s('Масулот мувоффақиятли ўчирилди!')

                        if(this.productionStore.inoutSetProducts.length === 0) {
                            this.$router.push('/production/'+this.productionStore.department_id+'/raw_materials_inout')
                        }

                        this.productionStore.inoutSetProductsId = []
                        this.productionStore.inoutSetRawMaterialsId = []
                        this.productionStore.inoutSetProducts.forEach((product, index) => {
                            this.productionStore.inoutSetProductsId.push(product.product_id)
                            this.productionStore.inoutSetRawMaterialsId.push(product.id)
                        })
                    }

                    this.productionStore.rmDetailsDeleteOutModal = false
                    this.productionStore.departmentRawMaterial   = {}
                } else {
                    this.swr();
                }
            }

            this.isDeleting = false

        },
        cancelDelete(){
            this.productionStore.rmDetailsDeleteOutModal = false
        }
    },
}
</script>

