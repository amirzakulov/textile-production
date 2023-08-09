<template>
    <Modal
        v-model="rmStore.rmEditSemiFinishProductOutModal"
        :title="rmStore.rmSemiFinishProduct.name +' ('+rmStore.rmSemiFinishProduct.code+')'"
        :mask-closable = "false"
        :closable = "false"
        width="768"
    >

        <Form ref="formValidate" :label-width="180">
            <FormItem label="* Миқдор" class="ivu-mb">
                <Input v-model="rmStore.rmSemiFinishProduct.count"
                       @on-change="countChanged"
                       placeholder="Махсулот Миқдор"></Input>
                <Paragraph class="m-0 p-0">Омборда: {{rmStore.rmSemiFinishProduct.stockCount}}</Paragraph>
                <Text :class="{ 'd-none': !raw_material_count_err.display }" type="danger">{{ raw_material_count_err.message }}</Text>
            </FormItem>
        </Form>
        <template #footer>
            <Button @click="rmStore.rmEditSemiFinishProductOutModal = false">Беркитиш</Button>
            <Button type="primary" @click="editSfProductOut" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
import {useRawMaterialsStore} from "../../../../stores/RawMaterialsStore";

export default {
    name: "Edit Semi Finish Product Modal",
    setup() {
        const rmStore = useRawMaterialsStore()
        return { rmStore }
    },
    data(){
        return {
            isEditing: false,
            raw_material_count_err: {display: false, message:"Тўлдириш мажбурий"},
            is_invalid: false,
        }
    },
    methods: {
        async editSfProductOut(){
            this.isEditing = true
            let stockCount  = this.rmStore.rmSemiFinishProduct.stockCount + this.rmStore.rmSemiFinishProduct.outCount
            if(this.rmStore.rmSemiFinishProduct.count === "" || this.rmStore.rawMaterial.count == 0) {
                this.is_invalid = true
                this.raw_material_count_err.display   = true
                this.raw_material_count_err.message   = "Тўлдириш мажбурий"

            } else if(this.rmStore.rmSemiFinishProduct.count > stockCount) {
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

            const res = await this.callApi('post', '/app/edit_rm_semi_finish_product_out', this.rmStore.rmSemiFinishProduct)
            this.isEditing = false

            if(res.status === 200) {
                if(res.data === false){
                    this.err("Ўзгартириш мумкин эмас!")
                } else {
                    this.s('Мувоффақиятли сақланди!')
                    this.rmStore.rmSetProducts = res.data;
                }

                this.rmStore.rmSemiFinishProduct             = {}
                this.rmStore.rmEditSemiFinishProductOutModal = false

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
            let count       = this.rmStore.rmSemiFinishProduct.count
            let stockCount  = this.rmStore.rmSemiFinishProduct.stockCount
            let outCount    = this.rmStore.rmSemiFinishProduct.outCount
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
