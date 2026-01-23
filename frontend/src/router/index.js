import { createRouter, createWebHistory } from 'vue-router'

import LoginView from '../views/LoginView.vue'
import CheckinView from '@/views/CheckinView.vue'
import SalesDashboardView from '../views/SalesDashboardView.vue'
import CustomerDashboardView from '@/views/CustomerDashboardView.vue'
import SpvRekapView from '@/views/SpvRekapView.vue'
import DashboardView from '@/views/DashboardView.vue'
import UserView from '@/views/UserView.vue'
import BranchesView from '@/views/BranchesView.vue'
import CustomerDatabaseView from '@/views/CustomerDatabaseView.vue'
import RecapReportView from '@/views/RecapReportView.vue'
import OptionReport from '@/views/OptionReport.vue'
import FollowUp from '@/components/FollowUp.vue'
import PhoneCall from '@/components/PhoneCall.vue'
import OptionVisit from '@/views/OptionVisit.vue'
import Checkout from '@/components/Checkout.vue'
import RecapREGCustomer from '@/components/RecapREGCustomer.vue'
import RecapBCICustomer from '@/components/RecapBCICustomer.vue'
import Assignment from '@/components/Assignment.vue'
import RecapSpv from '@/components/RecapSpv.vue'
import Achievement from '@/components/Achievement.vue'
import RecapGroup from '@/components/RecapGroup.vue'
import Revenue from '@/components/Revenue.vue'
import AssigmentProgress from '@/components/AssigmentProgress.vue'
import Kpi from '@/components/Kpi.vue'
import TargetKpi from '@/components/TargetKpi.vue'
import TargetKpiList from '@/components/TargetKpiList.vue'
import AttendanceList from '@/components/AttendanceList.vue'
import AttendanceForm from '@/components/AttendanceForm.vue'
import BciData from '@/components/BciData.vue'
import EditByUser from '@/components/EditByUser.vue'
import AssignedCustomer from '@/components/AssignedCustomer.vue'



const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: DashboardView,
    },
    {
      path: '/sales',
      name: 'sales',
      component: SalesDashboardView,
    },
    {
      path: '/customer',
      name: 'customer',
      component: CustomerDashboardView,
    },
    {
      path: '/spvdashboard',
      name: 'spv dashboard',
      component: SpvRekapView,
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/login',
      name: 'Login',
      component: LoginView,
    },
    {
      path: '/checkin',
      name: 'Checkin',
      component: CheckinView,
    },
    {
      path: '/checkout/:id',
      name: 'Checkout',
      component: Checkout,
    },
    {
      path: '/adduser',
      name: 'Add User',
      component: UserView,
    },
    {
      path: '/branches',
      name: 'Branches',
      component: BranchesView,
    },
    {
      path: '/customerdata',
      name: 'Customer Data',
      component: CustomerDatabaseView,
    },
    {
      path: '/recapreport',
      name: 'Recap Report ',
      component: RecapReportView,
    },
    {
      path: '/optionreport',
      name: 'Option Report ',
      component: OptionReport,
    },
    {
      path: '/followup',
      name: 'Follow Up',
      component: FollowUp,
    },
    {
      path: '/phonecall',
      name: 'Phone Call',
      component: PhoneCall,
    },
    {
      path: '/optionvisit',
      name: 'Option Visit',
      component: OptionVisit,
    },
    {
      path: "/recap-customer/bci",
      name: "RecapBCI",
      component: RecapBCICustomer,
    },
    {
      path: "/recap-customer/reg",
      name: "RecapREG",
      component: RecapREGCustomer,
    },
    {
      path: "/assignment",
      name: "Assignment",
      component: Assignment,
    },
    {
      path: "/personal-report/recaptype",
      name: "personal-report recap type",
      component: RecapSpv,
    },
    {
      path: "/personal-report/achivement",
      name: "personal-report achivement",
      component: Achievement,
    },
    {
      path: "/group-report/recap",
      name: "group-report recap",
      component: RecapGroup,
    },
    {
      path: "/revenue",
      name: "revenue",
      component: Revenue,
    },
    {
      path: "/assignmentprecentage",
      name: "assigment progress",
      component: AssigmentProgress,
    },
    {
      path: "/kpi",
      name: "KPI Sales",
      component: Kpi,
    },
    {
      path: "/targetkpilist",
      name: "kpi target list",
      component: TargetKpiList,
    },
    {
      path: "/targetkpi/:userId",
      name: "kpi target",
      component: TargetKpi,
      props: true
    },
    {
      path:'/attendance',
      name:'attendance-list',
      component: AttendanceList
    },
    {
      path:'/profile',
      name:'Profile',
      component: EditByUser
    },
    {
      path:'/assignedcustomer',
      name:'assigned',
      component: AssignedCustomer
    },
    {
      path:'/attendance/input/:userId',
      name:'attendance-form',
      component: AttendanceForm,
      props:true
    },
    {
      path:'/bcidatabase',
      name:'bci database',
      component: BciData,
      props:true
    }
    
  ],
})


router.beforeEach((to, from, next) => {
  const isAuth = localStorage.getItem('api_token'); // Menggunakan token, bukan boolean

  if (to.name !== "Login" && !isAuth) {
    next({ name: 'Login' });
  } else if (to.name === "Login" && isAuth) {
    next({ name: 'home' });
  } else {
    next();
  }
});
export default router
