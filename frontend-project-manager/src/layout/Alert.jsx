import { useContext, createContext, useState, useEffect } from "react"

const Alertcontext = createContext()

export default function AlertProvider({ children }) {
  const [showArrayAlert, setShowArrayAlert] = useState([])

  function showAlert(title = "", message = "", buttons = []) {
    let listAlert = showArrayAlert
    const btnAry = Array.isArray(buttons)
    console.log(showArrayAlert)
    listAlert.push({
      title: String(title).trim(),
      message: String(message).trim(),
      buttons: (btnAry? !buttons[0]? [{ text: "Oke" }]:buttons:[{ text: "Oke" }]).map(a => ({
        text: a.text,
        onPress: typeof a?.onPress === "function"?a?.onPress:null,
      }))
    })
    setShowArrayAlert([...listAlert])
  }

  return <Alertcontext.Provider value={{
    showAlert,
  }}>
    <div className={"fixed top-0 left-0 w-full h-screen justify-center items-center flex z-50 duration-300 "+(showArrayAlert.length !== 0? "bg-black/40 backdrop-blur-[2px]":"bg-black/0 pointer-events-none")}>
      {showArrayAlert.map((items, i) => (
        <div className="max-w-[400px] min-w-[200px] bg-white rounded-xl shadow-xl p-3.5 px-4.5">
          <h2 className="text-xl font-semibold">{items.title}</h2>
          <p>{items.message}</p>
          <div className="flex justify-end items-center">
            {items.buttons.map((btn, k) => (
              <button className="cursor-pointer hover:bg-blue-500/50 p-1 px-3.5 rounded-md" key={k} onClick={() => {
                if(typeof btn.onPress === "function") {
                  btn.onPress()
                }
                // const listAlert = showArrayAlert.filter(a != a.id)
                // setShowArrayAlert([...listAlert])
              }}>{btn.text}</button>
            ))}
          </div>
        </div>
      ))}
    </div>
    <>
      {children}
    </>
  </Alertcontext.Provider>
}

export function useAlert() {
  const a = useContext(Alertcontext)

  return (title = "", message = "", buttons = []) => {
    a.showAlert(title, message, buttons)
  }
}