import { RouteRecordRaw } from 'vue-router'
import MainLayout from 'layouts/MainLayout.vue'
import IndexPage from 'pages/IndexPage.vue'
import Login from 'pages/Login.vue'
import Colegiados from 'pages/Colegiados.vue'
import Certificados from 'pages/Certificados.vue'
import Users from 'pages/Users.vue'
const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: MainLayout,
    children: [
      { path: '', component: IndexPage, meta: { requiresAuth: true } },
      { path: 'colegiados', component: Colegiados, meta: { requiresAuth: true } },
      { path: 'certificados', component: Certificados, meta: { requiresAuth: true } },
      { path: 'users', component: Users, meta: { requiresAuth: true } }
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
