<template>
    <UsersHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <p class="_title0">
            <Button type="primary" @click="addModal=true" class="text-white"><Icon type="md-add-circle" class="mr-1" />Q'shish</Button>
        </p>

        <Row>
            <Col span="6">
                <div class="_overflow _table_div">
                    <table class="_table">
                        <thead>
                        <tr class="blue_thead bordered">
                            <th class="align-top">#ID</th>
                            <th>Nomi</th>
                            <th class="text-right">action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(role, i) in roles" :key="i" v-if="roles.length">
                            <td>{{role.id}}</td>
                            <td>{{role.roleName}}</td>
                            <td class="text-right">
                                <ButtonGroup>
                                    <Button type="primary" size="small" title="Taxrirlash" @click="showEditModal(role, i)"><Icon type="md-create"></Icon></Button>
                                    <Button type="error" size="small" title="O'chirish" @click="showDeletingModal(role, i)" :loading="role.isDeleting"><Icon type="md-close"></Icon></Button>
                                </ButtonGroup>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </Col>
        </Row>
        
    </div>


    <!-- Add user modal -->
    <Modal
        v-model="addModal"
        title="Foydalanuvchi turi qo'shish"
        :mask-closable = "false"
        :closable = "false"
        width="600"
    >

        <Form ref="formValidate" :label-width="150">
            <FormItem label="* Nomi" class="ivu-mb">
                <Input v-model="data.roleName" placeholder="Nomi"></Input>
                <Text :class="{ 'd-none': !role_name_err }" type="danger">Tanlash majburiy!</Text>
            </FormItem>
        </Form>
        <template #footer>
            <Button type="error" @click="addModal=false">Беркитиш</Button>
            <Button type="primary" @click="addRole" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Nomi...':'Saqlash'}}</Button>
        </template>
    </Modal>

    <!-- Edit user modal -->
    <Modal
        v-model="editModal"
        title="Foydalanuvchi turini taxrirlash"
        :mask-closable = "false"
        :closable = "false"
        width="600"
    >

        <Form ref="formValidate" :label-width="150">

            <FormItem label="* Nomi" class="ivu-mb">
                <Input v-model="editData.roleName" placeholder="Nomi" />
            </FormItem>
        </Form>
        <template #footer>
            <Button type="error" @click="editModal=false">Беркитиш</Button>
            <Button type="primary" @click="editRole" :disabled="isEditing" :loading="isEditing">{{isAdding ? 'Nomi...':'Saqlash'}}</Button>
        </template>
    </Modal>

    <!-- Delete tag modal -->
    <Modal v-model="showDeleteModal" width="360">
        <template #header>
            <p style="color:#f60;text-align:center">
                <Icon type="ios-close-circle" size="20" />
                <span>O'chirishni tasdiqlash!</span>
            </p>
        </template>
        <div style="text-align:center">
            <p>Xaqiqatdan ham o'chirasizmi?</p>
        </div>
        <template #footer>
            <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteRole">ХА</Button>
        </template>
    </Modal>
</template>

<script>
import UsersHeader from './parts/header.vue'
export default {
    name: "Roles",
    components: {
        UsersHeader:UsersHeader
    },
    data(){
        return {
            data: {
                roleName: '',
            },
            editData: {
                id: -1,
                roleName: '',
            },
            addModal : false,
            editModal : false,
            isAdding : false,
            isEditing : false,
            index: -1,
            showDeleteModal: false,
            isDeleting: false,
            deletingIndex: -1,
            roles: [],
            role: {},
            role_name_err: false,

        }
    },
    methods : {
        async addRole(){ //Add product

            if(this.data.roleName.trim() === '')    return this.err('Nomi majburiy.')

            const res = await this.callApi('post', '/app/add_role', this.data)

            if(res.status === 201) {
                this.roles.unshift(res.data);
                this.s('Foydalanuvchi turi muvoffaqiyatli qo\'shildi!')
                this.addModal = false
                this.data.roleName = ''
            } else {
                if(res.status == 422) {
                    if(res.data.errors.roleName) {
                        this.err(res.data.errors.roleName[0])
                    }
                } else {
                    this.swr()
                }
            }
        },

        async editRole(){ //edit product
            if(this.editData.roleName.trim() === '')    return this.err('Nomi majburiy.')

            const res = await this.callApi('post', '/app/edit_role', this.editData)

            if(res.status === 200) {

                this.roles[this.index].roleName  = res.data.roleName

                this.s('Foydalanuvchi turi ma\'lumotlari muvoffaqiyatli saqlandi!')
                this.editModal = false
            } else {
                if(res.status == 422) {
                    if(res.data.errors.roleName) {
                        this.err(res.data.errors.roleName[0])
                    }

                } else {
                    this.swr()
                }
            }
        },

        async showEditModal(role, index) {
            let roleObj = {
                id:       role.id,
                roleName: role.roleName,
            }

            this.editData = roleObj
            this.editModal = true
            this.index = index
        },
        editModalClose(){
            this.editModal = false
        },


        async deleteRole() {
            this.isDeleting = true
            const res = await this.callApi("post", "/app/delete_role", this.role)
            if(res.status === 200) {
                this.roles.splice(this.deletingIndex,1)
                this.s('Foydalanuvchi turi o\'chirildi!')
            } else {
                this.swr();
            }

            this.isDeleting = false
            this.showDeleteModal = false
        },

        async showDeletingModal(role, i){
            this.role               = role
            this.deletingIndex      = i
            this.showDeleteModal    = true
        },
    },

    async created(){
        const roles = await this.callApi('get', '/app/get_roles')
        this.roles = roles.data

    },
}
</script>
