import { useState } from "react"
import { FiMail, FiLock } from "react-icons/fi"
import { AiOutlineEye, AiOutlineEyeInvisible } from "react-icons/ai"
import RequestURL from "../lib/request"
import { useNavigate } from "react-router-dom"

function HandleAuthSession(token, user) {
  localStorage.setItem("token", String(token).trim())
  localStorage.setItem("user-data", JSON.stringify(user))
}

const Login = () => {
  const route = useNavigate()
  // Params
  const [showPassword, setShowPassword] = useState(false)
  // Forms
  const [email, setEmail] = useState("")
  const [password, setPassword] = useState("")
  const [codeOTP, setCodeOTP] = useState("")
  // Data Action
  const [dataForm, setDataForm] = useState({ type: "login", loading: false, error: "" })
  async function handleOTP() {
    setDataForm({ ...dataForm, loading: true, error: "", })
    const request = await RequestURL({
      url: "/auth/verify-otp",
      method: "POST",
      headers: {
        "content-type": "application/json"
      },
      data: {
        email: email,
        otp: codeOTP
      }
    })
    if(request.isError) {
      setDataForm({ ...dataForm, loading: false, error: request?.clientError||request?.data?.message||"Masalah tidak diketahui" })
      return;
    }
    // Token?
    if(request.data.token) {
      HandleAuthSession(request.data.token, request.data.user)
      route("/") // Finish! (ID: 00041)
      return; // Redirect Finish!
    }
    setDataForm({ ...dataForm, loading: false })
  }
  async function handleSubmit(e) {
    e.preventDefault()
    if(dataForm.type === "otp") {
      return handleOTP()
    }
    setDataForm({ ...dataForm, loading: true })
    const request = await RequestURL({
      url: "/auth/login",
      method: "POST",
      headers: {
        "content-type": "application/json"
      },
      data: {
        email: email,
        password: password
      }
    })
    if(request.data.requires_verification) {
      setDataForm({ ...dataForm, type: "otp", loading: false })
      return;
    }
    if(request.isError) {
      setDataForm({ ...dataForm, loading: false, error: request?.clientError||request?.data?.message||"Masalah tidak diketahui" })
      return;
    }
    if(request.data.token && request.data.user) {
      HandleAuthSession(request.data.token, request.data.user)
      route("/") // Finish! (ID: 00041)
      return; // Finish Login!
    }
  }
  console.log(dataForm)

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-b from-[#5db3dd] to-[#e6f1f6]">
      <div className="w-full max-w-md bg-white rounded-xl shadow-lg p-8 mx-4">
        <div className="text-center mb-5">
          <div className="flex justify-center">
            <img
              src="/crocodic-logo.png"
              alt="Logo"
              className="h-12 w-auto"
            />
          </div>
          <p className="text-gray-500 mt-4.5">
            Welcome back! Please sign in to your account.
          </p>
        </div>

        {!!dataForm.error && (
          <div className="flex items-center p-4 mb-6 text-sm text-red-700 bg-red-100 rounded-lg">
            <svg className="flex-shrink-0 inline w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
              <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clipRule="evenodd" />
            </svg>
            <span>{dataForm.error}</span>
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-6">
          {dataForm.type === "login"&&<>
            <div>
              <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">
                Email
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <FiMail className="h-5 w-5 text-gray-400" />
                </div>
                <input
                  id="email"
                  type="email"
                  placeholder="your@email.com"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  required
                  className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                />
              </div>
            </div>

            <div>
              <div className="w-full flex justify-between items-center mb-1">
                <label htmlFor="password" className="block text-sm font-medium text-gray-700">
                  Password
                </label>
                {/* <a href="#" className="text-sm text-indigo-600 hover:text-indigo-500 hover:underline">
                  Forgot password?
                </a> */}
              </div>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <FiLock className="h-5 w-5 text-gray-400" />
                </div>
                <input
                  id="password"
                  type={showPassword ? "text" : "password"}
                  placeholder="••••••••"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  required
                  className="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                />
                <button
                  type="button"
                  className="absolute inset-y-0 right-0 pr-3 flex items-center"
                  onClick={() => setShowPassword(!showPassword)}
                >
                  {showPassword ? (
                    <AiOutlineEyeInvisible className="h-5 w-5 text-gray-400 hover:text-gray-500" />
                  ) : (
                    <AiOutlineEye className="h-5 w-5 text-gray-400 hover:text-gray-500" />
                  )}
                </button>
              </div>
            </div>
          </>}
          {dataForm.type === "otp"&&<>
            <div>
              <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">Kode OTP</label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <FiMail className="h-5 w-5 text-gray-400" />
                </div>
                <input
                  id="otpcode"
                  type="text"
                  placeholder="######"
                  value={codeOTP}
                  onChange={(e) => setCodeOTP(e.target.value)}
                  required
                  className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                />
              </div>
            </div>
          </>}

          <button
            type="submit"
            disabled={dataForm.loading}
            className={`w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ${dataForm.loading ? "opacity-75 cursor-not-allowed" : ""}`}
          >
            {dataForm.loading ? (
              <>
                <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                  <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Signing in...
              </>
            ) : (
              "SIGN IN"
            )}
          </button>
        </form>

        <p className="text-neutral-600 text-sm text-center mt-3.5">Jika lupa kata sandi / belum memiliki akun, silahkan kontak admin/support</p>
      </div>
    </div>
  )
}

export default Login
