<template>
    <Modal
        v-model="productionStore.finishProductOutModal"
        title="Бўлимларга чиқим қилиш"
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
                <Col span="10">
                    <FormItem label="* Сана">
                        <DatePicker v-model="productionStore.created_date" format="yyyy-MM-dd" type="date"
                                    style="width: 272px;"
                                    placeholder="Санани танланг" />
                        <Text :class="{ 'd-none': !created_date_err}" type="danger"> Тўлдириш мажбурий!</Text>
                    </FormItem>
                </Col>
            </Row>

            <Row :gutter="8" style="border: 1px solid #ccc; margin-bottom: 5px; padding: 10px; background-color: #eeeff3;">
                <Col span="14">* Махсулот номи</Col>
                <Col span="9">* Миқдор</Col>
                <Col span="1"></Col>
            </Row>

            <template v-for="(productField, index) in productionStore.outgoingFinishProducts" :key="index">
                <Row :gutter="8" border="bottom" class="ivu-mb">
                    <Col span="14">
                        <FormItem>
                            <Select v-model="productionStore.outgoingFinishProducts[index].raw_material_balance_id" @on-change="productChanged(productionStore.outgoingFinishProducts[index].raw_material_balance_id, index)" filterable placeholder="Махсулотни танланг...">
                                <Option v-for="rmbProduct in productionStore.departmentFinishProducts" :value="rmbProduct.id" :key="rmbProduct.id">{{ rmbProduct.name + " (" + rmbProduct.code + ", " + rmbProduct.set_name + ")" }}</Option>
                            </Select>
                            <Text :class="{ 'd-none': !raw_material_id_err[index] }" type="danger">{{raw_material_id_err[index]}}</Text>
                        </FormItem>
                    </Col>
                    <Col span="9">
                        <FormItem>
                            <Input v-model="productionStore.outgoingFinishProducts[index].count"
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
            <Button @click="productionStore.finishProductOutModal = false">Беркитиш</Button>
            <Button type="primary" @click="addOutgoingProducts" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Махсулот...':'Сақлаш'}}</Button>
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
            isAdding: false,

            created_date_err:       false,
            department_id_err:      false,
            raw_material_id_err:    [false],
            raw_material_count_err: [{display:false, message: "Тўлдириш мажбурий!"}],
            selectedProduct:        [{count: 0}],
            is_invalid:             false,
        }
    },
    methods: {
        async addOutgoingProducts() {
            this.isAdding = true
            if(this.productionStore.created_date == '') {
                this.is_invalid = true
                this.created_date_err = true
            } else {
                this.created_date_err = false
            }

            if(this.productionStore.inoutSet.to_department_id === '') {
                this.is_invalid = true
                this.department_id_err = true
            } else {
                this.department_id_err = false
            }

            this.productionStore.outgoingFinishProducts.forEach((row, index) => {
                //check product_id
                if(row.raw_material_balance_id === '') {
                    this.is_invalid = true
                    this.raw_material_id_err[index] = true
                } else {
                    this.raw_material_id_err[index] = false
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
                inoutSet:       this.productionStore.inoutSet,
                products:       this.productionStore.outgoingFinishProducts,
                department_id:  this.productionStore.department_id,
                created_date:   this.myDateFormat(this.productionStore.created_date, "yyyy-mm-dd"),
            }

            const res = await this.callApi('post', '/app/add_production_outgoing_products', outgoingDetails)

            this.isAdding = false
            if(res.status === 200) {

                this.productionStore.inoutSets = res.data;

                const departmentFinishProducts                = await this.callApi("get", "/app/get_production_department_finish_products/"+this.productionStore.department_id)
                this.productionStore.departmentFinishProducts = departmentFinishProducts.data;

                const departmentRawMaterials                  = await this.callApi('get', '/app/get_production_department_rawmaterials/'+this.productionStore.department_id)
                this.productionStore.departmentRawMaterials   = departmentRawMaterials.data

                this.productionStore.finishProductOutModal = false

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
            this.productionStore.outgoingFinishProducts.push({raw_material_balance_id: '', count:''});

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

        async productChanged(raw_material_balance_id, rowIndex) {
            // const raw_material = await this.callApi('get', '/app/get_rm_rawmaterial/'+raw_material_id+'/'+this.productionStore.department_id)
            const raw_material = await this.callApi('get', '/app/get_rm_rawmaterial_balance/'+raw_material_balance_id)
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
