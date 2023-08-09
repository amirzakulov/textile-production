<template>

    <!-- Delete tag modal -->
    <Modal v-model="communalTypesStore.deviceDeleteModal" width="360">
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
<!--            <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteDevice">ХА</Button>-->
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
import { useCommunalTypesStore } from '../../../../../stores/CommunalTypesStore'
export default {
    // props: ["communalDeleleData"],
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
            const res = await this.callApi("post", "/app/delete_device", this.communalTypesStore.device)
            if(res.status === 200) {
                this.communalTypesStore.devices.splice(this.communalTypesStore.rowIndex,1)
                this.s('Хисоблагич мувоффақиятли ўчирилди!')
            } else {
                this.swr();
            }

            this.isDeleting = false
            this.communalTypesStore.deviceDeleteModal = false
        },
        cancelDelete(){
            this.communalTypesStore.deviceDeleteModal = false
        }
    },
    created(){

    }
}
</script>

