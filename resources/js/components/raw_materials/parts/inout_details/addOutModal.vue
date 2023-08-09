<template>
    <Modal
        v-model="rmStore.rmAddOutModal"
        title="Махсулот қўшиш"
        :mask-closable = "false"
        :closable = "false"
        width="768"
        @on-visible-change="rmStore.modalVisibility"
    >

        <Form ref="formValidate" :label-width="180">
            <FormItem label="* Махсулот" class="ivu-mb">
                <Select v-model="rmStore.rawMaterial.raw_material_balance_id"
                        @on-change="productChanged(rmStore.rawMaterial.raw_material_balance_id)"
                        filterable placeholder="Махсулотни танланг...">
                    <template v-for="rmbProduct in rmStore.departmentRawMaterials" :value="rmbProduct.id" :key="rmbProduct.id">
                        <Option v-if="!rmStore.rmSetRawMaterialsId.includes(rmbProduct.raw_material_id)" :value="rmbProduct.id">{{ rmbProduct.name + " (" + rmbProduct.code + ", " + rmbProduct.set_name + ")" }}</Option>
                    </template>
                </Select>
                <Text :class="{ 'd-none': !raw_material_id_err.display }" type="danger">{{raw_material_id_err.message}}</Text>
            </FormItem>
            <FormItem label="* Миқдор" class="ivu-mb">
                <Input v-model="rmStore.rawMaterial.count" @on-change="countChanged" placeholder="Махсулот Миқдор"></Input>
                <Paragraph :class="{ 'd-none': !stockCount }" class="m-0 p-0">Омборда: {{stockCount}}</Paragraph>
                <Text :class="{ 'd-none': !raw_material_count_err.display }" type="danger">{{raw_material_count_err.message}}</Text>
            </FormItem>
        </Form>
        <template #footer>
            <Button @click="rmStore.rmAddOutModal = false">Беркитиш</Button>
            <Button type="primary" @click="addRmProductOut" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
import {useRawMaterialsStore} from "../../../../stores/RawMaterialsStore";

export default {
    name: "Add RawMaterial Modal",
    setup() {
        const rmStore = useRawMaterialsStore()

        return { rmStore }
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
        async addRmProductOut(){
            this.isAdding = true
            if(this.rmStore.rawMaterial.raw_material_balance_id === null) {
                this.is_invalid = true
                this.raw_material_id_err.display = true
                this.raw_material_id_err.message = "Тўлдириш мажбурий"
            } else {
                this.raw_material_id_err.display = false
            }

            if(this.rmStore.rawMaterial.count === ""
                || this.rmStore.rawMaterial.count === 0
                || this.rmStore.rawMaterial.count === undefined
            ) {
                this.is_invalid = true
                this.raw_material_count_err.display   = true
                this.raw_material_count_err.message   = "Тўлдириш мажбурий"
            } else if(this.rmStore.rawMaterial.count > this.stockCount) {
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
                inoutSet:       this.rmStore.rmSet,
                rawMaterial:    this.rmStore.rawMaterial,
                department_id:  this.rmStore.department_id
            }

            const res = await this.callApi('post', '/app/add_rawmaterial_out', outgoingProduct)

            if(res.status === 200) {
                this.rmStore.rmSetProducts  = res.data;
                this.rmStore.rmSetRawMaterialsId.push(this.rmStore.rawMaterial.raw_material_id)
                this.rmStore.rmAddOutModal  = false

                this.s('Мувоффақиятли сақланди!')
                this.rmStore.rawMaterial = {}

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

            // const raw_material = await this.callApi('get', '/app/get_rm_rawmaterial/'+raw_material_id+'/'+this.rmStore.department_id)
            const raw_material = await this.callApi('get', '/app/get_rm_rawmaterial_balance/'+raw_material_balance_id)
            this.stockCount = raw_material.data.count
        },
        async countChanged(){
            let count       = this.rmStore.rawMaterial.count

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
