import { constantRoutes, asyncRoutesMap } from '@/router'
import { getRouter } from '@/api/router'

/**
 * Use meta.role to determine if the current user has permission
 * @param roles
 * @param route
 */
function hasPermission(roles, route) {
  if (route.meta && route.meta.roles) {
    return roles.some(role => route.meta.roles.includes(role))
  } else {
    return true
  }
}

/**
 * Filter asynchronous routing tables by recursion
 * @param routes asyncRoutes
 * @param roles
 */
export function filterAsyncRoutes(routes, roles) {
  const res = []
  routes.forEach(route => {
    const tmp = { ...route }
    if (hasPermission(roles, tmp)) {
      if (tmp.children) {
        tmp.children = filterAsyncRoutes(tmp.children, roles)
      }
      res.push(tmp)
    }
  })

  return res
}

const state = {
  routes: [],
  addRoutes: []
}

const mutations = {
  SET_ROUTES: (state, routes) => {
    state.addRoutes = routes
    state.routes = constantRoutes.concat(routes)
  }
}

const actions = {
  generateRoutes: async function({ commit }, roles) {
    var res = await getRouter()
    var asyncRoutes = res.data
    asyncRoutes = asyncRoutes.filter(current => {
      if (current.children == null) {
        delete current.children
      }
      // 重构路由结构
      // console.log(replaceComponent(asyncRoutes))
      return replaceComponent(current)
    })
    let accessedRoutes
    if (roles.includes('admin')) {
      accessedRoutes = asyncRoutes || []
    } else {
      accessedRoutes = filterAsyncRoutes(asyncRoutes, roles)
    }
    commit('SET_ROUTES', accessedRoutes)
    // resolve(accessedRoutes)
    return accessedRoutes
  }
}
/**
 * 菜单
 * @param comp
 * @returns {*}
 */
function replaceComponent(comp) {
  if (comp.component) {
    comp.component = asyncRoutesMap[comp.component]
  }
  if (comp.children && comp.children.length > 0) {
    for (let i = 0; i < comp.children.length; i++) {
      comp.children[i] = replaceComponent(comp.children[i])
    }
  }
  return comp
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
