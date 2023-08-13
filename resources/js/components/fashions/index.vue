<template>
    <FashionHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <Row :gutter="8" border="bottom" class="mb-3">
            <Col span="3">
                <Button type="primary" @click="fashionsStore.addModal = true" class="text-white"><Icon type="md-add" class="mr-1" />Қўшиш</Button>
            </Col>
            <Col span="6"></Col>
        </Row>

        <Row :gutter="8">
            <Col span="24">
                <Table size="small" :columns="columns" :data="fashionsStore.fashions" :loading="loading">
                    <template #created_at="{row}">
                        {{ myDateFormat(row.created_at, "MM.DD.YYYY") }}
                    </template>
                </Table>
            </Col>
        </Row>
    </div>

    <!-- Add fashion modal -->
    <FashionAddModal />

    <!-- Edit fashion modal -->
    <FashionEditModal />

    <!-- Delete tag modal -->
    <FashionDeleteModal />

</template>

<script>
import FashionHeader from './parts/header.vue'
import {useFashionsStore} from "../../stores/FashionsStore";
import {resolveComponent} from "vue";

import FashionAddModal from "./parts/addModal.vue"
import FashionEditModal from "./parts/editModal.vue"
import FashionDeleteModal from "./parts/deleteModal.vue"
export default {
    name: "fashions",
    components: {
        FashionHeader: FashionHeader,

        FashionAddModal:FashionAddModal,
        FashionEditModal:FashionEditModal,
        FashionDeleteModal:FashionDeleteModal,
    },
    setup() {
        const fashionsStore = useFashionsStore()

        return { fashionsStore }
    },
    data() {
        return {
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
                    title: "Тури",
                    key: 'category_name',
                    sortable: true
                },
                {
                    title: "Изох",
                    key: 'description',
                    sortable: true
                },
                {
                    title: "Сана",
                    slot: 'created_at',
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
                                        title:"Ko'rish",
                                        type: 'success',
                                        size: 'small',
                                        to: { name: 'fashion', params: {fashion_id:params.row.id} },

                                    }, {
                                        default() {
                                            return h(resolveComponent('Icon'), {
                                                type: 'md-eye',
                                                color: '#ffffff',
                                            })
                                        }
                                    }),

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
        showEditModal(fashion, index) {
            let fashionObj = {
                id:             fashion.id,
                code:           fashion.code,
                name:           fashion.name,
                description:    fashion.description,
                fashion_category_id:    fashion.fashion_category_id
            }

            this.fashionsStore.fashion = fashionObj
            this.fashionsStore.rowIndex = index
            this.fashionsStore.editModal = true
        },
        showDeletingModal(fashion, i){
            this.fashionsStore.fashion      = fashion
            this.fashionsStore.rowIndex     = i
            this.fashionsStore.deleteModal  = true
        },
    },
    async created(){
        const categories = await this.callApi('get', '/app/get_fashion_categories')
        this.fashionsStore.categories = categories.data

        const fashions = await this.callApi('get', '/app/get_fashions')
        this.fashionsStore.fashions = fashions.data
        this.loading = false
    }
}
</script>



