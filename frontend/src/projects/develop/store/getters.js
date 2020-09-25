const getters = {
  sidebar: state => state.app.sidebar,
  device: state => state.app.device,
  token: state => state.user.token,
  primary: state => state.user.is_primary,
  name: state => state.user.name,
  roles: state => state.user.roles
}
export default getters
