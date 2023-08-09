<template>
    <Modal
        v-model="rmStore.rmSetEditModal"
        title="Партияни тахрирлаш"
        :mask-closable = "false"
        :closable = "false"
        width="768"
        @on-visible-change="rmStore.modalVisibility"
    >

        <Form ref="formValidate" label-position="top">
            <Row :gutter="8" border="bottom" class="ivu-mb">
                <Col span="14">
                    <FormItem label="* Қайси бўлимга">
                        <Select v-model="rmStore.inoutSet.to_department_id" placeholder="Бўлим танланг...">
                            <template v-for="department in rmStore.departments" :key="department.id">
                                <Option v-if="department.id != rmStore.department_id" :value="department.id">{{ department.name }}</Option>
                            </template>
                        </Select>
                        <Text :class="{ 'd-none': !department_id_err}" type="danger">Тўлдириш мажбурий!</Text>
                    </FormItem>
                </Col>
            </Row>
        </Form>
        <template #footer>
            <Button @click="rmStore.rmSetEditModal = false">Беркитиш</Button>
            <Button type="primary" @click="editFpSet" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
    import {useRawMaterialsStore} from "../../../../stores/RawMaterialsStore";

    export default {
        name: "Edit Product Modal",
        setup() {
            const rmStore = useRawMaterialsStore()
            return { rmStore }
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

                if(this.rmStore.inoutSet.to_department_id === '') {
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
                    inoutSet:       this.rmStore.inoutSet, //from department data
                    rmSet:          this.rmStore.rmSet, //to department data
                    department_id:  this.rmStore.department_id
                }

                const res = await this.callApi('post', '/app/edit_rm_out_set', inoutSetData)

                if(res.status === 200) {
                    this.isEditing = false
                    if(res.data === false){
                        this.err("Ўзгартириш мумкин эмас!")
                    } else {
                        this.rmStore.rmSets[this.rmStore.rowIndex] = res.data;

                        this.s('Махсулотлар мувоффақиятли сақланди!')
                    }

                    this.rmStore.rmSetEditModal = false

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
                this.rmStore.outgoingFinishProducts.push({raw_material_id: '', count:''});

                this.selectedProduct.push({count: 0});
                this.raw_material_id_err.push(false)
                this.raw_material_count_err.push({display:false, message: "Тўлдириш мажбурий!"})

                this.s('Қатор қўшилди!')
            },
            handleRemove(index) {

                if(Object.keys(this.rmStore.outgoingFinishProducts).length > 1){
                    this.rmStore.outgoingFinishProducts.splice(index, 1);
                    this.s('Қаторни ўчирилди!')
                } else {
                    return this.err('Қаторни ўчираолмайсиз!')
                }
            },

            async productChanged(raw_material_id, rowIndex) {
                const raw_material = await this.callApi('get', '/app/get_rm_rawmaterial/'+raw_material_id+'/'+this.rmStore.department_id)
                this.selectedProduct[rowIndex].count = raw_material.data.count
            },
            async countChanged(rowIndex){
                let count       = this.rmStore.outgoingFinishProducts[rowIndex].count
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
            this.rmStore.inoutSet.from_department_id = this.rmStore.department_id
        }
    }
</script>
