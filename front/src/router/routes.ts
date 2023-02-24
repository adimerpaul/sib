import { RouteRecordRaw } from 'vue-router'
import MainLayout from 'layouts/MainLayout.vue'
import IndexPage from 'pages/IndexPage.vue'
import Login from 'pages/Login.vue'
import Colegiados from 'pages/Colegiados.vue'
import Certificados from 'pages/Certificados.vue'
import Users from 'pages/Users.vue'
import Datos from 'pages/Datos.vue'
import Seguimientos from 'pages/Seguimientos.vue'
import Ingresos from 'pages/Ingresos.vue'
import Activos from 'pages/Activos.vue'
import Cargos from 'pages/Cargos.vue'
import Planillas from 'pages/Planillas.vue'
const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: MainLayout,
    children: [
      { path: '', component: IndexPage, meta: { requiresAuth: true } },
      { path: 'colegiados', component: Colegiados, meta: { requiresAuth: true } },
      { path: 'certificados', component: Certificados, meta: { requiresAuth: true } },
      { path: 'users', component: Users, meta: { requiresAuth: true } },
      { path: 'datos', component: Datos, meta: { requiresAuth: true } },
      { path: 'seguimientos', component: Seguimientos, meta: { requiresAuth: true } },
      { path: 'ingresos', component: Ingresos, meta: { requiresAuth: true } },
      { path: 'activos', component: Activos, meta: { requiresAuth: true } },
      { path: 'cargos', component: Cargos, meta: { requiresAuth: true } },
      { path: 'planillas', component: Planillas, meta: { requiresAuth: true } }
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
