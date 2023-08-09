<template>
    <Modal
        v-model="rmStore.productInModal"
        title="Махсулот қўшиш"
        :mask-closable = "false"
        :closable = "false"
        width="960"
        @on-visible-change="rmStore.modalVisibility"
    >
        <div class="blink"><span>Доллар курсини янгилашни унутмадингизми?</span></div>
        <Form ref="formValidate">
            <Row :gutter="8" class="ivu-mt-4 ivu-mb-4">
                <Col span="12">
                    <FormItem label="">
                        <DatePicker v-model="rmStore.created_date" format="yyyy-MM-dd" type="date"
                                    style="width: 225px;"
                                    placeholder="Санани танланг" />
                        <Text :class="{ 'd-none': !created_date_err}" type="danger"> Тўлдириш мажбурий!</Text>
                    </FormItem>
                </Col>
            </Row>

            <Row :gutter="8" class="modal-table-header">
                <Col span="6">* Махсулот номи</Col>
                <Col span="6">* Валюта</Col>
                <Col span="6">* Нархи</Col>
                <Col span="5">* Миқдор</Col>
                <Col span="1"></Col>
            </Row>
            <template v-for="(productField, index) in rmStore.incomeProducts" :key="index">
                <Row :gutter="8" border="bottom" class="ivu-mb">
                    <Col span="6">
                        <FormItem>
                            <Select v-model="rmStore.incomeProducts[index].product_id" autofocus filterable placeholder="Махсулотни танланг...">
                                <Option v-for="product in rmStore.products" :value="product.id" :key="product.id">{{ product.name + " (" + product.code + ", " + product.country + ")" }}</Option>
                            </Select>
                            <Text :class="{ 'd-none': !product_id_err[index] }" type="danger">Тўлдириш мажбурий!</Text>
                        </FormItem>
                    </Col>
                    <Col span="6">
                        <FormItem>
                            <Select v-model="rmStore.incomeProducts[index].currency_id" placeholder="Валютани танланг...">
                                <Option v-for="currency in rmStore.currencies" :value="currency.id" :key="currency.id">{{ currency.name}}</Option>
                            </Select>
                            <Text :class="{ 'd-none': !currency_id_err[index] }" type="danger">Тўлдириш мажбурий!</Text>
                        </FormItem>
                    </Col>
                    <Col span="6">
                        <FormItem>
                            <Input v-model="rmStore.incomeProducts[index].price_value" placeholder="Махсулот Нархи"></Input>
                            <Text :class="{ 'd-none': !product_price_err[index] }" type="danger">Тўлдириш мажбурий!</Text>
                        </FormItem>
                    </Col>
                    <Col span="5">
                        <FormItem>
                            <Input v-model="rmStore.incomeProducts[index].count" placeholder="Махсулот Миқдор"></Input>
                            <Text :class="{ 'd-none': !product_count_err[index] }" type="danger">Тўлдириш мажбурий!</Text>
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
            <Button @click="rmStore.productInModal = false">Беркитиш</Button>
            <Button type="primary" @click="addIncomeProducts" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Махсулот...':'Сақлаш'}}</Button>
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

            inoutSet: {
                from_department_id: 1,
                to_department_id: 2,
            },

            created_date_err:       false,
            product_id_err:         [false],
            currency_id_err:        [false],
            product_price_err:      [false],
            product_count_err:      [false],
            is_invalid: false,

            blink: true
        }
    },
    methods: {
        async addIncomeProducts() {
            this.isAdding = true
            if(this.rmStore.created_date == '') {
                this.is_invalid = true
                this.created_date_err = true
            } else {
                this.created_date_err = false
            }

            this.rmStore.incomeProducts.forEach((row, index) => {
                //check product_id
                if(row.product_id == '') {
                    this.is_invalid = true
                    this.product_id_err[index] = true
                } else {
                    this.product_id_err[index] = false
                }

                if(row.currency_id === '') {
                    this.is_invalid     = true
                    this.currency_id_err[index]  = true
                } else {
                    this.currency_id_err[index]  = false
                }

                //check product price
                if(row.price_value == '') {
                    this.is_invalid = true
                    this.product_price_err[index] = true
                } else {
                    this.product_price_err[index] = false
                }

                //check product count
                if(row.count == '') {
                    this.is_invalid = true
                    this.product_count_err[index] = true
                } else {
                    this.product_count_err[index] = false
                }

            });

            if(this.is_invalid) {
                this.is_invalid = false
                this.isAdding = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            let incomeDetails = {
                inoutSet: this.inoutSet,
                products: this.rmStore.incomeProducts,
                department_id: this.rmStore.department_id,
                created_date: this.myDateFormat(this.rmStore.created_date, "yyyy-mm-dd"),
            }

            console.log(incomeDetails)
            const res = await this.callApi('post', '/app/add_rmset_income_products', incomeDetails)
            console.log(res.data)

            if(res.status === 200) {
                this.isAdding = false
                this.rmStore.rmSets.unshift(res.data);
                const departmentRawMaterials = await this.callApi('get', '/app/get_rm_rawmaterials/'+this.rmStore.department_id)
                this.rmStore.departmentRawMaterials = departmentRawMaterials.data
                this.rmStore.incomeProducts = [{product_id: '', currency_id: '', price_value: '', count:''}]

                this.rmStore.productInModal = false

                this.s('Махсулотлар мувоффақиятли қўшилди!')

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
            this.rmStore.incomeProducts.push(
                {
                    product_id: '',
                    currency_id: '',
                    price_value: '',
                    count:''
                }
            );
        },
        handleRemove(index) {

            if(Object.keys(this.rmStore.incomeProducts).length > 1){
                this.rmStore.incomeProducts.splice(index, 1);
            } else {
                return this.err('Қаторни ўчираолмайсиз!')
            }
        }
    },
    async created(){

    }
}
</script>
