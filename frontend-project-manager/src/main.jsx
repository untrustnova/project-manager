import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
// import {} from react-dom/server
import './index.css'
import App from './App.jsx'

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <App />
  </StrictMode>,
)
