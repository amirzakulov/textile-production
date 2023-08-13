import {defineStore} from 'pinia'

export const useUsersStore = defineStore("usersStore", {
    state: () => {
        return {
            users: [],
            user: {},

            addModal: false,
            editModal: false,
            deleteModal: false,

            roles: [],
            role: {},
            loggedUser: {},
            rowIndex: 0,

        }
    },

    getters: {},

    actions: {

    }
})
