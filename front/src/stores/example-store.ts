import { defineStore } from 'pinia'

export const useCounterStore = defineStore('counter', {
  state: () => ({
    counter: 0,
    isLogged: !!localStorage.getItem('tSib'),
    user: {},
    roles: []
  }),
  getters: {
    doubleCount: (state) => state.counter * 2
  },
  actions: {
    increment () {
      this.counter++
    }
  }
})
