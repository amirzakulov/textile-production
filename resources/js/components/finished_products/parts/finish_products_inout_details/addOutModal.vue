<template>
    <Modal
        v-model="fpStore.fpDetailsAddOutModal"
        title="Махсулот қўшиш"
        :mask-closable = "false"
        :closable = "false"
        width="768"
        @on-visible-change="fpStore.modalVisibility"
    >

        <Form ref="formValidate" :label-width="180">
            <FormItem label="* Махсулот" class="ivu-mb">
                <Select v-model="fpStore.departmentFinishProduct.raw_material_id"
                        @on-change="productChanged(fpStore.departmentFinishProduct.raw_material_id)"
                        filterable placeholder="Махсулотни танланг...">
                    <template v-for="product in fpStore.fpFinishProducts" :key="product.raw_material_id">
                        <Option v-if="!fpStore.inoutSetRawMaterialsId.includes(product.raw_material_id)" :value="product.raw_material_id">{{ product.name + " (" + product.code + ", " + product.set_name + ")" }}</Option>
                    </template>
                </Select>
                <Text :class="{ 'd-none': !raw_material_id_err.display }" type="danger">{{raw_material_id_err.message}}</Text>
            </FormItem>
            <FormItem label="* Миқдор" class="ivu-mb">
                <Input v-model="fpStore.departmentFinishProduct.count" @on-change="countChanged" placeholder="Махсулот Миқдор"></Input>
                <Paragraph :class="{ 'd-none': !stockCount }" class="m-0 p-0">Омборда: {{stockCount}}</Paragraph>
                <Text :class="{ 'd-none': !raw_material_count_err.display }" type="danger">{{raw_material_count_err.message}}</Text>
            </FormItem>
        </Form>
        <template #footer>
            <Button @click="fpStore.fpDetailsAddOutModal = false">Беркитиш</Button>
            <Button type="primary" @click="addProductionRmProductOut" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
    import {useFinishedProductsStore} from "../../../../stores/FinishedProductsStore";

export default {
    name: "Add RawMaterial Modal",
    setup(){
        const fpStore = useFinishedProductsStore()
        return { fpStore }
    },
    data(){
        return {
            isAdding: false,

            stockCount: 0,
            raw_material_id_err  : {display:false, message: ""},
            raw_material_count_err: {display:false, message: ""},
            is_invalid: false,
        }
    },
    methods: {
        async addProductionRmProductOut(){
            this.isAdding = true
            if(this.fpStore.departmentFinishProduct.raw_material_id == null) {
                this.is_invalid = true
                this.raw_material_id_err.display = true
                this.raw_material_id_err.message = "Тўлдириш мажбурий"
            } else {
                this.raw_material_id_err.display = false
            }

            if(this.fpStore.departmentFinishProduct.count === ""
                || this.fpStore.departmentFinishProduct.count === 0
                || this.fpStore.departmentFinishProduct.count === undefined
            ) {
                this.is_invalid = true
                this.raw_material_count_err.display   = true
                this.raw_material_count_err.message   = "Тўлдириш мажбурий"
            } else if(this.fpStore.departmentFinishProduct.count > this.stockCount) {
                this.is_invalid = true
                this.raw_material_count_err.display   = true
                this.raw_material_count_err.message   = "Омборда бунча махсулот йўқ"
            }

            if(this.is_invalid) {
                this.is_invalid = false
                this.isAdding = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            let outgoingProduct = {
                inoutSet:       this.fpStore.inoutSet,
                rawMaterial:    this.fpStore.departmentFinishProduct,
                department_id:  this.fpStore.department_id,
                finished_product:  {
                    finish_raw_material_id: this.fpStore.inoutSetProducts[0].finish_raw_material_id,
                    finish_product_id: this.fpStore.inoutSetProducts[0].finish_product_id,
                }
            }

            const res = await this.callApi('post', '/app/add_fp_finish_product_out', outgoingProduct)
            if(res.status === 200) {
                if(res.data === false){
                    this.err("Махсулот қўшиш мумкин эмас!")
                } else {
                    this.s('Мувоффақиятли қўшилди!')
                    this.fpStore.inoutSetProducts = res.data;
                    this.fpStore.inoutSetRawMaterialsId.push(this.fpStore.departmentFinishProduct.raw_material_id)
                    this.fpStore.departmentFinishProduct = {}
                }

                this.fpStore.fpDetailsAddOutModal   = false

            } else {
                if(res.status === 422) {
                    if(res.data.errors.count) {
                        this.err(res.data.errors.count[0])
                    }
                } else {
                    this.swr()
                }
            }

            this.isAdding = false
        },

        async productChanged(raw_material_id) {
            const raw_material = await this.callApi('get', '/app/get_rm_rawmaterial/'+raw_material_id+'/'+this.fpStore.department_id)
            this.stockCount = raw_material.data.count
        },
        async countChanged(){
            let count       = this.fpStore.departmentFinishProduct.count

            if(count > this.stockCount) {
                this.raw_material_count_err.display = true;
                this.raw_material_count_err.message = "Омборда бунча махсулот йўқ!";
            } else {
                this.raw_material_count_err.display = false;
                this.raw_material_count_err.message = "";
            }
        },
    },
    async created(){

    }
}
</script>
