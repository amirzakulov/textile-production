<template>
    <Modal v-model="productionStore.fpSetDeleteModal" width="360">
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
import {useProductionStore} from "../../../../stores/ProductionStore";

export default {
    name: "Delete Rm Set Modal",
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

            let url = "/app/delete_production_finish_product_in_set";
            if(this.productionStore.departmentInoutSet.inout === 2) {
                url = "/app/delete_production_finish_product_out_set";
            }

            const res = await this.callApi("post", url, this.productionStore.departmentInoutSet)

            if(res.status === 200) {
                if(res.data === false){
                    this.err("Ўчириш мумкин эмас!")
                } else {
                    this.s('Масулот мувоффақиятли ўчирилди!')

                    this.productionStore.inoutSets.splice(this.productionStore.rowIndex, 1);
                    const departmentFinishProducts                = await this.callApi("get", "/app/get_production_department_finish_products/"+this.productionStore.department_id)
                    this.productionStore.departmentFinishProducts = departmentFinishProducts.data;

                    const departmentRawMaterials                  = await this.callApi('get', '/app/get_production_department_rawmaterials/'+this.productionStore.department_id)
                    this.productionStore.departmentRawMaterials   = departmentRawMaterials.data
                }

            } else {
                this.swr();
            }

            this.isDeleting                         = false
            this.productionStore.fpSetDeleteModal   = false
            this.productionStore.departmentInoutSet = {}
        },
        cancelDelete(){
            this.productionStore.fpSetDeleteModal   = false
        }
    },
}
</script>

