<template>
    <StockHeader />
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <Form label-position="top" class="mb-2">
            <Row :gutter="8">
                <Col span="10"></Col>
                <Col span="2" class="text-right pt-2">
                    <span>қидирув:</span>
                </Col>
                <Col span="8">
                    <Input v-model="keywordName" placeholder="Махсулот номи..." />
                </Col>
                <Col span="4">
                    <Input v-model="keywordCode" placeholder="Махсулот коди..." />
                </Col>

            </Row>
        </Form>

        <ul class="nav nav-tabs bg-light">
            <li class="nav-item" :class="{ 'router-link-active': subIsActive('/raw-materials/1') }">
                <router-link :to="{ name: 'rawMaterials', params: {category_id: 1} }" class="nav-link"> Материаллар</router-link>
            </li>
            <li class="nav-item" :class="{ 'router-link-active': subIsActive('/raw-materials/2') }">
                <router-link :to="{ name: 'rawMaterials', params: {category_id: 2} }" class="nav-link">Фурнитура</router-link>
            </li>
        </ul>
        <Table height="500" size="small" :row-class-name="fillBgColor" :columns="columns" :data="filteredStockProducts" :loading="loading">
            <template #count="{row}">
                {{ row.count == null ? 0:row.count }}
            </template>
        </Table>
    </div>
</template>

<script>
import StockHeader from './parts/header.vue'
import {useRawMaterialsStore} from "../../stores/RawMaterialsStore";
import {resolveComponent} from "vue";

export default {
    name: "Raw Materials",
    components: {
        StockHeader:StockHeader,
    },
    setup() {
        const rmStore = useRawMaterialsStore()

        return { rmStore }
    },
    computed: {
        filteredStockProducts(){
            let productsTmp = this.rmStore.rmStockProducts

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

        }
    },
    data() {
        return {
            keywordName: '',
            keywordCode: '',
            category_id: 1,

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
                    width: "200",
                    sortable: true
                },
                {
                    title: "Ранги",
                    key: 'color',
                },
                {
                    title: "Давлат",
                    key: 'country',
                },
                {
                    title: "Бирлиги",
                    key: 'measurement',
                },
                {
                    title: "Миқдор",
                    slot: 'count',
                    sortable: true,
                    filters: [
                        {
                            label: 'Бўш эмас',
                            value: 1
                        },
                    ],
                    filterMultiple: false,
                    filterMethod (value, row) {
                        if(value === 1) {
                            return row.count !== null
                        }
                    },
                },
                {
                    title: "Минимал",
                    key: 'min_count',
                }

            ],
        }
    },
    methods: {
        fillBgColor(row, index){
            if(row.count < 1) {
                return "table-error-row"
            } else if(row.count < row.min_count){
                return "table-warning-row"
            }
            return '';
        },
        subIsActive(input) {
            const paths = Array.isArray(input) ? input : [input]
            return paths.some(path => {
                return this.$route.path.indexOf(path) === 0 // current path starts with this path string
            })
        },
    },
    async created() {
        document.title = "Хомашё Омбори"

        this.category_id = this.$route.params.category_id
        if(this.category_id === "") {
            this.category_id = 1
        }

        const rmStockProducts = await this.callApi('get', '/app/get_rmstock_products/'+this.category_id)
        this.rmStore.rmStockProducts = rmStockProducts.data

        this.loading = false
    }
}
</script>
