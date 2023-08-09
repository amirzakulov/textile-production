<template>
    <StockHeader />
            <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
            <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
                <Form label-position="top" class="mb-2">
                    <Row :gutter="8">
                        <Col span="10">
                            <Button type="primary" @click="productionStore.productAddModal = true" class="ivu-mr"><Icon type="md-add" />Махсулот қўшиш</Button>
                        </Col>
                        <Col span="2" class="text-right pt-2">
                            <span>қидирув:</span>
                        </Col>
                        <Col span="8">
                            <Input v-model="keywordName" clearable placeholder="Махсулот номи..." />
                        </Col>
                        <Col span="4">
                            <Input v-model="keywordCode" clearable placeholder="Махсулот коди..." />
                        </Col>
                    </Row>
                </Form>

                <Table size="small" :columns="columns" :data="filteredProducts" :loading="loading"></Table>

            </div>

    <ProductAddModal />
    <ProductEditModal />
    <ProductDeleteModal />
</template>

<script>
import StockHeader from './parts/header.vue'
import {useProductionStore} from "../../stores/ProductionStore";
import {resolveComponent} from "vue";

import ProductAddModal from './parts/products/addModal.vue'
import ProductEditModal from './parts/products/editModal.vue'
import ProductDeleteModal from './parts/products/deleteModal.vue'

export default {
    name: "rm products",
    components: {
        StockHeader:StockHeader,
        ProductAddModal:ProductAddModal,
        ProductEditModal:ProductEditModal,
        ProductDeleteModal:ProductDeleteModal,
    },
    setup(){
        const productionStore = useProductionStore()
        return { productionStore }
    },
    computed: {
        filteredProducts(){
            let productsTmp = this.productionStore.departmentProducts

            if (this.keywordName != '' && this.keywordName) {
                productsTmp = productsTmp.filter((product) => {
                    return product.name.toLowerCase().includes(this.keywordName.toLowerCase())
                })
            }

            if (this.keywordCode != '' && this.keywordCode) {
                productsTmp = productsTmp.filter((product) => {
                    return product.code.toLowerCase().includes(this.keywordCode.toLowerCase())
                })
            }

            return productsTmp

        },
    },
    data(){
        return {
            keywordName: '',
            keywordCode: '',
            loading: true,
            columns: [
                {
                    title: "Код",
                    key: 'code',
                    sortable: true
                },
                {
                    title: "Номи",
                    key: 'name',
                    sortable: true
                },
                {
                    title: "Бирлиги",
                    key: 'measurement',
                    width: "160",
                },
                {
                    title: "Модел",
                    key: 'fashion_name',
                    width: "160",
                },
                {
                    title: "action",
                    key: 'action',
                    width: 180,
                    align: 'right',
                    render: (h, params) => {

                        return h('div', {
                                class: 'ivu-btn-group ivu-btn-group-default'
                            },
                            [
                                h(resolveComponent('Button'),
                                    {
                                        title:"Тахрирлаш",
                                        type: 'primary',
                                        size: 'small',

                                        onClick: () => {
                                            this.showEditModal(params.row, params.index)
                                        }
                                    }, {
                                        default() {
                                            return h(resolveComponent('Icon'), {
                                                type: 'md-create'
                                            })
                                        }
                                    }),

                                h(resolveComponent('Button'),
                                    {
                                        title:"Ўчириш",
                                        type: 'error',
                                        size: 'small',
                                        loading: params.row.isDeleting,

                                        onClick: () => {
                                            this.showDeletingModal(params.row, params.index)
                                        }
                                    }, {
                                        default() {
                                            return h(resolveComponent('Icon'), {
                                                type: 'md-close'
                                            })
                                        }
                                    }),
                            ])
                    }
                },

            ],
        }
    },

    methods : {

        showEditModal(product, i) {
            let productObj = {
                id:             product.id,
                name:           product.name,
                code:           product.code,
                measurement:    product.measurement,
                fashion_id:     product.fashion_id,
            }

            this.productionStore.departmentProduct = productObj
            this.productionStore.rowIndex = i
            this.productionStore.productEditModal = true
        },

        async showDeletingModal(product, i){
            this.productionStore.departmentProduct  = product
            this.productionStore.rowIndex           = i
            this.productionStore.productDeleteModal = true
        },

    },

    async created(){
        document.title = "Хомашё Омбори | Махсулотлар"
        this.productionStore.department_id  = this.$route.params.department_id
        const [departmentProducts, measurements, fashions] = await Promise.all([
            this.callApi('get', '/app/get_production_department_products/'+this.productionStore.department_id),
            this.callApi('get', '/app/get_measurements'),
            this.callApi('get', '/app/get_fashions'),
        ])

        this.productionStore.departmentProducts = departmentProducts.data
        this.productionStore.measurements   = measurements.data
        this.productionStore.fashions       = fashions.data

        this.loading = false
    },
}
</script>
