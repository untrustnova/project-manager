import { Outlet, useLocation } from "react-router-dom"
import { createContext, useContext, useEffect, useState } from "react"
import isJson from "../lib/is-json"

const Authcontext = createContext()

export default function AuthorizationProvider({ children }) {
  const location = useLocation()
  const [dataUser, setDataUser] = useState({ loading: true, data: {}, token: "" })
  function SetDataAuth(token = "", datauser = {}) {
    const session_v_token = String(token).trim()
    const session_v_data = typeof datauser === "object"? JSON.stringify(datauser):String(datauser)
    localStorage.setItem("token", session_v_token)
    localStorage.setItem("user-data", session_v_data)
  }
  function GetDataAuth() {
    const session_user = localStorage.getItem("token")
    const session_data = localStorage.getItem("user-data")
    const dataresult = {
      token: String(session_user).trim(),
      user: isJson(session_data)? JSON.parse(session_data):{}
    }
    return dataresult
  }
  function CheckAuth() {
    const a = GetDataAuth()
    if(a.token && a.user) {
      setDataUser({ loading: false, data: a.user, token: a.token })
    }
    console.log(a)
  }

  useEffect(() => {
    CheckAuth()
  }, [location])

  return <Authcontext.Provider value={{
    SetDataAuth, GetDataAuth, userauth: dataUser
  }}>
    {children? children:<Outlet />}
  </Authcontext.Provider>
}

export function useAuthorization() {
  const a = useContext(Authcontext)

  return {
    userauth: a.userauth,
    GetDataAuth: () => a.GetDataAuth(),
    SetDataAuth: (token, userdata = {}) => a.SetDataAuth(token, userdata),
  }
}