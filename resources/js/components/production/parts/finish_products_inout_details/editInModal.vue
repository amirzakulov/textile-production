<template>
    <Modal
        v-model="productionStore.fpDetailsEditInModal"
        :title="productionStore.departmentFinishProduct.name +' ('+productionStore.departmentFinishProduct.code+')'"
        :mask-closable = "false"
        :closable = "false"
        width="768"
        @on-visible-change="productionStore.modalVisibility"
    >

        <Form ref="formValidate" :label-width="180">
            <FormItem label="* Миқдор" class="ivu-mb">
                <Input v-model="productionStore.departmentFinishProduct.count" placeholder="Махсулот Миқдор"></Input>
                <Text :class="{ 'd-none': !raw_material_count_err.display }" type="danger">{{ raw_material_count_err.message }}</Text>
            </FormItem>
        </Form>
        <template #footer>
            <Button @click="productionStore.fpDetailsEditInModal = false">Беркитиш</Button>
            <Button type="primary" @click="editProductionfpIn" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
import {useProductionStore} from "../../../../stores/ProductionStore";

export default {
    name: "Edit RawMaterial Modal",
    setup() {
        const productionStore = useProductionStore()

        return { productionStore }
    },
    data(){
        return {
            isEditing: false,
            raw_material_count_err: {display: false, message:"Тўлдириш мажбурий"},
            is_invalid: false,
        }
    },
    methods: {
        async editProductionfpIn(){
            this.isEditing = true

            if(this.productionStore.departmentFinishProduct.count === "" || this.productionStore.departmentFinishProduct.count === 0) {
                this.is_invalid = true
                this.raw_material_count_err.display   = true
                this.raw_material_count_err.message   = "Тўлдириш мажбурий"

            } else {
                this.raw_material_count_err.display   = false
            }

            if(this.is_invalid) {
                this.is_invalid = false
                this.isEditing = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            const res = await this.callApi('post', '/app/edit_production_finish_product_in', this.productionStore.departmentFinishProduct)

            this.isEditing = false
            if(res.status === 200) {
                if(res.data === false){
                    this.err("Ўзгартириш мумкин эмас!")
                } else {
                    this.s('Мувоффақиятли сақланди!')
                    this.productionStore.inoutSetProducts     = res.data;
                }

                this.isEditing = false
                this.productionStore.fpDetailsEditInModal = false
                this.productionStore.departmentFinishProduct = {}

            } else {
                if(res.status == 422) {
                    if(res.data.errors.count) {
                        this.err(res.data.errors.count[0])
                    }
                } else {
                    this.swr()
                }
            }
        },

    },
    async created(){}
}
</script>
