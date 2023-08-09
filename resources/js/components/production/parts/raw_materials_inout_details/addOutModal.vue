<template>
    <Modal
        v-model="productionStore.rmDetailsAddOutModal"
        title="Махсулот қўшиш"
        :mask-closable = "false"
        :closable = "false"
        width="768"
        @on-visible-change="productionStore.modalVisibility"
    >

        <Form ref="formValidate" :label-width="180">
            <FormItem label="* Махсулот" class="ivu-mb">
                <Select v-model="productionStore.departmentRawMaterial.raw_material_balance_id"
                        @on-change="productChanged(productionStore.departmentRawMaterial.raw_material_balance_id)"
                        filterable placeholder="Махсулотни танланг...">
                    <template v-for="rmbProduct in productionStore.departmentRawMaterials" :key="rmbProduct.id">
                        <Option v-if="!productionStore.inoutSetRawMaterialsId.includes(rmbProduct.raw_material_id)" :value="rmbProduct.id">{{ rmbProduct.name + " (" + rmbProduct.code + ", " + rmbProduct.set_name + ")" }}</Option>
                    </template>
                </Select>
                <Text :class="{ 'd-none': !raw_material_id_err.display }" type="danger">{{raw_material_id_err.message}}</Text>
            </FormItem>
            <FormItem label="* Миқдор" class="ivu-mb">
                <Input v-model="productionStore.departmentRawMaterial.count" @on-change="countChanged" placeholder="Махсулот Миқдор"></Input>
                <Paragraph :class="{ 'd-none': !stockCount }" class="m-0 p-0">Омборда: {{stockCount}}</Paragraph>
                <Text :class="{ 'd-none': !raw_material_count_err.display }" type="danger">{{raw_material_count_err.message}}</Text>
            </FormItem>
        </Form>
        <template #footer>
            <Button @click="productionStore.rmDetailsAddOutModal = false">Беркитиш</Button>
            <Button type="primary" @click="addProductionRmProductOut" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
    import {useProductionStore} from "../../../../stores/ProductionStore";

export default {
    name: "Add RawMaterial Modal",
    setup() {
        const productionStore = useProductionStore()

        return { productionStore }
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
            if(this.productionStore.departmentRawMaterial.raw_material_balance_id == null) {
                this.is_invalid = true
                this.raw_material_id_err.display = true
                this.raw_material_id_err.message = "Тўлдириш мажбурий"
            } else {
                this.raw_material_id_err.display = false
            }

            if(this.productionStore.departmentRawMaterial.count === ""
                || this.productionStore.departmentRawMaterial.count === 0
                || this.productionStore.departmentRawMaterial.count === undefined
            ) {
                this.is_invalid = true
                this.raw_material_count_err.display   = true
                this.raw_material_count_err.message   = "Тўлдириш мажбурий"
            } else if(this.productionStore.departmentRawMaterial.count > this.stockCount) {
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
                inoutSet:       this.productionStore.inoutSet,
                rawMaterial:    this.productionStore.departmentRawMaterial,
                department_id:  this.productionStore.department_id,
                finished_product:  {
                    finish_raw_material_id: this.productionStore.inoutSetProducts[0].finish_raw_material_id,
                    finish_product_id: this.productionStore.inoutSetProducts[0].finish_product_id,
                }
            }

            const res = await this.callApi('post', '/app/add_production_rawmaterial_out', outgoingProduct)

            if(res.status === 200) {
                if(res.data === false){
                    this.err("Махсулот қўшиш мумкин эмас!")
                } else {
                    this.s('Мувоффақиятли қўшилди!')
                    this.productionStore.inoutSetProducts = res.data;
                    this.productionStore.inoutSetRawMaterialsId.push(this.productionStore.departmentRawMaterial.raw_material_id)
                    this.productionStore.departmentRawMaterial = {}
                }

                this.productionStore.rmDetailsAddOutModal   = false

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

        async productChanged(raw_material_balance_id) {
            // const raw_material = await this.callApi('get', '/app/get_rm_rawmaterial/'+raw_material_id+'/'+this.productionStore.department_id)
            // this.stockCount = raw_material.data.count
            const raw_material = await this.callApi('get', '/app/get_rm_rawmaterial_balance/'+raw_material_balance_id)
            this.stockCount = raw_material.data.count
        },
        async countChanged(){
            let count       = this.productionStore.departmentRawMaterial.count

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
