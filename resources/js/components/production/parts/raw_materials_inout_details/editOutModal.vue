<template>
    <Modal
        v-model="productionStore.rmDetailsEditOutModal"
        :title="productionStore.departmentRawMaterial.name +' ('+productionStore.departmentRawMaterial.code+')'"
        :mask-closable = "false"
        :closable = "false"
        width="768"
        @on-visible-change="productionStore.modalVisibility"
    >

        <Form ref="formValidate" :label-width="180" @submit.prevent="editProductionRmProductOut">
            <FormItem label="* Миқдор" class="ivu-mb">
                <Input v-model="productionStore.departmentRawMaterial.count"
                       @on-change="countChanged"
                       placeholder="Махсулот Миқдор"
                ></Input>
                <Paragraph class="m-0 p-0">Омборда: {{productionStore.departmentRawMaterial.stockCount}}</Paragraph>
                <Text :class="{ 'd-none': !raw_material_count_err.display }" type="danger">{{ raw_material_count_err.message }}</Text>
            </FormItem>
        </Form>
        <template #footer>
            <Button @click="productionStore.rmDetailsEditOutModal = false">Беркитиш</Button>
            <Button type="primary" @click="editProductionRmProductOut" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
import {useProductionStore} from "../../../../stores/ProductionStore";

export default {
    name: "Edit RawMaterial Modal",
    setup() {
        const productionStore = useProductionStore()
        const vFocus = {mounted: (el) => el.focus()}

        return { productionStore, vFocus }
    },
    data(){
        return {
            isEditing: false,
            raw_material_count_err: {display: false, message:"Тўлдириш мажбурий"},
            is_invalid: false,
        }
    },
    methods: {
        async editProductionRmProductOut(e){
            e.preventDefault()

            this.isEditing = true
            let stockCount  = this.productionStore.departmentRawMaterial.stockCount + this.productionStore.departmentRawMaterial.outCount
            if(this.productionStore.departmentRawMaterial.count === "" || this.productionStore.departmentRawMaterial.count === 0) {
                this.is_invalid = true
                this.raw_material_count_err.display   = true
                this.raw_material_count_err.message   = "Тўлдириш мажбурий"

            } else if(this.productionStore.departmentRawMaterial.count > stockCount) {
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


            console.log("RM",this.productionStore.departmentRawMaterial)
            const res = await this.callApi('post', '/app/edit_production_rawmaterial_out', this.productionStore.departmentRawMaterial)
            console.log(res.data)
            if(res.status === 200) {
                if(res.data === false){
                    this.err("Ўзгартириш мумкин эмас!")
                } else {
                    this.s('Мувоффақиятли сақланди!')
                    this.productionStore.inoutSetProducts = res.data;
                }

                this.productionStore.rmDetailsEditOutModal = false

            } else {
                if(res.status == 422) {
                    if(res.data.errors.count) {
                        this.err(res.data.errors.count[0])
                    }
                } else {
                    this.swr()
                }
            }

            this.productionStore.departmentRawMaterial = {}
            this.isEditing = false
        },

        async countChanged(){
            let count       = this.productionStore.departmentRawMaterial.count
            let stockCount  = this.productionStore.departmentRawMaterial.stockCount
            let outCount    = this.productionStore.departmentRawMaterial.outCount
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
