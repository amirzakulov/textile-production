<template>
    <Modal
        v-model="productionStore.finishProductInModal"
        title="Тайёр махсулот қўшиш"
        :mask-closable = "false"
        :closable = "false"
        width="760"
        @on-visible-change="productionStore.modalVisibility"
    >
        <Form ref="incomeProducts" label-position="left">
            <Row :gutter="8" class="ivu-mt-4 ivu-mb-4">
                <Col span="24">
                    <FormItem label="">
                        <DatePicker v-model="productionStore.created_date" format="yyyy-MM-dd" type="date"
                                    style="width: 225px;"
                                    placeholder="Санани танланг" />
                        <Text :class="{ 'd-none': !created_date_err}" type="danger"> Тўлдириш мажбурий!</Text>
                    </FormItem>
                </Col>
            </Row>
            <h4 class="_title0">Моделни танланг</h4>
            <Row class="modal-table-header">
                <Col span="12" style="padding-right:10px;">
                    <FormItem label="* Махсулот номи" :key="productionStore.rowIndex">
                        <Select v-model="productionStore.departmentFinishProduct.product_id" placeholder="Махсулотни танланг...">
                            <Option v-for="product in productionStore.departmentProducts" :value="product.id" :key="product.id">{{ product.name }}</Option>
                        </Select>
                        <Text :class="{ 'd-none': !fashion_product_id_err }" type="danger">Тўлдириш мажбурий!</Text>
                    </FormItem>
                </Col>
                <Col span="11" style="padding-right:10px">
                    <FormItem label="* Миқдор" class="mb-0">
                        <Input v-model="productionStore.departmentFinishProduct.count" placeholder="Миқдор"></Input>
                        <Text :class="{ 'd-none': !fashion_count_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий!</Text>
                    </FormItem>
                </Col>
            </Row>
            <h4 class="_title0">Ишлатилган махсулотлар</h4>
            <Row :gutter="8" class="modal-table-header">
                <Col span="12">* Махсулот номи</Col>
                <Col span="11">* Миқдор</Col>
                <Col span="1"></Col>
            </Row>
            <template v-for="(productField, index) in productionStore.outgoingRawMaterials" :key="index">
                <Row :gutter="8" border="bottom" class="ivu-mb">
                    <Col span="12">
                        <FormItem>
                            <Select v-model="productionStore.outgoingRawMaterials[index].raw_material_balance_id" @on-change="productChanged(productionStore.outgoingRawMaterials[index].raw_material_balance_id, index)" placeholder="Махсулотни танланг...">
                                <template v-for="rmbProduct in productionStore.departmentRawMaterials" :key="rmbProduct.id">
                                    <Option :value="rmbProduct.id">{{ rmbProduct.name + " (" + rmbProduct.code + ", " + rmbProduct.set_name + " )" }}</Option>
                                </template>
                            </Select>
                            <Text :class="{ 'd-none': !raw_material_id_err[index] }" type="danger">Тўлдириш мажбурий!</Text>
                        </FormItem>
                    </Col>
                    <Col span="11">
                        <FormItem>
                            <Input v-model="productionStore.outgoingRawMaterials[index].count" placeholder="Махсулот Миқдор" @on-change="countChanged(index)"></Input>
                            <Paragraph :class="{ 'd-none': !selectedProduct[index].count }" class="m-0 p-0">Омборда: {{selectedProduct[index].count}}</Paragraph>
                            <Text :class="{ 'd-none': !raw_material_count_err[index].display }" type="danger" class="m-0 p-0">{{raw_material_count_err[index].message}}</Text>
                        </FormItem>
                    </Col>
                    <Col span="1" style="padding-top:5px;">
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
            <Button @click="productionStore.finishProductInModal = false">Беркитиш</Button>
            <Button type="primary" @click="addFinishProduct" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
import {useProductionStore} from "../../../../stores/ProductionStore";

export default {
    name: "Add Finish Product Modal",
    setup(){
        const productionStore = useProductionStore()

        return { productionStore }
    },
    data() {
        return {
            isAdding : false,

            created_date_err:       false,
            fashion_product_id_err: false,
            fashion_id_err:         false,
            fashion_count_err:      false,
            raw_material_id_err:    [false],
            raw_material_count_err: [{display:false, message: ""}],
            is_invalid:             false,
            selectedProduct:        [{count: 0}],
        }
    },
    methods: {
        async addFinishProduct () {
            if(this.productionStore.created_date == '') {
                this.is_invalid = true
                this.created_date_err = true
            } else {
                this.created_date_err = false
            }

            console.log(this.productionStore.departmentFinishProduct.product_id)
            if(this.productionStore.departmentFinishProduct.product_id == '' || this.productionStore.departmentFinishProduct.product_id == undefined) {
                this.is_invalid         = true
                this.fashion_product_id_err    = true
            } else {
                this.fashion_product_id_err    = false
            }

            if(this.productionStore.departmentFinishProduct.count === '') {
                this.is_invalid     = true
                this.fashion_count_err  = true
            } else {
                this.fashion_count_err  = false
            }

            this.productionStore.outgoingRawMaterials.forEach((row, index) => {
                //check raw_material_id
                if(row.raw_material_balance_id === '') {
                    this.is_invalid = true
                    this.raw_material_id_err[index] = true
                } else {
                    this.raw_material_id_err[index] = false
                }

                //check product count
                if(row.count === '' || row.count === 0) {
                    this.is_invalid = true
                    this.raw_material_count_err[index].message = 'Тўлдириш мажбурий!'
                    this.raw_material_count_err[index].display = true
                } else if(row.count > this.selectedProduct[index].count) {
                    this.is_invalid = true
                    this.raw_material_count_err[index].message = 'Омборда бунча махсулот йўқ!!'
                    this.raw_material_count_err[index].display = true
                } else {
                    this.raw_material_count_err[index].display = false
                }
            });

            if(this.is_invalid) {
                this.is_invalid = false
                this.isAdding = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            let finishProductData = {
                finishProduct:  this.productionStore.departmentFinishProduct,
                rawMaterials:   this.productionStore.outgoingRawMaterials,
                department_id:  this.productionStore.department_id,
                created_date: this.myDateFormat(this.productionStore.created_date, "yyyy-mm-dd"),
            }

            const res = await this.callApi('post', '/app/add_production_finish_product', finishProductData)

            if(res.status === 200) {
                this.isAdding = false
                this.productionStore.inoutSets = res.data;

                const departmentFinishProducts                = await this.callApi("get", "/app/get_production_department_finish_products/"+this.productionStore.department_id)
                this.productionStore.departmentFinishProducts = departmentFinishProducts.data;

                const departmentRawMaterials                  = await this.callApi('get', '/app/get_production_department_rawmaterials/'+this.productionStore.department_id)
                this.productionStore.departmentRawMaterials   = departmentRawMaterials.data

                this.productionStore.finishProductInModal     = false

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
        async productChanged(raw_material_balance_id, rowIndex) {
            // const raw_material = await this.callApi('get', '/app/get_production_rawmaterial/'+raw_material_id+'/'+this.productionStore.department_id)
            const raw_material = await this.callApi('get', '/app/get_rm_rawmaterial_balance/'+raw_material_balance_id)
            this.selectedProduct[rowIndex].count = raw_material.data.count
        },
        async countChanged(rowIndex){
            let count       = parseInt(this.productionStore.outgoingRawMaterials[rowIndex].count)
            let stockCount  = parseInt(this.selectedProduct[rowIndex].count)

            if(count < 1) {
                this.raw_material_count_err[rowIndex].message = "Тўлдириш мажбурий!"
                this.raw_material_count_err[rowIndex].display = true
            } else if(count > stockCount) {
                this.raw_material_count_err[rowIndex].message = "Омборда бунча махсулот йўқ!"
                this.raw_material_count_err[rowIndex].display = true
            } else {
                this.raw_material_count_err[rowIndex].message = ""
                this.raw_material_count_err[rowIndex].display = false
            }
        },
        handleAdd () {

            this.productionStore.outgoingRawMaterials.push({raw_material_balance_id: '', count:''});

            this.selectedProduct.push({count: 0});

            this.raw_material_id_err.push(false)
            this.raw_material_count_err.push({display:false, message: ""})
        },
        handleRemove(index) {
            if(Object.keys(this.productionStore.outgoingRawMaterials).length > 1){
                this.productionStore.outgoingRawMaterials.splice(index, 1);
                this.selectedProduct.splice(index, 1);
            } else {
                return this.err('Қаторни ўчираолмайсиз!')
            }
        },
    }
}
</script>
