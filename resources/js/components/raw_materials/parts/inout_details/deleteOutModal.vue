<template>
    <Modal v-model="rmStore.rmDeleteOutModal" width="360"
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
<!--            <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteRmProductOut">ХА</Button>-->
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

            const res = await this.callApi("post", "/app/delete_rawmaterial_out", this.rmStore.rawMaterial)

            if(res.status === 200) {
                if(res.data === false){
                    this.err("Ўчириш мумкин эмас!")
                } else {
                    this.s('Масулот мувоффақиятли ўчирилди!')
                    if(this.rmStore.rmSetProducts.length === 0) {
                        this.$router.push('/raw-materials/inout')
                    }

                    this.rmStore.rmSetProducts       = res.data;
                    this.rmStore.rmSetProductsId     = []
                    this.rmStore.rmSetRawMaterialsId = []
                    this.rmStore.rmSetProducts.forEach((product, index) => {
                        this.rmStore.rmSetProductsId.push(product.product_id)
                        this.rmStore.rmSetRawMaterialsId.push(product.id)
                    })
                }

                this.rmStore.rawMaterial        = {}
                this.isDeleting                 = false
                this.rmStore.rmDeleteOutModal   = false
            } else {
                this.swr();
            }

            this.isDeleting                 = false

        },
        cancelDelete(){
            this.rmStore.rmDeleteOutModal = false
        }
    },
}
</script>

