<template>
    <Modal
        v-model="fpStore.fpDetailsShowModal"
        title="Бир дона тайёр махсулот учун сарфланган хомашёлар"
        :mask-closable = "false"
        :closable = "false"
        width="700"
        @on-visible-change="fpStore.modalVisibility"
    >
        <Row :gutter="8" class="ivu-mb border-bottom bg-primary p-2 text-white">
            <Col span="12"><strong>Хомашё</strong></Col>
            <Col span="3"><strong>Бир.</strong></Col>
            <Col span="3"><strong>Чиқим</strong></Col>
            <Col span="3"><strong>Нарх (сўм)</strong></Col>
            <Col span="3">Қиймати</Col>
        </Row>
        <Row v-for="(raw_material, index) in fpStore.finishedProductRawMaterials" :key="index" :gutter="8" class="ivu-mb border-bottom">
            <Col span="12">{{raw_material.name}}</Col>
            <Col span="3">{{raw_material.measurement}}</Col>
            <Col span="3">{{formatPrice((-1) * raw_material.count / fpStore.departmentFinishProduct.inCount, 2)}}</Col>
            <Col span="3">{{formatPrice(raw_material.avg_price, 2)}}</Col>
            <Col span="3">{{formatPrice((-1) * raw_material.count / fpStore.departmentFinishProduct.inCount * raw_material.avg_price, 2)}}</Col>
        </Row>
        <Row :gutter="8" class="ivu-mb">
            <Col span="21"></Col>
            <Col span="3"><strong class="text-black">{{formatPrice(calculateCost, 2)}}</strong></Col>
        </Row>
    </Modal>
</template>

<script>
    import {useFinishedProductsStore} from "../../../../stores/FinishedProductsStore";

    export default {
        name: "Finished Products Out Modal",
        setup(){
            const fpStore = useFinishedProductsStore()
            return { fpStore }
        },
        computed: {
            calculateCost(){
                let total = 0
                this.fpStore.finishedProductRawMaterials.forEach((product, index) => {
                    if (product.isRawMaterial){
                        total += product.count / this.fpStore.departmentFinishProduct.inCount * product.avg_price
                    }

                })

                return (-1) * total
            }
        },
        data(){},
        methods: {},
        async created(){}
    }
</script>

