<template>
    <Modal
        v-model="rmStore.productOutModal"
        title="Бўлимларга чиқим қилиш"
        :mask-closable = "false"
        :closable = "false"
        width="768"
        @on-visible-change="rmStore.modalVisibility"
    >

        <Form ref="formValidate" label-position="top">
            <Row :gutter="8" border="bottom" class="ivu-mb-4">
                <Col span="14">
                    <FormItem label="* Қайси бўлимга">
                        <Select v-model="rmStore.inoutSet.to_department_id" placeholder="Бўлим танланг...">
                            <Option v-for="department in rmStore.departments" :value="department.id" :key="department.id">{{ department.name }}</Option>
                        </Select>
                        <Text :class="{ 'd-none': !department_id_err}" type="danger">Тўлдириш мажбурий!</Text>
                    </FormItem>
                </Col>
                <Col span="10">
                    <FormItem label="* Сана">
                        <DatePicker v-model="rmStore.created_date" format="yyyy-MM-dd" type="date"
                                    style="width: 272px;"
                                    placeholder="Санани танланг" />
                        <Text :class="{ 'd-none': !created_date_err}" type="danger"> Тўлдириш мажбурий!</Text>
                    </FormItem>
                </Col>
            </Row>

            <Row :gutter="8" class="modal-table-header">
                <Col span="14">* Махсулот номи</Col>
                <Col span="9">* Миқдор</Col>
                <Col span="1"></Col>
            </Row>

            <template v-for="(productField, index) in rmStore.outgoingProducts" :key="index">
                <Row :gutter="8" border="bottom" class="ivu-mb">
                    <Col span="14">
                        <FormItem>
                            <Select v-model="rmStore.outgoingProducts[index].raw_material_balance_id"
                                    @on-change="productChanged(rmStore.outgoingProducts[index].raw_material_balance_id, index)"
                                    filterable placeholder="Махсулотни танланг...">
                                <Option v-for="rmbProduct in rmStore.departmentRawMaterials" :value="rmbProduct.id" :key="rmbProduct.id">{{ rmbProduct.name + " (" + rmbProduct.code + ", " + rmbProduct.set_name + ")" }}</Option>
                            </Select>
                            <Text :class="{ 'd-none': !raw_material_balance_id_err[index].display }" type="danger">{{raw_material_balance_id_err[index].message}}</Text>
                        </FormItem>
                    </Col>
                    <Col span="9">
                        <FormItem>
                            <Input v-model="rmStore.outgoingProducts[index].count"
                                   @on-change="countChanged(index)"
                                   placeholder="Махсулот Миқдор"></Input>
                            <Paragraph :class="{ 'd-none': !selectedProduct[index].count }" class="m-0 p-0">Омборда: {{selectedProduct[index].count}}</Paragraph>
                            <Text :class="{ 'd-none': !raw_material_count_err[index].display }" type="danger">{{raw_material_count_err[index].message}}</Text>
                        </FormItem>
                    </Col>
                    <Col span="1" style="padding-top: 5px;">
                        <a href="javascript:void(0);">
                            <Icon type="md-remove-circle" size="20" color="#dc3545" @click="handleRemove(index)" />
                        </a>
                    </Col>
                </Row>
            </template>

            <FormItem>
                <Row>
                    <Col span="1" offset="23">
                        <Button type="success" @click="handleAdd" icon="md-add"></Button>
                    </Col>
                </Row>
            </FormItem>
        </Form>
        <template #footer>
            <Button @click="rmStore.productOutModal = false">Беркитиш</Button>
            <Button type="primary" @click="addOutgoingProducts" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Махсулот...':'Сақлаш'}}</Button>
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
            isAdding: false,

            // inoutSet: {from_department_id: 2, to_department_id: ''},

            created_date_err:       false,
            department_id_err:      false,
            raw_material_balance_id_err:    [{display:false, message: "Тўлдириш мажбурий!"}],
            raw_material_count_err: [{display:false, message: "Тўлдириш мажбурий!"}],
            selectedProduct:        [{count: 0}],
            is_invalid:             false,
        }
    },
    methods: {
        async addOutgoingProducts() {
            this.isAdding = true
            if(this.rmStore.created_date == '') {
                this.is_invalid = true
                this.created_date_err = true
            } else {
                this.created_date_err = false
            }

            if(this.rmStore.inoutSet.to_department_id === '') {
                this.is_invalid = true
                this.department_id_err = true
            } else {
                this.department_id_err = false
            }

            this.rmStore.outgoingProducts.forEach((row, index) => {
                //check product_id
                if(row.raw_material_balance_id === '') {
                    this.is_invalid = true
                    this.raw_material_balance_id_err[index].display = true
                } else {
                    this.raw_material_balance_id_err[index].display = false
                }

                //check product count
                if(row.count === '') {
                    this.is_invalid = true
                    this.raw_material_count_err[index].display = true
                } else {
                    this.raw_material_count_err[index].display = false
                }
            });

            if(this.is_invalid) {
                this.is_invalid = false
                this.isAdding   = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            let outgoingDetails = {
                inoutSet: this.rmStore.inoutSet,
                products: this.rmStore.outgoingProducts,
                department_id: this.rmStore.department_id,
                created_date: this.myDateFormat(this.rmStore.created_date, "yyyy-mm-dd"),
            }

            const res = await this.callApi('post', '/app/add_rmset_outgoing_products', outgoingDetails)

            this.isAdding = false
            if(res.status === 200) {
                this.isAdding = false
                this.rmStore.rmSets.unshift(res.data);
                this.rmStore.productOutModal = false
                this.rmStore.outgoingProducts = [{raw_material_balance_id: '', count: ''}],

                this.s('Махсулотлар мувоффақиятли қўшилди!')

            } else {
                if(res.status === 422) {
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
            this.rmStore.outgoingProducts.push({raw_material_balance_id: '', count:''});

            this.selectedProduct.push({count: 0});
            this.raw_material_balance_id_err.push({display:false, message: "Тўлдириш мажбурий!"})
            this.raw_material_count_err.push({display:false, message: "Тўлдириш мажбурий!"})

            this.s('Қатор қўшилди!')
        },
        handleRemove(index) {

            if(Object.keys(this.rmStore.outgoingProducts).length > 1){
                this.rmStore.outgoingProducts.splice(index, 1);
                this.s('Қаторни ўчирилди!')
            } else {
                return this.err('Қаторни ўчираолмайсиз!')
            }
        },

        async productChanged(raw_material_balance_id, rowIndex) {

            const raw_material = await this.callApi('get', '/app/get_rm_rawmaterial_balance/'+raw_material_balance_id)
            this.selectedProduct[rowIndex].count = raw_material.data.count
        },
        async countChanged(rowIndex){
            let count       = this.rmStore.outgoingProducts[rowIndex].count
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
    async created(){}
}
</script>
