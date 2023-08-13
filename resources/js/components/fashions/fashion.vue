<template>
    <FashionHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->

    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious bg-transparent border p-3">
        <Row :gutter="8">
            <Col span="3">
                <router-link :to="{ name: 'fashions'}" class="nav-link"><Icon type="ios-arrow-back" size="16" /> Орқага қайтиш</router-link>
            </Col>
            <Col span="17" offset="1">
                <ButtonGroup>
                    <Button type="primary" class="" ghost><strong>Модел: {{fashionsStore.fashion.name}}</strong></Button>
                    <Button type="primary" class="" ghost><strong>Коди: {{fashionsStore.fashion.code}}</strong></Button>
                </ButtonGroup>
                <ButtonGroup>
                    <Button type="error" class="ml-4" ghost><strong class="text-primary">Битта махсулот калкуляцияси: </strong><strong>{{calculateModel.toLocaleString()}} сўм</strong></Button>
                </ButtonGroup>
            </Col>
            <Col span="3">
                <Button type="primary" @click="fashionsStore.addFashionDetailsModal = true" class="text-white"><Icon type="md-add" class="mr-1" />Қисим қўшиш</Button>
            </Col>
        </Row>
    </div>
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <Row :gutter="8">
            <Col span="24">
                <Row><Col span="3">Модел калькуляцияси</Col></Row>
                <Table size="small" :columns="columns" :data="fashionsStore.fashionDetails" :loading="loading">
                    <template #total="{row}">
                        {{ formatPrice(row.count * row.price) }}
                    </template>
                </Table>
            </Col>
        </Row>
    </div>

    <FashionDetailsAddModal />
    <FashionDetailsEditModal />
    <FashionDetailsDeleteModal />

</template>

<script>
    import FashionHeader from './parts/header.vue'
    import {useFashionsStore} from "../../stores/FashionsStore";
    import {resolveComponent} from "vue";

    import FashionDetailsAddModal from "./parts/fashion_details/addModal.vue"
    import FashionDetailsEditModal from "./parts/fashion_details/editModal.vue"
    import FashionDetailsDeleteModal from "./parts/fashion_details/deleteModal.vue"

    export default {
        name: "fashions",
        components: {
            FashionHeader: FashionHeader,

            FashionDetailsAddModal:FashionDetailsAddModal,
            FashionDetailsEditModal:FashionDetailsEditModal,
            FashionDetailsDeleteModal:FashionDetailsDeleteModal,
        },
        setup() {
            const fashionsStore = useFashionsStore()
            return { fashionsStore }
        },
        computed: {
            calculateModel(){
                let total = 0
                this.fashionsStore.fashionDetails.forEach((product, index) => {
                    total += product.count * product.price
                })

                return this.formatPrice(total)
            }
        },
        data() {
            return {
                fashion_id: '',
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
                        title: "Ўлчов бирлиги",
                        key: 'measurement',
                        sortable: true
                    },
                    {
                        title: "Миқдори",
                        key: 'count',
                        sortable: true
                    },
                    {
                        title: "Нарх",
                        key: 'price',
                        sortable: true
                    },
                    {
                        title: "Қиймати",
                        slot: 'total',
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
                                            title:"Taxrirlash",
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
                                            title:"O'chirish",
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
            showEditModal(fashionDetail, index) {
                let fashionDetailObj = {
                    id:         fashionDetail.id,
                    fashion_id: fashionDetail.fashion_id,
                    product_id: fashionDetail.product_id,
                    count:      fashionDetail.count,
                    price:      fashionDetail.price,
                }

                this.fashionsStore.fashionDetail = fashionDetailObj
                this.fashionsStore.rowIndex = index
                this.fashionsStore.editFashionDetailsModal = true
            },
            showDeletingModal(fashionDetail, i){
                this.fashionsStore.fashionDetail = fashionDetail
                this.fashionsStore.rowIndex      = i
                this.fashionsStore.deleteFashionDetailsModal   = true
            },
        },
        async created(){
            this.fashion_id                     = this.$route.params.fashion_id

            const fashionDetails                = await this.callApi('get', '/app/get_fashion_details/'+this.fashion_id)
            this.fashionsStore.fashionDetails   = fashionDetails.data

            const fashion                       = await this.callApi('get', '/app/get_fashion/'+this.fashion_id)
            this.fashionsStore.fashion          = fashion.data

            const products                      = await this.callApi('get', '/app/get_products/'+2)
            this.fashionsStore.products         = products.data

            this.loading = false
        }
    }
</script>



