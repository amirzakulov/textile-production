<template>
    <Modal v-model="rmStore.rmSemiFinishProductDeleteSetModal" width="360">
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
import {useRawMaterialsStore} from "../../../../stores/RawMaterialsStore";

export default {
    name: "Delete Rm Set Modal",
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

            const res = await this.callApi("post", "/app/delete_rm_semi_finish_products_out_set", this.rmStore.rmSet)

            if(res.status === 200) {
                if(res.data === false){
                    this.err("Ўчириш мумкин эмас!")
                } else {
                    this.s('Масулот мувоффақиятли ўчирилди!')

                    this.rmStore.rmSets.splice(this.rmStore.rowIndex, 1);
                    const rmSemiFinishProducts        = await this.callApi('get', '/app/get_rm_semi_finish_products/'+this.rmStore.department_id)
                    this.rmStore.rmSemiFinishProducts = rmSemiFinishProducts.data
                }
            } else {
                this.swr();
            }

            this.isDeleting                                 = false
            this.rmStore.rmSemiFinishProductDeleteSetModal  = false
            this.rmStore.rmSet                              = {}
        },
        cancelDelete(){
            this.rmStore.rmSemiFinishProductDeleteSetModal = false
        }
    },
}
</script>

