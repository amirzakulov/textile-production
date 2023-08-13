<template>
    <UsersHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <p class="_title0">
            <Button type="primary" @click="addModal=true" class="text-white"><Icon type="md-add-circle" class="mr-1" />Foydalanuvchi q'shish</Button>
        </p>

        <Row>
            <Col span="14">
                <div class="_overflow _table_div">
                    <table class="_table">
                        <thead>
                        <tr class="blue_thead bordered">
                            <th class="align-top">#ID</th>
                            <th>Ism</th>
                            <th>username</th>
                            <th>Turi</th>
                            <th>Ombor</th>
                            <th class="text-right">action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="(user, u) in users" :key="u" v-if="users.length">
                            <tr v-if="loggedUser.role_id == 1">
                                <td>{{user.id}} </td>
                                <td>{{user.fullName}}</td>
                                <td>{{user.username}}</td>
                                <td>{{user.roleName}}</td>
                                <td>{{user.storeName}}</td>
                                <td class="text-right" width="200">
                                    <ButtonGroup>
                                        <Button type="primary" size="small" title="Taxrirlash" @click="showEditModal(user, u)"><Icon type="md-create"></Icon></Button>
                                        <Button type="error" size="small" title="O'chirish" @click="showDeletingModal(user, u)" :loading="user.isDeleting"><Icon type="md-close"></Icon></Button>
                                    </ButtonGroup>
                                </td>
                            </tr>
                            <tr v-else-if="loggedUser.role_id != 1 && user.role_id != 1">
                                <td>{{user.id}} </td>
                                <td>{{user.fullName}}</td>
                                <td>{{user.username}}</td>
                                <td>{{user.roleName}}</td>
                                <td>{{user.storeName}}</td>
                                <td class="text-right" width="200">
                                    <ButtonGroup>
                                        <Button type="primary" size="small" title="Taxrirlash" @click="showEditModal(user, u)"><Icon type="md-create"></Icon></Button>
                                        <Button type="error" size="small" title="O'chirish" @click="showDeletingModal(user, u)" :loading="user.isDeleting"><Icon type="md-close"></Icon></Button>
                                    </ButtonGroup>
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>
            </Col>
        </Row>
        
    </div>


    <!-- Add user modal -->
    <Modal
        v-model="addModal"
        title="Foydalanuvchi qo'shish"
        :mask-closable = "false"
        :closable = "false"
        width="600"
    >

        <Form ref="formValidate" :label-width="150">
            <FormItem label="* Ism" class="ivu-mb">
                <Input v-model="data.fullName" placeholder="Ism"></Input>
            </FormItem>
            <FormItem label="* Turi" class="ivu-mb">
                <Select v-model="data.role_id" placeholder="Turi">
                    <template v-for="(role, i) in roles" :key="i" >
                        <Option v-if="loggedUser.role_id == 1" :value="role.id">{{ role.roleName }}</Option>
                        <Option v-else-if="loggedUser.role_id != 1 && role.id != 1" :value="role.id">{{ role.roleName }}</Option>
                    </template>
                    
                </Select>
            </FormItem>
            <FormItem label="* Do'kon" class="ivu-mb">
                <Select v-model="data.store_id" placeholder="Do'kon">
                    <template v-for="(store, i) in stores" :key="i" >
                        <Option :value="store.id">{{ store.name }}</Option>
                    </template>
                    
                </Select>
            </FormItem>
            <FormItem label="* Foydalanuvchi" class="ivu-mb">
                <Input v-model="data.username" placeholder="Username"></Input>
            </FormItem>
            <FormItem label="* Parol" class="ivu-mb">
                <Input v-model="data.password" placeholder="Parol"></Input>
            </FormItem>
            
        </Form>
        <template #footer>
            <Button type="error" @click="addModal=false">Беркитиш</Button>
            <Button type="primary" @click="addUser" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>

    <!-- Edit user modal -->
    <Modal
        v-model="editModal"
        title="Foydalanuvchini taxrirlash"
        :mask-closable = "false"
        :closable = "false"
        width="600"
    >

        <Form ref="formValidate" :label-width="150">

            <FormItem label="* Ism" class="ivu-mb">
                <Input v-model="editData.fullName" placeholder="Ism"></Input>
            </FormItem>
            <FormItem label="* Turi" class="ivu-mb">
                <Select v-model="editData.role_id" placeholder="Turi">
                    <template v-for="(role, i) in roles" :key="i" >
                        <Option v-if="loggedUser.role_id == 1" :value="role.id">{{ role.roleName }}</Option>
                        <Option v-else-if="loggedUser.role_id != 1 && role.id != 1" :value="role.id">{{ role.roleName }}</Option>
                    </template>
                </Select>
            </FormItem>
            <FormItem label="* Do'kon" class="ivu-mb">
                <Select v-model="editData.store_id" placeholder="Do'kon">
                    <template v-for="(store, i) in stores" :key="i" >
                        <Option :value="store.id">{{ store.name }}</Option>
                    </template>
                    
                </Select>
            </FormItem>
            <FormItem label="* Username" class="ivu-mb">
                <Input v-model="editData.username" placeholder="Username"></Input>
            </FormItem>
            
            <Divider/>
            <small>Agar parolni o'zgartirishni xoxlamasangiz, katakchani bo'sh qoldiring!</small>
            <FormItem label="Parol" class="ivu-mb">
                <Input v-model="editData.password" placeholder="Parol"></Input>
            </FormItem>
        </Form>
        <template #footer>
            <Button type="error" @click="editModal=false">Беркитиш</Button>
            <Button type="primary" @click="editUser" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Махсулот...':'Сақлаш'}}</Button>
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
            <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteUser">ХА</Button>
        </template>
    </Modal>
</template>

<script>
import UsersHeader from './parts/header.vue'
export default {
    name: "users",
    // props: ['loggedUser'],
    components: {
        UsersHeader:UsersHeader
    },
    data(){
        return {
            data: {
                fullName: '',
                role_id: '',
                store_id: '',
                username: '',
                password: '',
            },
            editData: {
                id: -1,
                fullName: '',
                role_id: '',
                store_id: '',
                username: '',
                password: '',
            },
            addModal : false,
            editModal : false,
            isAdding : false,
            index: -1,
            showDeleteModal: false,
            isDeleting: false,
            deletingIndex: -1,
            users: [],
            roles: {},
            loggedUser: {},
            stores: [],
        }
    },
    methods : {
        async addUser(){ //Add product

            if(this.data.fullName.trim() === '')    return this.err('Ism majburiy.')
            if(this.data.role_id === '')            return this.err('Turi majburiy.')
            if(this.data.store_id === '')           return this.err("Do'kon majburiy.")
            if(this.data.username.trim() === '')    return this.err('Username majburiy.')
            if(this.data.password.trim() === '')    return this.err('Parol majburiy.')

            const res = await this.callApi('post', '/app/add_user', this.data)

            if(res.status === 201) {
                this.users.unshift(res.data);
                this.s('Foydalanuvchi muvoffaqiyatli qo\'shildi!')
                this.addModal = false
                this.data.fullName = ''
                this.data.username = ''
                this.data.password = ''
                this.data.role_id = ''
                this.data.store_id = ''
            } else {
                if(res.status == 422) {
                    if(res.data.errors.fullName) {
                        this.err(res.data.errors.fullName[0])
                    }

                    if(res.data.errors.username) {
                        this.err(res.data.errors.username[0])
                    }

                    if(res.data.errors.role_id) {
                        this.err(res.data.errors.role_id[0])
                    }

                    if(res.data.errors.password) {
                        this.err(res.data.errors.password[0])
                    }
                } else {
                    this.swr()
                }
            }
        },

        async editUser(){ //edit product
            if(this.editData.fullName.trim() === '')    return this.err('Ism majburiy.')
            if(this.editData.store_id === '')           return this.err("Do'kon majburiy.")
            if(this.editData.username.trim() === '')    return this.err('Username majburiy.')
            if(this.editData.role_id === '')            return this.err('Turi majburiy.')
            

            const res = await this.callApi('post', '/app/edit_user', this.editData)

            if(res.status === 200) {
                this.users[this.index].fullName  = res.data.fullName
                this.users[this.index].username  = res.data.username
                this.users[this.index].role_id  = res.data.role_id
                this.users[this.index].roleName  = res.data.roleName
                this.users[this.index].store_id  = res.data.store_id
                this.users[this.index].storeName  = res.data.storeName

                this.s('Foydalanuvchi ma\'lumotlari muvoffaqiyatli saqlandi!')
                this.editModal = false
            } else {
                if(res.status == 422) {
                    if(res.data.errors.fullName) {
                        this.err(res.data.errors.fullName[0])
                    }

                    if(res.data.errors.username) {
                        this.err(res.data.errors.username[0])
                    }

                    if(res.data.errors.password) {
                        this.err(res.data.errors.password[0])
                    }
                } else {
                    this.swr()
                }
            }
        },

        async showEditModal(user, index) {
            let userObj = {
                id:       user.id,
                fullName: user.fullName,
                username: user.username,
                role_id:  user.role_id,
                store_id: user.store_id,
            }

            this.editData = userObj
            this.editModal = true
            this.index = index
        },
        editModalClose(){
            this.editModal = false
        },

        async deleteUser() {
            this.isDeleting = true
            const res = await this.callApi("post", "/app/delete_user", this.user)
            if(res.status === 200) {
                this.users.splice(this.deletingIndex,1)
                this.s('Foydalananuvchi o\'chirildi!')
            } else {
                this.swr();
            }

            this.isDeleting = false
            this.showDeleteModal = false
        },

        async showDeletingModal(user, i){
            this.user               = user
            this.deletingIndex      = i
            this.showDeleteModal    = true
        },
    },

    async created(){
        const [users, roles, loggedUser, stores] = await Promise.all([
            this.callApi('get', '/app/get_users'),
            this.callApi('get', '/app/get_roles'),
            this.callApi('get', '/app/logged_user'),
            this.callApi('get', '/app/get_stores'),

        ])

        this.users = users.data
        this.roles = roles.data
        this.loggedUser = loggedUser.data
        this.stores = stores.data

        // console.log(this.loggedUser);
        console.log(this.users);

    },
}
</script>

<style scoped>

</style>
