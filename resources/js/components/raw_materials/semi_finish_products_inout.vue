<template>
    <ProductionHeader />

    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <Row :gutter="8" border="bottom" class="mb-3">
            <Col span="6">
                <ButtonGroup>
                    <Button type="error" @click="openRmSemiFinishProductsOutModal"><Icon type="md-remove" /> Чиқим</Button>
                </ButtonGroup>
            </Col>
        </Row>
        <InoutByInoutSets />
    </div>

    <SemiFinishProductOutModal />
    <SemiFinishProductDeleteOutModal />
</template>

<script>
import ProductionHeader from './parts/header.vue'
import { useRawMaterialsStore} from "../../stores/RawMaterialsStore";

import InoutByInoutSets from './parts/semi_finish_products_inout/table_by_inout_set.vue'

import SemiFinishProductOutModal from "./parts/semi_finish_products_inout/outModal"
import SemiFinishProductDeleteOutModal from "./parts/semi_finish_products_inout/deleteSetModal"

export default {
    name: "Production Raw Materials Inout",
    setup(){
        const rmStore = useRawMaterialsStore()

        return { rmStore }
    },
    components: {
        ProductionHeader:                ProductionHeader,
        InoutByInoutSets:                InoutByInoutSets,

        SemiFinishProductOutModal:       SemiFinishProductOutModal,
        SemiFinishProductDeleteOutModal: SemiFinishProductDeleteOutModal,

    },
    data() {
        return {}
    },
    methods: {
        openRmSemiFinishProductsOutModal(){
            this.rmStore.created_date = this.myDateFormat(null, "yyyy-mm-dd")
            this.rmStore.rmSemiFinishProductsOutModal  = true
        }
    },
    async created(){
        document.title = "Хомашё Омбори | Ярим тайёр махсулотлар"

        let department_types = {types: [2,3]}
        const [departments, rmSemiFinishProducts] = await Promise.all([
            this.callApi('post', '/app/get_departments_types', department_types),
            this.callApi('get', '/app/get_rm_semi_finish_products/'+this.rmStore.department_id),
        ])

        this.rmStore.departments                  = departments.data
        this.rmStore.rmSemiFinishProducts         = rmSemiFinishProducts.data
    },
}
</script>



