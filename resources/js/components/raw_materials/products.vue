<template>
    <StockHeader />
            <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
            <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
                <Form label-position="top" class="mb-2">
                    <Row :gutter="8">
                        <Col span="10">
                            <Button type="primary" @click="rmStore.productAddModal = true" class="ivu-mr"><Icon type="md-add" />Махсулот қўшиш</Button>
                        </Col>
                        <Col span="2" class="text-right pt-2">
                            <span>қидирув:</span>
                        </Col>
                        <Col span="8">
                            <Input v-model="rmStore.keywordName" placeholder="Махсулот номи..." />
                        </Col>
                        <Col span="4">
                            <Input v-model="rmStore.keywordCode" placeholder="Махсулот коди..." />
                        </Col>

                    </Row>
                </Form>

                <Table size="small" :columns="columns" :data="rmStore.filteredProducts" :loading="loading"></Table>

            </div>

    <!-- Add product modal -->
    <ProductAddModal />

    <!-- Edit tag modal -->
    <ProductEditModal />

    <!-- Delete tag modal -->
    <ProductDeleteModal />
</template>

<script>
import StockHeader from './parts/header.vue'
import {useRawMaterialsStore} from "../../stores/RawMaterialsStore";
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

    setup() {
        const rmStore = useRawMaterialsStore()

        return { rmStore }
    },
    data(){
        return {
            editDepartmentModal : false,
            editDepartmentData: {
                product_id: 0,
                department_id: [],
            },
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
                    title: "Ранги",
                    key: 'color',
                    sortable: true
                },
                {
                    title: "Давлат",
                    key: 'country',
                    sortable: true
                },
                {
                    title: "Бирлиги",
                    key: 'measurement',
                    width: "160",
                    sortable: true
                },
                {
                    title: "Минимал",
                    key: 'min_count',
                    width: "160",
                    sortable: true
                },
                {
                    title: "Категория",
                    key: 'catetory_name',
                    width: "160",
                    sortable: true
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
                                        title:"Дубликат",
                                        type: 'success',
                                        size: 'small',
                                        onClick: () => {
                                            this.duplicateProductModal(params.row, params.index)
                                        }
                                    }, {
                                        default() {
                                            return h(resolveComponent('Icon'), {
                                                type: 'ios-browsers-outline'
                                            })
                                        }
                                    }),

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
                color:          product.color,
                country:        product.country,
                measurement:    product.measurement,
                description:    product.description,
                min_count:      product.min_count,
                category_id:    product.category_id,
            }

            this.rmStore.product = productObj
            this.rmStore.rowIndex = i
            this.rmStore.productEditModal = true
        },

        async showDeletingModal(product, i){
            this.rmStore.product            = product
            this.rmStore.rowIndex           = i
            this.rmStore.productDeleteModal = true
        },

        duplicateProductModal(product){
            let productObj = {
                id:             product.id,
                name:           product.name,
                code:           product.code,
                color:          product.color,
                country:        product.country,
                measurement:    product.measurement,
                description:    product.description,
                min_count:      product.min_count,
                category_id:    product.category_id,
            }

            this.rmStore.product         = productObj
            this.rmStore.productAddModal = true
        }
    },

    async created(){
        document.title = "Хомашё Омбори | Махсулотлар"

        const [products, categories, measurements] = await Promise.all([
            this.callApi('get', '/app/get_products/'+this.rmStore.department_id),
            this.callApi('get', '/app/get_product_categories'),
            this.callApi('get', '/app/get_measurements')
        ])

        this.rmStore.products = products.data
        this.rmStore.categories = categories.data
        this.rmStore.measurements = measurements.data

        this.loading = false
    },
}
</script>
