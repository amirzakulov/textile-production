<template>
    <Modal
        v-model="productionStore.fpSetEditModal"
        title="Партияни тахрирлаш"
        :mask-closable = "false"
        :closable = "false"
        width="768"
        @on-visible-change="productionStore.modalVisibility"
    >

        <Form ref="formValidate" label-position="top">
            <Row :gutter="8" border="bottom" class="ivu-mb">
                <Col span="14">
                    <FormItem label="* Қайси бўлимга">
                        <Select v-model="productionStore.inoutSet.to_department_id" placeholder="Бўлим танланг...">
                            <template v-for="department in productionStore.departments" :key="department.id">
                                <Option v-if="department.id != productionStore.department_id" :value="department.id">{{ department.name }}</Option>
                            </template>
                        </Select>
                        <Text :class="{ 'd-none': !department_id_err}" type="danger">Тўлдириш мажбурий!</Text>
                    </FormItem>
                </Col>
            </Row>
        </Form>
        <template #footer>
            <Button @click="productionStore.fpSetEditModal = false">Беркитиш</Button>
            <Button type="primary" @click="editFpSet" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
    import {useProductionStore} from "../../../../stores/ProductionStore";

    export default {
        name: "Edit Product Modal",
        setup() {
            const productionStore = useProductionStore()

            return { productionStore }
        },
        data(){
            return {
                isEditing: false,

                department_id_err:      false,
                is_invalid:             false,
            }
        },
        methods: {
            async editFpSet() {
                this.isEditing = true
                if(this.productionStore.inoutSet.to_department_id === '') {
                    this.is_invalid = true
                    this.department_id_err = true
                } else {
                    this.department_id_err = false
                }

                if(this.is_invalid) {
                    this.is_invalid = false
                    this.isEditing   = false
                    return this.err('Барча катакларни тўлдириш мажбурий!')
                }

                let inoutSetData = {
                    inoutSet: this.productionStore.inoutSet,
                    departmentInoutSet: this.productionStore.departmentInoutSet,
                    department_id: this.productionStore.department_id
                }

                const res = await this.callApi('post', '/app/edit_production_finish_product_out_set', inoutSetData)
                if(res.status === 200) {
                    if(res.data === false){
                        this.err("Ўзгартириш мумкин эмас!")
                    } else {
                        this.productionStore.inoutSets[this.productionStore.rowIndex] = res.data;
                        this.s('Махсулотлар мувоффақиятли сақланди!')
                    }

                    this.isEditing = false
                    this.productionStore.fpSetEditModal = false
                } else {
                    if(res.status == 422) {
                        if(res.data.errors.name) {
                            this.err(res.data.errors.name[0])
                        }
                    } else {
                        this.swr()
                    }
                }
            },
            handleAdd () {
                this.index++;
                this.productionStore.outgoingFinishProducts.push({raw_material_id: '', count:''});

                this.selectedProduct.push({count: 0});
                this.raw_material_id_err.push(false)
                this.raw_material_count_err.push({display:false, message: "Тўлдириш мажбурий!"})

                this.s('Қатор қўшилди!')
            },
            handleRemove(index) {

                if(Object.keys(this.productionStore.outgoingFinishProducts).length > 1){
                    this.productionStore.outgoingFinishProducts.splice(index, 1);
                    this.s('Қаторни ўчирилди!')
                } else {
                    return this.err('Қаторни ўчираолмайсиз!')
                }
            },

            async productChanged(raw_material_id, rowIndex) {
                const raw_material = await this.callApi('get', '/app/get_rm_rawmaterial/'+raw_material_id+'/'+this.productionStore.department_id)
                this.selectedProduct[rowIndex].count = raw_material.data.count
            },
            async countChanged(rowIndex){
                let count       = this.productionStore.outgoingFinishProducts[rowIndex].count
                let stockCount  = this.selectedProduct[rowIndex].count
                if(count > stockCount) {
                    this.raw_material_count_err[rowIndex].display = true;
                    this.raw_material_count_err[rowIndex].message = "Омборда бунча махсулот йўқ!";
                } else {
                    this.raw_material_count_err[rowIndex].display = false;
                    this.raw_material_count_err[rowIndex].message = "";
                }
            },
        },
        async created(){
            this.productionStore.inoutSet.from_department_id = this.productionStore.department_id
        }
    }
</script>
