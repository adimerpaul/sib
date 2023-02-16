import { RouteRecordRaw } from 'vue-router'
import MainLayout from 'layouts/MainLayout.vue'
import IndexPage from 'pages/IndexPage.vue'
import Login from 'pages/Login.vue'
import Colegiados from 'pages/Colegiados.vue'
import Certificados from 'pages/Certificados.vue'
const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: MainLayout,
    children: [
      { path: '', component: IndexPage },
      { path: 'colegiados', component: Colegiados },
      { path: 'certificados', component: Certificados }
    ]
  },
  {
    path: '/login',
    component: Login
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
