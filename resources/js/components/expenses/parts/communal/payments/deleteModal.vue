<template>

    <!-- Delete tag modal -->
    <Modal v-model="communalTypesStore.paymentDeleteModal" width="360">
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
<!--            <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deletePayment">ХА</Button>-->
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

import {useCommunalTypesStore} from "../../../../../stores/CommunalTypesStore";

export default {
    setup() {
        const communalTypesStore = useCommunalTypesStore()

        return { communalTypesStore }
    },
    data(){
        return {
            isDeleting: false,
        }
    },
    methods: {
        async deleteItem(){
            this.isDeleting = true
            const res = await this.callApi("post", "/app/delete_communal_payment", this.communalTypesStore.payment)

            if(res.status === 200) {
                this.communalTypesStore.payments.splice(this.communalTypesStore.rowIndex,1)
                this.s('Хисоблагич мувоффақиятли ўчирилди!')

                this.communalTypesStore.payments.forEach((payment, index) => {
                    if(res.data.id == payment.id) {
                        payment.amount = res.data.amount
                    }
                })

            } else {
                this.swr();
            }

            this.isDeleting = false
            this.communalTypesStore.paymentDeleteModal = false
        },
        cancelDelete(){
            this.communalTypesStore.paymentDeleteModal = false
        }
    },
    created(){}
}
</script>

