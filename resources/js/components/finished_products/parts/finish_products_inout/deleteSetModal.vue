<template>
    <Modal v-model="fpStore.fpFinishedProductDeleteSetModal" width="360">
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
    name: "Delete Fp Set Modal",
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
            const res = await this.callApi("post", "/app/delete_fp_finish_products_out_set", this.fpStore.fpSet)
            if(res.status === 200) {
                if(res.data === false){
                    this.err("Ўчириш мумкин эмас!")
                } else {
                    this.s('Масулот мувоффақиятли ўчирилди!')

                    this.fpStore.inoutSets.splice(this.fpStore.rowIndex, 1);
                    this.fpStore.fpSet = {}
                }
            } else {
                this.swr();
            }

            this.isDeleting                              = false
            this.fpStore.fpFinishedProductDeleteSetModal = false
        },
        cancelDelete(){
            this.fpStore.fpFinishedProductDeleteSetModal = false
        }
    },
}
</script>

