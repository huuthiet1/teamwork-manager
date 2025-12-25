import { createRouter, createWebHistory } from 'vue-router'

// ================= PUBLIC PAGES =================
import Landing from '@/pages/LandingPage.vue'
import Login from '@/pages/auth/Login.vue'
import Register from '@/pages/auth/Register.vue'

// ================= LAYOUT =================
import DashboardLayout from '@/layouts/DashboardLayout.vue'

// ================= DASHBOARD =================
import Dashboard from '@/pages/Dashboard.vue'

// ================= GROUP =================
import GroupDetail from '@/pages/groups/GroupDetail.vue'
import GroupMembers from '@/pages/groups/GroupMembers.vue'
import GroupSettings from '@/pages/groups/GroupSettings.vue'

// ================= TASK =================
import TaskList from '@/pages/tasks/TaskList.vue'
import TaskCreate from '@/pages/tasks/TaskCreate.vue'
import TaskDetail from '@/pages/tasks/TaskDetail.vue'

// ================= SUBMISSION =================
import SubmissionDetail from '@/pages/submissions/SubmissionDetail.vue'

// ================= CHAT =================
import ChatPage from '@/pages/chat/ChatPage.vue'

// ================= NOTIFICATION =================
import NotificationList from '@/pages/notifications/NotificationList.vue'

// ================= AI =================
import AiDashboard from '@/pages/ai/AiDashboard.vue'

// ================= REPORT =================
import ReportExport from '@/pages/reports/ReportExport.vue'

// ================= CONTRIBUTION =================
import ContributionPage from '@/pages/contributions/ContributionPage.vue'

// ================= PROFILE =================
import Profile from '@/pages/profile/Profile.vue'
import ChangePassword from '@/pages/profile/ChangePassword.vue'

// ================= ERROR =================
import Forbidden from '@/pages/errors/Forbidden.vue'
import NotFound from '@/pages/errors/NotFound.vue'

// =================================================

const routes = [
  // ===== PUBLIC =====
  {
    path: '/',
    component: Landing,
  },
  {
    path: '/login',
    component: Login,
  },
  {
    path: '/register',
    component: Register,
  },

  // ===== DASHBOARD (AUTH REQUIRED) =====
  {
    path: '/',
    component: DashboardLayout,
    meta: { requiresAuth: true },
    children: [
      // ----- DASHBOARD -----
      {
        path: 'dashboard',
        name: 'dashboard',
        component: Dashboard,
      },

      // ----- GROUP -----
      {
        path: 'groups',
        component: () => import('@/pages/groups/GroupList.vue'),
    },
      {
        path: 'groups/:groupId',
        component: GroupDetail,
      },
      {
        path: 'groups/:groupId/members',
        component: GroupMembers,
      },
      {
        path: 'groups/:groupId/settings',
        component: GroupSettings,
        meta: { requiresLeader: true },
      },

      // ----- TASK -----
      {
        path: 'tasks',
        component: TaskList,
        meta: { requiresGroup: true },
      },
      {
        path: 'tasks/create',
        component: TaskCreate,
        meta: { requiresGroup: true, requiresLeader: true },
      },
      {
        path: 'tasks/:taskId',
        component: TaskDetail,
        meta: { requiresGroup: true },
      },

      // ----- SUBMISSION -----
      {
        path: 'submissions/:taskId',
        component: SubmissionDetail,
        meta: { requiresGroup: true },
      },

      // ----- CHAT -----
      {
        path: 'chat',
        component: ChatPage,
        meta: { requiresGroup: true },
      },

      // ----- NOTIFICATION -----
      {
        path: 'notifications',
        component: NotificationList,
      },

      // ----- AI -----
      {
        path: 'ai',
        component: AiDashboard,
      },

      // ----- REPORT -----
      {
        path: 'reports',
        component: ReportExport,
        meta: { requiresGroup: true, requiresLeader: true },
      },

      // ----- CONTRIBUTION -----
      {
        path: 'contributions',
        component: ContributionPage,
        meta: { requiresGroup: true, requiresLeader: true },
      },

      // ----- PROFILE -----
      {
        path: 'profile',
        component: Profile,
      },
      {
        path: 'profile/change-password',
        component: ChangePassword,
      },

      // ----- ERROR -----
      {
        path: '403',
        component: Forbidden,
      },
    ],
  },

  // ===== 404 =====
  {
    path: '/:pathMatch(.*)*',
    component: NotFound,
  },
]

// =================================================

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// ================= GLOBAL GUARD =================
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  const currentGroup = localStorage.getItem('current_group_id')

  // 🔐 Auth check
  if (to.meta.requiresAuth && !token) {
    return next('/login')
  }

  // 🔁 Logged in rồi thì không vào login/register
  if ((to.path === '/login' || to.path === '/register') && token) {
    return next('/dashboard')
  }

  // 👥 Bắt buộc chọn group
  if (to.meta.requiresGroup && !currentGroup) {
    return next('/dashboard')
  }

  // (Leader check sẽ làm ở backend + UI, FE chỉ hint)
  next()
})

export default router
