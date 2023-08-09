<template>
    <Modal
        v-model="productionStore.fpDetailsEditOutModal"
        :title="productionStore.departmentFinishProduct.name +' ('+productionStore.departmentFinishProduct.code+')'"
        :mask-closable = "false"
        :closable = "false"
        width="768"
        @on-visible-change="productionStore.modalVisibility"
    >

        <Form ref="formValidate" :label-width="180">
            <FormItem label="* Миқдор" class="ivu-mb">
                <Input v-model="productionStore.departmentFinishProduct.count"
                       @on-change="countChanged"
                       placeholder="Махсулот Миқдор"></Input>
                <Paragraph class="m-0 p-0">Омборда: {{productionStore.departmentFinishProduct.stockCount}}</Paragraph>
                <Text :class="{ 'd-none': !raw_material_count_err.display }" type="danger">{{ raw_material_count_err.message }}</Text>
            </FormItem>
        </Form>
        <template #footer>
            <Button @click="productionStore.fpDetailsEditOutModal = false">Беркитиш</Button>
            <Button type="primary" @click="editProductionfpOut" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
import {useProductionStore} from "../../../../stores/ProductionStore";

export default {
    name: "Edit RawMaterial Modal",
    setup(){
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
        async editProductionfpOut(){
            this.isEditing = true
            let stockCount  = this.productionStore.departmentFinishProduct.stockCount + this.productionStore.departmentFinishProduct.outCount
            if(this.productionStore.departmentFinishProduct.count === "" || this.productionStore.departmentFinishProduct.count === 0) {
                this.is_invalid = true
                this.raw_material_count_err.display   = true
                this.raw_material_count_err.message   = "Тўлдириш мажбурий"

            } else if(this.productionStore.departmentFinishProduct.count > stockCount) {
                this.is_invalid = true
                this.raw_material_count_err.display   = true
                this.raw_material_count_err.message   = "Омборда бунча махсулот йўқ"
            } else {
                this.raw_material_count_err.display   = false
            }

            if(this.is_invalid) {
                this.is_invalid = false
                this.isEditing = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            const res = await this.callApi('post', '/app/edit_production_finish_product_out', this.productionStore.departmentFinishProduct)

            this.isEditing = false
            if(res.status === 200) {
                if(res.data === false){
                    this.err("Ўзгартириш мумкин эмас!")
                } else {
                    this.s('Мувоффақиятли сақланди!')
                    this.productionStore.inoutSetProducts = res.data;
                }

                this.isEditing = false
                this.productionStore.departmentFinishProduct = {}
                this.productionStore.fpDetailsEditOutModal  = false

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

        async countChanged(){
            let count       = this.productionStore.departmentFinishProduct.count
            let stockCount  = this.productionStore.departmentFinishProduct.stockCount
            let outCount    = this.productionStore.departmentFinishProduct.outCount
            if(count > (stockCount + outCount)) {
                this.raw_material_count_err.display = true;
                this.raw_material_count_err.message = "Омборда бунча махсулот йўқ!";
            } else {
                this.raw_material_count_err.display = false;
                this.raw_material_count_err.message = "";
            }
        },

    },
    async created(){}
}
</script>
