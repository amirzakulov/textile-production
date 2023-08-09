<template>
    <Modal
        v-model="fpStore.fpDetailsEditOutModal"
        :title="fpStore.departmentFinishProduct.name +' ('+fpStore.departmentFinishProduct.code+')'"
        :mask-closable = "false"
        :closable = "false"
        width="768"
        @on-visible-change="fpStore.modalVisibility"
    >

        <Form ref="formValidate" :label-width="180">
            <FormItem label="* Миқдор" class="ivu-mb">
                <Input v-model="fpStore.departmentFinishProduct.count"
                       @on-change="countChanged"
                       placeholder="Махсулот Миқдор"></Input>
                <Paragraph class="m-0 p-0">Омборда: {{fpStore.departmentFinishProduct.stockCount}}</Paragraph>
                <Text :class="{ 'd-none': !raw_material_count_err.display }" type="danger">{{ raw_material_count_err.message }}</Text>
            </FormItem>
        </Form>
        <template #footer>
            <Button @click="fpStore.fpDetailsEditOutModal = false">Беркитиш</Button>
            <Button type="primary" @click="editFpProductOut" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
import {useFinishedProductsStore} from "../../../../stores/FinishedProductsStore";

export default {
    name: "Edit RawMaterial Modal",
    setup(){
        const fpStore = useFinishedProductsStore()
        return { fpStore }
    },
    data(){
        return {
            isEditing: false,
            raw_material_count_err: {display: false, message:"Тўлдириш мажбурий"},
            is_invalid: false,
        }
    },
    methods: {
        async editFpProductOut(){

            this.isEditing = true
            let stockCount  = this.fpStore.departmentFinishProduct.stockCount + this.fpStore.departmentFinishProduct.outCount
            if(this.fpStore.departmentFinishProduct.count === "" || this.fpStore.departmentFinishProduct.count === 0) {
                this.is_invalid = true
                this.raw_material_count_err.display   = true
                this.raw_material_count_err.message   = "Тўлдириш мажбурий"

            } else if(this.fpStore.departmentFinishProduct.count > stockCount) {
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

            const res = await this.callApi('post', '/app/edit_fp_finish_product_out', this.fpStore.departmentFinishProduct)

            this.isEditing = false
            if(res.status === 200) {
                if(res.data === false){
                    this.err("Ўзгартириш мумкин эмас!")
                } else {
                    this.fpStore.inoutSetProducts  = res.data;
                    this.s('Мувоффақиятли сақланди!')
                }

                this.fpStore.fpDetailsEditOutModal = false
                this.fpStore.departmentFinishProduct = {}

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
            let count       = this.fpStore.departmentFinishProduct.count
            let stockCount  = this.fpStore.departmentFinishProduct.stockCount
            let outCount    = this.fpStore.departmentFinishProduct.outCount
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
